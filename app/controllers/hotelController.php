<?php
require_once 'models/HotelModel.php';

/**
 * Controlador para gestión de hoteles
 */
class HotelController {
    private $hotelModel;

    public function __construct($db) {
        $this->hotelModel = new HotelModel($db);
    }

    // Manejar respuestas de error
    private function sendErrorResponse($message, $code = 400) {
        http_response_code($code);
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => $message]);
        exit;
    }

    // Manejar respuestas exitosas
    private function sendSuccessResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    public function handleRequest() {
        // Verificar si es una llamada AJAX o API
        $isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
                  strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

        $isApi = isset($_GET['action']) || isset($_POST['action']);

        if ($isApi || $isAjax) {
            $this->handleApiRequest();
        } else {
            $this->showView();
        }
    }

    private function handleApiRequest() {
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

        // Manejar OPTIONS request para CORS
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit(0);
        }

        // Obtener la acción
        $action = $_GET['action'] ?? $_POST['action'] ?? '';

        switch ($action) {
            case 'create':
                $this->create();
                break;
            case 'read':
                $this->read();
                break;
            case 'update':
                $this->update();
                break;
            case 'delete':
                $this->delete();
                break;
            default:
                $this->sendErrorResponse('Acción no válida: ' . $action);
        }
    }

    private function create() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendErrorResponse('Método no permitido. Use POST.', 405);
        }
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        if (empty($data)) {
            $this->sendErrorResponse('No se recibieron datos');
        }
        
        $result = $this->hotelModel->create($data);
        $this->sendSuccessResponse($result);
    }

    private function read() {
        $id = $_GET['id'] ?? null;
        
        if ($id) {
            $hotel = $this->hotelModel->getById($id);
            if ($hotel) {
                $this->sendSuccessResponse(['success' => true, 'data' => $hotel]);
            } else {
                $this->sendErrorResponse('Hotel no encontrado', 404);
            }
        } else {
            $hotels = $this->hotelModel->getAll();
            $this->sendSuccessResponse(['success' => true, 'data' => $hotels]);
        }
    }

    private function update() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->sendErrorResponse('Método no permitido. Use POST.', 405);
        }
        
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            $data = $_POST;
        }
        
        $id = $data['id'] ?? $_GET['id'] ?? null;
        
        if (!$id) {
            $this->sendErrorResponse('ID del hotel no proporcionado');
        }
        
        $result = $this->hotelModel->update($id, $data);
        $this->sendSuccessResponse($result);
    }

    private function delete() {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        $id = $data['id'] ?? $_GET['id'] ?? $_POST['id'] ?? null;
        
        if (!$id) {
            $this->sendErrorResponse('ID del hotel no proporcionado');
        }
        
        $result = $this->hotelModel->delete($id);
        $this->sendSuccessResponse($result);
    }

    private function showView() {
        require_once 'views/hotel_view.php';
    }
}
?>
?>