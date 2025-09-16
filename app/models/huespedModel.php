<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class HuespedModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
        if (!$this->db) {
            throw new Exception("Error al conectar con la base de datos.");
        }
    }

    public function obtenerHuespedesPaginados($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            $selectClause = "SELECT h.numDocumento, h.tipoDocumento, h.nombres, h.apellidos, h.numTelefono, h.correo, h.sexo, h.fechaCreacion";
            $fromClause = " FROM tp_huespedes h";
            $whereClauses = ["h.id_hotel = :id_hotel"];
            $params = [':id_hotel' => $id_hotel];

            if (!empty($filtros['busqueda'])) {
                $busqueda = '%' . trim($filtros['busqueda']) . '%';
                $whereClauses[] = "(h.nombres LIKE :busqueda OR h.apellidos LIKE :busqueda OR h.numDocumento LIKE :busqueda OR h.correo LIKE :busqueda)";
                $params[':busqueda'] = $busqueda;
            }

            $whereSql = " WHERE " . implode(' AND ', $whereClauses);

            $sqlTotal = "SELECT COUNT(*) as total" . $fromClause . $whereSql;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($params);
            $totalRegistros = (int)$stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];

            $sql = $selectClause . $fromClause . $whereSql . " ORDER BY h.apellidos, h.nombres LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => &$val) {
                $stmt->bindParam($key, $val);
            }
            $stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            $huespedes = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'huespedes' => $huespedes,
                'pagina' => (int)$pagina,
                'total' => $totalRegistros,
                'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
            ];
        } catch (PDOException $e) {
            error_log("Error en HuespedModel::obtenerHuespedesPaginados: " . $e->getMessage());
            throw new Exception("Error al obtener los huéspedes.");
        }
    }

    public function obtenerPorDocumento($numDocumento) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM tp_huespedes WHERE numDocumento = :numDocumento");
            $stmt->bindParam(':numDocumento', $numDocumento, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en HuespedModel::obtenerPorDocumento: " . $e->getMessage());
            return false;
        }
    }

    public function crearHuesped($datos) {
        try {
            $sql = "INSERT INTO tp_huespedes (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, sexo, id_hotel)
                    VALUES (:numDocumento, :tipoDocumento, :nombres, :apellidos, :numTelefono, :correo, :sexo, :id_hotel)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numDocumento', $datos['numDocumento']);
            $stmt->bindParam(':tipoDocumento', $datos['tipoDocumento']);
            $stmt->bindParam(':nombres', $datos['nombres']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':numTelefono', $datos['numTelefono']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':sexo', $datos['sexo']);
            $stmt->bindParam(':id_hotel', $datos['id_hotel'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en HuespedModel::crearHuesped: " . $e->getMessage());
            if ($e->getCode() == '23000') { // Error de clave duplicada
                if (strpos($e->getMessage(), 'PRIMARY') !== false) {
                    throw new Exception("El número de documento ya está registrado.");
                }
                if (strpos($e->getMessage(), 'uk_correo') !== false) {
                    throw new Exception("El correo electrónico ya está registrado.");
                }
            }
            throw new Exception("Error al crear el huésped.");
        }
    }

    public function actualizarHuesped($numDocumentoOriginal, $datos) {
        try {
            $sql = "UPDATE tp_huespedes SET
                        numDocumento = :numDocumento,
                        tipoDocumento = :tipoDocumento,
                        nombres = :nombres,
                        apellidos = :apellidos,
                        numTelefono = :numTelefono,
                        correo = :correo,
                        sexo = :sexo
                    WHERE numDocumento = :numDocumentoOriginal";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numDocumento', $datos['numDocumento']);
            $stmt->bindParam(':tipoDocumento', $datos['tipoDocumento']);
            $stmt->bindParam(':nombres', $datos['nombres']);
            $stmt->bindParam(':apellidos', $datos['apellidos']);
            $stmt->bindParam(':numTelefono', $datos['numTelefono']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':sexo', $datos['sexo']);
            $stmt->bindParam(':numDocumentoOriginal', $numDocumentoOriginal);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en HuespedModel::actualizarHuesped: " . $e->getMessage());
            if ($e->getCode() == '23000') {
                throw new Exception("El nuevo número de documento o correo ya está en uso.");
            }
            throw new Exception("Error al actualizar el huésped.");
        }
    }

    public function eliminarHuesped($numDocumento) {
        try {
            // Verificar si el huésped tiene reservas
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM tp_reservas WHERE hue_numDocumento = :numDocumento");
            $stmtCheck->bindParam(':numDocumento', $numDocumento);
            $stmtCheck->execute();
            if ($stmtCheck->fetchColumn() > 0) {
                throw new Exception("No se puede eliminar el huésped porque tiene reservas asociadas.");
            }

            $stmt = $this->db->prepare("DELETE FROM tp_huespedes WHERE numDocumento = :numDocumento");
            $stmt->bindParam(':numDocumento', $numDocumento);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en HuespedModel::eliminarHuesped: " . $e->getMessage());
            throw new Exception("Error al eliminar el huésped.");
        }
    }

    public function buscarHuespedes($id_hotel, $termino) {
        try {
            $sql = "SELECT numDocumento, nombres, apellidos 
                    FROM tp_huespedes 
                    WHERE id_hotel = :id_hotel 
                    AND (nombres LIKE :termino OR apellidos LIKE :termino OR numDocumento LIKE :termino)
                    ORDER BY apellidos, nombres LIMIT 10";
            $stmt = $this->db->prepare($sql);
            $terminoBusqueda = '%' . $termino . '%';
            $stmt->bindParam(':id_hotel', $id_hotel, PDO::PARAM_INT);
            $stmt->bindParam(':termino', $terminoBusqueda, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en HuespedModel::buscarHuespedes: " . $e->getMessage());
            return [];
        }
    }

    public function verificarExistencia($campo, $valor, $id_hotel, $documentoActual = null) {
        try {
            if ($campo !== 'numDocumento' && $campo !== 'correo') {
                return ['existe' => false];
            }

            $sql = "SELECT COUNT(*) as count FROM tp_huespedes WHERE $campo = :valor AND id_hotel = :id_hotel";
            $params = [':valor' => $valor, ':id_hotel' => $id_hotel];

            if ($documentoActual) {
                $sql .= " AND numDocumento != :documentoActual";
                $params[':documentoActual'] = $documentoActual;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            return ['existe' => $resultado['count'] > 0];
        } catch (PDOException $e) {
            error_log("Error en HuespedModel::verificarExistencia: " . $e->getMessage());
            return ['existe' => false, 'error' => 'Error de base de datos.'];
        }
    }
}
?>