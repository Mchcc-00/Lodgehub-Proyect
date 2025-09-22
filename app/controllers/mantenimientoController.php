<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../models/mantenimientoModel.php'; // Modelo principal que ya usamos

class MantenimientoController {
    private $mantenimientoModel;
    private $id_hotel;

    public function __construct() {
        $this->mantenimientoModel = new MantenimientoModel();
        
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

    public function obtener() {
        $this->validarHotel();
        try {
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $registrosPorPagina = 10;
            
            $filtros = [
                'estado' => $_GET['estado'] ?? 'all',
                'prioridad' => $_GET['prioridad'] ?? 'all',
                'tipo' => $_GET['tipo'] ?? 'all',
            ];

            $resultado = $this->mantenimientoModel->obtenerMantenimientosPaginados($this->id_hotel, $pagina, $registrosPorPagina, $filtros);
            $this->responderJson(['success' => true, 'data' => $resultado]);
            
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function obtenerPorId() {
        $this->validarHotel();
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new Exception('ID de mantenimiento no proporcionado.');

            $mantenimiento = $this->mantenimientoModel->obtenerPorId($id);
            
            if ($mantenimiento) {
                $this->responderJson(['success' => true, 'data' => $mantenimiento]);
            } else {
                $this->responderJson(['success' => false, 'message' => 'Mantenimiento no encontrado.']);
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
            if (!$id) throw new Exception('ID de mantenimiento no proporcionado.');

            $datos = [];
            $camposPermitidos = ['tipo', 'problemaDescripcion', 'prioridad', 'estado', 'observaciones'];
            foreach ($camposPermitidos as $campo) {
                if (isset($_POST[$campo])) {
                    $datos[$campo] = trim($_POST[$campo]);
                }
            }

            $resultado = $this->mantenimientoModel->actualizarMantenimiento($id, $datos);
            if ($resultado) {
                $this->responderJson(['success' => true, 'message' => 'Mantenimiento actualizado correctamente.']);
            } else {
                throw new Exception('No se pudo actualizar el mantenimiento.');
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
            if (!$id) throw new Exception('ID de mantenimiento no proporcionado.');

            $resultado = $this->mantenimientoModel->eliminarMantenimiento($id);
            if ($resultado) {
                $this->responderJson(['success' => true, 'message' => 'Mantenimiento eliminado correctamente.']);
            } else {
                throw new Exception('No se pudo eliminar el mantenimiento.');
            }
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function buscar() {
        $this->validarHotel();
        try {
            $termino = $_GET['termino'] ?? '';
            if (strlen($termino) < 2) throw new Exception('El término de búsqueda debe tener al menos 2 caracteres.');

            $resultado = $this->mantenimientoModel->buscarMantenimientos($this->id_hotel, $termino);
            $this->responderJson(['success' => true, 'data' => $resultado]);

        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function crear() {
        $this->validarHotel();
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido.');

            $camposRequeridos = ['id_habitacion', 'tipo', 'problemaDescripcion', 'frecuencia', 'prioridad', 'numDocumento', 'id_hotel'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    throw new Exception("El campo '{$campo}' es obligatorio.");
                }
            }

            // Validación condicional para cantFrecuencia
            if ($_POST['frecuencia'] === 'Sí' && (empty($_POST['cantFrecuencia']))) {
                throw new Exception("El campo 'Frecuencia' es obligatorio cuando el mantenimiento es recurrente.");
            }

            $datos = [
                'id_habitacion' => $_POST['id_habitacion'],
                'tipo' => $_POST['tipo'],
                'problemaDescripcion' => trim($_POST['problemaDescripcion']),
                'frecuencia' => $_POST['frecuencia'],
                'cantFrecuencia' => $_POST['frecuencia'] === 'Sí' ? $_POST['cantFrecuencia'] : 'No aplica', // Asignar un valor por defecto si no es recurrente
                'prioridad' => $_POST['prioridad'],
                'numDocumento' => $_POST['numDocumento'],
                'id_hotel' => $this->id_hotel,
                'observaciones' => isset($_POST['observaciones']) ? trim($_POST['observaciones']) : ''
            ];

            // Validar que el id_hotel del formulario coincida con el de la sesión
            if ((int)$_POST['id_hotel'] !== (int)$this->id_hotel) {
                throw new Exception("Inconsistencia en los datos del hotel.");
            }

            $resultado = $this->mantenimientoModel->crearMantenimiento($datos);

            if ($resultado) {
                $this->responderJson(['success' => true, 'message' => 'Tarea de mantenimiento creada correctamente.']);
            } else {
                throw new Exception('No se pudo crear la tarea de mantenimiento.');
            }

        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function obtenerHabitaciones() {
        $this->validarHotel();
        try {
            $habitaciones = $this->mantenimientoModel->obtenerHabitaciones($this->id_hotel);
            $this->responderJson(['success' => true, 'data' => $habitaciones]);
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => 'Error al obtener habitaciones.']);
        }
    }

    public function obtenerColaboradores() {
        $this->validarHotel();
        try {
            $colaboradores = $this->mantenimientoModel->obtenerColaboradores($this->id_hotel);
            $this->responderJson(['success' => true, 'data' => $colaboradores]);
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => 'Error al obtener colaboradores.']);
        }
    }
}

// Manejo de rutas/acciones
if (isset($_GET['action'])) {
    $controller = new MantenimientoController();
    $action = $_GET['action'];

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
    }
}
?>