<?php
session_start();
require_once __DIR__ . '/../models/habitacionesModel.php';

class HabitacionesController {
    private $modelo;

    public function __construct() {
        $this->modelo = new HabitacionesModel();
    }

    public function manejarPeticion() {
        $action = $_GET['action'] ?? null;

        try {
            switch ($action) {
                case 'crear':
                    $this->crear();
                    break;
                case 'obtener':
                    $this->obtener();
                    break;
                case 'obtenerPorId':
                    $this->obtenerPorId();
                    break;
                case 'actualizar':
                    $this->actualizar();
                    break;
                case 'eliminar':
                    $this->eliminar();
                    break;
                default:
                    $this->responder(['success' => false, 'message' => 'Acción no válida'], 400);
                    break;
            }
        } catch (Exception $e) {
            $this->responder(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    private function crear() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido');
        }

        // Validar que un hotel esté seleccionado en la sesión
        if (!isset($_SESSION['hotel_id']) || empty($_SESSION['hotel_id'])) {
            throw new Exception('No hay un hotel seleccionado en la sesión.');
        }
        $id_hotel = $_SESSION['hotel_id'];

        // Recoger y validar datos del formulario
        $numero = trim($_POST['numero'] ?? '');
        $tipoHabitacion = $_POST['tipoHabitacion'] ?? '';
        $costo = $_POST['costo'] ?? '';
        $capacidad = $_POST['capacidad'] ?? '';
        $descripcion = trim($_POST['descripcion'] ?? '');

        if (empty($numero) || empty($tipoHabitacion) || !is_numeric($costo) || !is_numeric($capacidad)) {
            throw new Exception('Datos incompletos o inválidos. Número, tipo, costo y capacidad son obligatorios.');
        }

        // Verificar si el número de habitación ya existe en el hotel
        if ($this->modelo->verificarNumeroExistente($numero, $id_hotel)) {
            throw new Exception("El número de habitación '{$numero}' ya existe en este hotel.");
        }

        // Procesar la imagen
        $rutaFoto = $this->procesarImagen($_FILES['foto'] ?? null);

        // Preparar datos para el modelo
        $datos = [
            ':numero' => $numero,
            ':costo' => $costo,
            ':capacidad' => $capacidad,
            ':tipoHabitacion' => $tipoHabitacion,
            ':foto' => $rutaFoto,
            ':descripcion' => $descripcion,
            ':estado' => 'Disponible', // Estado por defecto al crear
            ':id_hotel' => $id_hotel
        ];

        // Llamar al modelo para crear la habitación
        $exito = $this->modelo->crearHabitacion($datos);

        if ($exito) {
            $this->responder(['success' => true, 'message' => 'Habitación creada exitosamente.']);
        } else {
            throw new Exception('No se pudo crear la habitación en la base de datos.');
        }
    }

    private function obtener() {
        if (!isset($_SESSION['hotel_id']) || empty($_SESSION['hotel_id'])) {
            throw new Exception('No hay un hotel seleccionado en la sesión.');
        }
        $id_hotel = $_SESSION['hotel_id'];

        $pagina = filter_input(INPUT_GET, 'pagina', FILTER_VALIDATE_INT, ['options' => ['default' => 1]]);
        $registrosPorPagina = 9; // 9 tarjetas por página para un grid de 3x3

        $filtros = [
            'busqueda' => filter_input(INPUT_GET, 'busqueda', FILTER_SANITIZE_STRING) ?? '',
            'estado' => filter_input(INPUT_GET, 'estado', FILTER_SANITIZE_STRING) ?? 'all',
            'tipo' => filter_input(INPUT_GET, 'tipo', FILTER_SANITIZE_STRING) ?? 'all',
        ];

        $resultado = $this->modelo->obtenerHabitacionesPaginadas($id_hotel, $pagina, $registrosPorPagina, $filtros);
        
        $this->responder(['success' => true, 'data' => $resultado]);
    }

    private function obtenerPorId() {
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('ID de habitación no válido.');
        }

        $habitacion = $this->modelo->obtenerPorId($id);

        if ($habitacion) {
            $this->responder(['success' => true, 'data' => $habitacion]);
        } else {
            throw new Exception('Habitación no encontrada.');
        }
    }

    private function actualizar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('ID de habitación no proporcionado.');
        }

        $id_hotel = $_SESSION['hotel_id'];
        $numero = trim($_POST['numero'] ?? '');

        // Verificar si el número de habitación ya existe, excluyendo la actual
        if ($this->modelo->verificarNumeroExistente($numero, $id_hotel, $id)) {
            throw new Exception("El número de habitación '{$numero}' ya está en uso por otra habitación en este hotel.");
        }

        // Recoger datos a actualizar
        $datos = [
            'numero' => $numero,
            'tipoHabitacion' => $_POST['tipoHabitacion'] ?? null,
            'costo' => $_POST['costo'] ?? null,
            'capacidad' => $_POST['capacidad'] ?? null,
            'descripcion' => trim($_POST['descripcion'] ?? ''),
            'estado' => $_POST['estado'] ?? null,
        ];

        // Procesar nueva imagen si se subió
        if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
            // Opcional: eliminar la foto antigua antes de subir la nueva
            $habActual = $this->modelo->obtenerPorId($id);
            if ($habActual && !empty($habActual['foto'])) {
                $rutaAntigua = $_SERVER['DOCUMENT_ROOT'] . $habActual['foto'];
                if (file_exists($rutaAntigua)) {
                    unlink($rutaAntigua);
                }
            }
            $datos['foto'] = $this->procesarImagen($_FILES['foto']);
        }

        // Filtrar datos nulos para no sobrescribir campos que no se envían
        $datos = array_filter($datos, function($value) {
            return $value !== null;
        });

        if ($this->modelo->actualizarHabitacion($id, $datos)) {
            $this->responder(['success' => true, 'message' => 'Habitación actualizada exitosamente.']);
        } else {
            throw new Exception('No se pudo actualizar la habitación.');
        }
    }

    private function eliminar() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido');
        }

        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            throw new Exception('ID de habitación no proporcionado.');
        }

        // La lógica de si se puede eliminar (por ej. si tiene reservas) ya está en el modelo.
        $this->modelo->eliminarHabitacion($id);
        $this->responder(['success' => true, 'message' => 'Habitación eliminada exitosamente.']);
    }

    private function procesarImagen($file) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null; // No hay imagen o hubo un error
        }

        // Validaciones de seguridad
        if ($file['size'] > 5 * 1024 * 1024) { // 5 MB
            throw new Exception('El archivo de imagen es demasiado grande (máx 5MB).');
        }

        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $tipoMimeReal = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $file['tmp_name']);

        if (!in_array($tipoMimeReal, $tiposPermitidos)) {
            throw new Exception('Formato de imagen no permitido. Sube JPG, PNG, WEBP o GIF.');
        }

        // Generar nombre único y definir ruta
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'hab_' . uniqid() . '_' . time() . '.' . $extension;
        
        // Ruta relativa desde la raíz del servidor web
        $directorioUploads = '/lodgehub/public/uploads/habitaciones/';
        $rutaCompletaServidor = $_SERVER['DOCUMENT_ROOT'] . $directorioUploads;

        // Crear directorio si no existe
        if (!is_dir($rutaCompletaServidor)) {
            mkdir($rutaCompletaServidor, 0775, true);
        }

        $rutaArchivo = $rutaCompletaServidor . $nombreArchivo;

        if (move_uploaded_file($file['tmp_name'], $rutaArchivo)) {
            // Devolver la ruta web para guardarla en la BD
            return $directorioUploads . $nombreArchivo;
        } else {
            throw new Exception('Error al mover el archivo de imagen subido.');
        }
    }

    private function responder($data, $statusCode = 200) {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}

// Punto de entrada para las peticiones
$controlador = new HabitacionesController();
$controlador->manejarPeticion();