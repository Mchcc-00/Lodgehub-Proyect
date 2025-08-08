<?php
// 1. Iniciar la sesión para poder usar $_SESSION
session_start();

// 2. Incluir los archivos necesarios
require_once __DIR__ . '/../../../app/Models/Usuario.php';
require_once __DIR__ . '/../../../config/conexionGlobal.php';

// 3. Obtener el ID del usuario desde la URL
$numDocumento_Delete = $_GET['id'] ?? null;

if ($numDocumento_Delete) {
    $db = conexionDB();
    $usuarioModel = new Usuario($db);

    // 4. Llamar al método para eliminar
    $exito = $usuarioModel->eliminar($numDocumento_Delete);

    if ($exito) {
        // 5. Guardar mensaje de ÉXITO en la sesión
        $_SESSION['mensaje_exito'] = "Usuario eliminado correctamente.";
    } else {
        // 6. Guardar mensaje de ERROR en la sesión
        $_SESSION['mensaje_error'] = "No se pudo eliminar el usuario (posiblemente por registros asociados).";
    }
} else {
    $_SESSION['mensaje_error'] = "No se proporcionó un ID de usuario válido.";
}

// 7. Redirigir a la lista (sin mensajes en la URL)
header('Location: lista.php');
exit();
?>