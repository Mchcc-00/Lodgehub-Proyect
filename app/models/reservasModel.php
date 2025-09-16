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

    /**
     * Obtiene los registros de reservas de forma paginada y con filtros.
     */
    public function obtenerReservasPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            $selectClause = "SELECT 
                r.id,
                r.fechainicio,
                r.fechaFin,
                r.estado,
                r.pagoFinal,
                r.fechaRegistro,
                hab.numero as numeroHabitacion,
                CONCAT(COALESCE(h.nombres, ''), ' ', COALESCE(h.apellidos, '')) as nombreHuesped";

            $fromClause = " FROM tp_reservas r
                LEFT JOIN tp_habitaciones hab ON r.id_habitacion = hab.id
                LEFT JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento";

            // Cláusula WHERE
            $whereClauses = ["r.id_hotel = :id_hotel"];
            $params = [':id_hotel' => (int)$id_hotel];

            if (isset($filtros['estado']) && !empty($filtros['estado']) && $filtros['estado'] !== 'all') {
                $whereClauses[] = "r.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }

            if (isset($filtros['busqueda']) && !empty(trim($filtros['busqueda']))) {
                $busqueda = '%' . trim($filtros['busqueda']) . '%';
                $whereClauses[] = "(r.id LIKE :busqueda OR CONCAT(h.nombres, ' ', h.apellidos) LIKE :busqueda OR hab.numero LIKE :busqueda)";
                $params[':busqueda'] = $busqueda;
            }

            $whereSql = " WHERE " . implode(' AND ', $whereClauses);

            // Contar total de registros
            $sqlTotal = "SELECT COUNT(r.id)" . $fromClause . $whereSql;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($params);
            $totalRegistros = (int)$stmtTotal->fetchColumn();

            // Obtener registros para la página actual
            $sql = $selectClause . $fromClause . $whereSql . " ORDER BY r.fechaRegistro DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->bindValue(':limit', (int)$registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'reservas' => $reservas,
                'pagina' => (int)$pagina,
                'total' => $totalRegistros,
                'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
            ];

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::obtenerReservasPaginadas: " . $e->getMessage());
            throw new Exception("Error al obtener las reservas");
        }
    }

    /**
     * Obtiene un registro de reserva por su ID.
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT 
                    r.*,
                    hab.numero as numeroHabitacion,
                    CONCAT(COALESCE(h.nombres, ''), ' ', COALESCE(h.apellidos, '')) as nombreHuesped,
                    h.correo as correoHuesped,
                    h.numTelefono as telefonoHuesped,
                    u.nombres as nombreUsuario,
                    u.apellidos as apellidoUsuario
                FROM tp_reservas r
                LEFT JOIN tp_habitaciones hab ON r.id_habitacion = hab.id
                LEFT JOIN tp_huespedes h ON r.hue_numDocumento = h.numDocumento
                LEFT JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento
            WHERE r.id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un registro de reserva.
     */
    public function actualizarReserva($id, $datos) {
        try {
            $campos = [];
            $params = [':id' => (int)$id];
            $camposPermitidos = ['fechainicio', 'fechaFin', 'pagoFinal', 'estado', 'informacionAdicional'];
            
            foreach ($datos as $key => $value) {
                if (in_array($key, $camposPermitidos)) {
                    $campos[] = "$key = :$key";
                    $params[":$key"] = $value;
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

    /**
     * Elimina un registro de reserva.
     */
    public function eliminarReserva($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM tp_reservas WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en ReservasModel::eliminarReserva: " . $e->getMessage());
            // Capturar error de clave foránea
            if ($e->getCode() == '23000') {
                throw new Exception("No se puede eliminar la reserva porque tiene registros asociados (ej. una factura).");
            }
            throw new Exception("Error al eliminar la reserva.");
        }
    }

    /**
     * Obtiene las habitaciones disponibles de un hotel para un select.
     */
    public function obtenerHabitacionesDisponibles($id_hotel, $fecha_inicio, $fecha_fin) {
        try {
            $sql = "SELECT h.id, h.numero, h.capacidad, h.costo, th.descripcion as tipo_descripcion 
                    FROM tp_habitaciones h
                    JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
                    WHERE h.id_hotel = :id_hotel 
                    AND h.estadoMantenimiento = 'Activo'
                    AND h.id NOT IN (
                        SELECT id_habitacion FROM tp_reservas
                        WHERE estado IN ('Activa', 'Pendiente')
                        AND (
                            (fechainicio <= :fecha_inicio AND fechaFin > :fecha_inicio) OR
                            (fechainicio < :fecha_fin AND fechaFin >= :fecha_fin) OR
                            (fechainicio >= :fecha_inicio AND fechaFin <= :fecha_fin)
                        )
                    )
                    ORDER BY h.numero ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            $stmt->bindValue(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
            $stmt->bindValue(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones disponibles: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Crea un nuevo registro de reserva.
     */
    public function crearReserva($datos) {
        try {
            $sql = "INSERT INTO tp_reservas (id_hotel, us_numDocumento, hue_numDocumento, fechainicio, fechaFin, id_habitacion, pagoFinal, cantidadAdultos, cantidadNinos, motivoReserva, metodoPago, informacionAdicional, estado)
                    VALUES (:id_hotel, :us_numDocumento, :hue_numDocumento, :fechainicio, :fechaFin, :id_habitacion, :pagoFinal, :cantidadAdultos, :cantidadNinos, :motivoReserva, :metodoPago, :informacionAdicional, :estado)";
            
            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':id_hotel', $datos['id_hotel'], PDO::PARAM_INT);
            $stmt->bindValue(':us_numDocumento', $datos['us_numDocumento'], PDO::PARAM_STR);
            $stmt->bindValue(':hue_numDocumento', $datos['hue_numDocumento'], PDO::PARAM_STR);
            $stmt->bindValue(':fechainicio', $datos['fechainicio'], PDO::PARAM_STR);
            $stmt->bindValue(':fechaFin', $datos['fechaFin'], PDO::PARAM_STR);
            $stmt->bindValue(':id_habitacion', $datos['id_habitacion'], PDO::PARAM_INT);
            $stmt->bindValue(':pagoFinal', $datos['pagoFinal']);
            $stmt->bindValue(':cantidadAdultos', $datos['cantidadAdultos'], PDO::PARAM_INT);
            $stmt->bindValue(':cantidadNinos', $datos['cantidadNinos'], PDO::PARAM_INT);
            $stmt->bindValue(':motivoReserva', $datos['motivoReserva'], PDO::PARAM_STR);
            $stmt->bindValue(':metodoPago', $datos['metodoPago'], PDO::PARAM_STR);
            $stmt->bindValue(':informacionAdicional', $datos['informacionAdicional'], PDO::PARAM_STR);
            $stmt->bindValue(':estado', $datos['estado'], PDO::PARAM_STR);

            // La ejecución de la consulta devuelve true en caso de éxito o false en caso de error.
            return $stmt->execute();

        } catch (PDOException $e) {
            // Corregir el mensaje de error y relanzar la excepción para que el controlador la maneje.
            error_log("Error en ReservasModel::crearReserva: " . $e->getMessage());
            throw new Exception("Error al crear la reserva: " . $e->getMessage());
        }
    }

}
?>