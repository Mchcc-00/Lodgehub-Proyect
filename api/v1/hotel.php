<?php
/**
 * API para la gestión de Hoteles.
 * Maneja la creación y actualización de hoteles, incluyendo la subida de imágenes.
 */

// Headers para permitir CORS y definir el tipo de contenido de la respuesta
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST, GET, PUT, DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// SOLUCIÓN: Iniciar la sesión al principio del script para poder acceder y modificar $_SESSION.
session_start();

// Incluir la conexión y el modelo
require_once $_SERVER['DOCUMENT_ROOT'] . '/config/conexionGlobal.php';
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/models/hotelModel.php';

$db = conexionDB();
$hotelModel = new HotelModel();

$method = $_SERVER['REQUEST_METHOD'];

// Función para procesar la subida de la imagen
function procesarImagen($campoArchivo) {
    if (isset($_FILES[$campoArchivo]) && $_FILES[$campoArchivo]['error'] === UPLOAD_ERR_OK) {
        $archivo = $_FILES[$campoArchivo];
        
        // --- Validación de seguridad ---
        // 1. Tamaño del archivo (ej: max 5MB)
        if ($archivo['size'] > 5 * 1024 * 1024) {
            return ['success' => false, 'message' => 'El archivo es demasiado grande. Máximo 5MB.'];
        }
        
        // 2. Tipo de archivo (MIME type)
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $tipoMimeReal = mime_content_type($archivo['tmp_name']);
        if (!in_array($tipoMimeReal, $tiposPermitidos)) {
            return ['success' => false, 'message' => 'Tipo de archivo no permitido. Solo se aceptan JPG, PNG y GIF.'];
        }

        // --- Mover el archivo ---
        $nombreOriginal = pathinfo($archivo['name'], PATHINFO_FILENAME);
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        
        // Limpiar nombre y generar uno único
        $nombreSeguro = preg_replace("/[^A-Za-z0-9\-_]/", '', $nombreOriginal);
        $nombreUnico = $nombreSeguro . '_' . uniqid() . '.' . $extension;
        
        // Ruta de destino
        $rutaDestino = __DIR__ . '/public/uploads/hoteles/' . $nombreUnico;
        
        // Crear el directorio si no existe
        if (!is_dir(dirname($rutaDestino))) {
            mkdir(dirname($rutaDestino), 0755, true);
        }

        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            // Devolver la URL relativa para guardarla en la BD
            $urlRelativa = '/public/uploads/hoteles/' . $nombreUnico;
            return ['success' => true, 'url' => $urlRelativa];
        } else {
            return ['success' => false, 'message' => 'Error al mover el archivo subido.'];
        }
    }
    return ['success' => true, 'url' => null]; // No se subió archivo nuevo
}

// Solo manejaremos peticiones POST, ya que FormData las envía así
if ($method == 'POST') {
    // Los datos vienen de un formulario multipart/form-data
    $datos = [
        'id' => $_POST['id'] ?? null,
        'nit' => $_POST['nit'] ?? null,
        'nombre' => $_POST['nombre'] ?? null,
        'direccion' => $_POST['direccion'] ?? null,
        'telefono' => $_POST['telefono'] ?? null,
        'correo' => $_POST['correo'] ?? null,
        'descripcion' => $_POST['descripcion'] ?? null,
        'numDocumentoAdmin' => $_POST['numDocumento'] ?? null, // El campo se llama numDocumento en el form
    ];

    // Procesar la imagen
    $resultadoImagen = procesarImagen('foto');
    if (!$resultadoImagen['success']) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => $resultadoImagen['message']]);
        exit;
    }

    // Si se subió una nueva imagen, usamos su URL.
    if ($resultadoImagen['url'] !== null) {
        $datos['foto'] = $resultadoImagen['url'];
    }

    // Decidir si es CREAR o ACTUALIZAR
    if (empty($datos['id'])) {
        // --- CREAR ---
        $resultado = $hotelModel->crearHotel($datos);

        // SOLUCIÓN: Si la creación fue exitosa, actualizar la sesión del usuario.
        if ($resultado['success']) {
            $_SESSION['hotel_id'] = $resultado['id'];
            $_SESSION['hotel_nombre'] = $datos['nombre'];
            $_SESSION['tipo_admin'] = 'hotel'; // Cambiar de 'super' a 'hotel'
            
            // Poblar el array $_SESSION['hotel'] para que la homepage lo lea de inmediato
            $_SESSION['hotel'] = [
                'id' => $resultado['id'],
                'nombre' => $datos['nombre'],
                'nit' => $datos['nit'],
                'direccion' => $datos['direccion'],
                'telefono' => $datos['telefono'],
                'correo' => $datos['correo'],
                'foto' => $datos['foto'] ?? null,
                'descripcion' => $datos['descripcion']
            ];

            // Añadir el nuevo hotel a la lista de hoteles asignados en la sesión
            $_SESSION['hoteles_asignados'][] = ['id' => $resultado['id'], 'nombre' => $datos['nombre']];
        }

    } else {
        // --- ACTUALIZAR ---
        // Si no se subió una nueva foto, no queremos borrar la existente.
        if (!isset($datos['foto'])) {
            $hotelActual = $hotelModel->obtenerHotelPorId($datos['id']);
            if ($hotelActual['success']) {
                $datos['foto'] = $hotelActual['data']['foto'];
            }
        }
        $resultado = $hotelModel->actualizarHotel($datos['id'], $datos);
    }

    if ($resultado['success']) {
        http_response_code(200); // OK
    } else {
        http_response_code(400); // Bad Request
    }
    echo json_encode($resultado);
} else {
    // Para otros métodos como GET o DELETE, puedes mantener tu lógica actual si la tienes.
    http_response_code(405); // Method Not Allowed
    echo json_encode(['success' => false, 'message' => 'Método no permitido.']);
}
?>