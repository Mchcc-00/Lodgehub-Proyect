<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class ReservasModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
    }

    public function obtenerReservasPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        $offset = ($pagina - 1) * $registrosPorPagina;

        // --- INICIO: Construcción de la consulta con JOINs directos ---
        $selectClause = "SELECT 
            r.id,
            r.pagoFinal,
            r.fechainicio,
            r.fechaFin,
            (r.cantidadAdultos + r.cantidadNinos + r.cantidadDiscapacitados) as totalPersonas,
            r.estado,
            CONCAT(h.nombres, ' ', h.apellidos) as nombreHuesped,
            h.numDocumento as huespedDocumento,
            hab.numero as numeroHabitacion,
            hot.nombre as nombreHotel";

        $fromClause = " FROM tp_reservas r
            LEFT JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento
            LEFT JOIN tp_habitaciones hab ON r.id_habitacion = hab.id
            LEFT JOIN tp_hotel hot ON r.id_hotel = hot.id";

        // Construcción de la cláusula WHERE
        $whereClauses = ["r.id_hotel = :id_hotel"];
        $params = [':id_hotel' => $id_hotel];

        if (isset($filtros['estado']) && $filtros['estado'] !== 'all') {
            $whereClauses[] = "r.estado = :estado";
            $params[':estado'] = $filtros['estado'];
        }

        if (isset($filtros['busqueda']) && !empty($filtros['busqueda'])) {
            $whereClauses[] = "(r.id LIKE :busqueda OR CONCAT(h.nombres, ' ', h.apellidos) LIKE :busqueda OR hab.numero LIKE :busqueda)";
            $params[':busqueda'] = '%' . $filtros['busqueda'] . '%';
        }

        $whereSql = " WHERE " . implode(' AND ', $whereClauses);
        // --- FIN: Construcción de la consulta ---

        // Contar total de registros
        $sqlTotal = "SELECT COUNT(r.id)" . $fromClause . $whereSql;
        $stmtTotal = $this->db->prepare($sqlTotal);
        $stmtTotal->execute($params);
        $totalRegistros = $stmtTotal->fetchColumn();

        // Obtener registros para la página actual
        $sql = $selectClause . $fromClause . $whereSql . " ORDER BY r.fechainicio DESC LIMIT :limit OFFSET :offset";
        $stmt = $this->db->prepare($sql);

        // Bind de parámetros de la cláusula WHERE de forma segura
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }

        $stmt->bindParam(':limit', $registrosPorPagina, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'reservas' => $reservas,
            'pagina' => $pagina,
            'registrosPorPagina' => $registrosPorPagina,
            'total' => $totalRegistros,
            'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
        ];
    }

    private function verificarVistaExiste() {
        try {
            $stmt = $this->db->query("SELECT 1 FROM v_reservas_detalle LIMIT 1");
            return true;
        } catch (PDOException $e) {
            return false;
        }
    }

    private function crearJoinManual() {
        return "SELECT 
            r.id,
            r.pagoFinal,
            r.fechainicio,
            r.fechaFin,
            r.cantidadAdultos,
            r.cantidadNinos,
            r.cantidadDiscapacitados,
            (r.cantidadAdultos + r.cantidadNinos + r.cantidadDiscapacitados) as totalPersonas,
            r.motivoReserva,
            r.metodoPago,
            r.informacionAdicional,
            r.estado,
            r.fechaRegistro,
            r.id_hotel,
            r.id_habitacion,
            CONCAT(h.nombres, ' ', h.apellidos) as nombreHuesped,
            h.numDocumento as huespedDocumento,
            CONCAT(u.nombres, ' ', u.apellidos) as nombreUsuario,
            u.numDocumento as usuarioDocumento,
            hab.numero as numeroHabitacion,
            hab.tipo as tipoHabitacion,
            hot.nombre as nombreHotel,
            DATEDIFF(r.fechaFin, r.fechainicio) as diasEstadia
        FROM tp_reservas r
        LEFT JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento
        LEFT JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento
        LEFT JOIN tp_habitaciones hab ON r.id_habitacion = hab.id
        LEFT JOIN tp_hotel hot ON r.id_hotel = hot.id";
    }

    public function obtenerPorId($id) {
        try {
            $esVista = $this->verificarVistaExiste();
            
            if ($esVista) {
                $stmt = $this->db->prepare("SELECT * FROM v_reservas_detalle WHERE id = :id");
            } else {
                $sql = "SELECT 
                    r.id,
                    r.pagoFinal,
                    r.fechainicio,
                    r.fechaFin,
                    r.cantidadAdultos,
                    r.cantidadNinos,
                    r.cantidadDiscapacitados,
                    (r.cantidadAdultos + r.cantidadNinos + r.cantidadDiscapacitados) as totalPersonas,
                    r.motivoReserva,
                    r.metodoPago,
                    r.informacionAdicional,
                    r.estado,
                    r.fechaRegistro,
                    r.id_hotel,
                    r.id_habitacion,
                    CONCAT(h.nombres, ' ', h.apellidos) as nombreHuesped,
                    h.numDocumento as huespedDocumento,
                    CONCAT(u.nombres, ' ', u.apellidos) as nombreUsuario,
                    u.numDocumento as usuarioDocumento,
                    hab.numero as numeroHabitacion,
                    hab.tipo as tipoHabitacion,
                    hot.nombre as nombreHotel,
                    DATEDIFF(r.fechaFin, r.fechainicio) as diasEstadia
                FROM tp_reservas r
                LEFT JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento
                LEFT JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento
                LEFT JOIN tp_habitaciones hab ON r.id_habitacion = hab.id
                LEFT JOIN tp_hotel hot ON r.id_hotel = hot.id
                WHERE r.id = :id";
                $stmt = $this->db->prepare($sql);
            }
            
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
            // Opcional: Primero verificar si la reserva puede ser eliminada
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