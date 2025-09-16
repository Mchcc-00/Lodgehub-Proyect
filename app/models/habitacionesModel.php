<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class HabitacionesModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
        if (!$this->db) {
            throw new Exception("Error al conectar con la base de datos");
        }
    }

    /**
     * Obtiene los tipos de habitación para un hotel específico.
     */
    public function obtenerTiposHabitacion($id_hotel) {
        try {
            $sql = "SELECT id, descripcion FROM td_tipohabitacion WHERE id_hotel = :id_hotel ORDER BY descripcion ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::obtenerTiposHabitacion: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si un número de habitación ya existe en un hotel.
     */
    public function verificarNumeroExistente($numero, $id_hotel, $id_actual = null) {
        try {
            $sql = "SELECT COUNT(*) FROM tp_habitaciones WHERE numero = :numero AND id_hotel = :id_hotel";
            if ($id_actual) {
                $sql .= " AND id != :id_actual";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            if ($id_actual) {
                $stmt->bindValue(':id_actual', (int)$id_actual, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::verificarNumeroExistente: " . $e->getMessage());
            return true; // Asumir que existe para prevenir duplicados en caso de error
        }
    }

    /**
     * Crea una nueva habitación.
     */
    public function crearHabitacion($datos) {
        try {
            $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, foto, descripcion, estado, id_hotel)
                    VALUES (:numero, :costo, :capacidad, :tipoHabitacion, :foto, :descripcion, :estado, :id_hotel)";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($datos);
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::crearHabitacion: " . $e->getMessage());
            throw new Exception("Error al crear la habitación: " . $e->getMessage());
        }
    }

    /**
     * Obtiene las habitaciones de forma paginada y con filtros.
     */
    public function obtenerHabitacionesPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            $selectClause = "SELECT h.id, h.numero, h.costo, h.capacidad, h.estado, h.foto, th.descripcion as tipo_descripcion";
            $fromClause = " FROM tp_habitaciones h JOIN td_tipohabitacion th ON h.tipoHabitacion = th.id";
            
            $whereClauses = ["h.id_hotel = :id_hotel"];
            $params = [':id_hotel' => (int)$id_hotel];

            if (!empty($filtros['estado']) && $filtros['estado'] !== 'all') {
                $whereClauses[] = "h.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'all') {
                $whereClauses[] = "h.tipoHabitacion = :tipo";
                $params[':tipo'] = (int)$filtros['tipo'];
            }
            if (!empty(trim($filtros['busqueda']))) {
                $whereClauses[] = "(h.numero LIKE :busqueda OR h.descripcion LIKE :busqueda OR th.descripcion LIKE :busqueda)";
                $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
            }

            $whereSql = " WHERE " . implode(' AND ', $whereClauses);

            // Contar total de registros
            $sqlTotal = "SELECT COUNT(h.id)" . $fromClause . $whereSql;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($params);
            $totalRegistros = (int)$stmtTotal->fetchColumn();

            // Obtener registros para la página actual
            $sql = $selectClause . $fromClause . $whereSql . " ORDER BY h.numero ASC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', (int)$registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'habitaciones' => $habitaciones,
                'pagina' => (int)$pagina,
                'total' => $totalRegistros,
                'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
            ];
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::obtenerHabitacionesPaginadas: " . $e->getMessage());
            throw new Exception("Error al obtener las habitaciones");
        }
    }

    /**
     * Obtiene una habitación por su ID.
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT h.*, th.descripcion as tipo_descripcion 
                    FROM tp_habitaciones h 
                    JOIN td_tipohabitacion th ON h.tipoHabitacion = th.id 
                    WHERE h.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una habitación.
     */
    public function actualizarHabitacion($id, $datos) {
        try {
            $campos = [];
            $params = [':id' => (int)$id];
            $camposPermitidos = ['numero', 'costo', 'capacidad', 'tipoHabitacion', 'descripcion', 'estado'];
            
            foreach ($camposPermitidos as $campo) {
                if (isset($datos[$campo])) {
                    $campos[] = "$campo = :$campo";
                    $params[":$campo"] = $datos[$campo];
                }
            }

            // Manejo de la foto
            if (isset($datos['foto'])) {
                $campos[] = "foto = :foto";
                $params[":foto"] = $datos['foto'];
            }

            if (empty($campos)) {
                throw new Exception("No hay campos válidos para actualizar");
            }

            $sql = "UPDATE tp_habitaciones SET " . implode(', ', $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::actualizarHabitacion: " . $e->getMessage());
            throw new Exception("Error al actualizar la habitación: " . $e->getMessage());
        }
    }

    /**
     * Elimina una habitación.
     */
    public function eliminarHabitacion($id) {
        try {
            // Primero, verificar si la habitación tiene reservas asociadas
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM tp_reservas WHERE id_habitacion = :id");
            $stmtCheck->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmtCheck->execute();
            if ($stmtCheck->fetchColumn() > 0) {
                throw new Exception("No se puede eliminar la habitación porque tiene reservas asociadas.");
            }

            $stmt = $this->db->prepare("DELETE FROM tp_habitaciones WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::eliminarHabitacion: " . $e->getMessage());
            throw new Exception("Error al eliminar la habitación: " . $e->getMessage());
        }
    }
}
?>