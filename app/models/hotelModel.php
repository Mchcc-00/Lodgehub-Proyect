<?php
require_once 'config/conexionGlobal.php';

$db = conexionDB();

/**
 * Modelo para gestión de hoteles
 */
class HotelModel {
    private $conn;
    private $table_name = "tp_hotel";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function validateHotelData($data) {
        $errors = [];

        // Validación NIT
        if (empty($data['nit'])) {
            $errors['nit'] = 'El NIT es requerido';
        } elseif (!preg_match('/^[0-9\-]+$/', $data['nit'])) {
            $errors['nit'] = 'El NIT debe contener solo números y guiones';
        } elseif (strlen($data['nit']) > 20) {
            $errors['nit'] = 'El NIT no puede exceder 20 caracteres';
        } elseif ($this->nitExists($data['nit'], $data['id'] ?? null)) {
            $errors['nit'] = 'Este NIT ya está registrado';
        }

        // Validación nombre
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        } elseif (strlen($data['nombre']) > 100) {
            $errors['nombre'] = 'El nombre no puede exceder 100 caracteres';
        }

        // Validación dirección
        if (!empty($data['direccion']) && strlen($data['direccion']) > 200) {
            $errors['direccion'] = 'La dirección no puede exceder 200 caracteres';
        }

        // Validación descripción
        if (!empty($data['descripcion']) && strlen($data['descripcion']) > 1000) {
            $errors['descripcion'] = 'La descripción no puede exceder 1000 caracteres';
        }

        // Validación documento administrador
        if (empty($data['numDocumento'])) {
            $errors['numDocumento'] = 'El número de documento del administrador es requerido';
        } elseif (!$this->adminExists($data['numDocumento'])) {
            $errors['numDocumento'] = 'El administrador no existe en el sistema';
        }

        // Validación teléfono
        if (!empty($data['telefono'])) {
            if (!preg_match('/^[\+]?[0-9\-\s\(\)]+$/', $data['telefono'])) {
                $errors['telefono'] = 'El teléfono contiene caracteres no válidos';
            }
            if (strlen($data['telefono']) > 20) {
                $errors['telefono'] = 'El teléfono no puede exceder 20 caracteres';
            }
        }

        // Validación correo
        if (!empty($data['correo'])) {
            if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
                $errors['correo'] = 'El formato del correo no es válido';
            }
            if (strlen($data['correo']) > 100) {
                $errors['correo'] = 'El correo no puede exceder 100 caracteres';
            }
        }

        // Validación foto URL
        if (!empty($data['foto'])) {
            if (!filter_var($data['foto'], FILTER_VALIDATE_URL)) {
                $errors['foto'] = 'La URL de la foto no es válida';
            }
        }

        return $errors;
    }

    private function nitExists($nit, $excludeId = null) {
        try {
            $sql = "SELECT id FROM " . $this->table_name . " WHERE nit = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($excludeId) {
                $stmt->execute([$nit, $excludeId]);
            } else {
                $stmt->execute([$nit]);
            }
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en nitExists: " . $e->getMessage());
            return false;
        }
    }

    private function adminExists($numDocumento) {
        try {
            $sql = "SELECT numDocumento FROM tp_usuarios WHERE numDocumento = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$numDocumento]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en adminExists: " . $e->getMessage());
            // Si hay error, asumimos que el admin existe para no bloquear innecesariamente
            return true;
        }
    }

    public function create($data) {
        $errors = $this->validateHotelData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $sql = "INSERT INTO " . $this->table_name . " 
                (nit, nombre, direccion, descripcion, numDocumento, telefono, correo, foto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                $data['nit'],
                $data['nombre'],
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['descripcion']) ? $data['descripcion'] : null,
                $data['numDocumento'],
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null,
                !empty($data['foto']) ? $data['foto'] : null
            ]);

            if ($result) {
                return [
                    'success' => true, 
                    'message' => 'Hotel creado exitosamente',
                    'id' => $this->conn->lastInsertId()
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en create: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }

        return ['success' => false, 'error' => 'Error desconocido al crear hotel'];
    }

    public function getAll() {
        try {
            $sql = "SELECT h.*, u.nombre as admin_nombre 
                    FROM " . $this->table_name . " h 
                    LEFT JOIN tp_usuarios u ON h.numDocumento = u.numDocumento 
                    ORDER BY h.id DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en getAll: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT h.*, u.nombre as admin_nombre 
                    FROM " . $this->table_name . " h 
                    LEFT JOIN tp_usuarios u ON h.numDocumento = u.numDocumento 
                    WHERE h.id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en getById: " . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        $data['id'] = $id;
        $errors = $this->validateHotelData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $sql = "UPDATE " . $this->table_name . " 
                SET nit = ?, nombre = ?, direccion = ?, descripcion = ?, numDocumento = ?, 
                    telefono = ?, correo = ?, foto = ? 
                WHERE id = ?";

        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                $data['nit'],
                $data['nombre'],
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['descripcion']) ? $data['descripcion'] : null,
                $data['numDocumento'],
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null,
                !empty($data['foto']) ? $data['foto'] : null,
                $id
            ]);

            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Hotel actualizado exitosamente'];
            } elseif ($result && $stmt->rowCount() === 0) {
                return ['success' => false, 'error' => 'No se realizaron cambios o el hotel no existe'];
            }
        } catch (PDOException $e) {
            error_log("Error en update: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }

        return ['success' => false, 'error' => 'Error desconocido al actualizar hotel'];
    }

    public function delete($id) {
        try {
            $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Hotel eliminado exitosamente'];
            } else {
                return ['success' => false, 'error' => 'Hotel no encontrado'];
            }
        } catch (PDOException $e) {
            error_log("Error en delete: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
}
?>