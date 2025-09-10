<?php
/**
 * Controlador para la gestión de habitaciones
 */

require_once '../models/habitacionesModel.php';

class HabitacionController {
    private $habitacion;
    
    public function __construct() {
        $this->habitacion = new Habitacion();
    }
    
    /**
     * Mostrar la vista principal con todas las habitaciones
     */
    public function index() {
        $id_hotel = isset($_GET['hotel']) ? $_GET['hotel'] : null;
        $habitaciones = $this->habitacion->obtenerTodas($id_hotel);
        $hoteles = $this->habitacion->obtenerHoteles();
        
        include 'views/habitaciones/index.php';
    }
    
    /**
     * Mostrar formulario para crear nueva habitación
     */
    public function crear() {
        $hoteles = $this->habitacion->obtenerHoteles();
        $tipos = [];
        
        if (isset($_GET['hotel'])) {
            $tipos = $this->habitacion->obtenerTiposPorHotel($_GET['hotel']);
        }
        
        include 'views/habitaciones/crear.php';
    }
    
    /**
     * Procesar la creación de una nueva habitación
     */
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->validarDatos($_POST);
            
            if ($datos['valid']) {
                $resultado = $this->habitacion->crear($datos['data']);
                
                if ($resultado['success']) {
                    $this->jsonResponse($resultado);
                } else {
                    $this->jsonResponse($resultado);
                }
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Datos inválidos', 'errors' => $datos['errors']]);
            }
        }
    }
    
    /**
     * Mostrar formulario para editar habitación
     */
    public function editar($id) {
        $habitacion = $this->habitacion->obtenerPorId($id);
        
        if (!$habitacion) {
            header("Location: index.php?error=Habitación no encontrada");
            exit();
        }
        
        $hoteles = $this->habitacion->obtenerHoteles();
        $tipos = $this->habitacion->obtenerTiposPorHotel($habitacion['id_hotel']);
        
        include 'views/habitaciones/editar.php';
    }
    
    /**
     * Procesar la actualización de una habitación
     */
    public function update($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = $this->validarDatos($_POST);
            
            if ($datos['valid']) {
                $resultado = $this->habitacion->actualizar($id, $datos['data']);
                $this->jsonResponse($resultado);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Datos inválidos', 'errors' => $datos['errors']]);
            }
        }
    }
    
    /**
     * Eliminar una habitación
     */
    public function eliminar($id) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $resultado = $this->habitacion->eliminar($id);
            $this->jsonResponse($resultado);
        }
    }
    
    /**
     * Obtener tipos de habitación por hotel (AJAX)
     */
    public function obtenerTipos() {
        if (isset($_GET['hotel'])) {
            $tipos = $this->habitacion->obtenerTiposPorHotel($_GET['hotel']);
            $this->jsonResponse(['success' => true, 'data' => $tipos]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Hotel no especificado']);
        }
    }
    
    /**
     * Buscar habitaciones con filtros
     */
    public function buscar() {
        $filtros = [
            'hotel' => $_GET['hotel'] ?? '',
            'tipo' => $_GET['tipo'] ?? '',
            'estado' => $_GET['estado'] ?? '',
            'numero' => $_GET['numero'] ?? ''
        ];
        
        $habitaciones = $this->habitacion->buscar($filtros);
        $this->jsonResponse(['success' => true, 'data' => $habitaciones]);
    }
    
    /**
     * Obtener datos de una habitación específica (AJAX)
     */
    public function obtener($id) {
        $habitacion = $this->habitacion->obtenerPorId($id);
        
        if ($habitacion) {
            $this->jsonResponse(['success' => true, 'data' => $habitacion]);
        } else {
            $this->jsonResponse(['success' => false, 'message' => 'Habitación no encontrada']);
        }
    }
    
    /**
     * Validar datos del formulario
     */
    private function validarDatos($datos) {
        $errors = [];
        $cleanData = [];
        
        // Validar número
        if (empty($datos['numero'])) {
            $errors['numero'] = 'El número de habitación es requerido';
        } else {
            $cleanData['numero'] = trim($datos['numero']);
        }
        
        // Validar costo
        if (empty($datos['costo']) || !is_numeric($datos['costo']) || $datos['costo'] <= 0) {
            $errors['costo'] = 'El costo debe ser un número mayor a 0';
        } else {
            $cleanData['costo'] = floatval($datos['costo']);
        }
        
        // Validar capacidad
        if (empty($datos['capacidad']) || !is_numeric($datos['capacidad']) || $datos['capacidad'] <= 0) {
            $errors['capacidad'] = 'La capacidad debe ser un número mayor a 0';
        } else {
            $cleanData['capacidad'] = intval($datos['capacidad']);
        }
        
        // Validar tipo de habitación
        if (empty($datos['tipoHabitacion'])) {
            $errors['tipoHabitacion'] = 'El tipo de habitación es requerido';
        } else {
            $cleanData['tipoHabitacion'] = intval($datos['tipoHabitacion']);
        }
        
        // Validar hotel
        if (empty($datos['id_hotel'])) {
            $errors['id_hotel'] = 'El hotel es requerido';
        } else {
            $cleanData['id_hotel'] = intval($datos['id_hotel']);
        }
        
        // Validar estado
        $estadosValidos = ['Disponible', 'Reservada', 'Ocupada', 'Mantenimiento'];
        if (empty($datos['estado']) || !in_array($datos['estado'], $estadosValidos)) {
            $errors['estado'] = 'El estado no es válido';
        } else {
            $cleanData['estado'] = $datos['estado'];
        }
        
        // Validar estado mantenimiento
        $estadosMantenimiento = ['Activo', 'Inactivo'];
        $cleanData['estadoMantenimiento'] = in_array($datos['estadoMantenimiento'] ?? 'Activo', $estadosMantenimiento) 
            ? $datos['estadoMantenimiento'] 
            : 'Activo';
        
        // Campos opcionales
        $cleanData['foto'] = $datos['foto'] ?? '';
        $cleanData['descripcion'] = $datos['descripcion'] ?? '';
        $cleanData['descripcionMantenimiento'] = $datos['descripcionMantenimiento'] ?? '';
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'data' => $cleanData
        ];
    }
    
    /**
     * Manejar subida de imagen
     */
    public function subirImagen() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imagen'])) {
            $archivo = $_FILES['imagen'];
            
            // Validar archivo
            $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'];
            $tamanoMaximo = 5 * 1024 * 1024; // 5MB
            
            if (!in_array($archivo['type'], $tiposPermitidos)) {
                $this->jsonResponse(['success' => false, 'message' => 'Tipo de archivo no permitido']);
                return;
            }
            
            if ($archivo['size'] > $tamanoMaximo) {
                $this->jsonResponse(['success' => false, 'message' => 'El archivo es demasiado grande']);
                return;
            }
            
            // Crear directorio si no existe
            $directorioDestino = 'uploads/habitaciones/';
            if (!file_exists($directorioDestino)) {
                mkdir($directorioDestino, 0755, true);
            }
            
            // Generar nombre único
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreArchivo = uniqid('habitacion_') . '.' . $extension;
            $rutaCompleta = $directorioDestino . $nombreArchivo;
            
            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                $this->jsonResponse(['success' => true, 'url' => $rutaCompleta]);
            } else {
                $this->jsonResponse(['success' => false, 'message' => 'Error al subir el archivo']);
            }
        }
    }
    
    /**
     * Enviar respuesta JSON
     */
    private function jsonResponse($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }
}

// Manejo de rutas
if (isset($_GET['action'])) {
    $controller = new HabitacionController();
    $action = $_GET['action'];
    
    switch ($action) {
        case 'index':
            $controller->index();
            break;
            
        case 'crear':
            $controller->crear();
            break;
            
        case 'store':
            $controller->store();
            break;
            
        case 'editar':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->editar($id);
            } else {
                header("Location: ?action=index");
            }
            break;
            
        case 'update':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->update($id);
            }
            break;
            
        case 'eliminar':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->eliminar($id);
            }
            break;
            
        case 'obtener-tipos':
            $controller->obtenerTipos();
            break;
            
        case 'buscar':
            $controller->buscar();
            break;
            
        case 'obtener':
            $id = $_GET['id'] ?? null;
            if ($id) {
                $controller->obtener($id);
            }
            break;
            
        case 'subir-imagen':
            $controller->subirImagen();
            break;
            
        default:
            $controller->index();
            break;
    }
} else {
    $controller = new HabitacionController();
    $controller->index();
}
?>