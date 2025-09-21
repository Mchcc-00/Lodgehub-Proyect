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

    public function obtenerHabitacionesPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros) {
        try {
            $offset = ($pagina - 1) * $registrosPorPagina;

            $selectClause = "SELECT 
                h.id, h.numero, h.costo, h.capacidad, h.foto, h.descripcion, h.estado,
                th.descripcion as tipo_descripcion";

            $fromClause = " FROM tp_habitaciones h
                LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id";

            // Cláusula WHERE
            $whereClauses = ["m.id_hotel = :id_hotel"];
            $params = [':id_hotel' => (int)$id_hotel];

            if (isset($filtros['estado']) && !empty($filtros['estado']) && $filtros['estado'] !== 'all') {
                $whereClauses[] = "h.estado = :estado";
                $params[':estado'] = $filtros['estado'];
            }
            if (isset($filtros['tipo']) && !empty($filtros['tipo']) && $filtros['tipo'] !== 'all') {
                $whereClauses[] = "h.tipoHabitacion = :tipo";
                $params[':tipo'] = (int)$filtros['tipo'];
            }

            if (isset($filtros['busqueda']) && !empty(trim($filtros['busqueda']))) {
                $busqueda = '%' . trim($filtros['busqueda']) . '%';
                $whereClauses[] = "(h.numero LIKE :busqueda OR h.descripcion LIKE :busqueda OR th.descripcion LIKE :busqueda)";
                $params[':busqueda'] = $busqueda;
            }

            $whereSql = " WHERE " . implode(' AND ', $whereClauses);

            // Contar total de registros
            $sqlTotal = "SELECT COUNT(h.id)" . $fromClause . $whereSql;
            $stmtTotal = $this->db->prepare($sqlTotal);
            $stmtTotal->execute($params);
            $totalRegistros = (int)$stmtTotal->fetchColumn();

            // Obtener registros para la página actual
            $sql = $selectClause . $fromClause . $whereSql . " ORDER BY h.numero ASC LIMIT :limit OFFSET :offset";
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

    public function obtenerPorId($id) {
        try {
            $sql = "SELECT h.*, th.descripcion as tipo_descripcion 
                    FROM tp_habitaciones h
                    LEFT JOIN td_tipoHabitacion th ON h.tipoHabitacion = th.id
                    WHERE h.id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::obtenerPorId: " . $e->getMessage());
            return false;
        }
    }

    public function crearHabitacion($datos) {
        try {
            $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, foto, descripcion, estado, id_hotel)
                    VALUES (:numero, :costo, :capacidad, :tipoHabitacion, :foto, :descripcion, :estado, :id_hotel)";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':numero', $datos['numero'], PDO::PARAM_STR);
            $stmt->bindValue(':costo', $datos['costo']);
            $stmt->bindValue(':capacidad', $datos['capacidad'], PDO::PARAM_INT);
            $stmt->bindValue(':tipoHabitacion', $datos['tipoHabitacion'], PDO::PARAM_INT);
            $stmt->bindValue(':foto', $datos['foto'], PDO::PARAM_STR);
            $stmt->bindValue(':descripcion', $datos['descripcion'], PDO::PARAM_STR);
            $stmt->bindValue(':estado', 'Disponible', PDO::PARAM_STR);
            $stmt->bindValue(':id_hotel', $datos['id_hotel'], PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::crearHabitacion: " . $e->getMessage());
            if ($e->getCode() == '23000') { // Error de clave única
                throw new Exception("El número de habitación '{$datos['numero']}' ya existe en este hotel.");
            }
            throw new Exception("Error al crear la habitación: " . $e->getMessage());
        }
    }

    public function actualizarHabitacion($id, $datos) {
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
            
            if (isset($datos['foto'])) {
                $campos[] = "foto = :foto";
                $params[":foto"] = $datos['foto'];
            }

            if (empty($campos)) {
                throw new Exception("No hay campos válidos para actualizar");
            }

            $sql = "UPDATE tp_habitaciones SET " . implode(', ', $campos) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::actualizarHabitacion: " . $e->getMessage());
            if ($e->getCode() == '23000') {
                throw new Exception("El número de habitación '{$datos['numero']}' ya existe en este hotel.");
            }
            throw new Exception("Error al actualizar la habitación: " . $e->getMessage());
        }
    }

    public function eliminarHabitacion($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM tp_habitaciones WHERE id = :id");
            $stmt->bindValue(':id', (int)$id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            error_log("Error en HabitacionesModel::eliminarHabitacion: " . $e->getMessage());
            if ($e->getCode() == '23000') {
                throw new Exception("No se puede eliminar la habitación porque tiene registros asociados (ej. reservas o mantenimientos).");
            }
            throw new Exception("Error al eliminar la habitación.");
        }
    }

    public function obtenerTiposHabitacion($id_hotel) {
        try {
            $sql = "SELECT id, descripcion FROM td_tipoHabitacion WHERE id_hotel = :id_hotel ORDER BY descripcion ASC";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':id_hotel', (int)$id_hotel, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener tipos de habitación: " . $e->getMessage());
            return [];
        }
    }
}
?>