<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class ReservasModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
    }

    public function obtenerReservasPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        $offset = ($pagina - 1) * $registrosPorPagina;
        
        $whereClauses = ["r.id_hotel = :id_hotel"];
        $params = [':id_hotel' => $id_hotel];

        if (isset($filtros['estado']) && $filtros['estado'] !== 'all') {
            $whereClauses[] = "r.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
            $whereClauses[] = "(r.id LIKE :busqueda OR r.nombreHuesped LIKE :busqueda OR r.numeroHabitacion LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $whereSql = implode(' AND ', $whereClauses);

        // Contar total de registros
        $sqlTotal = "SELECT COUNT(*) FROM v_reservas_detalle r WHERE $whereSql";
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->execute($params);
        $totalRegistros = $stmtTotal->fetchColumn();

        // Obtener registros para la página actual
        $sql = "SELECT * FROM v_reservas_detalle r WHERE $whereSql ORDER BY r.fechainicio DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        // Bind de parámetros de la cláusula WHERE de forma segura
        foreach ($params as $key => $val) {
            if ($key === ':busqueda') {
                $stmt->bindValue($key, $val, PDO::PARAM_STR);
            } else {
                $stmt->bindValue($key, $val);
            }
        }

        $stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Log para depuración
        if ($totalRegistros > 0 && empty($reservas)) {
            error_log("ReservasModel: Se encontraron {$totalRegistros} registros pero el fetch devolvió un array vacío. Pagina: {$pagina}");
        }

        return [
            'reservas' => $reservas,
            'pagina' => $pagina,
            'registrosPorPagina' => $registrosPorPagina,
            'total' => $totalRegistros,
            'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
        ];
    }

    public function obtenerPorId($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM v_reservas_detalle WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en ReservasModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarReserva($id, $datos) {
        try {
            $campos = [];
            $params = [':id' => $id];

            foreach ($datos as $key => $value) {
                $campos[] = "$key = :$key";
                $params[":$key"] = $value;
            }

            if (empty($campos)) {
                return false;
            }

            $sql = "UPDATE tp_reservas SET " . implode(', ', $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::actualizarReserva: " . $e->getMessage());
            return false;
        }
    }

    public function eliminarReserva($id) {
        try {
            // Opcional: Primero verificar si la reserva puede ser eliminada (ej. no está 'Activa')
            $reserva = $this->obtenerPorId($id);
            if ($reserva && $reserva['estado'] === 'Activa') {
                 // Opcional: Podrías lanzar una excepción o devolver un mensaje específico
                 // throw new Exception("No se puede eliminar una reserva activa.");
            }

            $stmt = $this->db->prepare("DELETE FROM tp_reservas WHERE id = :id");
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en ReservasModel::eliminarReserva: " . $e->getMessage());
            // Verificar si es un error de clave foránea
            if ($e->getCode() == '23000') {
                throw new Exception("No se puede eliminar la reserva porque tiene registros asociados (ej. una factura).");
            }
            return false;
        }
    }
}
?>
