<?php
require_once __DIR__ . '/../../config/conexionGlobal.php'; // Usar tu conexión existente

class HotelModel {
    private $db;
    private $table = 'tp_hotel';

    public function __construct() {
        $this->db = conexionDB(); // Usar tu función de conexión PDO
    }

    // Crear un nuevo hotel
    public function crearHotel($datos) {
        // Iniciar una transacción para asegurar la integridad de los datos
        $this->db->beginTransaction();

        try {
            // 1. Insertar el hotel en la tabla tp_hotel
            $queryHotel = "INSERT INTO " . $this->table . " 
                     (nit, nombre, direccion, telefono, correo, foto, descripcion, numDocumentoAdmin) 
                     VALUES (:nit, :nombre, :direccion, :telefono, :correo, :foto, :descripcion, :numDocumentoAdmin)";
            
            $stmtHotel = $this->db->prepare($queryHotel);
            
            // Vincular parámetros
            $stmtHotel->bindParam(':nit', $datos['nit']);
            $stmtHotel->bindParam(':nombre', $datos['nombre']);
            $stmtHotel->bindParam(':direccion', $datos['direccion']);
            $stmtHotel->bindParam(':telefono', $datos['telefono']);
            $stmtHotel->bindParam(':correo', $datos['correo']);
            $stmtHotel->bindParam(':foto', $datos['foto']);
            $stmtHotel->bindParam(':descripcion', $datos['descripcion']);
            $stmtHotel->bindParam(':numDocumentoAdmin', $datos['numDocumentoAdmin']);
            
            if (!$stmtHotel->execute()) {
                throw new PDOException("Error al crear el hotel.");
            }

            // Obtener el ID del hotel recién creado
            $hotelId = $this->db->lastInsertId();

            // 2. Asignar el administrador al hotel en la tabla ti_personal
            $queryPersonal = "INSERT INTO ti_personal (id_hotel, numDocumento, roles) VALUES (:id_hotel, :numDocumento, :roles)";
            $stmtPersonal = $this->db->prepare($queryPersonal);
            
            $rolEspecifico = 'Administrador de Hotel'; // SOLUCIÓN: Rol específico para el dueño del hotel
            $stmtPersonal->bindParam(':id_hotel', $hotelId);
            $stmtPersonal->bindParam(':numDocumento', $datos['numDocumentoAdmin']);
            $stmtPersonal->bindParam(':roles', $rolEspecifico);

            if (!$stmtPersonal->execute()) {
                throw new PDOException("Error al asignar el administrador al hotel.");
            }

            // 3. Actualizar el rol principal del usuario a 'Colaborador'
            // para que en futuros logins, el sistema sepa que ya gestiona un hotel.
            $queryUpdateUser = "UPDATE tp_usuarios SET roles = 'Colaborador' WHERE numDocumento = :numDocumento AND roles = 'Administrador'";
            $stmtUpdateUser = $this->db->prepare($queryUpdateUser);
            $stmtUpdateUser->bindParam(':numDocumento', $datos['numDocumentoAdmin']);
            if (!$stmtUpdateUser->execute()) {
                throw new PDOException("Error al actualizar el rol del usuario.");
            }

            // Si todo fue bien, confirmar la transacción
            $this->db->commit();

            return [
                'success' => true,
                'message' => 'Hotel creado y administrador asignado exitosamente.',
                'id' => $hotelId
            ];

        } catch (PDOException $e) {
            // Si algo falla, revertir la transacción
            $this->db->rollBack();

            if ($e->getCode() == 23000) { // Código de error para duplicados
                return ['success' => false, 'message' => 'El NIT ya está registrado para otro hotel.'];
            }
            
            // Devolver un mensaje de error genérico
            return ['success' => false, 'message' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }

    // Obtener todos los hoteles
    public function obtenerHoteles() {
        try {
            $query = "SELECT h.*, u.nombres, u.apellidos 
                     FROM " . $this->table . " h
                     LEFT JOIN tp_usuarios u ON h.numDocumentoAdmin = u.numDocumento
                     ORDER BY h.nombre ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener hoteles: ' . $e->getMessage()
            ];
        }
    }

    // Obtener hotel por ID
    public function obtenerHotelPorId($id) {
        try {
            $query = "SELECT h.*, u.nombres, u.apellidos 
                     FROM " . $this->table . " h
                     LEFT JOIN tp_usuarios u ON h.numDocumentoAdmin = u.numDocumento
                     WHERE h.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $hotel = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($hotel) {
                return [
                    'success' => true,
                    'data' => $hotel
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Hotel no encontrado'
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener el hotel: ' . $e->getMessage()
            ];
        }
    }

    // Actualizar hotel
    public function actualizarHotel($id, $datos) {
        try {
            $query = "UPDATE " . $this->table . " 
                     SET nit = :nit, nombre = :nombre, direccion = :direccion, 
                         telefono = :telefono, correo = :correo, foto = :foto, 
                         descripcion = :descripcion, numDocumentoAdmin = :numDocumentoAdmin
                     WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':nit', $datos['nit']);
            $stmt->bindParam(':nombre', $datos['nombre']);
            $stmt->bindParam(':direccion', $datos['direccion']);
            $stmt->bindParam(':telefono', $datos['telefono']);
            $stmt->bindParam(':correo', $datos['correo']);
            $stmt->bindParam(':foto', $datos['foto']);
            $stmt->bindParam(':descripcion', $datos['descripcion']);
            $stmt->bindParam(':numDocumentoAdmin', $datos['numDocumentoAdmin']);
            
            if ($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Hotel actualizado exitosamente'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error al actualizar el hotel'
            ];
            
        } catch (PDOException $e) {
            if ($e->getCode() == 23000) {
                return [
                    'success' => false,
                    'message' => 'El NIT ya está registrado para otro hotel'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    // Eliminar hotel
    public function eliminarHotel($id) {
        try {
            $query = "DELETE FROM " . $this->table . " WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            
            if ($stmt->execute()) {
                if ($stmt->rowCount() > 0) {
                    return [
                        'success' => true,
                        'message' => 'Hotel eliminado exitosamente'
                    ];
                }
                
                return [
                    'success' => false,
                    'message' => 'Hotel no encontrado'
                ];
            }
            
            return [
                'success' => false,
                'message' => 'Error al eliminar el hotel'
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error en la base de datos: ' . $e->getMessage()
            ];
        }
    }

    // Verificar si el NIT ya existe (excluyendo un ID específico para edición)
    public function nitExiste($nit, $excludeId = null) {
        try {
            $query = "SELECT COUNT(*) FROM " . $this->table . " WHERE nit = :nit";
            if ($excludeId) {
                $query .= " AND id != :excludeId";
            }
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':nit', $nit);
            if ($excludeId) {
                $stmt->bindParam(':excludeId', $excludeId);
            }
            
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }

    // Verificar si el administrador existe
    public function administradorExiste($numDocumento) {
        try {
            $query = "SELECT COUNT(*) FROM tp_usuarios WHERE numDocumento = :numDocumento";
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numDocumento', $numDocumento);
            $stmt->execute();
            
            return $stmt->fetchColumn() > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }

    // Obtener hoteles por administrador
    public function obtenerHotelesPorAdmin($numDocumento) {
        try {
            $query = "SELECT * FROM " . $this->table . " 
                     WHERE numDocumentoAdmin = :numDocumento 
                     ORDER BY nombre ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':numDocumento', $numDocumento);
            $stmt->execute();
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC)
            ];
            
        } catch (PDOException $e) {
            return [
                'success' => false,
                'message' => 'Error al obtener hoteles: ' . $e->getMessage()
            ];
        }
    }

}