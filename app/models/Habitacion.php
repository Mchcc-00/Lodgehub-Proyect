<?php
// models/Habitacion.php
require_once '../../config/conexionGlobal.php';

class Habitacion {
    public $numero;
    public $costo;
    public $capacidad;
    public $tipoHabitacion;
    public $tamano;
    public $estado;

    public function __construct($data) {
        $this->numero = $data['numero'] ?? '';
        $this->costo = $data['costo'] ?? '';
        $this->capacidad = $data['capacidad'] ?? '';
        $this->tipoHabitacion = $data['tipoHabitacion'] ?? '';
        $this->tamano = $data['tamano'] ?? '';
        $this->estado = $data['estado'] ?? '';
    }

    // Obtener todas las habitaciones
    public static function all($pdo) {
        $stmt = $pdo->query("SELECT * FROM tp_habitaciones ORDER BY numero ASC");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return array_map(fn($row) => new Habitacion($row), $result);
    }

    // Buscar una habitación por número
    public static function find($pdo, $numero) {
        $stmt = $pdo->prepare("SELECT * FROM tp_habitaciones WHERE numero = ?");
        $stmt->execute([$numero]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ? new Habitacion($row) : null;
    }

    // Crear una nueva habitación
    public static function create($pdo, $data) {
        $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, tamano, estado) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['numero'],
            $data['costo'],
            $data['capacidad'],
            $data['tipoHabitacion'],
            $data['tamano'],
            $data['estado']
        ]);
        return new Habitacion($data);
    }

    // Actualizar una habitación existente
    public static function update($pdo, $numero, $data) {
        $sql = "UPDATE tp_habitaciones SET costo=?, capacidad=?, tipoHabitacion=?, tamano=?, estado=? WHERE numero=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $data['costo'],
            $data['capacidad'],
            $data['tipoHabitacion'],
            $data['tamano'],
            $data['estado'],
            $numero
        ]);
        return new Habitacion($data);
    }

    // Eliminar una habitación
    public static function delete($pdo, $numero) {
        $stmt = $pdo->prepare("DELETE FROM tp_habitaciones WHERE numero=?");
        return $stmt->execute([$numero]);
    }
}