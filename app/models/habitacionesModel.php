<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class HabitacionesModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
    }

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
                        estado
                    ) VALUES (
                        :numero, 
                        :costo, 
                        :capacidad, 
                        :tipoHabitacion, 
                        :foto,
                        :descripcion,
                        :estado
                    )";

            $stmt = $this->db->prepare($sql);
            
            $stmt->bindParam(':numero', $datos['numero'], PDO::PARAM_STR);
            $stmt->bindParam(':costo', $datos['costo'], PDO::PARAM_STR);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':tipoHabitacion', $datos['tipoHabitacion'], PDO::PARAM_INT);
            $stmt->bindParam(':foto', $datos['foto'], PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindParam(':estado', $datos['estado'], PDO::PARAM_STR);

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
                           th.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id 
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
    public function obtenerTodasLasHabitaciones($filtro = null) {
        try {
            $sql = "SELECT h.*, 
                           th.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id";
            
            $parametros = [];
            
            if ($filtro && $filtro !== 'all') {
                $sql .= " WHERE h.estado = :filtro";
                $parametros[':filtro'] = $filtro;
            }
            
            $sql .= " ORDER BY h.numero ASC";
            
            $stmt = $this->db->prepare($sql);
            
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $valor, PDO::PARAM_STR);
            }
            
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones: " . $e->getMessage());
            throw new Exception("Error al obtener las habitaciones");
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
                    $stmt->bindParam($param, $parametros[$param], PDO::PARAM_STR);
                } elseif ($param === ':costo') {
                    $stmt->bindParam($param, $parametros[$param], PDO::PARAM_STR);
                } elseif ($param === ':capacidad' || $param === ':tipoHabitacion') {
                    $stmt->bindParam($param, $parametros[$param], PDO::PARAM_INT);
                } else {
                    $stmt->bindParam($param, $parametros[$param], PDO::PARAM_STR);
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
                           th.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id 
                    WHERE h.numero LIKE :termino 
                    OR h.descripcion LIKE :termino 
                    OR h.estado LIKE :termino 
                    OR th.descripcion LIKE :termino
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
     * Obtener tipos de habitación
     */
    public function obtenerTiposHabitacion() {
        try {
            $sql = "SELECT id, descripcion FROM td_tipoHabitacion ORDER BY descripcion ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener tipos de habitación: " . $e->getMessage());
            throw new Exception("Error al obtener tipos de habitación");
        }
    }

    /**
     * Obtener habitaciones paginadas
     */
    public function obtenerHabitacionesPaginadas($pagina = 1, $registrosPorPagina = 12, $filtro = null) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;
            
            // Construir WHERE clause
            $whereClause = '';
            $parametros = [];
            
            if ($filtro && $filtro !== 'all') {
                $whereClause = 'WHERE h.estado = :filtro';
                $parametros[':filtro'] = $filtro;
            }
            
            // Obtener el total de registros
            $sqlTotal = "SELECT COUNT(*) as total FROM tp_habitaciones h 
                        LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id 
                        $whereClause";
            $stmtTotal = $this->db->prepare($sqlTotal);
            
            foreach ($parametros as $param => $valor) {
                $stmtTotal->bindParam($param, $valor, PDO::PARAM_STR);
            }
            
            $stmtTotal->execute();
            $total = $stmtTotal->fetch(PDO::FETCH_ASSOC)['total'];
            
            // Obtener los registros de la página actual
            $sql = "SELECT h.*, 
                           th.descripcion as tipo_descripcion
                    FROM tp_habitaciones h 
                    LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id 
                    $whereClause
                    ORDER BY h.numero ASC 
                    LIMIT :limit OFFSET :offset";
            
            $stmt = $this->db->prepare($sql);
            
            // Bind parámetros de filtro
            foreach ($parametros as $param => $valor) {
                $stmt->bindParam($param, $valor, PDO::PARAM_STR);
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
                        AVG(costo) as precio_promedio
                    FROM tp_habitaciones";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            throw new Exception("Error al obtener estadísticas");
        }
    }

    /**
     * Actualizar estado de habitación a mantenimiento
     */
    public function ponerEnMantenimiento($numero, $descripcionMantenimiento) {
        try {
            $sql = "UPDATE tp_habitaciones 
                    SET estado = 'Mantenimiento', 
                        descripcionMantenimiento = :descripcion,
                        estadoMantenimiento = 'Activo'
                    WHERE numero = :numero";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
            $stmt->bindParam(':descripcion', $descripcionMantenimiento, PDO::PARAM_STR);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al poner habitación en mantenimiento: " . $e->getMessage());
            throw new Exception("Error al poner habitación en mantenimiento");
        }
    }

    /**
     * Finalizar mantenimiento de habitación
     */
    public function finalizarMantenimiento($numero) {
        try {
            $sql = "UPDATE tp_habitaciones 
                    SET estado = 'Disponible', 
                        estadoMantenimiento = 'Inactivo',
                        descripcionMantenimiento = NULL
                    WHERE numero = :numero";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $numero, PDO::PARAM_STR);
            
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error al finalizar mantenimiento: " . $e->getMessage());
            throw new Exception("Error al finalizar mantenimiento");
        }
    }
}
?>
