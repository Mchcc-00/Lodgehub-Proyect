<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class HabitacionesModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
    }

    // ==================== MÉTODOS PARA TIPOS DE HABITACIÓN ====================

    /**
     * Crear un nuevo tipo de habitación
     */
    public function crearTipoHabitacion($datos) {
        try {
            $sql = "INSERT INTO td_tipoHabitacion (
                        descripcion, 
                        cantidad
                    ) VALUES (
                        :descripcion, 
                        :cantidad
                    )";

            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':cantidad', $datos['cantidad'], PDO::PARAM_INT);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al crear tipo de habitación: " . $e->getMessage());
            throw new Exception("Error al crear el tipo de habitación: " . $e->getMessage());
        }
    }

    /**
     * Obtener todos los tipos de habitación
     */
    public function obtenerTiposHabitacion() {
        try {
            $sql = "SELECT * FROM td_tipoHabitacion ORDER BY descripcion";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener tipos de habitación: " . $e->getMessage());
            throw new Exception("Error al obtener los tipos de habitación");
        }
    }

    /**
     * Obtener un tipo de habitación por ID
     */
    public function obtenerTipoHabitacionPorId($id) {
        try {
            $sql = "SELECT * FROM td_tipoHabitacion WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener tipo de habitación: " . $e->getMessage());
            throw new Exception("Error al obtener el tipo de habitación");
        }
    }

    /**
     * Verificar si existe un tipo de habitación con el ID dado
     */
    public function tipoHabitacionExiste($id) {
        try {
            $sql = "SELECT COUNT(*) as total FROM td_tipoHabitacion WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;

        } catch (PDOException $e) {
            error_log("Error al verificar tipo de habitación: " . $e->getMessage());
            throw new Exception("Error al verificar la existencia del tipo de habitación");
        }
    }

    // ==================== MÉTODOS PARA HABITACIONES ====================

    /**
     * Crear una nueva habitación
     */
    public function crearHabitacion($datos) {
        try {
            $sql = "INSERT INTO tp_habitaciones (
                        numero, 
                        costo, 
                        capacidad, 
                        tipoHabitacion, 
                        foto, 
                        descripcion, 
                        estado,
                        descripcionMantenimiento,
                        estadoMantenimiento
                    ) VALUES (
                        :numero, 
                        :costo, 
                        :capacidad, 
                        :tipoHabitacion, 
                        :foto, 
                        :descripcion, 
                        :estado,
                        :descripcionMantenimiento,
                        :estadoMantenimiento
                    )";

            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':numero', $datos['numero'], PDO::PARAM_STR);
            $stmt->bindParam(':costo', $datos['costo'], PDO::PARAM_STR);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':tipoHabitacion', $datos['tipoHabitacion'], PDO::PARAM_INT);
            $stmt->bindParam(':foto', $datos['foto'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcionMantenimiento', $datos['descripcionMantenimiento'], PDO::PARAM_STR);
            $stmt->bindParam(':estadoMantenimiento', $datos['estadoMantenimiento'], PDO::PARAM_STR);

            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al crear habitación: " . $e->getMessage());
            throw new Exception("Error al crear la habitación: " . $e->getMessage());
        }
    }

    /**
     * Verificar si existe una habitación con el número dado
     */
    public function habitacionExiste($numero) {
        try {
            $sql = "SELECT COUNT(*) as total FROM tp_habitaciones WHERE numero = :numero";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado['total'] > 0;

        } catch (PDOException $e) {
            error_log("Error al verificar habitación: " . $e->getMessage());
            throw new Exception("Error al verificar la existencia de la habitación");
        }
    }

    /**
     * Obtener una habitación por número
     */
    public function obtenerHabitacionPorNumero($numero) {
        try {
            $sql = "SELECT h.*, 
                           t.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                    WHERE h.numero = :numero";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener habitación: " . $e->getMessage());
            throw new Exception("Error al obtener la habitación");
        }
    }

    /**
     * Obtener todas las habitaciones con filtros opcionales
     */
    public function obtenerTodasLasHabitaciones($filtro = null, $estado = null, $tipoHabitacion = null) {
        try {
            $sql = "SELECT h.*, 
                           t.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id";
            
            $conditions = [];
            $parametros = [];
            
            if ($filtro && $filtro !== 'all') {
                if ($filtro === 'Disponible' || $filtro === 'Reservada' || $filtro === 'Ocupada' || $filtro === 'Mantenimiento') {
                    $conditions[] = "h.estado = :filtro";
                    $parametros[':filtro'] = $filtro;
                } elseif (strpos($filtro, 'capacidad-') === 0) {
                    $capacidad = intval(str_replace('capacidad-', '', $filtro));
                    $conditions[] = "h.capacidad >= :capacidad";
                    $parametros[':capacidad'] = $capacidad;
                }
            }

            if ($estado) {
                $conditions[] = "h.estado = :estado";
                $parametros[':estado'] = $estado;
            }

            if ($tipoHabitacion) {
                $conditions[] = "h.tipoHabitacion = :tipoHabitacion";
                $parametros[':tipoHabitacion'] = $tipoHabitacion;
            }

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }
            
            $sql .= " ORDER BY h.numero ASC";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($parametros as $param => $valor) {
                if ($param === ':capacidad' || $param === ':tipoHabitacion') {
                    $stmt->bindParam($param, $valor, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($param, $valor, PDO::PARAM_STR);
                }
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones: " . $e->getMessage());
            throw new Exception("Error al obtener las habitaciones");
        }
    }

    /**
     * Obtener habitaciones disponibles
     */
    public function obtenerHabitacionesDisponibles($tipoHabitacion = null, $capacidadMinima = null) {
        try {
            $sql = "SELECT h.*, 
                           t.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                    WHERE h.estado = 'Disponible' AND h.estadoMantenimiento = 'Activo'";
            
            $parametros = [];
            
            if ($tipoHabitacion) {
                $sql .= " AND h.tipoHabitacion = :tipoHabitacion";
                $parametros[':tipoHabitacion'] = $tipoHabitacion;
            }
            
            if ($capacidadMinima) {
                $sql .= " AND h.capacidad >= :capacidadMinima";
                $parametros[':capacidadMinima'] = $capacidadMinima;
            }
            
            $sql .= " ORDER BY h.numero ASC";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $valor, PDO::PARAM_INT);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones disponibles: " . $e->getMessage());
            throw new Exception("Error al obtener habitaciones disponibles");
        }
    }

    /**
     * Actualizar una habitación
     */
    public function actualizarHabitacion($numero, $datos) {
        try {
            // Construir la consulta SQL dinámicamente
            $campos = [];
            $parametros = [];
            
            foreach ($datos as $campo => $valor) {
                $campos[] = "$campo = :$campo";
                $parametros[":$campo"] = $valor;
            }
            
            $sql = "UPDATE tp_habitaciones SET " . implode(', ', $campos) . " WHERE numero = :numero";
            $parametros[':numero'] = $numero;
            
            $stmt = $this->db->prepare($sql);
            
            // Bind de parámetros
            foreach ($parametros as $param => $valor) {
                if ($param === ':numero') {
                    $stmt->bindParam($param, $valor, PDO::PARAM_STR);
                } elseif (in_array($param, [':capacidad', ':tipoHabitacion'])) {
                    $stmt->bindParam($param, $valor, PDO::PARAM_INT);
                } elseif ($param === ':costo') {
                    $stmt->bindParam($param, $valor, PDO::PARAM_STR);
                } else {
                    $stmt->bindParam($param, $valor, PDO::PARAM_STR);
                }
            }
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al actualizar habitación: " . $e->getMessage());
            throw new Exception("Error al actualizar la habitación");
        }
    }

    /**
     * Eliminar una habitación
     */
    public function eliminarHabitacion($numero) {
        try {
            $sql = "DELETE FROM tp_habitaciones WHERE numero = :numero";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al eliminar habitación: " . $e->getMessage());
            throw new Exception("Error al eliminar la habitación");
        }
    }

    /**
     * Buscar habitaciones por término de búsqueda
     */
    public function buscarHabitaciones($termino) {
        try {
            $sql = "SELECT h.*, 
                           t.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                    WHERE h.numero LIKE :termino 
                    OR h.descripcion LIKE :termino 
                    OR h.estado LIKE :termino 
                    OR t.descripcion LIKE :termino
                    ORDER BY h.numero ASC";
            
            $stmt = $this->db->prepare($sql);
            $terminoBusqueda = '%' . $termino . '%';
            $stmt->bindParam(':termino', $terminoBusqueda, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al buscar habitaciones: " . $e->getMessage());
            throw new Exception("Error al buscar habitaciones");
        }
    }

    /**
     * Obtener habitaciones paginadas
     */
    public function obtenerHabitacionesPaginadas($pagina = 1, $registrosPorPagina = 10, $filtro = null, $estado = null, $tipoHabitacion = null) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;
            
            // Construir WHERE clause
            $conditions = [];
            $parametros = [];
            
            if ($filtro && $filtro !== 'all') {
                if ($filtro === 'Disponible' || $filtro === 'Reservada' || $filtro === 'Ocupada' || $filtro === 'Mantenimiento') {
                    $conditions[] = "h.estado = :filtro";
                    $parametros[':filtro'] = $filtro;
                } elseif (strpos($filtro, 'capacidad-') === 0) {
                    $capacidad = intval(str_replace('capacidad-', '', $filtro));
                    $conditions[] = "h.capacidad >= :capacidad";
                    $parametros[':capacidad'] = $capacidad;
                }
            }

            if ($estado) {
                $conditions[] = "h.estado = :estado";
                $parametros[':estado'] = $estado;
            }

            if ($tipoHabitacion) {
                $conditions[] = "h.tipoHabitacion = :tipoHabitacion";
                $parametros[':tipoHabitacion'] = $tipoHabitacion;
            }

            $whereClause = '';
            if (!empty($conditions)) {
                $whereClause = 'WHERE ' . implode(' AND ', $conditions);
            }
            
            // Obtener el total de registros
            $sqlTotal = "SELECT COUNT(*) as total FROM tp_habitaciones h 
                        LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                        $whereClause";
            $stmtTotal = $this->db->prepare($sqlTotal);
            
            foreach ($parametros as $param => $valor) {
                if ($param === ':capacidad' || $param === ':tipoHabitacion') {
                    $stmtTotal->bindParam($param, $valor, PDO::PARAM_INT);
                } else {
                    $stmtTotal->bindParam($param, $valor, PDO::PARAM_STR);
                }
            }
            
            $stmtTotal->execute();
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Obtener los registros de la página actual
            $sql = "SELECT h.*, 
                           t.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                    $whereClause
                    ORDER BY h.numero ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind parámetros de filtro
            foreach ($parametros as $param => $valor) {
                if ($param === ':capacidad' || $param === ':tipoHabitacion') {
                    $stmt->bindParam($param, $valor, PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($param, $valor, PDO::PARAM_STR);
                }
            }
            
            // Bind parámetros de paginación
            $stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
            $stmt->execute();
            
            $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'habitaciones' => $habitaciones,
                'total' => $total,
                'pagina' => $pagina,
                'registrosPorPagina' => $registrosPorPagina,
                'totalPaginas' => ceil($total / $registrosPorPagina)
            ];

        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones paginadas: " . $e->getMessage());
            throw new Exception("Error al obtener habitaciones paginadas");
        }
    }

    /**
     * Cambiar estado de una habitación
     */
    public function cambiarEstadoHabitacion($numero, $nuevoEstado, $descripcionMantenimiento = null) {
        try {
            $sql = "UPDATE tp_habitaciones SET estado = :estado";
            $parametros = [':numero' => $numero, ':estado' => $nuevoEstado];
            
            if ($nuevoEstado === 'Mantenimiento' && $descripcionMantenimiento) {
                $sql .= ", descripcionMantenimiento = :descripcionMantenimiento";
                $parametros[':descripcionMantenimiento'] = $descripcionMantenimiento;
            } elseif ($nuevoEstado !== 'Mantenimiento') {
                // Limpiar descripción de mantenimiento si no es mantenimiento
                $sql .= ", descripcionMantenimiento = NULL";
            }
            
            $sql .= " WHERE numero = :numero";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $valor, PDO::PARAM_STR);
            }
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al cambiar estado de habitación: " . $e->getMessage());
            throw new Exception("Error al cambiar estado de la habitación");
        }
    }

    /**
     * Obtener habitaciones en mantenimiento
     */
    public function obtenerHabitacionesEnMantenimiento() {
        try {
            $sql = "SELECT h.*, 
                           t.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                    WHERE h.estado = 'Mantenimiento'
                    ORDER BY h.numero ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones en mantenimiento: " . $e->getMessage());
            throw new Exception("Error al obtener habitaciones en mantenimiento");
        }
    }

    /**
     * Obtener estadísticas de habitaciones
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN estado = 'Disponible' THEN 1 ELSE 0 END) as disponibles,
                        SUM(CASE WHEN estado = 'Reservada' THEN 1 ELSE 0 END) as reservadas,
                        SUM(CASE WHEN estado = 'Ocupada' THEN 1 ELSE 0 END) as ocupadas,
                        SUM(CASE WHEN estado = 'Mantenimiento' THEN 1 ELSE 0 END) as mantenimiento,
                        SUM(CASE WHEN estadoMantenimiento = 'Activo' THEN 1 ELSE 0 END) as activas,
                        SUM(CASE WHEN estadoMantenimiento = 'Inactivo' THEN 1 ELSE 0 END) as inactivas,
                        AVG(costo) as costo_promedio
                    FROM tp_habitaciones";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            throw new Exception("Error al obtener estadísticas");
        }
    }
}
?>