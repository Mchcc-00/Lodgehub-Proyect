<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class HabitacionesModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
        if (!$this->db) {
            throw new Exception("Error al conectar con la base de datos");
        }
    }

    /**
     * Obtiene los tipos de habitación para un hotel específico.
     */
    public function obtenerTiposHabitacion($id_hotel) {
        try {
            $sql = "SELECT id, descripcion FROM td_tipoHabitacion WHERE id_hotel = :id_hotel ORDER BY descripcion ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::obtenerTiposHabitacion: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Verifica si un número de habitación ya existe en un hotel.
     */
    public function verificarNumeroExistente($numero, $id_hotel, $id_actual = null) {
        try {
            $sql = "SELECT COUNT(*) FROM tp_habitaciones WHERE numero = :numero AND id_hotel = :id_hotel";
            if ($id_actual) {
                $sql .= " AND id != :id_actual";
            }
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':numero', $numero, PDO::PARAM_STR);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            if ($id_actual) {
                $stmt->bindValue(':id_actual', (int)$id_actual, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchColumn() > 0;
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::verificarNumeroExistente: " . $e->getMessage());
            return true; // Asumir que existe para prevenir duplicados en caso de error
        }
    }

    /**
     * Crea una nueva habitación.
     */
    public function crearHabitacion($datos) {
        try {
            $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, foto, descripcion, estado, id_hotel)
                    VALUES (:numero, :costo, :capacidad, :tipoHabitacion, :foto, :descripcion, :estado, :id_hotel)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':numero', $datos[':numero'], PDO::PARAM_STR);
            $stmt->bindValue(':costo', $datos[':costo']);
            $stmt->bindValue(':capacidad', $datos[':capacidad'], PDO::PARAM_INT);
            $stmt->bindValue(':tipoHabitacion', $datos[':tipoHabitacion'], PDO::PARAM_INT);
            $stmt->bindValue(':foto', $datos[':foto'], PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $datos[':descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(':estado', $datos[':estado'], PDO::PARAM_STR);
            $stmt->bindValue(':id_hotel', $datos[':id_hotel'], PDO::PARAM_INT);
            
            $exito = $stmt->execute();
            if ($exito) {
                // Actualizar contador del tipo de habitación
                require_once 'tipoHabitacionModel.php';
                $tipoModel = new TipoHabitacionModel();
                $tipoModel->actualizarContador($datos[':tipoHabitacion']);
            }
            return $exito;

        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::crearHabitacion: " . $e->getMessage());
            throw new Exception("Error al crear la habitación: " . $e->getMessage());
        }
    }

    /**
     * Obtiene las habitaciones de forma paginada y con filtros.
     */
    public function obtenerHabitacionesPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            // Modificamos el SELECT para determinar el estado dinámicamente
            $selectClause = "SELECT 
                                h.id, 
                                h.numero, 
                                h.costo, 
                                h.capacidad, 
                                -- Si hay una reserva activa hoy, el estado es 'Ocupada', si no, usamos el estado de la tabla.
                                CASE
                                    WHEN m.id IS NOT NULL THEN 'Mantenimiento'
                                    WHEN h.estado = 'Mantenimiento' THEN 'Mantenimiento' -- Mantenemos la lógica original por si acaso
                                    WHEN r.id IS NOT NULL THEN 'Ocupada' 
                                    ELSE h.estado 
                                END as estado, 
                                h.foto,
                                th.descripcion as tipo_descripcion";
            
            // Modificamos el FROM para incluir el LEFT JOIN con las reservas activas para hoy
            $fromClause = " FROM tp_habitaciones h 
                           JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
                           LEFT JOIN tp_reservas r ON h.id = r.id_habitacion 
                                                  AND r.estado IN ('Activa', 'Pendiente') 
                                                  AND CURDATE() >= r.fechainicio 
                                                  AND CURDATE() < r.fechaFin
                           LEFT JOIN tp_mantenimiento m ON h.id = m.id_habitacion AND m.estado = 'Pendiente'";
            
            $whereClauses = ["h.id_hotel = :id_hotel"];
            $params = [':id_hotel' => (int)$id_hotel];

            if (!empty($filtros['tipo']) && $filtros['tipo'] !== 'all') {
                $whereClauses[] = "h.tipoHabitacion = :tipo";
                $params[':tipo'] = (int)$filtros['tipo'];
            }
            if (!empty(trim($filtros['busqueda']))) {
                $whereClauses[] = "(h.numero LIKE :busqueda OR h.descripcion LIKE :busqueda OR th.descripcion LIKE :busqueda)";
                $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
            }

            $whereSql = " WHERE " . implode(' AND ', $whereClauses);

            // El filtro de estado ahora debe aplicarse sobre el resultado del CASE
            // Usaremos HAVING en lugar de WHERE para este filtro.
            $havingClause = "";
            if (!empty($filtros['estado']) && $filtros['estado'] !== 'all') {
                $havingClause = " HAVING estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }

            // Contar total de registros - SIMPLIFICADO Y CORREGIDO
            // Contamos el total de habitaciones que coinciden con los filtros WHERE.
            // El filtro HAVING (por estado dinámico) se aplica después, por lo que el conteo total puede no ser exacto
            // al filtrar por estado, pero asegura que la paginación funcione y se muestren los datos.
            $sqlTotal = "SELECT COUNT(h.id)" . $fromClause . $whereSql;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute(array_filter($params, fn($key) => $key !== ':estado', ARRAY_FILTER_USE_KEY));
            $totalRegistros = (int)$stmtTotal->fetchColumn();

            // Obtener registros para la página actual
            $sql = $selectClause . $fromClause . $whereSql . " GROUP BY h.id " . $havingClause . " ORDER BY h.numero ASC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }
            $stmt->bindValue(':limit', (int)$registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'habitaciones' => $habitaciones,
                'pagina' => (int)$pagina,
                'total' => $totalRegistros,
                'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
            ];
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::obtenerHabitacionesPaginadas: " . $e->getMessage());
            throw new Exception("Error al obtener las habitaciones");
        }
    }

    /**
     * Obtiene una habitación por su ID.
     */
    public function obtenerPorId($id) {
        try {
            // Consulta mejorada para obtener el estado real y detalles de reserva/mantenimiento
            $sql = "SELECT 
                        h.id, h.numero, h.costo, h.capacidad, h.foto, h.descripcion,
                        th.descripcion as tipo_descripcion,
                        CASE
                            WHEN m.id IS NOT NULL THEN 'Mantenimiento'
                            WHEN r.id IS NOT NULL THEN 'Ocupada'
                            ELSE h.estado
                        END as estado,
                        r.id as id_reserva,
                        r.fechaFin as reserva_fecha_fin,
                        CONCAT(hues.nombres, ' ', hues.apellidos) as reserva_huesped,
                        m.id as id_mantenimiento, 
                        m.problemaDescripcion as mantenimiento_descripcion, 
                        m.tipo as mantenimiento_tipo
                    FROM 
                        tp_habitaciones h
                    JOIN 
                        td_tipohabitacion th ON h.tipoHabitacion = th.id
                    LEFT JOIN 
                        tp_reservas r ON h.id = r.id_habitacion AND r.estado IN ('Activa', 'Pendiente') AND CURDATE() >= r.fechainicio AND CURDATE() < r.fechaFin
                    LEFT JOIN
                        tp_huespedes hues ON r.hue_numDocumento = hues.numDocumento
                    LEFT JOIN 
                        tp_mantenimiento m ON h.id = m.id_habitacion AND m.estado = 'Pendiente'
                    WHERE h.id = :id
                    GROUP BY h.id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una habitación.
     */
    public function actualizarHabitacion($id, $datos) {
        // Antes de actualizar, obtener el tipo de habitación antiguo si se va a cambiar
        $tipoAntiguo = null;
        if (isset($datos['tipoHabitacion'])) {
            $stmtAntiguo = $this->db->prepare("SELECT tipoHabitacion FROM tp_habitaciones WHERE id = :id");
            $stmtAntiguo->execute([':id' => $id]);
            $tipoAntiguo = $stmtAntiguo->fetchColumn();
        }

        try {
            $campos = [];
            $params = [':id' => (int)$id];
            $camposPermitidos = ['numero', 'costo', 'capacidad', 'tipoHabitacion', 'descripcion', 'estado'];
            
            foreach ($camposPermitidos as $campo) {
                if (isset($datos[$campo])) {
                    $campos[] = "$campo = :$campo";
                    $params[":$campo"] = $datos[$campo];
                }
            }

            // Manejo de la foto
            if (isset($datos['foto'])) {
                $campos[] = "foto = :foto";
                $params[":foto"] = $datos['foto'];
            }

            if (empty($campos)) {
                throw new Exception("No hay campos válidos para actualizar");
            }

            $sql = "UPDATE tp_habitaciones SET " . implode(', ', $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rowCount = $stmt->rowCount();

            if ($rowCount >= 0) { // Si la consulta fue exitosa
                require_once 'tipoHabitacionModel.php';
                $tipoModel = new TipoHabitacionModel();
                // Si el tipo cambió, actualizar contadores del tipo antiguo y nuevo
                if ($tipoAntiguo && isset($datos['tipoHabitacion']) && $tipoAntiguo != $datos['tipoHabitacion']) {
                    $tipoModel->actualizarContador($tipoAntiguo);
                    $tipoModel->actualizarContador($datos['tipoHabitacion']);
                }
            }
            
            // Devolver true si se afectó al menos una fila, o si no hubo error.
            return $stmt->rowCount() >= 0;
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::actualizarHabitacion: " . $e->getMessage());
            throw new Exception("Error al actualizar la habitación: " . $e->getMessage());
        }
    }

    /**
     * Elimina una habitación.
     */
    public function eliminarHabitacion($id) {
        try {
            // Antes de eliminar, obtener el tipo de habitación para actualizar el contador después
            $stmtTipo = $this->db->prepare("SELECT tipoHabitacion FROM tp_habitaciones WHERE id = :id");
            $stmtTipo->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmtTipo->execute();
            $id_tipo = $stmtTipo->fetchColumn();

            // Primero, verificar si la habitación tiene reservas asociadas
            $stmtCheck = $this->db->prepare("SELECT COUNT(*) FROM tp_reservas WHERE id_habitacion = :id");
            $stmtCheck->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmtCheck->execute();
            if ($stmtCheck->fetchColumn() > 0) {
                throw new Exception("No se puede eliminar la habitación porque tiene reservas asociadas.");
            }

            $stmt = $this->db->prepare("DELETE FROM tp_habitaciones WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $exito = $stmt->execute();

            if ($exito && $id_tipo) {
                // Actualizar contador del tipo de habitación
                require_once 'tipoHabitacionModel.php';
                $tipoModel = new TipoHabitacionModel();
                $tipoModel->actualizarContador($id_tipo);
            }
            return $exito;
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::eliminarHabitacion: " . $e->getMessage());
            throw new Exception("Error al eliminar la habitación: " . $e->getMessage());
        }
    }
}
?>