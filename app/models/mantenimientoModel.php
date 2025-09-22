<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

class MantenimientoModel {
    private $db;

    public function __construct() {
        $this->db = conexionDB();
        if (!$this->db) {
            throw new Exception("Error al conectar con la base de datos");
        }
    }

    /**
     * Obtiene los registros de mantenimiento de forma paginada y con filtros.
     */
    public function obtenerMantenimientosPaginados($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            $selectClause = "SELECT 
                m.id,
                m.tipo,
                m.problemaDescripcion,
                m.fechaRegistro,
                m.prioridad,
                m.estado,
                hab.numero as numeroHabitacion,
                CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, '')) as nombreResponsable";

            $fromClause = " FROM tp_mantenimiento m
                LEFT JOIN tp_habitaciones hab ON m.id_habitacion = hab.id
                LEFT JOIN tp_usuarios u ON m.numDocumento = u.numDocumento";

            // Cláusula WHERE
            $whereClauses = ["m.id_hotel = :id_hotel"];
            $params = [':id_hotel' => (int)$id_hotel];

            if (isset($filtros['estado']) && !empty($filtros['estado']) && $filtros['estado'] !== 'all') {
                $whereClauses[] = "m.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            if (isset($filtros['prioridad']) && !empty($filtros['prioridad']) && $filtros['prioridad'] !== 'all') {
                $whereClauses[] = "m.prioridad = :prioridad";
                $params[':prioridad'] = $filtros['prioridad'];
            }
            if (isset($filtros['tipo']) && !empty($filtros['tipo']) && $filtros['tipo'] !== 'all') {
                $whereClauses[] = "m.tipo = :tipo";
                $params[':tipo'] = $filtros['tipo'];
            }

            if (isset($filtros['busqueda']) && !empty(trim($filtros['busqueda']))) {
                $whereClauses[] = "(m.id LIKE :busqueda OR m.problemaDescripcion LIKE :busqueda OR hab.numero LIKE :busqueda)";
                $params[':busqueda'] = '%' . trim($filtros['busqueda']) . '%';
            }

            $whereSql = " WHERE " . implode(' AND ', $whereClauses);

            // Contar total de registros
            $sqlTotal = "SELECT COUNT(m.id)" . $fromClause . $whereSql;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($params);
            $totalRegistros = (int)$stmtTotal->fetchColumn();

            // Obtener registros para la página actual
            $sql = $selectClause . $fromClause . $whereSql . " ORDER BY m.fechaRegistro DESC LIMIT :limit OFFSET :offset";
            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $val) {
                $stmt->bindValue($key, $val, is_int($val) ? PDO::PARAM_INT : PDO::PARAM_STR);
            }

            $stmt->bindValue(':limit', (int)$registrosPorPagina, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            $mantenimientos = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'mantenimientos' => $mantenimientos,
                'pagina' => (int)$pagina,
                'total' => $totalRegistros,
                'totalPaginas' => ceil($totalRegistros / $registrosPorPagina)
            ];

        } catch (PDOException $e) {
            error_log("Error en MantenimientoModel::obtenerMantenimientosPaginados: " . $e->getMessage());
            throw new Exception("Error al obtener los mantenimientos");
        }
    }

    /**
     * Obtiene un registro de mantenimiento por su ID.
     */
    public function obtenerPorId($id) {
        try {
            $sql = "SELECT 
                    m.*,
                    hab.numero as numeroHabitacion,
                    CONCAT(COALESCE(u.nombres, ''), ' ', COALESCE(u.apellidos, '')) as nombreResponsable,
                    u.correo as correoResponsable
                FROM tp_mantenimiento m
                LEFT JOIN tp_habitaciones hab ON m.id_habitacion = hab.id
                LEFT JOIN tp_usuarios u ON m.numDocumento = u.numDocumento
            WHERE m.id = :id";

            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;

        } catch (PDOException $e) {
            error_log("Error en MantenimientoModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un registro de mantenimiento.
     */
    public function actualizarMantenimiento($id, $datos) {
        try {
            $campos = [];
            $params = [':id' => (int)$id];
            $camposPermitidos = ['tipo', 'problemaDescripcion', 'prioridad', 'estado', 'observaciones', 'numDocumento', 'id_habitacion'];
            
            foreach ($datos as $key => $value) {
                if (in_array($key, $camposPermitidos)) {
                    $campos[] = "$key = :$key";
                    $params[":$key"] = $value;
                }
            }

            if (empty($campos)) {
                throw new Exception("No hay campos válidos para actualizar");
            }

            // Añadir la fecha de actualización
            $campos[] = "ultimaActualizacion = CURRENT_TIMESTAMP";

            $sql = "UPDATE tp_mantenimiento SET " . implode(', ', $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            
            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Error en MantenimientoModel::actualizarMantenimiento: " . $e->getMessage());
            throw new Exception("Error al actualizar el mantenimiento: " . $e->getMessage());
        }
    }

    /**
     * Elimina un registro de mantenimiento.
     */
    public function eliminarMantenimiento($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM tp_mantenimiento WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();

        } catch (PDOException $e) {
            error_log("Error en MantenimientoModel::eliminarMantenimiento: " . $e->getMessage());
            throw new Exception("Error al eliminar el mantenimiento");
        }
    }

    /**
     * Busca mantenimientos por un término.
     */
    public function buscarMantenimientos($id_hotel, $termino) {
        try {
            $sql = "SELECT m.id, m.tipo, m.problemaDescripcion, m.fechaRegistro, m.prioridad, m.estado, hab.numero as numeroHabitacion, CONCAT(u.nombres, ' ', u.apellidos) as nombreResponsable
                    FROM tp_mantenimiento m
                    LEFT JOIN tp_habitaciones hab ON m.id_habitacion = hab.id
                    LEFT JOIN tp_usuarios u ON m.numDocumento = u.numDocumento
                    WHERE m.id_hotel = :id_hotel AND (m.id LIKE :termino OR m.problemaDescripcion LIKE :termino OR hab.numero LIKE :termino)
                    ORDER BY m.fechaRegistro DESC";
            
            $stmt = $this->db->prepare($sql);
            $terminoBusqueda = '%' . $termino . '%';
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            $stmt->bindValue(':termino', $terminoBusqueda, PDO::PARAM_STR);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log("Error al buscar mantenimientos: " . $e->getMessage());
            throw new Exception("Error al buscar mantenimientos");
        }
    }

    /**
     * Crea un nuevo registro de mantenimiento.
     */
    public function crearMantenimiento($datos) {
        try {
            $sql = "INSERT INTO tp_mantenimiento (id_habitacion, tipo, problemaDescripcion, frecuencia, cantFrecuencia, prioridad, numDocumento, id_hotel, observaciones)
                    VALUES (:id_habitacion, :tipo, :problemaDescripcion, :frecuencia, :cantFrecuencia, :prioridad, :numDocumento, :id_hotel, :observaciones)";
            
            $stmt = $this->db->prepare($sql);

            $stmt->bindValue(':id_habitacion', $datos['id_habitacion'], PDO::PARAM_INT);
            $stmt->bindValue(':tipo', $datos['tipo'], PDO::PARAM_STR);
            $stmt->bindValue(':problemaDescripcion', $datos['problemaDescripcion'], PDO::PARAM_STR);
            $stmt->bindValue(':frecuencia', $datos['frecuencia'], PDO::PARAM_STR);
            $stmt->bindValue(':cantFrecuencia', $datos['cantFrecuencia'], PDO::PARAM_STR);
            $stmt->bindValue(':prioridad', $datos['prioridad'], PDO::PARAM_STR);
            $stmt->bindValue(':numDocumento', $datos['numDocumento'], PDO::PARAM_STR);
            $stmt->bindValue(':id_hotel', $datos['id_hotel'], PDO::PARAM_INT);
            $stmt->bindValue(':observaciones', $datos['observaciones'], PDO::PARAM_STR);

            $exito = $stmt->execute();

            // Si se crea el mantenimiento, actualizamos el estado de la habitación a 'Mantenimiento'
            if ($exito) {
                $stmtUpdateHab = $this->db->prepare("UPDATE tp_habitaciones SET estado = 'Mantenimiento' WHERE id = :id_habitacion");
                $stmtUpdateHab->bindValue(':id_habitacion', $datos['id_habitacion'], PDO::PARAM_INT);
                $stmtUpdateHab->execute();
            }

            return $exito;

        } catch (PDOException $e) {
            error_log("Error en MantenimientoModel::crearMantenimiento: " . $e->getMessage());
            throw new Exception("Error al crear el registro de mantenimiento: " . $e->getMessage());
        }
    }

    /**
     * Obtiene las habitaciones de un hotel para un select.
     */
    public function obtenerHabitaciones($id_hotel) {
        try {
            // SOLUCIÓN ALTERNATIVA: Usar LEFT JOIN para excluir habitaciones con mantenimiento pendiente.
            // Este método es más robusto y a menudo más eficiente que usar una subconsulta con NOT IN.
            $sql = "SELECT h.id, h.numero, th.descripcion as tipo_descripcion 
                    FROM tp_habitaciones h
                    JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
                    LEFT JOIN tp_mantenimiento m ON h.id = m.id_habitacion AND m.estado = 'Pendiente'
                    WHERE h.id_hotel = :id_hotel
                      AND m.id IS NULL -- La clave: solo trae habitaciones donde no se encontró un mantenimiento pendiente.
                    ORDER BY h.numero ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener habitaciones: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene los colaboradores de un hotel para un select.
     */
    public function obtenerColaboradores($id_hotel) {
        $sql = "SELECT u.numDocumento, u.nombres, u.apellidos FROM tp_usuarios u JOIN ti_personal p ON u.numDocumento = p.numDocumento WHERE p.id_hotel = :id_hotel AND u.roles = 'Colaborador' ORDER BY u.nombres ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>