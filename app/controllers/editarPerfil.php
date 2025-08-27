<?php
session_start();
header('Content-Type: application/json');

// Verificar si es una petición AJAX y el usuario está logueado
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) != 'xmlhttprequest' ||
    !isset($_SESSION['numDocumento'])) {
    
    echo json_encode([
        'success' => false, 
        'message' => 'Acceso no autorizado'
    ]);
    exit();
}

// Incluir conexión a la base de datos
require_once '../../config/conexionGlobal.php';

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener y limpiar datos
        $nombres = trim($_POST['nombres']);
        $apellidos = trim($_POST['apellidos']);
        $numTelefono = trim($_POST['numTelefono']);
        $correo = trim($_POST['correo']);
        $sexo = $_POST['sexo'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        
        // Validaciones
        $errores = [];
        
        if (empty($nombres)) {
            $errores[] = "El nombre es obligatorio.";
        }
        
        if (empty($apellidos)) {
            $errores[] = "Los apellidos son obligatorios.";
        }
        
        if (empty($numTelefono)) {
            $errores[] = "El número de teléfono es obligatorio.";
        } elseif (!preg_match('/^[0-9+\-\s]+$/', $numTelefono)) {
            $errores[] = "El número de teléfono no tiene un formato válido.";
        }
        
        if (empty($correo)) {
            $errores[] = "El correo electrónico es obligatorio.";
        } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El correo electrónico no tiene un formato válido.";
        }
        
        // Si hay errores, devolverlos
        if (!empty($errores)) {
            echo json_encode([
                'success' => false,
                'message' => implode('<br>', $errores)
            ]);
            exit();
        }
        
        // Verificar si el correo ya existe para otro usuario
        $db = conexionDB();
        $stmt = $db->prepare("SELECT numDocumento FROM tp_usuarios WHERE correo = ? AND numDocumento != ?");
        $stmt->execute([$correo, $_SESSION['numDocumento']]);
        
        if ($stmt->fetch()) {
            echo json_encode([
                'success' => false,
                'message' => 'El correo electrónico ya está registrado por otro usuario.'
            ]);
            exit();
        }
        
        // Actualizar en la base de datos
        $stmt = $db->prepare("
            UPDATE tp_usuarios 
            SET nombres = ?, apellidos = ?, numTelefono = ?, correo = ?, sexo = ?, fechaNacimiento = ? 
            WHERE numDocumento = ?
        ");
        
        if ($stmt->execute([$nombres, $apellidos, $numTelefono, $correo, $sexo, $fechaNacimiento, $_SESSION['numDocumento']])) {
            // Actualizar variables de sesión
            $_SESSION['nombres'] = $nombres;
            $_SESSION['apellidos'] = $apellidos;
            
            // Obtener datos actualizados para devolver
            $stmt = $db->prepare("SELECT nombres, apellidos, correo, numTelefono, sexo, fechaNacimiento FROM tp_usuarios WHERE numDocumento = ?");
            $stmt->execute([$_SESSION['numDocumento']]);
            $usuarioActualizado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo json_encode([
                'success' => true,
                'message' => 'Perfil actualizado correctamente.',
                'data' => $usuarioActualizado
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al actualizar el perfil. Intenta nuevamente.'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido.'
        ]);
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error interno del servidor: ' . $e->getMessage()
    ]);
}
?>