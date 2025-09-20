<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// SOLUCIÓN: Corregir el nombre del archivo para que coincida con las mayúsculas/minúsculas (reservasModel.php)
require_once '../models/reservasModel.php';

class ReservasController {
    private $reservasModel;
    private $id_hotel;

    public function __construct() {
        $this->reservasModel = new ReservasModel();
        
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
                'busqueda' => $_GET['busqueda'] ?? null,
            ];

            $resultado = $this->reservasModel->obtenerReservasPaginadas($this->id_hotel, $pagina, $registrosPorPagina, $filtros);
            $this->responderJson(['success' => true, 'data' => $resultado]);
            
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function obtenerPorId() {
        $this->validarHotel();
        try {
            $id = $_GET['id'] ?? null;
            if (!$id) throw new Exception('ID de reserva no proporcionado.');

            $reserva = $this->reservasModel->obtenerPorId($id);
            
            if ($reserva) {
                $this->responderJson(['success' => true, 'data' => $reserva]);
            } else {
                $this->responderJson(['success' => false, 'message' => 'Reserva no encontrada.']);
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
            if (!$id) throw new Exception('ID de reserva no proporcionado.');

            $datos = [];
            $camposPermitidos = ['fechainicio', 'fechaFin', 'pagoFinal', 'estado', 'informacionAdicional'];
            foreach ($camposPermitidos as $campo) {
                if (isset($_POST[$campo])) {
                    $datos[$campo] = trim($_POST[$campo]);
                }
            }

            $resultado = $this->reservasModel->actualizarReserva($id, $datos);
            if ($resultado) {
                $this->responderJson(['success' => true, 'message' => 'Reserva actualizada correctamente.']);
            } else {
                throw new Exception('No se pudo actualizar la reserva.');
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
            if (!$id) throw new Exception('ID de reserva no proporcionado.');

            $resultado = $this->reservasModel->eliminarReserva($id);
            if ($resultado) {
                $this->responderJson(['success' => true, 'message' => 'Reserva eliminada correctamente.']);
            } else {
                throw new Exception('No se pudo eliminar la reserva.');
            }
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function crear() {
        $this->validarHotel();
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') throw new Exception('Método no permitido.');

            $camposRequeridos = ['id_hotel', 'us_numDocumento', 'hue_numDocumento', 'fechainicio', 'fechaFin', 'id_habitacion', 'pagoFinal'];
            foreach ($camposRequeridos as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    throw new Exception("El campo '{$campo}' es obligatorio.");
                }
            }

            // Validar que el id_hotel del formulario coincida con el de la sesión
            if ((int)$_POST['id_hotel'] !== (int)$this->id_hotel) {
                throw new Exception("Inconsistencia en los datos del hotel.");
            }

            $datos = [
                'id_hotel' => $this->id_hotel,
                'us_numDocumento' => $_POST['us_numDocumento'],
                'hue_numDocumento' => $_POST['hue_numDocumento'],
                'fechainicio' => $_POST['fechainicio'],
                'fechaFin' => $_POST['fechaFin'],
                'id_habitacion' => (int)$_POST['id_habitacion'],
                'pagoFinal' => (float)$_POST['pagoFinal'],
                'cantidadAdultos' => isset($_POST['cantidadAdultos']) ? (int)$_POST['cantidadAdultos'] : 1,
                'cantidadNinos' => isset($_POST['cantidadNinos']) ? (int)$_POST['cantidadNinos'] : 0,
                'motivoReserva' => $_POST['motivoReserva'] ?? 'Personal',
                'metodoPago' => $_POST['metodoPago'] ?? 'Efectivo',
                'informacionAdicional' => isset($_POST['informacionAdicional']) ? trim($_POST['informacionAdicional']) : null,
                'estado' => 'Activa' // O 'Pendiente' según tu lógica de negocio
            ];

            $resultado = $this->reservasModel->crearReserva($datos);

            if ($resultado) {
                $this->responderJson(['success' => true, 'message' => 'Reserva creada correctamente.']);
            } else {
                throw new Exception('No se pudo crear la reserva.');
            }

        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function habitacionesDisponibles() {
        try {
            // La validación del hotel se hace sobre el parámetro GET, no sobre la sesión.
            $id_hotel_peticion = $_GET['id_hotel'] ?? null;
            $fecha_inicio = $_GET['fechainicio'] ?? null;
            $fecha_fin = $_GET['fechaFin'] ?? null;

            if (!$id_hotel_peticion || !$fecha_inicio || !$fecha_fin) {
                throw new Exception('El ID del hotel no fue proporcionado en la petición.');
            }

            // Validar que la fecha de fin sea posterior a la de inicio
            if (new DateTime($fecha_fin) <= new DateTime($fecha_inicio)) {
                throw new Exception('La fecha de fin debe ser posterior a la fecha de inicio.');
            }

            $habitaciones = $this->reservasModel->obtenerHabitacionesDisponibles($id_hotel_peticion, $fecha_inicio, $fecha_fin);
            $this->responderJson(['success' => true, 'data' => $habitaciones]);
        } catch (Exception $e) {
            $this->responderJson(['success' => false, 'message' => 'Error al obtener habitaciones disponibles.']);
        }
    }
}

// Manejo de rutas/acciones
if (isset($_GET['action'])) {
    $controller = new ReservasController();
    $action = $_GET['action'];

    if (method_exists($controller, $action)) {
        $controller->$action();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Acción no válida.']);
    }
}
?>