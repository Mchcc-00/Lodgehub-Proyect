<?php
require_once '../models/tipoHabitacionModel.php';
require_once '../views/validarSesion.php';

header('Content-Type: application/json');

class TipoHabitacionController {
    private $model;

    public function __construct() {
        $this->model = new TipoHabitacionModel();
    }

    public function manejarPeticion() {
        $action = $_GET['action'] ?? '';

        // Validar que un hotel esté seleccionado en la sesión para todas las acciones
        if (!isset($_SESSION['hotel_id']) || empty($_SESSION['hotel_id'])) {
            echo json_encode(['success' => false, 'message' => 'Error: No hay un hotel seleccionado en la sesión.']);
            return;
        }
        $id_hotel = $_SESSION['hotel_id'];

        try {
            switch ($action) {
                case 'listar':
                    $this->listar($id_hotel);
                    break;
                case 'crear':
                    $this->crear($id_hotel);
                    break;
                case 'obtenerPorId':
                    $this->obtenerPorId();
                    break;
                case 'actualizar':
                    $this->actualizar($id_hotel);
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
                default:
                    echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
                    break;
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function listar($id_hotel) {
        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $busqueda = $_GET['busqueda'] ?? '';
        $registrosPorPagina = 10;

        // Actualizar contadores antes de listar
        $this->model->actualizarContador(null, $id_hotel);

        $resultado = $this->model->obtenerTiposHabitacionPaginados($id_hotel, $pagina, $registrosPorPagina, $busqueda);
        echo json_encode(['success' => true, 'data' => $resultado]);
    }

    private function crear($id_hotel) {
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($descripcion)) {
            throw new Exception('La descripción no puede estar vacía.');
        }
        if (strlen($descripcion) > 20) {
            throw new Exception('La descripción no puede exceder los 20 caracteres.');
        }

        if ($this->model->verificarDescripcionExistente($descripcion, $id_hotel)) {
            throw new Exception('La descripción "' . htmlspecialchars($descripcion) . '" ya existe en este hotel.');
        }

        $datos = [
            'descripcion' => $descripcion,
            'id_hotel' => $id_hotel
        ];

        if ($this->model->crearTipoHabitacion($datos)) {
            echo json_encode(['success' => true, 'message' => 'Tipo de habitación creado exitosamente.']);
        } else {
            throw new Exception('No se pudo crear el tipo de habitación.');
        }
    }

    private function obtenerPorId() {
        $id = $_GET['id'] ?? 0;
        if ($id <= 0) {
            throw new Exception('ID no válido.');
        }
        $tipo = $this->model->obtenerPorId($id);
        if ($tipo) {
            echo json_encode(['success' => true, 'data' => $tipo]);
        } else {
            throw new Exception('Tipo de habitación no encontrado.');
        }
    }

    private function actualizar($id_hotel) {
        $id = $_POST['id'] ?? 0;
        $descripcion = trim($_POST['descripcion'] ?? '');

        if ($id <= 0) {
            throw new Exception('ID no válido para actualizar.');
        }
        if (empty($descripcion)) {
            throw new Exception('La descripción no puede estar vacía.');
        }
        if (strlen($descripcion) > 20) {
            throw new Exception('La descripción no puede exceder los 20 caracteres.');
        }

        if ($this->model->verificarDescripcionExistente($descripcion, $id_hotel, $id)) {
            throw new Exception('La descripción "' . htmlspecialchars($descripcion) . '" ya existe en este hotel.');
        }

        if ($this->model->actualizarTipoHabitacion($id, $descripcion)) {
            echo json_encode(['success' => true, 'message' => 'Tipo de habitación actualizado exitosamente.']);
        } else {
            throw new Exception('No se pudo actualizar el tipo de habitación.');
        }
    }

    private function eliminar() {
        $id = $_POST['id'] ?? 0;
        if ($id <= 0) {
            throw new Exception('ID no válido para eliminar.');
        }

        // Validación CRÍTICA: Verificar si el tipo de habitación está en uso.
        if ($this->model->verificarUso($id)) {
            throw new Exception('No se puede eliminar. Este tipo de habitación está asignado a una o más habitaciones.');
        }

        if ($this->model->eliminarTipoHabitacion($id)) {
            echo json_encode(['success' => true, 'message' => 'Tipo de habitación eliminado exitosamente.']);
        } else {
            throw new Exception('No se pudo eliminar el tipo de habitación.');
        }
    }
}

$controller = new TipoHabitacionController();
$controller->manejarPeticion();
?>