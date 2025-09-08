<?php
/**
 * API REST para gestión de hoteles
 * Ruta: /api/v1/hotels.php
 */

// INICIAR SESIÓN PRIMERO: Es crucial para que la API reconozca al usuario logueado.
// La ruta correcta para validar la sesión, partiendo desde /api/v1/
require_once __DIR__ . '/../../app/views/validarSesion.php';

// Configurar headers para API REST
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');

// Manejar preflight requests (CORS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// La ruta correcta para el controlador
require_once __DIR__ . '/../../app/controllers/hotelController.php';

// Función para enviar respuesta JSON
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Función para manejar errores
function handleError($message, $statusCode = 500) {
    sendJsonResponse([
        'success' => false,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ], $statusCode);
}

try {
    // Verificar que la sesión esté activa
    if (!isset($_SESSION['user']) || !isset($_SESSION['user']['numDocumento'])) {
        handleError('Sesión no válida. Por favor, inicia sesión.', 401);
    }

    // Instanciar el controlador
    $hotelController = new HotelController();
    
    // Obtener método HTTP
    $method = $_SERVER['REQUEST_METHOD'];
    
    // Obtener parámetros de la URL
    $requestUri = $_SERVER['REQUEST_URI'];
    $pathInfo = isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '';
    
    // Extraer ID si existe en la URL (ej: /api/v1/hotels.php/123)
    $hotelId = null;
    if (!empty($pathInfo)) {
        $segments = explode('/', trim($pathInfo, '/'));
        if (!empty($segments[0]) && is_numeric($segments[0])) {
            $hotelId = (int)$segments[0];
        }
    }
    
    // También verificar parámetro GET id
    if (!$hotelId && isset($_GET['id']) && is_numeric($_GET['id'])) {
        $hotelId = (int)$_GET['id'];
    }

    // Rutear según el método HTTP
    switch ($method) {
        case 'GET':
            if ($hotelId) {
                // GET /api/v1/hotels.php/123 - Obtener hotel específico
                $hotelController->obtenerHotel($hotelId);
            } else {
                // GET /api/v1/hotels.php - Obtener todos los hoteles
                $hotelController->obtenerHoteles();
            }
            break;

        case 'POST':
            // POST /api/v1/hotels.php - Crear nuevo hotel
            $hotelController->crearHotel();
            break;

        case 'PUT':
            // PUT /api/v1/hotels.php - Actualizar hotel
            if ($hotelId) {
                $hotelController->actualizarHotel($hotelId);
            } else {
                // Si no hay ID en URL, buscar en el body
                $hotelController->actualizarHotel();
            }
            break;

        case 'DELETE':
            // DELETE /api/v1/hotels.php/123 - Eliminar hotel
            if ($hotelId) {
                $hotelController->eliminarHotel($hotelId);
            } else {
                // Si no hay ID en URL, buscar en el body
                $hotelController->eliminarHotel();
            }
            break;

        default:
            handleError('Método HTTP no permitido: ' . $method, 405);
            break;
    }

} catch (Exception $e) {
    // Log del error (en producción, usar un sistema de logs apropiado)
    error_log("Error en API Hotels: " . $e->getMessage() . " - Línea: " . $e->getLine());
    
    handleError('Error interno del servidor. Contacte al administrador.', 500);
}
