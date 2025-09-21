<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/habitacionesModel.php';

class HabitacionesController {
    private $habitacionesModel;
    private $id_hotel;

    public function __construct() {
        $this->habitacionesModel = new HabitacionesModel();
        
        // Asegurarse de que la sesión esté iniciada y obtener el id del hotel
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
            $this->responderJson(['success' => false, 'message' => 'No se ha seleccionado un hotel. Por favor, inicie sesión de nuevo.']);
        }
    }

    private function manejarFoto($file, $numeroHabitacion) {
        if (isset($file) && $file['error'] === UPLOAD_ERR_OK) {
            $uploadDir = __DIR__ . '/../../public/uploads/habitaciones/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $fileName = 'hab_' . $this->id_hotel . '_' . preg_replace('/[^a-zA-Z0-9]/', '', $numeroHabitacion) . '_' . time() . '.' . $extension;
            $uploadFile = $uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
                return '/public/uploads/habitaciones/' . $fileName;
            }
        }
        return null;
    }

    public function obtener() {
        $this->validarHotel();
        try {
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $registrosPorPagina = 8; // Ajustar para un grid
            
            $filtros = [
                'estado' => $_GET['estado'] ?? 'all',
                'tipo' => $_GET['tipo'] ?? 'all',
                'busqueda' => $_GET['busqueda'] ?? null,
            ];

            $resultado = $this->habitacionesModel->obtenerHabitacionesPaginadas($this->id_hotel, $pagina, $registrosPorPagina, $filtros);
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

            $habitacion = $this->habitacionesModel->obtenerPorId($id);
            
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

            $camposRequeridos = ['numero', 'costo', 'capacidad', 'tipoHabitacion'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    throw new Exception("El campo '{$campo}' es obligatorio.");
                }
            }

            $fotoPath = $this->manejarFoto($_FILES['foto'] ?? null, $_POST['numero']);

            $datos = [
                'numero' => trim($_POST['numero']),
                'costo' => (float)$_POST['costo'],
                'capacidad' => (int)$_POST['capacidad'],
                'tipoHabitacion' => (int)$_POST['tipoHabitacion'],
                'descripcion' => isset($_POST['descripcion']) ? trim($_POST['descripcion']) : null,
                'foto' => $fotoPath,
                'id_hotel' => $this->id_hotel
            ];

            $resultado = $this->habitacionesModel->crearHabitacion($datos);

            if ($resultado) {
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
                    $datos[$campo] = trim($_POST[$campo]);
                }
            }

            $fotoPath = $this->manejarFoto($_FILES['foto'] ?? null, $_POST['numero']);
            if ($fotoPath) {
                // Si se sube foto nueva, eliminar la anterior
                $habitacionActual = $this->habitacionesModel->obtenerPorId($id);
                if ($habitacionActual && !empty($habitacionActual['foto'])) {
                    $rutaFotoAntigua = __DIR__ . '/../../' . ltrim($habitacionActual['foto'], '/');
                    if (file_exists($rutaFotoAntigua)) {
                        unlink($rutaFotoAntigua);
                    }
                }
                $datos['foto'] = $fotoPath;
            }

            $resultado = $this->habitacionesModel->actualizarHabitacion($id, $datos);
            if ($resultado) {
                $this->responderJson(['success' => true, 'message' => 'Habitación actualizada correctamente.']);
            } else {
                throw new Exception('No se pudo actualizar la habitación o no hubo cambios.');
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

            // Obtener info de la habitación para borrar la foto
            $habitacion = $this->habitacionesModel->obtenerPorId($id);
            
            $resultado = $this->habitacionesModel->eliminarHabitacion($id);
            if ($resultado) {
                // Si se eliminó de la BD, borrar el archivo de foto
                if ($habitacion && !empty($habitacion['foto'])) {
                    $rutaFoto = __DIR__ . '/../../' . ltrim($habitacion['foto'], '/');
                    if (file_exists($rutaFoto)) {
                        unlink($rutaFoto);
                    }
                }
                $this->responderJson(['success' => true, 'message' => 'Habitación eliminada correctamente.']);
            } else {
                throw new Exception('No se pudo eliminar la habitación.');
            }
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}

// Manejo de rutas/acciones
if (isset($_REQUEST['action'])) {
    $controller = new HabitacionesController();
    $action = $_REQUEST['action'];

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
    }
}
?>