<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class PqrsModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
    }

    /**
     * Crear una nueva PQRS
     */
    public function crearPqrs($datos) {
        try {
            $sql = "INSERT INTO tp_pqrs (
                        tipo, 
                        descripcion, 
                        numDocumento, 
                        prioridad, 
                        categoria, 
                        estado,
                        id_hotel
                    ) VALUES (
                        :tipo, 
                        :descripcion, 
                        :numDocumento, 
                        :prioridad, 
                        :categoria, 
                        :estado,
                        :id_hotel
                    )";

            $stmt = $this->db->prepare($sql);
            
            // SOLUCIÓN: Usar execute() con un array de parámetros es la forma correcta y más segura en PDO.
            // Esto reemplaza las múltiples llamadas a bindParam.
            return $stmt->execute([
                ':tipo' => $datos['tipo'],
                ':descripcion' => $datos['descripcion'],
                ':numDocumento' => $datos['numDocumento'],
                ':prioridad' => $datos['prioridad'],
                ':categoria' => $datos['categoria'],
                ':estado' => $datos['estado'],
                ':id_hotel' => $datos['id_hotel']
            ]);

        } catch (PDOException $e) {
            error_log("Error al crear PQRS: " . $e->getMessage());
            throw new Exception("Error al crear la PQRS: " . $e->getMessage());
        }
    }

    /**
     * Verificar si existe una PQRS con el ID dado
     */
    public function pqrsExiste($id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tp_pqrs WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;

        } catch (PDOException $e) {
            error_log("Error al verificar PQRS: " . $e->getMessage());
            throw new Exception("Error al verificar la existencia de la PQRS");
        }
    }

    /**
     * Verificar si existe un usuario con el número de documento dado
     */
    public function usuarioExiste($numDocumento) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tp_usuarios WHERE numDocumento = :numDocumento";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numDocumento', $numDocumento, PDO::PARAM_STR);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;

        } catch (PDOException $e) {
            error_log("Error al verificar usuario: " . $e->getMessage());
            throw new Exception("Error al verificar la existencia del usuario");
        }
    }

    /**
     * Obtener información de un usuario por número de documento
     */
    public function obtenerUsuarioPorDocumento($numDocumento) {
        try {
            $sql = "SELECT numDocumento, nombres, apellidos, correo FROM tp_usuarios WHERE numDocumento = :numDocumento";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numDocumento', $numDocumento, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            throw new Exception("Error al obtener el usuario");
        }
    }

    /**
     * Obtener una PQRS por ID
     */
    public function obtenerPqrsPorId($id) {
        try {
            $sql = "SELECT p.*, 
                           u.nombres as usuario_nombres, 
                           u.apellidos as usuario_apellidos,
                           u.correo as usuario_correo
                    FROM tp_pqrs p 
                    LEFT JOIN tp_usuarios u ON p.numDocumento = u.numDocumento 
                    WHERE p.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener PQRS: " . $e->getMessage());
            throw new Exception("Error al obtener la PQRS");
        }
    }

    /**
     * Obtener todas las PQRS con filtros opcionales
     */
    public function obtenerTodasLasPqrs($filtro = null, $id_hotel = null) {
        try {
            $sql = "SELECT p.*, 
                           u.nombres as usuario_nombres, 
                           u.apellidos as usuario_apellidos
                    FROM tp_pqrs p 
                    LEFT JOIN tp_usuarios u ON p.numDocumento = u.numDocumento";
            
            $parametros = [];
            $where = [];
            
            if ($id_hotel) {
                $where[] = "p.id_hotel = :id_hotel";
                $parametros[':id_hotel'] = $id_hotel;
            }
            if ($filtro && $filtro !== 'all') {
                $where[] = "(p.estado = :filtro OR p.tipo = :filtro)";
                $parametros[':filtro'] = $filtro;
            }
            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            
            $sql .= " ORDER BY p.fechaRegistro DESC";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $valor, PDO::PARAM_STR);
                if ($param === ':id_hotel') {
                    $stmt->bindParam($param, $valor, PDO::PARAM_INT);
                }
            }

            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener PQRS: " . $e->getMessage());
            throw new Exception("Error al obtener las PQRS");
        }
    }

    /**
     * Actualizar una PQRS
     */
    public function actualizarPqrs($id, $datos) {
        try {
            // Construir la consulta SQL dinámicamente
            $campos = [];
            $parametros = [];
            
            foreach ($datos as $campo => $valor) {
                $campos[] = "$campo = :$campo";
                $parametros[":$campo"] = $valor;
            }
            
            $sql = "UPDATE tp_pqrs SET " . implode(', ', $campos) . " WHERE id = :id";
            $parametros[':id'] = $id;
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros
            foreach ($parametros as $param => $valor) {
                if ($param === ':id') {
                    $stmt->bindParam($param, $parametros[$param], PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($param, $parametros[$param], PDO::PARAM_STR);
                }
            }
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al actualizar PQRS: " . $e->getMessage());
            throw new Exception("Error al actualizar la PQRS");
        }
    }

    /**
     * Eliminar una PQRS
     */
    public function eliminarPqrs($id) {
        try {
            $sql = "DELETE FROM tp_pqrs WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al eliminar PQRS: " . $e->getMessage());
            throw new Exception("Error al eliminar la PQRS");
        }
    }

    /**
     * Buscar PQRS por término de búsqueda
     */
    public function buscarPqrs($termino, $id_hotel = null) {
        try {
            $sql = "SELECT p.*, 
                           u.nombres as usuario_nombres, 
                           u.apellidos as usuario_apellidos
                    FROM tp_pqrs p 
                    LEFT JOIN tp_usuarios u ON p.numDocumento = u.numDocumento";
            
            $where = [];
            $params = [];

            if ($id_hotel) {
                $where[] = "p.id_hotel = :id_hotel";
                $params[':id_hotel'] = $id_hotel;
            }

            if (!empty($termino)) {
                $where[] = "(p.id LIKE :termino OR p.descripcion LIKE :termino OR p.numDocumento LIKE :termino OR p.tipo LIKE :termino OR u.nombres LIKE :termino OR u.apellidos LIKE :termino)";
                $params[':termino'] = '%' . $termino . '%';
            }

            if (!empty($where)) {
                $sql .= " WHERE " . implode(' AND ', $where);
            }
            $sql .= " ORDER BY p.fechaRegistro DESC";
            
            $stmt = $this->db->prepare($sql);
            // La primera llamada a execute() con $params es correcta.
            $stmt->execute($params);
            // La segunda llamada es innecesaria y puede causar errores.
            // La eliminamos.
            // $stmt->execute(); 
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al buscar PQRS: " . $e->getMessage());
            throw new Exception("Error al buscar PQRS");
        }
    }

    /**
     * Obtener PQRS paginadas
     */
    public function obtenerPqrsPaginadas($pagina = 1, $registrosPorPagina = 10, $filtro = null, $id_hotel = null) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;
            
            // Construir WHERE clause
            $whereClause = '';
            $parametros = [];
            $where = [];
            
            if ($id_hotel) {
                $where[] = "p.id_hotel = :id_hotel";
                $parametros[':id_hotel'] = $id_hotel;
            }
            if ($filtro && $filtro !== 'all') {
                $where[] = '(p.estado = :filtro OR p.tipo = :filtro)';
                $parametros[':filtro'] = $filtro;
            }
            if (!empty($where)) {
                $whereClause = " WHERE " . implode(' AND ', $where);
            }
            
            // Obtener el total de registros
            $sqlTotal = "SELECT COUNT(*) as total FROM tp_pqrs p 
                        LEFT JOIN tp_usuarios u ON p.numDocumento = u.numDocumento 
                        $whereClause";
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($parametros);
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Obtener los registros de la página actual
            $sql = "SELECT p.*, 
                           u.nombres as usuario_nombres, 
                           u.apellidos as usuario_apellidos
                    FROM tp_pqrs p 
                    LEFT JOIN tp_usuarios u ON p.numDocumento = u.numDocumento 
                    $whereClause
                    ORDER BY p.fechaRegistro DESC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros de filtro
            foreach ($parametros as $param => &$valor) { // Usar referencia para bindParam
                if ($param === ':id_hotel') {
                    $stmt->bindParam($param, $valor, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($param, $valor, PDO::PARAM_STR);
                }
            }
            unset($valor); // Romper la referencia

            // Bind parámetros de paginación
            $stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute(); // Ejecutar sin argumentos, ya que todo fue bindeado
            
            $pqrs = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'pqrs' => $pqrs,
                'total' => $total,
                'pagina' => $pagina,
                'registrosPorPagina' => $registrosPorPagina,
                'totalPaginas' => ceil($total / $registrosPorPagina)
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener PQRS paginadas: " . $e->getMessage());
            throw new Exception("Error al obtener PQRS paginadas");
        }
    }

    /**
     * Obtener estadísticas de PQRS
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'Pendiente' THEN 1 ELSE 0 END) as pendientes,
                        SUM(CASE WHEN estado = 'Finalizado' THEN 1 ELSE 0 END) as finalizados,
                        SUM(CASE WHEN tipo = 'Quejas' THEN 1 ELSE 0 END) as quejas,
                        SUM(CASE WHEN tipo = 'Reclamos' THEN 1 ELSE 0 END) as reclamos,
                        SUM(CASE WHEN tipo = 'Sugerencias' THEN 1 ELSE 0 END) as sugerencias,
                        SUM(CASE WHEN tipo = 'Felicitaciones' THEN 1 ELSE 0 END) as felicitaciones,
                        SUM(CASE WHEN prioridad = 'Alto' THEN 1 ELSE 0 END) as alta_prioridad
                    FROM tp_pqrs";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            throw new Exception("Error al obtener estadísticas");
        }
    }

    /**
     * Obtener PQRS vencidas (que han pasado su fecha límite y siguen pendientes)
     */
    public function obtenerPqrsVencidas() {
        try {
            $sql = "SELECT p.*, 
                           u.nombres as usuario_nombres, 
                           u.apellidos as usuario_apellidos
                    FROM tp_pqrs p 
                    LEFT JOIN tp_usuarios u ON p.numDocumento = u.numDocumento 
                    WHERE p.estado = 'Pendiente' 
                    AND p.fechaLimite < CURDATE()
                    ORDER BY p.fechaLimite ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener PQRS vencidas: " . $e->getMessage());
            throw new Exception("Error al obtener PQRS vencidas");
        }
    }
}
?>