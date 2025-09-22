<?php
/**
 * Controlador de Colaboradores - CORREGIDO
 * Maneja todas las peticiones relacionadas con la gestión de colaboradores
 */

// Headers para JSON y CORS
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

// Manejar preflight OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Función para responder con JSON válido
function responderJSON($success, $message, $data = null, $codigo = 200) {
    http_response_code($codigo);
    $response = [
        'success' => $success,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    if ($data !== null) {
        $response['data'] = $data;
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit();
}

// Función para registrar errores
function logError($error, $context = '') {
    $logMessage = date('Y-m-d H:i:s') . " - ERROR: $error";
    if ($context) {
        $logMessage .= " - Context: $context";
    }
    error_log($logMessage);
}

try {
    // Incluir el modelo
    require_once '../models/misColaboradoresModel.php';
    
    class ColaboradorController {
        private $colaboradorModel;
        
        public function __construct() {
            try {
                // SOLUCIÓN: Usar el nombre de clase correcto "MisColaboradoresModel" que definimos en el modelo.
                $this->colaboradorModel = new MisColaboradoresModel();
            } catch (Exception $e) {
                logError("Error al inicializar modelo: " . $e->getMessage());
                responderJSON(false, "Error de conexión con la base de datos", null, 500);
            }
        }
        
        /**
         * Verifica si el usuario logueado es un Administrador.
         */
        private function esAdmin() {
            // Asegurarse de que la sesión esté iniciada antes de verificar.
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }
            // SOLUCIÓN: Hacer la validación más flexible para que coincida con la de la vista.
            // Un administrador puede ser 'super' (sin hotel) o 'hotel' (gestionando un hotel).
            // Ambos deben tener acceso a las funciones del controlador.
            $esAdmin = isset($_SESSION['tipo_admin']) && in_array($_SESSION['tipo_admin'], ['super', 'hotel']);
            return $esAdmin;
        }

        public function manejarPeticion() {
            try {
                // La acción puede venir por GET (listar, etc.) o por POST (crear, actualizar, etc.)
                $action = $_REQUEST['action'] ?? null;
                $requestData = null;

                if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                    // Si el contenido es JSON, lo decodificamos.
                    // Si es multipart/form-data (como en nuestro caso), PHP ya pobló $_POST y $_FILES.
                    if (strpos($_SERVER["CONTENT_TYPE"] ?? '', "application/json") !== false) {
                        $input = file_get_contents('php://input');
                        $requestData = json_decode($input, true) ?? [];
                        // Si la acción no vino por GET, la buscamos en el cuerpo JSON
                        if (!$action && isset($requestData['action'])) {
                            $action = $requestData['action'];
                        }
                    } else {
                        $requestData = $_POST;
                    }
                }
                switch ($action) {
                    case 'crear':
                        $this->crear();
                        break;
                    
                    case 'listar':
                        $this->listar();
                        break;
                    
                    case 'obtener':
                        $this->obtener();
                        break;
                    
                    case 'actualizar':
                        $this->actualizar($requestData);
                        break;
                    
                    case 'eliminar':
                        $this->eliminar($requestData);
                        break;
                    
                    case 'cambiarPassword':
                        $this->cambiarPassword($requestData);
                        break;
                    
                    case 'checkDocumento':
                        $this->checkDocumento();
                        break;
                    
                    case 'checkEmail':
                        $this->checkEmail();
                        break;
                    
                    case 'estadisticas':
                        $this->estadisticas();
                        break;
                    
                    default:
                        responderJSON(false, 'Acción no válida: ' . htmlspecialchars($action ?? 'ninguna'), null, 400);
                        break;
                }
            } catch (Exception $e) {
                logError($e->getMessage(), 'manejarPeticion');
                responderJSON(false, 'Error del servidor', null, 500);
            }
        }
        
        private function crear() {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                    responderJSON(false, 'Método no permitido', null, 405);
                }
                
                // Leer datos directamente de $_POST y $_FILES
                $datos = $_POST;
                
                // Validar datos básicos
                if (empty($datos['numDocumento']) || empty($datos['correo'])) {
                    responderJSON(false, 'Documento y correo son requeridos', null, 400);
                }
                
                // Asegurarse de que el rol sea válido antes de la validación completa
                if (!in_array($datos['roles'] ?? '', ['Colaborador', 'Usuario'])) {
                    responderJSON(false, 'El rol seleccionado no es válido.', null, 400);
                }

                $errores = $this->colaboradorModel->validarDatos($datos);
                if (!empty($errores)) {
                    responderJSON(false, implode(', ', $errores), null, 400);
                }
                
                // Manejar archivo de foto desde $_FILES
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                    $datos['foto'] = $_FILES['foto'];
                }
                
                // Añadir el id_hotel del administrador a los datos que se enviarán al modelo
                // Asegurarse de que la sesión esté iniciada.
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                if (isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id'])) {
                    $datos['id_hotel_admin'] = intval($_SESSION['hotel_id']);
                } else {
                    responderJSON(false, 'Error: No se pudo identificar el hotel del administrador. Por favor, inicie sesión de nuevo.', null, 403);
                }
                
                $resultado = $this->colaboradorModel->crear($datos);
                
                if ($resultado['success']) {
                    responderJSON(true, $resultado['message'], null, 201);
                } else {
                    responderJSON(false, $resultado['message'], null, 400);
                }
                
            } catch (Exception $e) {
                logError($e->getMessage(), 'crear');
                responderJSON(false, 'Error al crear colaborador', null, 500);
            }
        }
        
        private function listar() {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                $filtros = [
                    'busqueda' => $_GET['busqueda'] ?? '',
                    'rol' => $_GET['rol'] ?? 'all',
                    'tipoDocumento' => $_GET['tipoDocumento'] ?? 'all',
                    'sexo' => $_GET['sexo'] ?? 'all'
                ];

                // Asegurarse de que siempre se filtre por el hotel del administrador
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }

                if (isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id'])) {
                    $filtros['id_hotel_admin'] = $_SESSION['hotel_id'];
                } else {
                    // Si no hay hotel en sesión, no mostrar resultados (o manejar como prefieras)
                    responderJSON(true, 'No hay un hotel seleccionado para mostrar colaboradores.', []);
                    return;
                }
                
                $resultado = $this->colaboradorModel->listar($filtros);
                
                if ($resultado['success']) {
                    responderJSON(true, 'Colaboradores obtenidos', $resultado['data']);
                } else {
                    responderJSON(false, $resultado['message'], null, 500);
                }
                
            } catch (Exception $e) {
                logError($e->getMessage(), 'listar');
                responderJSON(false, 'Error al listar colaboradores', null, 500);
            }
        }
        
        private function obtener() {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                $documento = $_GET['documento'] ?? $_POST['documento'] ?? '';
                
                if (empty($documento)) {
                    responderJSON(false, 'Documento requerido', null, 400);
                }
                
                $resultado = $this->colaboradorModel->obtenerPorDocumento($documento);
                
                if ($resultado['success']) {
                    responderJSON(true, 'Colaborador obtenido', $resultado['data']);
                } else {
                    responderJSON(false, $resultado['message'], null, 404);
                }
                
            } catch (Exception $e) {
                logError($e->getMessage(), 'obtener');
                responderJSON(false, 'Error al obtener colaborador', null, 500);
            }
        }
        
        private function checkDocumento() {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                $documento = $_REQUEST['numDocumento'] ?? '';
                
                if (empty($documento)) {
                    responderJSON(false, 'Documento requerido', null, 400);
                }
                
                $existe = $this->colaboradorModel->existeDocumento($documento);
                responderJSON(true, 'Verificación completada', ['exists' => $existe]);
                
            } catch (Exception $e) {
                logError("Excepción en checkDocumento: " . $e->getMessage(), 'doc: ' . ($documento ?? 'null'));
                responderJSON(false, 'Error del sistema al verificar documento. Detalles: ' . $e->getMessage(), null, 500);
            }
        }
        
        private function checkEmail() {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                $correo = $_REQUEST['correo'] ?? '';
                
                if (empty($correo)) {
                    responderJSON(false, 'Correo requerido', null, 400);
                }
                
                if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                    responderJSON(false, 'Formato de correo inválido', null, 400);
                }
                
                $existe = $this->colaboradorModel->existeCorreo($correo);
                responderJSON(true, 'Verificación completada', ['exists' => $existe]);
                
            } catch (Exception $e) {
                logError("Excepción en checkEmail: " . $e->getMessage(), 'email: ' . ($correo ?? 'null'));
                responderJSON(false, 'Error del sistema al verificar correo. Detalles: ' . $e->getMessage(), null, 500);
            }
        }
        
        private function actualizar($datos) {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$datos) {
                    responderJSON(false, 'Método no permitido', null, 405);
                }
                
                $documentoOriginal = $datos['documentoOriginal'] ?? '';
                unset($datos['documentoOriginal'], $datos['action']); // Limpiamos datos de control
                
                if (empty($documentoOriginal)) {
                    responderJSON(false, 'Documento original requerido', null, 400);
                }
                
                $errores = $this->colaboradorModel->validarDatos($datos, true);
                if (!empty($errores)) {
                    responderJSON(false, implode(', ', $errores), null, 400);
                }
                
                $resultado = $this->colaboradorModel->actualizar($documentoOriginal, $datos);
                
                if ($resultado['success']) {
                    responderJSON(true, $resultado['message']);
                } else {
                    responderJSON(false, $resultado['message'], null, 400);
                }
                
            } catch (Exception $e) {
                logError($e->getMessage(), 'actualizar');
                responderJSON(false, 'Error al actualizar colaborador', null, 500);
            }
        }
        
        private function eliminar($datos) {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($datos)) {
                    responderJSON(false, 'Método no permitido', null, 405);
                }
                
                $documento = $datos['documento'] ?? '';

                if (empty($documento)) {
                    responderJSON(false, 'Documento requerido', null, 400);
                }
                
                if (!$this->colaboradorModel->puedeEliminar($documento)) {
                    responderJSON(false, 'No se puede eliminar este colaborador', null, 400);
                }
                
                $resultado = $this->colaboradorModel->eliminar($documento);
                
                if ($resultado['success']) {
                    responderJSON(true, $resultado['message']);
                } else {
                    responderJSON(false, $resultado['message'], null, 400);
                }
                
            } catch (Exception $e) {
                logError($e->getMessage(), 'eliminar');
                responderJSON(false, 'Error al eliminar colaborador', null, 500);
            }
        }
        
        private function cambiarPassword($datos) {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                $documento = $datos['documento'] ?? '';
                $nuevaPassword = $datos['nuevaPassword'] ?? '';
                $solicitarCambio = isset($datos['solicitarCambio']) ? $datos['solicitarCambio'] : false;
                
                if (empty($documento) || empty($nuevaPassword)) {
                    responderJSON(false, 'Documento y nueva contraseña son requeridos', null, 400);
                }
                
                if (strlen($nuevaPassword) < 6) {
                    responderJSON(false, 'La contraseña debe tener al menos 6 caracteres', null, 400);
                }
                
                $resultado = $this->colaboradorModel->cambiarPassword($documento, $nuevaPassword, $solicitarCambio);
                
                if ($resultado['success']) {
                    responderJSON(true, $resultado['message']);
                } else {
                    responderJSON(false, $resultado['message'], null, 400);
                }
                
            } catch (Exception $e) {
                logError($e->getMessage(), 'cambiarPassword');
                responderJSON(false, 'Error al cambiar contraseña', null, 500);
            }
        }
        
        private function estadisticas() {
            try {
                if (!$this->esAdmin()) {
                    responderJSON(false, 'Acceso denegado. Permisos insuficientes.', null, 403);
                }

                // Obtener el hotel de la sesión para filtrar las estadísticas
                $id_hotel_admin = $_SESSION['hotel_id'] ?? null;
                $resultado = $this->colaboradorModel->obtenerEstadisticas($id_hotel_admin);
                
                if ($resultado['success']) {
                    responderJSON(true, 'Estadísticas obtenidas', $resultado['data']);
                } else {
                    responderJSON(false, $resultado['message'], null, 500);
                }
                
            } catch (Exception $e) {
                logError($e->getMessage(), 'estadisticas');
                responderJSON(false, 'Error al obtener estadísticas', null, 500);
            }
        }
        
        private function obtenerDatosFormulario() {
            return [
                'numDocumento' => $this->limpiarInput($_POST['numDocumento'] ?? ''),
                'tipoDocumento' => $this->limpiarInput($_POST['tipoDocumento'] ?? ''),
                'nombres' => $this->limpiarInput($_POST['nombres'] ?? ''),
                'apellidos' => $this->limpiarInput($_POST['apellidos'] ?? ''),
                'numTelefono' => $this->limpiarInput($_POST['numTelefono'] ?? ''),
                'correo' => $this->limpiarInput($_POST['correo'] ?? ''),
                'sexo' => $this->limpiarInput($_POST['sexo'] ?? ''),
                'fechaNacimiento' => $this->limpiarInput($_POST['fechaNacimiento'] ?? ''),
                'password' => $_POST['password'] ?? '',
                'roles' => $this->limpiarInput($_POST['roles'] ?? ''),
                'solicitarContraseña' => isset($_POST['solicitarContraseña'])
            ];
        }
        
        private function limpiarInput($input) {
            return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
        }
    }
    
    // Ejecutar el controlador solo si se accede directamente
    if (basename($_SERVER['PHP_SELF']) == 'MisColaboradoresControllers.php') {
        $controller = new ColaboradorController();
        $controller->manejarPeticion();
    }
    
} catch (Exception $e) {
    logError("Error crítico: " . $e->getMessage(), 'main');
    responderJSON(false, 'Error crítico del servidor', null, 500);
}
?>