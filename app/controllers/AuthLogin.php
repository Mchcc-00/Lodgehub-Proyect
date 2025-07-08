<?php
session_start();

// Incluir configuraciones
require_once __DIR__ . '../../views/login/databse.php';
require_once __DIR__ . '../../views/login/functions.php';

// Validar token CSRF
if (empty($_POST['csrf_token']) || !validateCsrfToken($_POST['csrf_token'])) {
    $_SESSION['login_error'] = "Error de seguridad. Por favor recargue la página.";
    header("Location: ../views/login.php");
    exit();
}

// Validar entrada
if (empty($_POST['correo']) || empty($_POST['password'])) {
    $_SESSION['login_error'] = "Por favor ingrese correo y contraseña";
    header("Location: ../views/login.php");
    exit();
}

$correo = trim($_POST['correo']);
$password = $_POST['password'];

// Registrar intento de login (para prevención de fuerza bruta)
registerLoginAttempt($correo);

// Verificar si hay muchos intentos fallidos
if (isAccountLocked($correo)) {
    $_SESSION['login_error'] = "Demasiados intentos fallidos. Por favor intente más tarde.";
    header("Location: ../views/login.php");
    exit();
}

try {
    // Buscar usuario con PDO
    $db = getDatabaseConnection();
    $stmt = $db->prepare("SELECT * FROM tp_empleados WHERE correo = :correo");
    $stmt->bindParam(':correo', $correo);
    $stmt->execute();
    
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user) {
        // Verificar contraseña hasheada
        if (password_verify($password, $user['password'])) {
            // Restablecer contador de intentos fallidos
            resetFailedAttempts($correo);
            
            // Regenerar ID de sesión para prevenir fixation
            session_regenerate_id(true);
            
            // Almacenar datos de usuario en sesión
            $_SESSION['user'] = [
                'id' => $user['id'],
                'correo' => $user['correo'],
                'nombre' => $user['nombre'] ?? '',
                'last_login' => time()
            ];
            
            header("Location: ../views/homepage/homepage.php");
            exit();
        } else {
            // Contraseña incorrecta
            recordFailedAttempt($correo);
            $_SESSION['login_error'] = "Credenciales inválidas";
            header("Location: ../views/login.php");
            exit();
        }
    } else {
        // Usuario no encontrado
        recordFailedAttempt($correo);
        $_SESSION['login_error'] = "Credenciales inválidas";
        header("Location: ../views/login.php");
        exit();
    }
} catch (PDOException $e) {
    error_log("Error de base de datos: " . $e->getMessage());
    $_SESSION['login_error'] = "Error en el sistema. Por favor intente más tarde.";
    header("Location: ../views/login.php");
    exit();
}