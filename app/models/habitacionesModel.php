<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class Room {
    private $db;
    
    public function __construct($database) {
        $this->db = conexionDB();
    }
    
    // Obtener todas las habitaciones con información del tipo
    public function getAllRooms() {
        $query = "SELECT h.numero, h.costo, h.capacidad, h.foto, h.descripcion, 
                         h.estado, h.descripcionMantenimiento, h.estadoMantenimiento,
                         t.descripcion as tipoDescripcion
                  FROM tp_habitaciones h 
                  INNER JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                  ORDER BY h.numero";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener habitación por número
    public function getRoomByNumber($numero) {
        $query = "SELECT h.*, t.descripcion as tipoDescripcion
                  FROM tp_habitaciones h 
                  INNER JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                  WHERE h.numero = ?";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$numero]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
    
    // Crear nueva habitación
    public function createRoom($data) {
        $query = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, foto, descripcion, estado, descripcionMantenimiento, estadoMantenimiento) 
                  VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['numero'],
            $data['costo'],
            $data['capacidad'],
            $data['tipoHabitacion'],
            $data['foto'] ?? null,
            $data['descripcion'] ?? null,
            $data['estado'] ?? 'Disponible',
            $data['descripcionMantenimiento'] ?? null,
            $data['estadoMantenimiento'] ?? 'Activo'
        ]);
    }
    
    // Actualizar habitación
    public function updateRoom($numero, $data) {
        $query = "UPDATE tp_habitaciones SET 
                  costo = ?, capacidad = ?, tipoHabitacion = ?, foto = ?, 
                  descripcion = ?, estado = ?, descripcionMantenimiento = ?, estadoMantenimiento = ?
                  WHERE numero = ?";
        
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            $data['costo'],
            $data['capacidad'],
            $data['tipoHabitacion'],
            $data['foto'],
            $data['descripcion'],
            $data['estado'],
            $data['descripcionMantenimiento'],
            $data['estadoMantenimiento'],
            $numero
        ]);
    }
    
    // Eliminar habitación
    public function deleteRoom($numero) {
        $query = "DELETE FROM tp_habitaciones WHERE numero = ?";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([$numero]);
    }
    
    // Obtener tipos de habitación
    public function getRoomTypes() {
        $query = "SELECT * FROM td_tipoHabitacion ORDER BY descripcion";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Verificar si el número de habitación ya existe
    public function roomExists($numero) {
        $query = "SELECT COUNT(*) FROM tp_habitaciones WHERE numero = ?";
        $stmt = $this->db->prepare($query);
        $stmt->execute([$numero]);
        return $stmt->fetchColumn() > 0;
    }
    
    // Filtrar habitaciones por estado
    public function getRoomsByStatus($estado) {
        $query = "SELECT h.*, t.descripcion as tipoDescripcion
                  FROM tp_habitaciones h 
                  LEFT JOIN td_tipoHabitacion t ON h.tipoHabitacion = t.id 
                  WHERE h.estado = ?
                  ORDER BY h.numero";
        
        $stmt = $this->db->prepare($query);
        $stmt->execute([$estado]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>