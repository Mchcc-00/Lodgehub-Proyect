<?php
/**
 * Modelo para la gestión de habitaciones
 */

require_once  '../../config/conexionGlobal.php.php';

class Habitacion {
    private $db;
    
    public function __construct() {
        $this->db = conexionDB();
    }
    
    /**
     * Obtener todas las habitaciones con información del tipo y hotel
     */
    public function obtenerTodas($id_hotel = null) {
        try {
            $sql = "SELECT h.*, 
                           th.descripcion as tipo_descripcion,
                           hot.nombre as hotel_nombre
                    FROM tp_habitaciones h
                    INNER JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
                    INNER JOIN tp_hotel hot ON h.id_hotel = hot.id";
            
            $params = [];
            if ($id_hotel) {
                $sql .= " WHERE h.id_hotel = :id_hotel";
                $params[':id_hotel'] = $id_hotel;
            }
            
            $sql .= " ORDER BY h.numero ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener una habitación por ID
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT h.*, 
                           th.descripcion as tipo_descripcion,
                           hot.nombre as hotel_nombre
                    FROM tp_habitaciones h
                    INNER JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
                    INNER JOIN tp_hotel hot ON h.id_hotel = hot.id
                    WHERE h.id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener habitación: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Crear una nueva habitación
     */
    public function crear($datos) {
        try {
            // Verificar que el número no esté duplicado en el mismo hotel
            if ($this->existeNumero($datos['numero'], $datos['id_hotel'])) {
                return ['success' => false, 'message' => 'El número de habitación ya existe en este hotel'];
            }
            
            $sql = "INSERT INTO tp_habitaciones (
                        numero, costo, capacidad, tipoHabitacion, 
                        foto, descripcion, estado, id_hotel
                    ) VALUES (
                        :numero, :costo, :capacidad, :tipoHabitacion,
                        :foto, :descripcion, :estado, :id_hotel
                    )";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $datos['numero']);
            $stmt->bindParam(':costo', $datos['costo']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':tipoHabitacion', $datos['tipoHabitacion'], PDO::PARAM_INT);
            $stmt->bindParam(':foto', $datos['foto']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':id_hotel', $datos['id_hotel'], PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Habitación creada exitosamente', 'id' => $this->db->lastInsertId()];
            }
            
            return ['success' => false, 'message' => 'Error al crear la habitación'];
        } catch (PDOException $e) {
            error_log("Error al crear habitación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar una habitación
     */
    public function actualizar($id, $datos) {
        try {
            // Verificar que el número no esté duplicado en el mismo hotel (excluyendo la actual)
            if ($this->existeNumero($datos['numero'], $datos['id_hotel'], $id)) {
                return ['success' => false, 'message' => 'El número de habitación ya existe en este hotel'];
            }
            
            $sql = "UPDATE tp_habitaciones SET 
                        numero = :numero,
                        costo = :costo,
                        capacidad = :capacidad,
                        tipoHabitacion = :tipoHabitacion,
                        foto = :foto,
                        descripcion = :descripcion,
                        estado = :estado,
                        descripcionMantenimiento = :descripcionMantenimiento,
                        estadoMantenimiento = :estadoMantenimiento,
                        id_hotel = :id_hotel
                    WHERE id = :id";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':numero', $datos['numero']);
            $stmt->bindParam(':costo', $datos['costo']);
            $stmt->bindParam(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindParam(':tipoHabitacion', $datos['tipoHabitacion'], PDO::PARAM_INT);
            $stmt->bindParam(':foto', $datos['foto']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':estado', $datos['estado']);
            $stmt->bindParam(':descripcionMantenimiento', $datos['descripcionMantenimiento']);
            $stmt->bindParam(':estadoMantenimiento', $datos['estadoMantenimiento']);
            $stmt->bindParam(':id_hotel', $datos['id_hotel'], PDO::PARAM_INT);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Habitación actualizada exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al actualizar la habitación'];
        } catch (PDOException $e) {
            error_log("Error al actualizar habitación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar una habitación
     */
    public function eliminar($id) {
        try {
            // Verificar si la habitación tiene reservas activas
            if ($this->tieneReservasActivas($id)) {
                return ['success' => false, 'message' => 'No se puede eliminar la habitación porque tiene reservas activas'];
            }
            
            $sql = "DELETE FROM tp_habitaciones WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            
            if ($stmt->execute()) {
                return ['success' => true, 'message' => 'Habitación eliminada exitosamente'];
            }
            
            return ['success' => false, 'message' => 'Error al eliminar la habitación'];
        } catch (PDOException $e) {
            error_log("Error al eliminar habitación: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener tipos de habitación por hotel
     */
    public function obtenerTiposPorHotel($id_hotel) {
        try {
            $sql = "SELECT * FROM td_tipoHabitacion WHERE id_hotel = :id_hotel ORDER BY descripcion";
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_hotel', $id_hotel, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener tipos de habitación: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener todos los hoteles
     */
    public function obtenerHoteles() {
        try {
            $sql = "SELECT id, nombre FROM tp_hotel ORDER BY nombre";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener hoteles: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Verificar si existe el número de habitación en el hotel
     */
    private function existeNumero($numero, $id_hotel, $excluir_id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM tp_habitaciones WHERE numero = :numero AND id_hotel = :id_hotel";
            $params = [':numero' => $numero, ':id_hotel' => $id_hotel];
            
            if ($excluir_id) {
                $sql .= " AND id != :id";
                $params[':id'] = $excluir_id;
            }
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar número: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si la habitación tiene reservas activas
     */
    private function tieneReservasActivas($id_habitacion) {
        try {
            // Aquí deberías verificar en tu tabla de reservas
            // Como no tienes la estructura, asumo que existe una tabla tp_reservas
            $sql = "SELECT COUNT(*) FROM tp_reservas 
                    WHERE id_habitacion = :id_habitacion 
                    AND estado IN ('Activa', 'Confirmada', 'En proceso')";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindParam(':id_habitacion', $id_habitacion, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            // Si no existe la tabla de reservas, permitir eliminar
            return false;
        }
    }
    
    /**
     * Buscar habitaciones con filtros
     */
    public function buscar($filtros = []) {
        try {
            $sql = "SELECT h.*, 
                           th.descripcion as tipo_descripcion,
                           hot.nombre as hotel_nombre
                    FROM tp_habitaciones h
                    INNER JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
                    INNER JOIN tp_hotel hot ON h.id_hotel = hot.id
                    WHERE 1=1";
            
            $params = [];
            
            if (!empty($filtros['hotel'])) {
                $sql .= " AND h.id_hotel = :hotel";
                $params[':hotel'] = $filtros['hotel'];
            }
            
            if (!empty($filtros['tipo'])) {
                $sql .= " AND h.tipoHabitacion = :tipo";
                $params[':tipo'] = $filtros['tipo'];
            }
            
            if (!empty($filtros['estado'])) {
                $sql .= " AND h.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            
            if (!empty($filtros['numero'])) {
                $sql .= " AND h.numero LIKE :numero";
                $params[':numero'] = '%' . $filtros['numero'] . '%';
            }
            
            $sql .= " ORDER BY h.numero ASC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en búsqueda: " . $e->getMessage());
            return [];
        }
    }
}