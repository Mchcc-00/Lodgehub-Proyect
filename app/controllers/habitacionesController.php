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
                'foto' => isset($_POST['foto']) ? $this->sanitizarTexto($_POST['foto']) : null,
                'descripcion' => isset($_POST['descripcion']) ? $this->sanitizarTexto($_POST['descripcion']) : null,
                'estado' => 'Disponible' // Estado por defecto
            ];

            // Validaciones específicas
            $this->validarDatos($datos);

            // Verificar si la habitación ya existe
            if ($this->habitacionesModel->habitacionExiste($datos['numero'])) {
                throw new Exception('Ya existe una habitación con este número');
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
            $registrosPorPagina = isset($_GET['registros']) ? max(1, min(50, intval($_GET['registros']))) : 12;
            $filtro = isset($_GET['filtro']) ? $this->sanitizarTexto($_GET['filtro']) : null;
            
            if (isset($_GET['paginado']) && $_GET['paginado'] === 'true') {
                $resultado = $this->habitacionesModel->obtenerHabitacionesPaginadas($pagina, $registrosPorPagina, $filtro);
                echo json_encode([
                    'success' => true,
                    'data' => $resultado
                ]);
            } else {
                $habitaciones = $this->habitacionesModel->obtenerTodasLasHabitaciones($filtro);
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
            $camposPermitidos = ['costo', 'capacidad', 'tipoHabitacion', 'foto', 'descripcion', 'estado', 'descripcionMantenimiento'];

            foreach ($camposPermitidos as $campo) {
                if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
                    if ($campo === 'costo') {
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

            // Validar datos
            $this->validarDatosActualizacion($datos);

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

    public function buscarHabitaciones() {
        header('Content-Type: application/json');
        
        try {
            $termino = isset($_GET['termino']) ? trim($_GET['termino']) : '';
            
            if (empty($termino)) {
                throw new Exception('Término de búsqueda es requerido');
            }

            if (strlen($termino) < 1) {
                throw new Exception('El término de búsqueda debe tener al menos 1 caracter');
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

    public function ponerEnMantenimiento() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            if (!isset($_POST['numero']) || empty(trim($_POST['numero']))) {
                throw new Exception('Número de habitación es requerido');
            }

            $numero = $this->sanitizarTexto($_POST['numero']);
            $descripcion = isset($_POST['descripcionMantenimiento']) ? $this->sanitizarTexto($_POST['descripcionMantenimiento']) : 'Mantenimiento programado';

            $resultado = $this->habitacionesModel->ponerEnMantenimiento($numero, $descripcion);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Habitación puesta en mantenimiento'
                ]);
            } else {
                throw new Exception('Error al poner habitación en mantenimiento');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function finalizarMantenimiento() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            if (!isset($_POST['numero']) || empty(trim($_POST['numero']))) {
                throw new Exception('Número de habitación es requerido');
            }

            $numero = $this->sanitizarTexto($_POST['numero']);

            $resultado = $this->habitacionesModel->finalizarMantenimiento($numero);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Mantenimiento finalizado exitosamente'
                ]);
            } else {
                throw new Exception('Error al finalizar mantenimiento');
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

        // Validar costo
        if ($datos['costo'] <= 0) {
            throw new Exception('El costo debe ser mayor a 0');
        }

        // Validar capacidad
        if ($datos['capacidad'] <= 0 || $datos['capacidad'] > 20) {
            throw new Exception('La capacidad debe ser entre 1 y 20 personas');
        }

        // Validar estado
        if (isset($datos['estado'])) {
            $estadosPermitidos = ['Disponible', 'Reservada', 'Ocupada', 'Mantenimiento'];
            if (!in_array($datos['estado'], $estadosPermitidos)) {
                throw new Exception('Estado no válido');
            }
        }

        // Validar descripción
        if (isset($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            throw new Exception('La descripción no puede tener más de 500 caracteres');
        }
    }

    private function validarDatosActualizacion($datos) {
        // Validar costo
        if (isset($datos['costo']) && $datos['costo'] <= 0) {
            throw new Exception('El costo debe ser mayor a 0');
        }

        // Validar capacidad
        if (isset($datos['capacidad']) && ($datos['capacidad'] <= 0 || $datos['capacidad'] > 20)) {
            throw new Exception('La capacidad debe ser entre 1 y 20 personas');
        }

        // Validar estado
        if (isset($datos['estado'])) {
            $estadosPermitidos = ['Disponible', 'Reservada', 'Ocupada', 'Mantenimiento'];
            if (!in_array($datos['estado'], $estadosPermitidos)) {
                throw new Exception('Estado no válido');
            }
        }

        // Validar descripción
        if (isset($datos['descripcion']) && strlen($datos['descripcion']) > 500) {
            throw new Exception('La descripción no puede tener más de 500 caracteres');
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
        case 'ponerMantenimiento':
            $controller->ponerEnMantenimiento();
            break;
        case 'finalizarMantenimiento':
            $controller->finalizarMantenimiento();
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
    }
}