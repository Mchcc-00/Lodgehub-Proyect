<?php
header('Content-Type: application/json; charset=utf-8');
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../models/reservasModel.php';

class ReservasController {
    private $reservasModel;

    public function __construct() {
        $this->reservasModel = new ReservasModel();
    }

    private function responder($success, $message, $data = null, $statusCode = 200) {
        http_response_code($statusCode);
        echo json_encode(['success' => $success, 'message' => $message, 'data' => $data]);
        exit;
    }

    public function manejarPeticion() {
        $action = $_GET['action'] ?? null;
        $input = null;

        // Si es POST y el contenido es JSON, decodificamos el cuerpo de la petición
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && strpos($_SERVER["CONTENT_TYPE"] ?? '', "application/json") !== false) {
            $input = json_decode(file_get_contents('php://input'), true);
            // La acción puede venir en el cuerpo JSON
            if (isset($input['action'])) {
                $action = $input['action'];
            }
        }

        try {
            switch ($action) {
                case 'listar':
                    $this->listarReservas();
                    break;
                case 'obtener':
                    $this->obtenerReserva();
                    break;
                case 'actualizar':
                    $this->actualizarReserva($input);
                    break;
                case 'eliminar':
                    $this->eliminarReserva($input);
                    break;
                default:
                    $this->responder(false, 'Acción no válida', null, 400);
            }
        } catch (Exception $e) {
            error_log("Error en ReservasController (Accion: $action): " . $e->getMessage());
            $this->responder(false, 'Error interno del servidor. Por favor, contacte a soporte.', null, 500);
        }
    }

    private function listarReservas() {
        $id_hotel = $_SESSION['hotel_id'] ?? null;
        if (!$id_hotel) {
            $this->responder(false, 'No se ha seleccionado un hotel.', null, 403);
        }

        $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
        $registrosPorPagina = isset($_GET['registros']) ? (int)$_GET['registros'] : 10;
        
        $filtros = [
            'estado' => $_GET['filtro'] ?? 'all',
            'busqueda' => $_GET['busqueda'] ?? ''
        ];

        $resultado = $this->reservasModel->obtenerReservasPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros);
        $this->responder(true, 'Reservas obtenidas', $resultado);
    }

    private function obtenerReserva() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $this->responder(false, 'ID de reserva no proporcionado', null, 400);
        }

        $reserva = $this->reservasModel->obtenerPorId((int)$id);
        if ($reserva) {
            // Validar que la reserva pertenezca al hotel del usuario
            if ($reserva['id_hotel'] != ($_SESSION['hotel_id'] ?? null)) {
                $this->responder(false, 'Acceso denegado a esta reserva.', null, 403);
            }
            $this->responder(true, 'Reserva obtenida', $reserva);
        } else {
            $this->responder(false, 'Reserva no encontrada', null, 404);
        }
    }

    private function actualizarReserva($input) {
        $id = $input['id'] ?? null;

        if (!$id) {
            $this->responder(false, 'ID de reserva no proporcionado', null, 400);
        }

        // Obtener reserva para validar permisos
        $reservaActual = $this->reservasModel->obtenerPorId((int)$id);
        if (!$reservaActual || $reservaActual['id_hotel'] != ($_SESSION['hotel_id'] ?? null)) {
            $this->responder(false, 'Acceso denegado o reserva no encontrada.', null, 403);
        }

        // Filtrar datos que se pueden actualizar
        $datosActualizables = [];
        $camposPermitidos = ['fechainicio', 'fechaFin', 'pagoFinal', 'estado', 'informacionAdicional'];
        
        foreach ($camposPermitidos as $campo) {
            if (isset($input[$campo])) {
                $datosActualizables[$campo] = $input[$campo];
            }
        }

        if (empty($datosActualizables)) {
            $this->responder(false, 'No hay datos para actualizar', null, 400);
        }

        $resultado = $this->reservasModel->actualizarReserva((int)$id, $datosActualizables);

        if ($resultado) {
            $this->responder(true, 'Reserva actualizada correctamente');
        } else {
            $this->responder(false, 'Error al actualizar la reserva', null, 500);
        }
    }

    private function eliminarReserva($input) {
        $id = $input['id'] ?? null;

        if (!$id) {
            $this->responder(false, 'ID de reserva no proporcionado', null, 400);
        }

        // Obtener reserva para validar permisos
        $reservaActual = $this->reservasModel->obtenerPorId((int)$id);
        if (!$reservaActual || $reservaActual['id_hotel'] != ($_SESSION['hotel_id'] ?? null)) {
            $this->responder(false, 'Acceso denegado o reserva no encontrada.', null, 403);
        }

        $resultado = $this->reservasModel->eliminarReserva((int)$id);

        if ($resultado) {
            $this->responder(true, 'Reserva eliminada correctamente');
        } else {
            $this->responder(false, 'Error al eliminar la reserva', null, 500);
        }
    }
}

$controller = new ReservasController();
$controller->manejarPeticion();
?>