<?php
require_once __DIR__ . '/../../config/conexionGlobal.php'; // Ruta corregida

class HuespedModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
    }

    /**
     * Crear un nuevo huésped
     */
    public function crearHuesped($datos) {
        try {
            $sql = "INSERT INTO tp_huespedes (
                        numDocumento, 
                        numTelefono, 
                        correo, 
                        nombres, 
                        apellidos, 
                        tipoDocumento, 
                        sexo
                    ) VALUES (
                        :numDocumento, 
                        :numTelefono, 
                        :correo, 
                        :nombres, 
                        :apellidos, 
                        :tipoDocumento, 
                        :sexo
                    )";

            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':numDocumento', $datos['numDocumento'], PDO::PARAM_STR);
            $stmt->bindParam(':numTelefono', $datos['numTelefono'], PDO::PARAM_STR);
            $stmt->bindParam(':correo', $datos['correo'], PDO::PARAM_STR);
            $stmt->bindParam(':nombres', $datos['nombres'], PDO::PARAM_STR);
            $stmt->bindParam(':apellidos', $datos['apellidos'], PDO::PARAM_STR);
            $stmt->bindParam(':tipoDocumento', $datos['tipoDocumento'], PDO::PARAM_STR);
            $stmt->bindParam(':sexo', $datos['sexo'], PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al crear huésped: " . $e->getMessage());
            throw new Exception("Error al crear el huésped: " . $e->getMessage());
        }
    }

    /**
     * Verificar si existe un huésped con el número de documento dado
     */
    public function existeHuesped($numDocumento) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tp_huespedes WHERE numDocumento = :numDocumento";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numDocumento', $numDocumento, PDO::PARAM_STR);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;

        } catch (PDOException $e) {
            error_log("Error al verificar huésped: " . $e->getMessage());
            throw new Exception("Error al verificar la existencia del huésped");
        }
    }

    /**
     * Obtener un huésped por número de documento
     */
    public function obtenerHuespedPorDocumento($numDocumento) {
        try {
            $sql = "SELECT * FROM tp_huespedes WHERE numDocumento = :numDocumento";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numDocumento', $numDocumento, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener huésped: " . $e->getMessage());
            throw new Exception("Error al obtener el huésped");
        }
    }

    /**
     * Obtener todos los huéspedes
     */
    public function obtenerTodosLosHuespedes() {
        try {
            $sql = "SELECT * FROM tp_huespedes ORDER BY apellidos, nombres";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener huéspedes: " . $e->getMessage());
            throw new Exception("Error al obtener los huéspedes");
        }
    }

    /**
     * Actualizar un huésped
     */
    public function actualizarHuesped($numDocumento, $datos) {
        try {
            // Construir la consulta SQL dinámicamente
            $campos = [];
            $parametros = [];
            
            foreach ($datos as $campo => $valor) {
                $campos[] = "$campo = :$campo";
                $parametros[":$campo"] = $valor;
            }
            
            $sql = "UPDATE tp_huespedes SET " . implode(', ', $campos) . " WHERE numDocumento = :numDocumento";
            $parametros[':numDocumento'] = $numDocumento;
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $parametros[$param], PDO::PARAM_STR);
            }
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al actualizar huésped: " . $e->getMessage());
            throw new Exception("Error al actualizar el huésped");
        }
    }

    /**
     * Eliminar un huésped
     */
    public function eliminarHuesped($numDocumento) {
        try {
            $sql = "DELETE FROM tp_huespedes WHERE numDocumento = :numDocumento";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numDocumento', $numDocumento, PDO::PARAM_STR);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al eliminar huésped: " . $e->getMessage());
            throw new Exception("Error al eliminar el huésped");
        }
    }

    /**
     * Buscar huéspedes por término de búsqueda
     */
    public function buscarHuespedes($termino) {
        try {
            $sql = "SELECT * FROM tp_huespedes 
                    WHERE nombres LIKE :termino 
                    OR apellidos LIKE :termino 
                    OR numDocumento LIKE :termino 
                    OR correo LIKE :termino
                    ORDER BY apellidos, nombres";
            
            $stmt = $this->db->prepare($sql);
            $terminoBusqueda = '%' . $termino . '%';
            $stmt->bindParam(':termino', $terminoBusqueda, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al buscar huéspedes: " . $e->getMessage());
            throw new Exception("Error al buscar huéspedes");
        }
    }

    /**
     * Obtener huéspedes paginados
     */
    public function obtenerHuespedesPaginados($pagina = 1, $registrosPorPagina = 10) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;
            
            // Obtener el total de registros
            $sqlTotal = "SELECT COUNT(*) as total FROM tp_huespedes";
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute();
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Obtener los registros de la página actual
            $sql = "SELECT * FROM tp_huespedes 
                    ORDER BY apellidos, nombres 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $huespedes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'huespedes' => $huespedes,
                'total' => $total,
                'pagina' => $pagina,
                'registrosPorPagina' => $registrosPorPagina,
                'totalPaginas' => ceil($total / $registrosPorPagina)
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener huéspedes paginados: " . $e->getMessage());
            throw new Exception("Error al obtener huéspedes paginados");
        }
    }

    /**
     * Verificar si un correo ya está registrado (excluyendo el huésped actual)
     */
    public function correoExiste($correo, $numDocumentoExcluir = null) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tp_huespedes WHERE correo = :correo";
            $parametros = [':correo' => $correo];
            
            if ($numDocumentoExcluir) {
                $sql .= " AND numDocumento != :numDocumento";
                $parametros[':numDocumento'] = $numDocumentoExcluir;
            }
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $valor, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $resultado['total'] > 0;

        } catch (PDOException $e) {
            error_log("Error al verificar correo: " . $e->getMessage());
            throw new Exception("Error al verificar el correo");
        }
    }

    /**
     * Obtener estadísticas de huéspedes
     */
    public function obtenerEstadisticas() {
        try {
            $estadisticas = [];
            
            // Total de huéspedes
            $sql = "SELECT COUNT(*) as total FROM tp_huespedes";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $estadisticas['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Distribución por sexo
            $sql = "SELECT sexo, COUNT(*) as cantidad FROM tp_huespedes GROUP BY sexo";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $estadisticas['porSexo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Distribución por tipo de documento
            $sql = "SELECT tipoDocumento, COUNT(*) as cantidad FROM tp_huespedes GROUP BY tipoDocumento";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $estadisticas['porTipoDocumento'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $estadisticas;

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            throw new Exception("Error al obtener estadísticas");
        }
    }
}
?>