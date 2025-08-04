<?php


class Habitacion {
    // Propiedad estática para la conexión PDO
    private static $pdo;

    // Propiedades de la habitación con sus tipos declarados (disponible en PHP 7.4+)
    public string $numero;
    public float $costo;
    public int $capacidad;
    public int $tipoHabitacion;
    public int $tamano;
    public int $estado;

    /**
     * Establece la conexión PDO estáticamente para toda la clase.
     */
    public static function setPdoConnection(PDO $pdo) {
        self::$pdo = $pdo;
    }

    /**
     * Constructor para inicializar una instancia de Habitacion.
     */
    public function __construct(array $data) {
        $this->numero = (string) ($data['numero'] ?? '');
        $this->costo = (float) ($data['costo'] ?? 0.0);
        $this->capacidad = (int) ($data['capacidad'] ?? 0);
        $this->tipoHabitacion = (int) ($data['tipoHabitacion'] ?? 0);
        $this->tamano = (int) ($data['tamano'] ?? 0);
        $this->estado = (int) ($data['estado'] ?? 0);
    }

    /**
     * Obtiene todas las habitaciones y las devuelve como un array de objetos Habitacion.
     */
    public static function all(): array {
        try {
            $stmt = self::$pdo->query("SELECT * FROM tp_habitaciones ORDER BY numero ASC");
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return array_map(fn($row) => new Habitacion($row), $result);
        } catch (PDOException $e) {
            // Manejo de errores
            return [];
        }
    }

    /**
     * Busca una habitación por su número y devuelve un objeto Habitacion o null.
     */
    public static function find(string $numero): ?Habitacion {
        try {
            $stmt = self::$pdo->prepare("SELECT * FROM tp_habitaciones WHERE numero = ?");
            $stmt->execute([$numero]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? new Habitacion($row) : null;
        } catch (PDOException $e) {
            // Manejo de errores
            return null;
        }
    }

    /**
     * Crea una nueva habitación.
     */
    public static function create(array $data): bool {
        try {
            $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, tamano, estado) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $data['numero'],
                $data['costo'],
                $data['capacidad'],
                $data['tipoHabitacion'],
                $data['tamano'],
                $data['estado']
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Actualiza una habitación existente.
     */
    public static function update(string $numero, array $data): bool {
        try {
            $sql = "UPDATE tp_habitaciones SET costo=?, capacidad=?, tipoHabitacion=?, tamano=?, estado=? WHERE numero=?";
            $stmt = self::$pdo->prepare($sql);
            $stmt->execute([
                $data['costo'],
                $data['capacidad'],
                $data['tipoHabitacion'],
                $data['tamano'],
                $data['estado'],
                $numero
            ]);
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    /**
     * Elimina una habitación.
     */
    public static function delete(string $numero): bool {
        try {
            $stmt = self::$pdo->prepare("DELETE FROM tp_habitaciones WHERE numero=?");
            return $stmt->execute([$numero]);
        } catch (PDOException $e) {
            return false;
        }
    }
}