<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class TipoHabitacionModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
        if (!$this->db) {
            throw new Exception("Error al conectar con la base de datos");
        }
    }

    /**
     * Verifica si una descripción de tipo de habitación ya existe en un hotel.
     */
    public function verificarDescripcionExistente($descripcion, $id_hotel, $id_actual = null) {
        try {
            $sql = "SELECT COUNT(*) FROM td_tipohabitacion WHERE descripcion = :descripcion AND id_hotel = :id_hotel";
            if ($id_actual) {
                $sql .= " AND id != :id_actual";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            if ($id_actual) {
                $stmt->bindValue(':id_actual', (int)$id_actual, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en TipoHabitacionModel::verificarDescripcionExistente: " . $e->getMessage());
            return true; // Asumir que existe para prevenir duplicados en caso de error
        }
    }

    /**
     * Crea un nuevo tipo de habitación.
     */
    public function crearTipoHabitacion($datos) {
        try {
            $sql = "INSERT INTO td_tipohabitacion (descripcion, id_hotel) VALUES (:descripcion, :id_hotel)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(':id_hotel', $datos['id_hotel'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en TipoHabitacionModel::crearTipoHabitacion: " . $e->getMessage());
            throw new Exception("Error al crear el tipo de habitación: " . $e->getMessage());
        }
    }

    /**
     * Obtiene los tipos de habitación de forma paginada.
     */
    public function obtenerTiposHabitacionPaginados($id_hotel, $pagina, $registrosPorPagina, $busqueda) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            $whereClause = "WHERE id_hotel = :id_hotel";
            $params = [':id_hotel' => (int)$id_hotel];

            if (!empty(trim($busqueda))) {
                $whereClause .= " AND descripcion LIKE :busqueda";
                $params[':busqueda'] = '%' . trim($busqueda) . '%';
            }

            // Contar total de registros
            $sqlTotal = "SELECT COUNT(*) FROM td_tipohabitacion " . $whereClause;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($params);
            $totalRegistros = (int)$stmtTotal->fetchColumn();

            // Obtener registros para la página actual
            $sql = "SELECT * FROM td_tipohabitacion " . $whereClause . " ORDER BY descripcion ASC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', (int)$registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'tipos' => $tipos,
                'pagina' => (int)$pagina,
                'total' => $totalRegistros,
                'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
            ];
        } catch (PDOException $e) {
            error_log("Error en TipoHabitacionModel::obtenerTiposHabitacionPaginados: " . $e->getMessage());
            throw new Exception("Error al obtener los tipos de habitación");
        }
    }

    /**
     * Obtiene un tipo de habitación por su ID.
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT * FROM td_tipohabitacion WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Error en TipoHabitacionModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un tipo de habitación.
     */
    public function actualizarTipoHabitacion($id, $descripcion) {
        try {
            $sql = "UPDATE td_tipohabitacion SET descripcion = :descripcion WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':descripcion', $descripcion, PDO::PARAM_STR);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en TipoHabitacionModel::actualizarTipoHabitacion: " . $e->getMessage());
            throw new Exception("Error al actualizar el tipo de habitación.");
        }
    }

    /**
     * Elimina un tipo de habitación.
     */
    public function eliminarTipoHabitacion($id) {
        try {
            // La validación de si está en uso se hace en el controlador con verificarUso()
            $stmt = $this->db->prepare("DELETE FROM td_tipohabitacion WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en TipoHabitacionModel::eliminarTipoHabitacion: " . $e->getMessage());
            throw new Exception("Error al eliminar el tipo de habitación.");
        }
    }

    /**
     * Verifica si un tipo de habitación está siendo utilizado por alguna habitación.
     */
    public function verificarUso($id_tipo) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM tp_habitaciones WHERE tipoHabitacion = :id_tipo");
        $stmt->bindValue(':id_tipo', (int)$id_tipo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn() > 0;
    }


    /**
     * Actualiza el contador de habitaciones para un tipo específico.
     * Si no se provee un ID, actualiza todos los tipos para un hotel.
     */
    public function actualizarContador($id_tipo = null, $id_hotel = null) {
        try {
            $sql = "UPDATE td_tipohabitacion th
                    SET th.cantidad = (SELECT COUNT(*) FROM tp_habitaciones h WHERE h.tipoHabitacion = th.id)
                    WHERE 1=1";
            if ($id_tipo) $sql .= " AND th.id = " . (int)$id_tipo;
            if ($id_hotel) $sql .= " AND th.id_hotel = " . (int)$id_hotel;
            
            $this->db->exec($sql);
        } catch (PDOException $e) {
            error_log("Error en TipoHabitacionModel::actualizarContador: " . $e->getMessage());
        }
    }
}
