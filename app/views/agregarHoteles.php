<?php
// config.php
class Database {
    private $host = 'localhost';
    private $db_name = 'lodgehub';
    private $username = 'root';
    private $password = '';
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8", 
                                $this->username, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch(PDOException $exception) {
            error_log("Error de conexión a la base de datos: " . $exception->getMessage());
            return null;
        }
        return $this->conn;
    }
}

// hotel_manager.php
class HotelManager {
    private $conn;
    private $table_name = "tp_hotel";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function validateHotelData($data) {
        $errors = [];

        // Validación NIT
        if (empty($data['nit'])) {
            $errors['nit'] = 'El NIT es requerido';
        } elseif (!preg_match('/^[0-9\-]+$/', $data['nit'])) {
            $errors['nit'] = 'El NIT debe contener solo números y guiones';
        } elseif (strlen($data['nit']) > 20) {
            $errors['nit'] = 'El NIT no puede exceder 20 caracteres';
        } elseif ($this->nitExists($data['nit'], $data['id'] ?? null)) {
            $errors['nit'] = 'Este NIT ya está registrado';
        }

        // Validación nombre
        if (empty($data['nombre'])) {
            $errors['nombre'] = 'El nombre es requerido';
        } elseif (strlen($data['nombre']) > 100) {
            $errors['nombre'] = 'El nombre no puede exceder 100 caracteres';
        }

        // Validación dirección
        if (!empty($data['direccion']) && strlen($data['direccion']) > 200) {
            $errors['direccion'] = 'La dirección no puede exceder 200 caracteres';
        }

        // Validación descripción
        if (!empty($data['descripcion']) && strlen($data['descripcion']) > 1000) {
            $errors['descripcion'] = 'La descripción no puede exceder 1000 caracteres';
        }

        // Validación documento administrador
        if (empty($data['numDocumento'])) {
            $errors['numDocumento'] = 'El número de documento del administrador es requerido';
        } elseif (!$this->adminExists($data['numDocumento'])) {
            $errors['numDocumento'] = 'El administrador no existe en el sistema';
        }

        // Validación teléfono
        if (!empty($data['telefono'])) {
            if (!preg_match('/^[\+]?[0-9\-\s\(\)]+$/', $data['telefono'])) {
                $errors['telefono'] = 'El teléfono contiene caracteres no válidos';
            }
            if (strlen($data['telefono']) > 20) {
                $errors['telefono'] = 'El teléfono no puede exceder 20 caracteres';
            }
        }

        // Validación correo
        if (!empty($data['correo'])) {
            if (!filter_var($data['correo'], FILTER_VALIDATE_EMAIL)) {
                $errors['correo'] = 'El formato del correo no es válido';
            }
            if (strlen($data['correo']) > 100) {
                $errors['correo'] = 'El correo no puede exceder 100 caracteres';
            }
        }

        // Validación foto URL
        if (!empty($data['foto'])) {
            if (!filter_var($data['foto'], FILTER_VALIDATE_URL)) {
                $errors['foto'] = 'La URL de la foto no es válida';
            }
        }

        return $errors;
    }

    private function nitExists($nit, $excludeId = null) {
        try {
            $sql = "SELECT id FROM " . $this->table_name . " WHERE nit = ?";
            if ($excludeId) {
                $sql .= " AND id != ?";
            }
            
            $stmt = $this->conn->prepare($sql);
            if ($excludeId) {
                $stmt->execute([$nit, $excludeId]);
            } else {
                $stmt->execute([$nit]);
            }
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en nitExists: " . $e->getMessage());
            return false;
        }
    }

    private function adminExists($numDocumento) {
        try {
            $sql = "SELECT numDocumento FROM tp_usuarios WHERE numDocumento = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$numDocumento]);
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            error_log("Error en adminExists: " . $e->getMessage());
            // Si hay error, asumimos que el admin existe para no bloquear innecesariamente
            return true;
        }
    }

    public function createHotel($data) {
        $errors = $this->validateHotelData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $sql = "INSERT INTO " . $this->table_name . " 
                (nit, nombre, direccion, descripcion, numDocumento, telefono, correo, foto) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                $data['nit'],
                $data['nombre'],
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['descripcion']) ? $data['descripcion'] : null,
                $data['numDocumento'],
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null,
                !empty($data['foto']) ? $data['foto'] : null
            ]);

            if ($result) {
                return [
                    'success' => true, 
                    'message' => 'Hotel creado exitosamente',
                    'id' => $this->conn->lastInsertId()
                ];
            }
        } catch (PDOException $e) {
            error_log("Error en createHotel: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }

        return ['success' => false, 'error' => 'Error desconocido al crear hotel'];
    }

    public function getAllHotels() {
        try {
            $sql = "SELECT h.*, u.nombre as admin_nombre 
                    FROM " . $this->table_name . " h 
                    LEFT JOIN tp_usuarios u ON h.numDocumento = u.numDocumento 
                    ORDER BY h.id DESC";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error en getAllHotels: " . $e->getMessage());
            return [];
        }
    }

    public function getHotelById($id) {
        try {
            $sql = "SELECT h.*, u.nombre as admin_nombre 
                    FROM " . $this->table_name . " h 
                    LEFT JOIN tp_usuarios u ON h.numDocumento = u.numDocumento 
                    WHERE h.id = ?";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error en getHotelById: " . $e->getMessage());
            return false;
        }
    }

    public function updateHotel($id, $data) {
        $data['id'] = $id;
        $errors = $this->validateHotelData($data);
        if (!empty($errors)) {
            return ['success' => false, 'errors' => $errors];
        }

        $sql = "UPDATE " . $this->table_name . " 
                SET nit = ?, nombre = ?, direccion = ?, descripcion = ?, numDocumento = ?, 
                    telefono = ?, correo = ?, foto = ? 
                WHERE id = ?";

        try {
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([
                $data['nit'],
                $data['nombre'],
                !empty($data['direccion']) ? $data['direccion'] : null,
                !empty($data['descripcion']) ? $data['descripcion'] : null,
                $data['numDocumento'],
                !empty($data['telefono']) ? $data['telefono'] : null,
                !empty($data['correo']) ? $data['correo'] : null,
                !empty($data['foto']) ? $data['foto'] : null,
                $id
            ]);

            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Hotel actualizado exitosamente'];
            } elseif ($result && $stmt->rowCount() === 0) {
                return ['success' => false, 'error' => 'No se realizaron cambios o el hotel no existe'];
            }
        } catch (PDOException $e) {
            error_log("Error en updateHotel: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }

        return ['success' => false, 'error' => 'Error desconocido al actualizar hotel'];
    }

    public function deleteHotel($id) {
        try {
            $sql = "DELETE FROM " . $this->table_name . " WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $result = $stmt->execute([$id]);
            
            if ($result && $stmt->rowCount() > 0) {
                return ['success' => true, 'message' => 'Hotel eliminado exitosamente'];
            } else {
                return ['success' => false, 'error' => 'Hotel no encontrado'];
            }
        } catch (PDOException $e) {
            error_log("Error en deleteHotel: " . $e->getMessage());
            return ['success' => false, 'error' => 'Error en la base de datos: ' . $e->getMessage()];
        }
    }
}

// Manejar respuestas de error
function sendErrorResponse($message, $code = 400) {
    http_response_code($code);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

// Manejar respuestas exitosas
function sendSuccessResponse($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

// Verificar si es una llamada AJAX o API
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
          strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$isApi = isset($_GET['action']) || isset($_POST['action']);

if ($isApi || $isAjax) {
    header('Content-Type: application/json; charset=utf-8');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');

    // Manejar OPTIONS request para CORS
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
        exit(0);
    }

    $database = new Database();
    $db = $database->getConnection();
    
    if (!$db) {
        sendErrorResponse('Error de conexión a la base de datos', 500);
    }
    
    $hotelManager = new HotelManager($db);

    // Obtener la acción
    $action = $_GET['action'] ?? $_POST['action'] ?? '';

    switch ($action) {
        case 'create':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                sendErrorResponse('Método no permitido. Use POST.', 405);
            }
            
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            if (empty($data)) {
                sendErrorResponse('No se recibieron datos');
            }
            
            $result = $hotelManager->createHotel($data);
            sendSuccessResponse($result);
            break;

        case 'read':
            $id = $_GET['id'] ?? null;
            
            if ($id) {
                $hotel = $hotelManager->getHotelById($id);
                if ($hotel) {
                    sendSuccessResponse(['success' => true, 'data' => $hotel]);
                } else {
                    sendErrorResponse('Hotel no encontrado', 404);
                }
            } else {
                $hotels = $hotelManager->getAllHotels();
                sendSuccessResponse(['success' => true, 'data' => $hotels]);
            }
            break;

        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                sendErrorResponse('Método no permitido. Use POST.', 405);
            }
            
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            if (!$data) {
                $data = $_POST;
            }
            
            $id = $data['id'] ?? $_GET['id'] ?? null;
            
            if (!$id) {
                sendErrorResponse('ID del hotel no proporcionado');
            }
            
            $result = $hotelManager->updateHotel($id, $data);
            sendSuccessResponse($result);
            break;

        case 'delete':
            $input = file_get_contents('php://input');
            $data = json_decode($input, true);
            
            $id = $data['id'] ?? $_GET['id'] ?? $_POST['id'] ?? null;
            
            if (!$id) {
                sendErrorResponse('ID del hotel no proporcionado');
            }
            
            $result = $hotelManager->deleteHotel($id);
            sendSuccessResponse($result);
            break;

        default:
            sendErrorResponse('Acción no válida: ' . $action);
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Hoteles</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(to right, #7da2db)!important;
            padding: 20px;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin-bottom: 2rem;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .form-container h2 {
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3b82f6;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #333;
        }

        .required::after {
            content: " *";
            color: #e74c3c;
        }

        input, select, textarea {
            padding: 0.75rem;
            border: 2px solid #e1e1e1;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        textarea {
            resize: vertical;
            min-height: 120px;
        }

        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        input.error, select.error, textarea.error {
            border-color: #e74c3c;
        }

        .char-counter {
            font-size: 0.75rem;
            color: #666;
            margin-top: 0.25rem;
            text-align: right;
        }

        .char-counter.warning {
            color: #f39c12;
        }

        .char-counter.danger {
            color: #e74c3c;
        }

        .error {
            color: #e74c3c;
            font-size: 0.875rem;
            margin-top: 0.25rem;
            min-height: 1.2rem;
        }

        .success {
            color: #27ae60;
            font-size: 0.875rem;
            margin-top: 1rem;
            padding: 0.75rem;
            background-color: #d5f4e6;
            border-radius: 5px;
            border: 1px solid #27ae60;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            text-align: center;
        }

        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .btn-primary {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
        }

        .btn-primary:hover:not(:disabled) {
            background-color: #3b82f6;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .btn-danger {
            background-color: #e74c3c;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c0392b;
        }

        .btn-edit {
            background-color: #f39c12;
            color: white;
            margin-right: 0.5rem;
        }

        .btn-edit:hover {
            background-color: #e67e22;
        }

        .hotels-list {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .hotels-list h2 {
            color: #333;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #3b82f6;
        }

        .hotel-card {
            border: 1px solid #e1e1e1;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
            background: white;
        }

        .hotel-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
            border-color: #3b82f6;
        }

        .hotel-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid #eee;
        }

        .hotel-name {
            font-size: 1.25rem;
            font-weight: 600;
            color: #333;
        }

        .hotel-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
        }

        .info-item {
            display: flex;
            flex-direction: column;
        }

        .info-label {
            font-weight: 600;
            color: #666;
            font-size: 0.875rem;
            margin-bottom: 0.25rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .info-value {
            color: #333;
            font-size: 1rem;
        }

        .description-preview {
            max-height: 80px;
            overflow: hidden;
            position: relative;
            line-height: 1.4;
        }

        .description-preview.expanded {
            max-height: none;
        }

        .description-toggle {
            color: #667eea;
            cursor: pointer;
            font-size: 0.875rem;
            margin-top: 0.5rem;
            text-decoration: underline;
        }

        .description-toggle:hover {
            color: #5a6fd8;
        }

        .loading {
            text-align: center;
            padding: 3rem;
            color: #666;
            font-size: 1.1rem;
        }

        .loading::after {
            content: '';
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3b82f6;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .no-hotels {
            text-align: center;
            padding: 3rem;
            color: #666;
            font-style: italic;
            font-size: 1.1rem;
        }

        .alert {
            padding: 1rem;
            margin: 1rem 0;
            border-radius: 5px;
            font-weight: 500;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-success {
            background-color: #d1edff;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid #eee;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .hotel-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .hotel-info {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
        }

        @media (max-width: 480px) {
            .header h1 {
                font-size: 1.8rem;
            }
            
            .form-container, .hotels-list {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Sistema de Gestión de Hoteles</h1>
            <p>Administra y valida información de hoteles en tiempo real</p>
        </div>

        <div class="form-container">
            <h2>Agregar/Editar Hotel</h2>
            <form id="hotelForm" novalidate>
                <input type="hidden" id="hotelId" name="id">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="nit" class="required">NIT</label>
                        <input type="text" id="nit" name="nit" required maxlength="20" 
                               placeholder="Ej: 901234567-1">
                        <div class="error" id="nit-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="nombre" class="required">Nombre del Hotel</label>
                        <input type="text" id="nombre" name="nombre" required maxlength="100"
                               placeholder="Nombre completo del hotel">
                        <div class="char-counter" id="nombre-counter">0/100</div>
                        <div class="error" id="nombre-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="numDocumento" class="required">Documento Administrador</label>
                        <input type="text" id="numDocumento" name="numDocumento" required
                               placeholder="Número de documento del administrador">
                        <div class="error" id="numDocumento-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="telefono">Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" maxlength="20"
                               placeholder="Ej: +57 300 123 4567">
                        <div class="error" id="telefono-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo Electrónico</label>
                        <input type="email" id="correo" name="correo" maxlength="100"
                               placeholder="correo@ejemplo.com">
                        <div class="error" id="correo-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="foto">URL Foto</label>
                        <input type="url" id="foto" name="foto"
                               placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="error" id="foto-error"></div>
                    </div>
                </div>

                <div class="form-group full-width">
                    <label for="direccion">Dirección</label>
                    <textarea id="direccion" name="direccion" rows="3" maxlength="200"
                              placeholder="Dirección completa del hotel"></textarea>
                    <div class="char-counter" id="direccion-counter">0/200</div>
                    <div class="error" id="direccion-error"></div>
                </div>

                <div class="form-group full-width">
                    <label for="descripcion">Descripción del Hotel</label>
                    <textarea id="descripcion" name="descripcion" rows="5" maxlength="1000"
                              placeholder="Describe las características, servicios y amenidades del hotel..."></textarea>
                    <div class="char-counter" id="descripcion-counter">0/1000</div>
                    <div class="error" id="descripcion-error"></div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary" id="submitBtn">
                        <span id="submitText">Guardar Hotel</span>
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="cancelEdit()" 
                            id="cancelBtn" style="display:none;">
                        Cancelar
                    </button>
                </div>
                
                <div id="form-messages"></div>
            </form>
        </div>

        <div class="hotels-list">
            <h2>Lista de Hoteles</h2>
            <div id="hotelsList">
                <div class="loading">Cargando hoteles</div>
            </div>
        </div>
    </div>

    <script>
        let editingHotelId = null;
        let isSubmitting = false;

        document.addEventListener('DOMContentLoaded', function() {
            loadHotels();
            
            const form = document.getElementById('hotelForm');
            form.addEventListener('submit', handleFormSubmit);
            
            // Auto-refresh cada 30 segundos
            setInterval(loadHotels, 30000);
            
            // Validación en tiempo real
            setupRealTimeValidation();
            
            // Configurar contadores de caracteres
            setupCharCounters();
        });

        function setupCharCounters() {
            const textFields = [
                { id: 'nombre', max: 100 },
                { id: 'direccion', max: 200 },
                { id: 'descripcion', max: 1000 }
            ];

            textFields.forEach(field => {
                const input = document.getElementById(field.id);
                const counter = document.getElementById(field.id + '-counter');
                
                if (input && counter) {
                    input.addEventListener('input', function() {
                        updateCharCounter(input, counter, field.max);
                    });
                    
                    // Inicializar contador
                    updateCharCounter(input, counter, field.max);
                }
            });
        }

        function updateCharCounter(input, counter, maxLength) {
            const currentLength = input.value.length;
            counter.textContent = `${currentLength}/${maxLength}`;
            
            // Cambiar color según el porcentaje usado
            const percentage = (currentLength / maxLength) * 100;
            counter.classList.remove('warning', 'danger');
            
            if (percentage >= 90) {
                counter.classList.add('danger');
            } else if (percentage >= 75) {
                counter.classList.add('warning');
            }
        }

        function setupRealTimeValidation() {
            const inputs = document.querySelectorAll('input, textarea');
            inputs.forEach(input => {
                input.addEventListener('blur', function() {
                    validateField(this);
                });
                
                input.addEventListener('input', function() {
                    clearFieldError(this);
                });
            });
        }

        function validateField(field) {
            const value = field.value.trim();
            const name = field.name;
            let error = '';

            switch (name) {
                case 'nit':
                    if (!value) {
                        error = 'El NIT es requerido';
                    } else if (!/^[0-9\-]+$/.test(value)) {
                        error = 'El NIT debe contener solo números y guiones';
                    } else if (value.length > 20) {
                        error = 'El NIT no puede exceder 20 caracteres';
                    }
                    break;
                    
                case 'nombre':
                    if (!value) {
                        error = 'El nombre es requerido';
                    } else if (value.length > 100) {
                        error = 'El nombre no puede exceder 100 caracteres';
                    }
                    break;
                    
                case 'numDocumento':
                    if (!value) {
                        error = 'El número de documento es requerido';
                    }
                    break;
                    
                case 'telefono':
                    if (value && !/^[\+]?[0-9\-\s\(\)]+$/.test(value)) {
                        error = 'El teléfono contiene caracteres no válidos';
                    } else if (value.length > 20) {
                        error = 'El teléfono no puede exceder 20 caracteres';
                    }
                    break;
                    
                case 'correo':
                    if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        error = 'El formato del correo no es válido';
                    } else if (value.length > 100) {
                        error = 'El correo no puede exceder 100 caracteres';
                    }
                    break;
                    
                case 'direccion':
                    if (value.length > 200) {
                        error = 'La dirección no puede exceder 200 caracteres';
                    }
                    break;
                    
                case 'descripcion':
                    if (value.length > 1000) {
                        error = 'La descripción no puede exceder 1000 caracteres';
                    }
                    break;
                    
                case 'foto':
                    if (value && !isValidUrl(value)) {
                        error = 'La URL de la foto no es válida';
                    }
                    break;
            }

            if (error) {
                showFieldError(name, error);
                field.classList.add('error');
            } else {
                clearFieldError(field);
                field.classList.remove('error');
            }

            return !error;
        }

        function isValidUrl(string) {
            try {
                new URL(string);
                return true;
            } catch (_) {
                return false;
            }
        }

        async function handleFormSubmit(e) {
            e.preventDefault();
            
            if (isSubmitting) {
                return;
            }
            
            clearAllErrors();
            
            // Validar todos los campos
            const form = e.target;
            const inputs = form.querySelectorAll('input, textarea');
            let isValid = true;
            
            inputs.forEach(input => {
                if (!validateField(input)) {
                    isValid = false;
                }
            });
            
            if (!isValid) {
                showMessage('Por favor, corrije los errores en el formulario', 'error');
                return;
            }
            
            isSubmitting = true;
            setSubmitButton(true);
            
            const formData = new FormData(form);
            const data = Object.fromEntries(formData.entries());
            
            // Limpiar campos vacíos para enviar como null
            Object.keys(data).forEach(key => {
                if (data[key] === '') {
                    data[key] = null;
                }
            });
            
            const isEditing = editingHotelId !== null;
            const action = isEditing ? 'update' : 'create';
            const url = window.location.href.split('?')[0] + '?action=' + action;
            
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify(data)
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Error parsing JSON:', parseError);
                    console.error('Response text:', responseText);
                    throw new Error('Respuesta inválida del servidor');
                }
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    resetForm();
                    await loadHotels();
                } else if (result.errors) {
                    showFormErrors(result.errors);
                    showMessage('Por favor, corrije los errores indicados', 'error');
                } else {
                    throw new Error(result.error || 'Error desconocido');
                }
                
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error de conexión: ' + error.message, 'error');
            } finally {
                isSubmitting = false;
                setSubmitButton(false);
            }
        }

        async function loadHotels() {
            try {
                const url = window.location.href.split('?')[0] + '?action=read';
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const responseText = await response.text();
                
                let result;
                try {
                    result = JSON.parse(responseText);
                } catch (parseError) {
                    console.error('Error parsing JSON en loadHotels:', parseError);
                    throw new Error('Respuesta inválida del servidor');
                }
                
                const hotelsList = document.getElementById('hotelsList');
                
                if (result.success && result.data && result.data.length > 0) {
                    hotelsList.innerHTML = result.data.map(hotel => createHotelCard(hotel)).join('');
                } else if (result.success) {
                    hotelsList.innerHTML = '<div class="no-hotels">No hay hoteles registrados</div>';
                } else {
                    throw new Error(result.error || 'Error desconocido');
                }
                
            } catch (error) {
                console.error('Error en loadHotels:', error);
                document.getElementById('hotelsList').innerHTML = 
                    '<div class="alert alert-error">Error al cargar hoteles: ' + error.message + '</div>';
            }
        }

        function createHotelCard(hotel) {
            const foto = hotel.foto ? `<img src="${escapeHtml(hotel.foto)}" alt="Foto del hotel" style="max-width: 100px; height: auto; border-radius: 5px; margin-bottom: 1rem;" onerror="this.style.display='none'">` : '';
            
            // Preparar la descripción con función de expandir/contraer
            let descripcionHtml = '';
            if (hotel.descripcion) {
                const descripcion = escapeHtml(hotel.descripcion);
                const isLong = descripcion.length > 150;
                const preview = isLong ? descripcion.substring(0, 150) + '...' : descripcion;
                
                descripcionHtml = `
                    <div class="info-item" style="grid-column: 1 / -1;">
                        <div class="info-label">Descripción</div>
                        <div class="info-value">
                            <div class="description-preview" id="desc-${hotel.id}">
                                ${isLong ? preview : descripcion}
                            </div>
                            ${isLong ? `
                                <div class="description-toggle" onclick="toggleDescription(${hotel.id})">
                                    <span id="toggle-text-${hotel.id}">Ver más</span>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
            }
            
            return `
                <div class="hotel-card">
                    <div class="hotel-header">
                        <div>
                            <div class="hotel-name">${escapeHtml(hotel.nombre)}</div>
                            <small style="color: #666;">NIT: ${escapeHtml(hotel.nit)}</small>
                        </div>
                        <div>
                            <button class="btn btn-edit" onclick="editHotel(${hotel.id})" title="Editar hotel">
                                Editar
                            </button>
                            <button class="btn btn-danger" onclick="deleteHotel(${hotel.id})" title="Eliminar hotel">
                                Eliminar
                            </button>
                        </div>
                    </div>
                    ${foto}
                    <div class="hotel-info">
                        <div class="info-item">
                            <div class="info-label">Administrador</div>
                            <div class="info-value">${escapeHtml(hotel.admin_nombre || 'No encontrado')}</div>
                            <small style="color: #666;">Doc: ${escapeHtml(hotel.numDocumento)}</small>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Teléfono</div>
                            <div class="info-value">${escapeHtml(hotel.telefono || 'No registrado')}</div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">Correo</div>
                            <div class="info-value">${hotel.correo ? `<a href="mailto:${escapeHtml(hotel.correo)}" style="color: #667eea;">${escapeHtml(hotel.correo)}</a>` : 'No registrado'}</div>
                        </div>
                        <div class="info-item" style="grid-column: 1 / -1;">
                            <div class="info-label">Dirección</div>
                            <div class="info-value">${escapeHtml(hotel.direccion || 'No registrada')}</div>
                        </div>
                        ${descripcionHtml}
                    </div>
                </div>
            `;
        }

        function toggleDescription(hotelId) {
            const descElement = document.getElementById(`desc-${hotelId}`);
            const toggleElement = document.getElementById(`toggle-text-${hotelId}`);
            
            if (descElement.classList.contains('expanded')) {
                descElement.classList.remove('expanded');
                toggleElement.textContent = 'Ver más';
                // Restaurar texto truncado
                const fullText = descElement.getAttribute('data-full-text');
                const preview = fullText.substring(0, 150) + '...';
                descElement.innerHTML = preview;
            } else {
                descElement.classList.add('expanded');
                toggleElement.textContent = 'Ver menos';
                // Mostrar texto completo
                if (!descElement.getAttribute('data-full-text')) {
                    // Obtener el texto completo del hotel
                    loadHotelDescription(hotelId, descElement);
                } else {
                    descElement.innerHTML = descElement.getAttribute('data-full-text');
                }
            }
        }

        async function loadHotelDescription(hotelId, descElement) {
            try {
                const url = window.location.href.split('?')[0] + '?action=read&id=' + hotelId;
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const result = await response.json();
                if (result.success && result.data && result.data.descripcion) {
                    const fullText = escapeHtml(result.data.descripcion);
                    descElement.setAttribute('data-full-text', fullText);
                    descElement.innerHTML = fullText;
                }
            } catch (error) {
                console.error('Error loading description:', error);
            }
        }

        async function editHotel(id) {
            try {
                const url = window.location.href.split('?')[0] + '?action=read&id=' + id;
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success && result.data) {
                    fillForm(result.data);
                    editingHotelId = id;
                    document.getElementById('submitText').textContent = 'Actualizar Hotel';
                    document.getElementById('cancelBtn').style.display = 'inline-block';
                    document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
                    showMessage('Editando hotel: ' + result.data.nombre, 'success');
                } else {
                    throw new Error(result.error || 'Hotel no encontrado');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error al cargar datos del hotel: ' + error.message, 'error');
            }
        }

        async function deleteHotel(id) {
            if (!confirm('¿Estás seguro de que quieres eliminar este hotel?\n\nEsta acción no se puede deshacer.')) {
                return;
            }
            
            try {
                const url = window.location.href.split('?')[0] + '?action=delete';
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ id: id })
                });
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showMessage(result.message, 'success');
                    await loadHotels();
                } else {
                    throw new Error(result.error || 'Error desconocido');
                }
            } catch (error) {
                console.error('Error:', error);
                showMessage('Error al eliminar hotel: ' + error.message, 'error');
            }
        }

        function fillForm(hotel) {
            document.getElementById('hotelId').value = hotel.id || '';
            document.getElementById('nit').value = hotel.nit || '';
            document.getElementById('nombre').value = hotel.nombre || '';
            document.getElementById('direccion').value = hotel.direccion || '';
            document.getElementById('descripcion').value = hotel.descripcion || '';
            document.getElementById('numDocumento').value = hotel.numDocumento || '';
            document.getElementById('telefono').value = hotel.telefono || '';
            document.getElementById('correo').value = hotel.correo || '';
            document.getElementById('foto').value = hotel.foto || '';
            
            // Actualizar contadores de caracteres
            updateCharCounter(document.getElementById('nombre'), document.getElementById('nombre-counter'), 100);
            updateCharCounter(document.getElementById('direccion'), document.getElementById('direccion-counter'), 200);
            updateCharCounter(document.getElementById('descripcion'), document.getElementById('descripcion-counter'), 1000);
        }

        function cancelEdit() {
            if (editingHotelId && confirm('¿Estás seguro de que quieres cancelar la edición?')) {
                resetForm();
            } else if (!editingHotelId) {
                resetForm();
            }
        }

        function resetForm() {
            document.getElementById('hotelForm').reset();
            editingHotelId = null;
            document.getElementById('submitText').textContent = 'Guardar Hotel';
            document.getElementById('cancelBtn').style.display = 'none';
            clearAllErrors();
            clearMessages();
            
            // Remover clases de error
            document.querySelectorAll('input, textarea').forEach(input => {
                input.classList.remove('error');
            });
            
            // Reinicializar contadores
            setupCharCounters();
        }

        function setSubmitButton(loading) {
            const btn = document.getElementById('submitBtn');
            const text = document.getElementById('submitText');
            
            if (loading) {
                btn.disabled = true;
                text.textContent = 'Procesando...';
            } else {
                btn.disabled = false;
                text.textContent = editingHotelId ? 'Actualizar Hotel' : 'Guardar Hotel';
            }
        }

        function clearAllErrors() {
            document.querySelectorAll('.error').forEach(el => {
                el.textContent = '';
            });
        }

        function clearFieldError(field) {
            const errorEl = document.getElementById(field.name + '-error');
            if (errorEl) {
                errorEl.textContent = '';
            }
        }

        function showFieldError(fieldName, message) {
            const errorEl = document.getElementById(fieldName + '-error');
            if (errorEl) {
                errorEl.textContent = message;
            }
        }

        function showFormErrors(errors) {
            for (const [field, message] of Object.entries(errors)) {
                showFieldError(field, message);
                const input = document.getElementById(field);
                if (input) {
                    input.classList.add('error');
                }
            }
        }

        function showMessage(message, type = 'info') {
            const messagesEl = document.getElementById('form-messages');
            const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
            
            messagesEl.innerHTML = `<div class="alert ${alertClass}">${escapeHtml(message)}</div>`;
            
            // Auto-hide success messages
            if (type === 'success') {
                setTimeout(() => {
                    clearMessages();
                }, 5000);
            }
        }

        function clearMessages() {
            document.getElementById('form-messages').innerHTML = '';
        }

        function escapeHtml(text) {
            if (!text && text !== 0) return '';
            const div = document.createElement('div');
            div.textContent = text.toString();
            return div.innerHTML;
        }

        // Manejo de errores globales
        window.addEventListener('error', function(e) {
            console.error('Error global:', e.error);
        });

        window.addEventListener('unhandledrejection', function(e) {
            console.error('Promise rechazado:', e.reason);
            showMessage('Ocurrió un error inesperado: ' + e.reason, 'error');
        });
    </script>
</body>
</html>