<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/habitacionesModel.php';

class HabitacionesController {
    private $model;
    private $id_hotel;

    public function __construct() {
        $this->model = new HabitacionesModel();
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->id_hotel = $_SESSION['hotel_id'] ?? null;
    }

    private function responderJson($datos) {
        header('Content-Type: application/json');
        echo json_encode($datos);
        exit;
    }

    private function validarHotel() {
        if (!$this->id_hotel) {
            $this->responderJson(['success' => false, 'message' => 'No se ha seleccionado un hotel.']);
        }
    }

    public function obtener() {
        $this->validarHotel();
        try {
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $filtros = [
                'estado' => $_GET['estado'] ?? 'all',
                'tipo' => $_GET['tipo'] ?? 'all',
                'busqueda' => $_GET['busqueda'] ?? '',
            ];
            $resultado = $this->model->obtenerHabitacionesPaginadas($this->id_hotel, $pagina, 10, $filtros);
            $this->responderJson(['success' => true, 'data' => $resultado]);
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function obtenerPorId() {
        $this->validarHotel();
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new Exception('ID de habitación no proporcionado.');
            $habitacion = $this->model->obtenerPorId($id);
            if ($habitacion) {
                $this->responderJson(['success' => true, 'data' => $habitacion]);
            } else {
                $this->responderJson(['success' => false, 'message' => 'Habitación no encontrada.']);
            }
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function crear() {
        $this->validarHotel();
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido.');

            $datos = [
                ':numero' => trim($_POST['numero']),
                ':costo' => (float)$_POST['costo'],
                ':capacidad' => (int)$_POST['capacidad'],
                ':tipoHabitacion' => (int)$_POST['tipoHabitacion'],
                ':descripcion' => trim($_POST['descripcion']),
                ':estado' => 'Disponible',
                ':id_hotel' => (int)$this->id_hotel,
                ':foto' => null
            ];

            if ($this->model->verificarNumeroExistente($datos[':numero'], $this->id_hotel)) {
                throw new Exception("El número de habitación '{$datos[':numero']}' ya existe en este hotel.");
            }

            // Manejo de la foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $foto = $this->manejarCargaFoto($_FILES['foto']);
                $datos[':foto'] = $foto;
            }

            if ($this->model->crearHabitacion($datos)) {
                $this->responderJson(['success' => true, 'message' => 'Habitación creada correctamente.']);
            } else {
                throw new Exception('No se pudo crear la habitación.');
            }
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function actualizar() {
        $this->validarHotel();
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido.');
            
            $id = $_POST['id'] ?? null;
            if (!$id) throw new Exception('ID de habitación no proporcionado.');

            $datos = [];
            $camposPermitidos = ['numero', 'costo', 'capacidad', 'tipoHabitacion', 'descripcion', 'estado'];
            foreach ($camposPermitidos as $campo) {
                if (isset($_POST[$campo])) {
                    $datos[$campo] = $_POST[$campo];
                }
            }

            if ($this->model->verificarNumeroExistente($datos['numero'], $this->id_hotel, $id)) {
                throw new Exception("El número de habitación '{$datos['numero']}' ya existe en este hotel.");
            }

            // Manejo de la foto
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                $datos['foto'] = $this->manejarCargaFoto($_FILES['foto']);
            }

            if ($this->model->actualizarHabitacion($id, $datos)) {
                $this->responderJson(['success' => true, 'message' => 'Habitación actualizada correctamente.']);
            } else {
                throw new Exception('No se pudo actualizar la habitación.');
            }
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function eliminar() {
        $this->validarHotel();
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido.');
            $id = $_POST['id'] ?? null;
            if (!$id) throw new Exception('ID de habitación no proporcionado.');

            if ($this->model->eliminarHabitacion($id)) {
                $this->responderJson(['success' => true, 'message' => 'Habitación eliminada correctamente.']);
            } else {
                // La excepción será lanzada desde el modelo si hay reservas.
            }
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function obtenerTipos() {
        $this->validarHotel();
        try {
            $tipos = $this->model->obtenerTiposHabitacion($this->id_hotel);
            $this->responderJson(['success' => true, 'data' => $tipos]);
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => 'Error al obtener tipos de habitación.']);
        }
    }

    private function manejarCargaFoto($file) {
        $uploadDir = __DIR__ . '/../../public/uploads/habitaciones/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $fileName = uniqid('hab_', true) . '.' . $extension;
        $uploadFile = $uploadDir . $fileName;

        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        if (!in_array(strtolower($extension), $allowedTypes)) {
            throw new Exception('Tipo de archivo no permitido.');
        }

        if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
            return '/lodgehub/public/uploads/habitaciones/' . $fileName;
        } else {
            throw new Exception('Error al subir la foto.');
        }
    }
}

// Manejo de rutas/acciones
if (isset($_GET['action'])) {
    $controller = new HabitacionesController();
    $action = $_GET['action'];

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
    }
}
?>