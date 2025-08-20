<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../models/HabitacionesModel.php';

class HabitacionesController {
    private $habitacionesModel;

    public function __construct() {
        $this->habitacionesModel = new HabitacionesModel();
    }

    public function crearHabitacion() {
        header('Content-Type: application/json');
        
        try {
            // Validar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Validar que todos los campos requeridos estén presentes
            $camposRequeridos = [
                'numero',
                'costo', 
                'capacidad',
                'tipoHabitacion'
            ];

            foreach ($camposRequeridos as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    throw new Exception("El campo {$campo} es requerido");
                }
            }

            // Sanitizar y validar datos
            $datos = [
                'numero' => $this->sanitizarTexto($_POST['numero']),
                'costo' => floatval($_POST['costo']),
                'capacidad' => intval($_POST['capacidad']),
                'tipoHabitacion' => intval($_POST['tipoHabitacion']),
                'foto' => isset($_POST['foto']) && !empty(trim($_POST['foto'])) ? $this->sanitizarTexto($_POST['foto']) : null,
                'descripcion' => isset($_POST['descripcion']) && !empty(trim($_POST['descripcion'])) ? $this->sanitizarTexto($_POST['descripcion']) : null,
                'estado' => isset($_POST['estado']) && !empty(trim($_POST['estado'])) ? $this->sanitizarTexto($_POST['estado']) : 'Disponible',
                'descripcionMantenimiento' => isset($_POST['descripcionMantenimiento']) && !empty(trim($_POST['descripcionMantenimiento'])) ? $this->sanitizarTexto($_POST['descripcionMantenimiento']) : null,
                'estadoMantenimiento' => isset($_POST['estadoMantenimiento']) && !empty(trim($_POST['estadoMantenimiento'])) ? $this->sanitizarTexto($_POST['estadoMantenimiento']) : 'Activo'
            ];

            // Validaciones específicas
            $this->validarDatos($datos);

            // Verificar si el número de habitación ya existe
            if ($this->habitacionesModel->habitacionExiste($datos['numero'])) {
                throw new Exception('Ya existe una habitación con este número');
            }

            // Verificar si el tipo de habitación existe
            if (!$this->habitacionesModel->tipoHabitacionExiste($datos['tipoHabitacion'])) {
                throw new Exception('El tipo de habitación especificado no existe');
            }

            // Crear la habitación
            $resultado = $this->habitacionesModel->crearHabitacion($datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Habitación creada exitosamente',
                    'data' => $datos
                ]);
            } else {
                throw new Exception('Error al crear la habitación en la base de datos');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerHabitaciones() {
        header('Content-Type: application/json');
        
        try {
            // Soporte para paginación y filtros
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $registrosPorPagina = isset($_GET['registros']) ? max(1, min(50, intval($_GET['registros']))) : 10;
            $filtro = isset($_GET['filtro']) && !empty(trim($_GET['filtro'])) ? $this->sanitizarTexto($_GET['filtro']) : null;
            $estado = isset($_GET['estado']) && !empty(trim($_GET['estado'])) ? $this->sanitizarTexto($_GET['estado']) : null;
            $tipoHabitacion = isset($_GET['tipoHabitacion']) && !empty(trim($_GET['tipoHabitacion'])) ? intval($_GET['tipoHabitacion']) : null;
            
            if (isset($_GET['paginado']) && $_GET['paginado'] === 'true') {
                $resultado = $this->habitacionesModel->obtenerHabitacionesPaginadas($pagina, $registrosPorPagina, $filtro, $estado, $tipoHabitacion);
                echo json_encode([
                    'success' => true,
                    'data' => $resultado
                ]);
            } else {
                $habitaciones = $this->habitacionesModel->obtenerTodasLasHabitaciones($filtro, $estado, $tipoHabitacion);
                echo json_encode([
                    'success' => true,
                    'data' => $habitaciones
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerHabitacionPorNumero($numero) {
        header('Content-Type: application/json');
        
        try {
            if (empty($numero)) {
                throw new Exception('Número de habitación es requerido');
            }

            $habitacion = $this->habitacionesModel->obtenerHabitacionPorNumero($numero);
            
            if ($habitacion) {
                echo json_encode([
                    'success' => true,
                    'data' => $habitacion
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Habitación no encontrada'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actualizarHabitacion() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Validar que el número esté presente
            if (!isset($_POST['numero']) || empty(trim($_POST['numero']))) {
                throw new Exception('Número de habitación es requerido');
            }

            $numero = $this->sanitizarTexto($_POST['numero']);

            // Verificar si la habitación existe
            if (!$this->habitacionesModel->habitacionExiste($numero)) {
                throw new Exception('La habitación no existe');
            }

            // Preparar datos para actualizar (solo los campos que se envíen)
            $datos = [];
            $camposPermitidos = ['costo', 'capacidad', 'tipoHabitacion', 'foto', 'descripcion', 'estado', 'descripcionMantenimiento', 'estadoMantenimiento'];

            foreach ($camposPermitidos as $campo) {
                if (isset($_POST[$campo])) {
                    if ($campo === 'foto' && empty(trim($_POST[$campo]))) {
                        $datos[$campo] = null;
                    } elseif ($campo === 'descripcion' && empty(trim($_POST[$campo]))) {
                        $datos[$campo] = null;
                    } elseif ($campo === 'descripcionMantenimiento' && empty(trim($_POST[$campo]))) {
                        $datos[$campo] = null;
                    } elseif (in_array($campo, ['costo'])) {
                        $datos[$campo] = floatval($_POST[$campo]);
                    } elseif (in_array($campo, ['capacidad', 'tipoHabitacion'])) {
                        $datos[$campo] = intval($_POST[$campo]);
                    } else {
                        $datos[$campo] = $this->sanitizarTexto($_POST[$campo]);
                    }
                }
            }

            if (empty($datos)) {
                throw new Exception('No hay datos para actualizar');
            }

            // Validar datos de actualización
            $this->validarDatosActualizacion($datos);

            // Si se especifica un tipo de habitación, verificar que existe
            if (isset($datos['tipoHabitacion']) && !$this->habitacionesModel->tipoHabitacionExiste($datos['tipoHabitacion'])) {
                throw new Exception('El tipo de habitación especificado no existe');
            }

            $resultado = $this->habitacionesModel->actualizarHabitacion($numero, $datos);

            if ($resultado) {
                // Obtener los datos actualizados para devolverlos
                $habitacionActualizada = $this->habitacionesModel->obtenerHabitacionPorNumero($numero);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Habitación actualizada exitosamente',
                    'data' => $habitacionActualizada
                ]);
            } else {
                throw new Exception('Error al actualizar la habitación');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminarHabitacion() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            if (!isset($_POST['numero']) || empty(trim($_POST['numero']))) {
                throw new Exception('Número de habitación es requerido');
            }

            $numero = $this->sanitizarTexto($_POST['numero']);

            // Verificar si la habitación existe
            if (!$this->habitacionesModel->habitacionExiste($numero)) {
                throw new Exception('La habitación no existe');
            }

            // Verificar si la habitación está ocupada o reservada
            $habitacion = $this->habitacionesModel->obtenerHabitacionPorNumero($numero);
            if ($habitacion && in_array($habitacion['estado'], ['Ocupada', 'Reservada'])) {
                throw new Exception('No se puede eliminar una habitación que está ocupada o reservada');
            }

            $resultado = $this->habitacionesModel->eliminarHabitacion($numero);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Habitación eliminada exitosamente'
                ]);
            } else {
                throw new Exception('Error al eliminar la habitación');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Buscar habitaciones
    public function buscarHabitaciones() {
        header('Content-Type: application/json');
        
        try {
            $termino = isset($_GET['termino']) ? trim($_GET['termino']) : '';
            
            if (empty($termino)) {
                throw new Exception('Término de búsqueda es requerido');
            }

            if (strlen($termino) < 1) {
                throw new Exception('El término de búsqueda debe tener al menos 1 carácter');
            }

            $habitaciones = $this->habitacionesModel->buscarHabitaciones($termino);
            
            echo json_encode([
                'success' => true,
                'data' => $habitaciones,
                'total' => count($habitaciones)
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Obtener tipos de habitación
    public function obtenerTiposHabitacion() {
        header('Content-Type: application/json');
        
        try {
            $tipos = $this->habitacionesModel->obtenerTiposHabitacion();
            
            echo json_encode([
                'success' => true,
                'data' => $tipos
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Obtener habitaciones disponibles
    public function obtenerHabitacionesDisponibles() {
        header('Content-Type: application/json');
        
        try {
            $tipoHabitacion = isset($_GET['tipoHabitacion']) && !empty(trim($_GET['tipoHabitacion'])) ? intval($_GET['tipoHabitacion']) : null;
            $capacidadMinima = isset($_GET['capacidadMinima']) && !empty(trim($_GET['capacidadMinima'])) ? intval($_GET['capacidadMinima']) : null;
            
            $habitaciones = $this->habitacionesModel->obtenerHabitacionesDisponibles($tipoHabitacion, $capacidadMinima);
            
            echo json_encode([
                'success' => true,
                'data' => $habitaciones,
                'total' => count($habitaciones)
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Cambiar estado de habitación
    public function cambiarEstadoHabitacion() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            if (!isset($_POST['numero']) || empty(trim($_POST['numero']))) {
                throw new Exception('Número de habitación es requerido');
            }

            if (!isset($_POST['estado']) || empty(trim($_POST['estado']))) {
                throw new Exception('Estado es requerido');
            }

            $numero = $this->sanitizarTexto($_POST['numero']);
            $estado = $this->sanitizarTexto($_POST['estado']);
            $descripcionMantenimiento = isset($_POST['descripcionMantenimiento']) && !empty(trim($_POST['descripcionMantenimiento'])) ? $this->sanitizarTexto($_POST['descripcionMantenimiento']) : null;

            // Validar estado
            $estadosPermitidos = ['Disponible', 'Reservada', 'Ocupada', 'Mantenimiento'];
            if (!in_array($estado, $estadosPermitidos)) {
                throw new Exception('Estado no válido');
            }

            // Si el estado es Mantenimiento, la descripción es requerida
            if ($estado === 'Mantenimiento' && empty($descripcionMantenimiento)) {
                throw new Exception('La descripción del mantenimiento es requerida cuando el estado es Mantenimiento');
            }

            $resultado = $this->habitacionesModel->cambiarEstadoHabitacion($numero, $estado, $descripcionMantenimiento);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Estado de habitación actualizado exitosamente'
                ]);
            } else {
                throw new Exception('Error al cambiar el estado de la habitación');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function sanitizarTexto($texto) {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }

    private function validarDatos($datos) {
        // Validar número de habitación
        if (strlen($datos['numero']) < 1 || strlen($datos['numero']) > 5) {
            throw new Exception('El número de habitación debe tener entre 1 y 5 caracteres');
        }

        if (!preg_match('/^[0-9A-Za-z]+$/', $datos['numero'])) {
            throw new Exception('El número de habitación solo puede contener letras y números');
        }

        // Validar costo
        if ($datos['costo'] <= 0) {
            throw new Exception('El costo debe ser mayor a 0');
        }

        if ($datos['costo'] > 99999999.99) {
            throw new Exception('El costo no puede ser mayor a 99,999,999.99');
        }

        // Validar capacidad
        if ($datos['capacidad'] <= 0 || $datos['capacidad'] > 999) {
            throw new Exception('La capacidad debe ser entre 1 y 999 personas');
        }

        // Validar estado
        $estadosPermitidos = ['Disponible', 'Reservada', 'Ocupada', 'Mantenimiento'];
        if (!in_array($datos['estado'], $estadosPermitidos)) {
            throw new Exception('Estado no válido');
        }

        // Validar estado de mantenimiento
        $estadosMantenimientoPermitidos = ['Activo', 'Inactivo'];
        if (!in_array($datos['estadoMantenimiento'], $estadosMantenimientoPermitidos)) {
            throw new Exception('Estado de mantenimiento no válido');
        }

        // Validar descripción si está presente
        if (!empty($datos['descripcion']) && strlen($datos['descripcion']) > 65535) {
            throw new Exception('La descripción no puede tener más de 65535 caracteres');
        }

        // Validar descripción de mantenimiento si está presente
        if (!empty($datos['descripcionMantenimiento']) && strlen($datos['descripcionMantenimiento']) > 65535) {
            throw new Exception('La descripción de mantenimiento no puede tener más de 65535 caracteres');
        }

        // Validar foto si está presente
        if (!empty($datos['foto']) && strlen($datos['foto']) > 255) {
            throw new Exception('La URL de la foto no puede tener más de 255 caracteres');
        }
    }

    private function validarDatosActualizacion($datos) {
        // Validar campos individuales solo si están presentes
        if (isset($datos['costo'])) {
            if ($datos['costo'] <= 0) {
                throw new Exception('El costo debe ser mayor a 0');
            }
            if ($datos['costo'] > 99999999.99) {
                throw new Exception('El costo no puede ser mayor a 99,999,999.99');
            }
        }

        if (isset($datos['capacidad'])) {
            if ($datos['capacidad'] <= 0 || $datos['capacidad'] > 999) {
                throw new Exception('La capacidad debe ser entre 1 y 999 personas');
            }
        }

        if (isset($datos['estado'])) {
            $estadosPermitidos = ['Disponible', 'Reservada', 'Ocupada', 'Mantenimiento'];
            if (!in_array($datos['estado'], $estadosPermitidos)) {
                throw new Exception('Estado no válido');
            }
        }

        if (isset($datos['estadoMantenimiento'])) {
            $estadosMantenimientoPermitidos = ['Activo', 'Inactivo'];
            if (!in_array($datos['estadoMantenimiento'], $estadosMantenimientoPermitidos)) {
                throw new Exception('Estado de mantenimiento no válido');
            }
        }

        if (isset($datos['descripcion']) && !is_null($datos['descripcion']) && strlen($datos['descripcion']) > 65535) {
            throw new Exception('La descripción no puede tener más de 65535 caracteres');
        }

        if (isset($datos['descripcionMantenimiento']) && !is_null($datos['descripcionMantenimiento']) && strlen($datos['descripcionMantenimiento']) > 65535) {
            throw new Exception('La descripción de mantenimiento no puede tener más de 65535 caracteres');
        }

        if (isset($datos['foto']) && !is_null($datos['foto']) && strlen($datos['foto']) > 255) {
            throw new Exception('La URL de la foto no puede tener más de 255 caracteres');
        }
    }
}

// Manejo de rutas
if (isset($_GET['action'])) {
    $controller = new HabitacionesController();
    
    switch ($_GET['action']) {
        case 'crear':
            $controller->crearHabitacion();
            break;
        case 'obtener':
            $controller->obtenerHabitaciones();
            break;
        case 'obtenerPorNumero':
            $numero = $_GET['numero'] ?? '';
            $controller->obtenerHabitacionPorNumero($numero);
            break;
        case 'actualizar':
            $controller->actualizarHabitacion();
            break;
        case 'eliminar':
            $controller->eliminarHabitacion();
            break;
        case 'buscar':
            $controller->buscarHabitaciones();
            break;
        case 'obtenerTipos':
            $controller->obtenerTiposHabitacion();
            break;
        case 'obtenerDisponibles':
            $controller->obtenerHabitacionesDisponibles();
            break;
        case 'cambiarEstado':
            $controller->cambiarEstadoHabitacion();
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
    }
} else {
    // Si no hay action, asumir que es crear (POST directo)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new HabitacionesController();
        $controller->crearHabitacion();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    }
}
?>