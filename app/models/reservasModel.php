<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class ReservasModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
        if (!$this->db) {
            throw new Exception("Error al conectar con la base de datos");
        }
    }

    public function obtenerReservasPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            // --- INICIO: Construcción de la consulta con JOINs directos ---
            $selectClause = "SELECT 
                r.id,
                r.pagoFinal,
                r.fechainicio,
                r.fechaFin,
                (r.cantidadAdultos + r.cantidadNinos + r.cantidadDiscapacitados) as totalPersonas,
                r.estado,
                CONCAT(COALESCE(h.nombres, ''), ' ', COALESCE(h.apellidos, '')) as nombreHuesped,
                h.numDocumento as huespedDocumento,
                hab.numero as numeroHabitacion,
                hot.nombre as nombreHotel";

            $fromClause = " FROM tp_reservas r
                LEFT JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento
                LEFT JOIN tp_habitaciones hab ON r.id_habitacion = hab.id
                LEFT JOIN tp_hotel hot ON r.id_hotel = hot.id";

            // Construcción de la cláusula WHERE
            $whereClauses = ["r.id_hotel = :id_hotel"];
            $params = [':id_hotel' => (int)$id_hotel];

            if (isset($filtros['estado']) && $filtros['estado'] !== 'all' && !empty($filtros['estado'])) {
                $whereClauses[] = "r.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }

            if (isset($filtros['busqueda']) && !empty(trim($filtros['busqueda']))) {
                $whereClauses[] = "(r.id LIKE :busqueda OR CONCAT(COALESCE(h.nombres, ''), ' ', COALESCE(h.apellidos, '')) LIKE :busqueda OR hab.numero LIKE :busqueda)";
                $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
            }

            $whereSql = " WHERE " . implode(' AND ', $whereClauses);
            // --- FIN: Construcción de la consulta ---

            // Contar total de registros
            $sqlTotal = "SELECT COUNT(r.id)" . $fromClause . $whereSql;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($params);
            $totalRegistros = (int)$stmtTotal->fetchColumn();

            // Obtener registros para la página actual
            $sql = $selectClause . $fromClause . $whereSql . " ORDER BY r.fechainicio DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);

            // Bind de parámetros de la cláusula WHERE de forma segura
            foreach ($params as $key => $val) {
                if ($key === ':id_hotel') {
                    $stmt->bindValue($key, $val, PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $val, PDO::PARAM_STR);
                }
            }

            $stmt->bindValue(':limit', (int)$registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'reservas' => $reservas,
                'pagina' => (int)$pagina,
                'registrosPorPagina' => (int)$registrosPorPagina,
                'total' => $totalRegistros,
                'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
            ];

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::obtenerReservasPaginadas: " . $e->getMessage());
            throw new Exception("Error al obtener las reservas");
        }
    }

    public function obtenerPorId($id) {
        try {
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
                    CONCAT(COALESCE(h.nombres, ''), ' ', COALESCE(h.apellidos, '')) as nombreHuesped,
                    h.numDocumento as huespedDocumento,
                    CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, '')) as nombreUsuario,
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
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            return $resultado ?: false;

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function actualizarReserva($id, $datos) {
        try {
            $campos = [];
            $params = [':id' => (int)$id];

            // Validar y sanitizar datos
            $camposPermitidos = ['fechainicio', 'fechaFin', 'pagoFinal', 'estado', 'informacionAdicional', 'cantidadAdultos', 'cantidadNinos', 'cantidadDiscapacitados'];
            
            foreach ($datos as $key => $value) {
                if (in_array($key, $camposPermitidos)) {
                    $campos[] = "$key = :$key";
                    
                    // Aplicar validaciones específicas por campo
                    switch ($key) {
                        case 'pagoFinal':
                            $params[":$key"] = floatval($value);
                            break;
                        case 'cantidadAdultos':
                        case 'cantidadNinos':
                        case 'cantidadDiscapacitados':
                            $params[":$key"] = intval($value);
                            break;
                        case 'fechainicio':
                        case 'fechaFin':
                            // Validar formato de fecha
                            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $value)) {
                                throw new Exception("Formato de fecha inválido para $key");
                            }
                            $params[":$key"] = $value;
                            break;
                        case 'estado':
                            $estadosValidos = ['Activa', 'Pendiente', 'Finalizada', 'Cancelada'];
                            if (!in_array($value, $estadosValidos)) {
                                throw new Exception("Estado inválido");
                            }
                            $params[":$key"] = $value;
                            break;
                        default:
                            $params[":$key"] = $value;
                            break;
                    }
                }
            }

            if (empty($campos)) {
                throw new Exception("No hay campos válidos para actualizar");
            }

            $sql = "UPDATE tp_reservas SET " . implode(', ', $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::actualizarReserva: " . $e->getMessage());
            throw new Exception("Error al actualizar la reserva: " . $e->getMessage());
        }
    }

    public function eliminarReserva($id) {
        try {
            // Verificar si la reserva existe
            $reserva = $this->obtenerPorId($id);
            if (!$reserva) {
                throw new Exception("La reserva no existe");
            }

            // Opcional: Verificar reglas de negocio antes de eliminar
            if ($reserva['estado'] === 'Activa') {
                // Puedes descomentar esta línea si quieres evitar eliminar reservas activas
                // throw new Exception("No se puede eliminar una reserva activa. Cancélala primero.");
            }

            $stmt = $this->db->prepare("DELETE FROM tp_reservas WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::eliminarReserva: " . $e->getMessage());
            
            // Verificar si es un error de clave foránea
            if ($e->getCode() == '23000' || strpos($e->getMessage(), 'foreign key constraint fails') !== false) {
                throw new Exception("No se puede eliminar la reserva porque tiene registros asociados (ej. una factura).");
            }
            throw new Exception("Error al eliminar la reserva");
        }
    }

    public function verificarDisponibilidad($id_habitacion, $fecha_inicio, $fecha_fin, $excluir_reserva_id = null) {
        try {
            $sql = "SELECT COUNT(*) FROM tp_reservas 
                    WHERE id_habitacion = :id_habitacion 
                    AND estado IN ('Activa', 'Pendiente')
                    AND (
                        (:fecha_inicio < fechaFin AND :fecha_fin > fechainicio)
                    )";
            
            $params = [
                ':id_habitacion' => (int)$id_habitacion,
                ':fecha_inicio' => $fecha_inicio,
                ':fecha_fin' => $fecha_fin
            ];

            // Si hay una reserva a excluir (útil para ediciones)
            if ($excluir_reserva_id) {
                $sql .= " AND id != :excluir_id";
                $params[':excluir_id'] = (int)$excluir_reserva_id;
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            return $stmt->fetchColumn() == 0;

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::verificarDisponibilidad: " . $e->getMessage());
            return false; // En caso de error, es más seguro asumir que no está disponible.
        }
    }

    public function obtenerEstadisticas($id_hotel) {
        try {
            $sql = "SELECT 
                        estado,
                        COUNT(*) as cantidad,
                        SUM(pagoFinal) as total_ingresos
                    FROM tp_reservas 
                    WHERE id_hotel = :id_hotel 
                    GROUP BY estado";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::obtenerEstadisticas: " . $e->getMessage());
            return [];
        }
    }
}
?>