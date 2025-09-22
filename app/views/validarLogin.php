<?php
/**
 * validarLogin.php - Lógica de autenticación de usuarios.
 * Refactorizado para usar PDO y una lógica de sesión más clara.
 */

// 1. Iniciar sesión y cargar dependencias
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../../config/conexionGlobal.php';

// 2. Validar la entrada
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['correo']) || empty($_POST['password'])) {
    header("Location: login.php?mensaje=Error: Correo y contraseña son obligatorios");
    exit;
}

$correo = trim($_POST['correo']);
$password = trim($_POST['password']);

try {
    // 3. Buscar al usuario por correo
    $db = conexionDB();
    $stmt = $db->prepare("SELECT numDocumento, nombres, apellidos, correo, password, roles FROM tp_usuarios WHERE correo = :correo AND sesionCaducada = '1'");
    $stmt->execute([':correo' => $correo]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // 4. Verificar usuario y contraseña
    if (!$usuario || !password_verify($password, $usuario['password'])) {
        error_log("Intento de login fallido para: $correo desde {$_SERVER['REMOTE_ADDR']}");
        header("Location: login.php?mensaje=Correo o contraseña incorrectos");
        exit;
    }

    // 5. Iniciar y poblar la sesión con datos del usuario
    $_SESSION['user'] = [
        'numDocumento' => $usuario['numDocumento'],
        'correo' => $usuario['correo'],
        'nombres' => $usuario['nombres'],
        'apellidos' => $usuario['apellidos'],
        'roles' => $usuario['roles'] // Rol principal de tp_usuarios
    ];
    // Para compatibilidad con código antiguo
    $_SESSION['numDocumento'] = $usuario['numDocumento'];
    $_SESSION['nombres'] = $usuario['nombres'];

    // 6. Buscar hoteles asignados al usuario
    $stmt = $db->prepare(
        "SELECT h.id, h.nombre, h.nit, h.direccion, h.telefono, h.correo, h.foto, h.descripcion, p.roles as roles_especificos
         FROM ti_personal p
         JOIN tp_hotel h ON p.id_hotel = h.id
         WHERE p.numDocumento = :numDocumento"
    );
    $stmt->execute([':numDocumento' => $usuario['numDocumento']]);
    $hoteles_asignados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $_SESSION['hoteles_asignados'] = $hoteles_asignados;

    // 7. Establecer el primer hotel como activo por defecto
    if (!empty($hoteles_asignados)) {
        $hotel_activo = $hoteles_asignados[0];
        $_SESSION['hotel'] = $hotel_activo;
        $_SESSION['hotel_id'] = $hotel_activo['id'];
        $_SESSION['hotel_nombre'] = $hotel_activo['nombre'];
        $_SESSION['roles_especificos'] = $hotel_activo['roles_especificos'];
    } else {
        // Limpiar datos de hotel si no hay ninguno asignado
        unset($_SESSION['hotel'], $_SESSION['hotel_id'], $_SESSION['hotel_nombre'], $_SESSION['roles_especificos']);
    }

    // 8. Determinar permisos y destino según el rol principal
    $rol_principal = $usuario['roles'];
    $destino = 'login.php?mensaje=Error: Rol no reconocido'; // Destino por defecto

    switch ($rol_principal) {
        case 'Administrador':
            // Si el administrador ya tiene hoteles, se comporta como un 'Administrador de Hotel'.
            // Si no tiene, es un 'Super Administrador' que necesita crear su primer hotel.
            if (!empty($hoteles_asignados)) {
                $_SESSION['tipo_admin'] = 'hotel';
            } else {
                $_SESSION['tipo_admin'] = 'super';
            }
            $_SESSION['nivel_acceso'] = 3;
            $destino = "homepage.php";
            break;

        case 'Colaborador':
            // Es un usuario que ya está vinculado a uno o más hoteles.
            if (empty($hoteles_asignados)) {
                // Caso anómalo: es colaborador pero no tiene hotel.
                header("Location: login.php?mensaje=Error: Colaborador sin hotel asignado.");
                exit;
            }
            // Verificar si es "Administrador de Hotel"
            if ($_SESSION['roles_especificos'] === 'Administrador de Hotel') {
                $_SESSION['tipo_admin'] = 'hotel';
                $_SESSION['nivel_acceso'] = 3;
                // SOLUCIÓN: Si es un Administrador de Hotel, su rol principal en la sesión
                // debe ser 'Administrador' para que tenga acceso a las funciones de gestión.
                // Esto corrige el problema de "Acceso Denegado" después de iniciar sesión.
                if ($_SESSION['user']['roles'] === 'Colaborador') {
                    $_SESSION['user']['roles'] = 'Administrador';
                }
            } else {
                $_SESSION['tipo_admin'] = 'colaborador';
                $_SESSION['nivel_acceso'] = 2;
            }
            $destino = "homepage.php";
            break;

        case 'Usuario':
            // Usuario final, cliente.
            $_SESSION['nivel_acceso'] = 1;
            $destino = "../../index.php"; // Redirigir a la página pública principal
            break;
    }

    // 9. Logging y redirección
    error_log("Login exitoso: {$correo} - Rol: {$rol_principal} - IP: {$_SERVER['REMOTE_ADDR']}");
    header("Location: $destino");
    exit;

} catch (PDOException $e) {
    error_log("Error de base de datos en login: " . $e->getMessage());
    header("location: login.php?mensaje=Correo o contraseña incorrectos");
    exit;
}
