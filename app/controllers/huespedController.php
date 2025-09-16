<?php
require_once __DIR__ . '/../models/huespedModel.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

class HuespedController {
    private $model;

    public function __construct() {
        $this->model = new HuespedModel();
    }

    public function manejarPeticion() {
        $action = $_GET['action'] ?? null;

        try {
            if (!isset($_SESSION['hotel_id']) || empty($_SESSION['hotel_id'])) {
                throw new Exception("No se ha seleccionado un hotel.");
            }
            $id_hotel = $_SESSION['hotel_id'];

            switch ($action) {
                case 'obtener':
                    $this->obtenerHuespedes($id_hotel);
                    break;
                case 'crear':
                    $this->crearHuesped($id_hotel);
                    break;
                case 'obtenerPorDocumento':
                    $this->obtenerPorDocumento();
                    break;
                case 'actualizar':
                    $this->actualizarHuesped();
                    break;
                case 'eliminar':
                    $this->eliminarHuesped();
                    break;
                case 'buscar':
                    $this->buscarHuespedes($id_hotel);
                    break;
                case 'verificar':
                    $this->verificarExistencia($id_hotel);
                    break;
                default:
                    throw new Exception("Acción no válida.");
            }
        } catch (Exception $e) {
            $this->responder(['success' => false, 'message' => $e->getMessage()], 400);
        }
    }

    private function obtenerHuespedes($id_hotel) {
        $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);
        $registrosPorPagina = 10;
        $filtros = [
            'busqueda' => filter_input(INPUT_GET, 'busqueda', FILTER_SANITIZE_STRING)
        ];

        $resultado = $this->model->obtenerHuespedesPaginados($id_hotel, $pagina, $registrosPorPagina, $filtros);
        $this->responder(['success' => true, 'data' => $resultado]);
    }

    private function crearHuesped($id_hotel) {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Método no permitido.");
        }

        $datos = $this->sanitizarDatosHuesped($_POST);
        $datos['id_hotel'] = $id_hotel;

        $errores = $this->validarDatos($datos);
        if (!empty($errores)) {
            throw new Exception(implode(", ", $errores));
        }

        if ($this->model->crearHuesped($datos)) {
            $this->responder(['success' => true, 'message' => 'Huésped creado exitosamente.']);
        } else {
            throw new Exception("No se pudo crear el huésped.");
        }
    }

    private function obtenerPorDocumento() {
        $numDocumento = filter_input(INPUT_GET, 'numDocumento', FILTER_SANITIZE_STRING);
        if (!$numDocumento) {
            throw new Exception("Número de documento no proporcionado.");
        }
        $huesped = $this->model->obtenerPorDocumento($numDocumento);
        if ($huesped) {
            $this->responder(['success' => true, 'data' => $huesped]);
        } else {
            throw new Exception("Huésped no encontrado.");
        }
    }

    private function actualizarHuesped() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Método no permitido.");
        }

        $numDocumentoOriginal = $_POST['numDocumentoOriginal'] ?? null;
        if (!$numDocumentoOriginal) {
            throw new Exception("Falta el documento original para la actualización.");
        }

        $datos = $this->sanitizarDatosHuesped($_POST);
        $errores = $this->validarDatos($datos, true);
        if (!empty($errores)) {
            throw new Exception(implode(", ", $errores));
        }

        if ($this->model->actualizarHuesped($numDocumentoOriginal, $datos)) {
            $this->responder(['success' => true, 'message' => 'Huésped actualizado exitosamente.']);
        } else {
            throw new Exception("No se pudo actualizar el huésped.");
        }
    }

    private function eliminarHuesped() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception("Método no permitido.");
        }
        $numDocumento = $_POST['numDocumento'] ?? null;
        if (!$numDocumento) {
            throw new Exception("Número de documento no proporcionado.");
        }

        if ($this->model->eliminarHuesped($numDocumento)) {
            $this->responder(['success' => true, 'message' => 'Huésped eliminado exitosamente.']);
        }
        // La excepción en caso de error se lanza desde el modelo
    }

    private function buscarHuespedes($id_hotel) {
        $termino = filter_input(INPUT_GET, 'termino', FILTER_SANITIZE_STRING);
        if (strlen($termino) < 2) {
            $this->responder(['success' => true, 'data' => []]);
            return;
        }
        $huespedes = $this->model->buscarHuespedes($id_hotel, $termino);
        $this->responder(['success' => true, 'data' => $huespedes]);
    }

    private function verificarExistencia($id_hotel) {
        $campo = filter_input(INPUT_GET, 'campo', FILTER_SANITIZE_STRING);
        $valor = filter_input(INPUT_GET, 'valor', FILTER_SANITIZE_STRING);
        $documentoActual = filter_input(INPUT_GET, 'documentoActual', FILTER_SANITIZE_STRING);

        $resultado = $this->model->verificarExistencia($campo, $valor, $id_hotel, $documentoActual);
        $this->responder(['success' => true, 'data' => $resultado]);
    }

    private function sanitizarDatosHuesped($postData) {
        return [
            'numDocumento' => filter_var($postData['numDocumento'] ?? '', FILTER_SANITIZE_STRING),
            'tipoDocumento' => filter_var($postData['tipoDocumento'] ?? '', FILTER_SANITIZE_STRING),
            'nombres' => filter_var($postData['nombres'] ?? '', FILTER_SANITIZE_STRING),
            'apellidos' => filter_var($postData['apellidos'] ?? '', FILTER_SANITIZE_STRING),
            'numTelefono' => filter_var($postData['numTelefono'] ?? '', FILTER_SANITIZE_STRING),
            'correo' => filter_var($postData['correo'] ?? '', FILTER_SANITIZE_EMAIL),
            'sexo' => filter_var($postData['sexo'] ?? '', FILTER_SANITIZE_STRING),
        ];
    }

    private function validarDatos($datos, $esActualizacion = false) {
        $errores = [];
        if (empty($datos['numDocumento'])) $errores[] = "El número de documento es obligatorio.";
        if (empty($datos['tipoDocumento'])) $errores[] = "El tipo de documento es obligatorio.";
        if (empty($datos['nombres'])) $errores[] = "Los nombres son obligatorios.";
        if (empty($datos['apellidos'])) $errores[] = "Los apellidos son obligatorios.";
        if (empty($datos['numTelefono'])) $errores[] = "El teléfono es obligatorio.";
        if (empty($datos['correo']) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo no es válido.";
        }
        if (empty($datos['sexo'])) $errores[] = "El sexo es obligatorio.";

        return $errores;
    }

    private function responder($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}

$controller = new HuespedController();
$controller->manejarPeticion();

?>