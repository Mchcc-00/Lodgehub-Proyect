<?php
require_once ('../../config/conexionGlobal.php');
$db = conexionDB();

if (!isset($_POST['correo']) || !isset($_POST['password'])) {
    header("location: login.php?mensaje=Error: Datos incompletos");
    exit;
}

$correo = trim($_POST['correo']);
$password = trim($_POST['password']);

if (empty($correo) || empty($password)) {
    header("location: login.php?mensaje=Error: Correo y contraseña son obligatorios");
    exit;
}

session_start();
$_SESSION['correo'] = $correo;

$conexion = mysqli_connect("localhost", "root", "", "lodgehub");

if (!$conexion) {
    header("location: login.php?mensaje=Error: No se pudo conectar a la base de datos");
    exit;
}

// Escape del correo para prevenir inyección SQL
$correo_escaped = mysqli_real_escape_string($conexion, $correo);

// Primero obtenemos los datos del usuario
$consulta = "SELECT numDocumento, correo, password, nombres, apellidos, roles 
             FROM tp_usuarios
             WHERE correo = '$correo_escaped' AND sesionCaducada = 1";

$resultado = mysqli_query($conexion, $consulta);

if (!$resultado) {
    mysqli_close($conexion);
    header("location: login.php?mensaje=Error en la consulta: " . mysqli_error($conexion));
    exit;
}

$filas = mysqli_num_rows($resultado);

if ($filas > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    
    // Verificar la contraseña
    // Si la contraseña en BD empieza con $2y$ es encriptada, si no es texto plano
    $password_bd = $usuario['password'];
    
    $password_valida = false;
    
    if (substr($password_bd, 0, 3) === '$2y') {
        // Contraseña encriptada - usar password_verify
        $password_valida = password_verify($password, $password_bd);
    } else {
        // Contraseña en texto plano (para compatibilidad con contraseñas viejas)
        $password_valida = ($password === $password_bd);
        
        // OPCIONAL: Actualizar a contraseña encriptada automáticamente
        if ($password_valida) {
            $nueva_hash = password_hash($password, PASSWORD_DEFAULT);
            $nueva_hash_escaped = mysqli_real_escape_string($conexion, $nueva_hash);
            $numDoc_escaped = mysqli_real_escape_string($conexion, $usuario['numDocumento']);
            
            $actualizar = "UPDATE tp_usuarios SET password = '$nueva_hash_escaped' WHERE numDocumento = '$numDoc_escaped'";
            mysqli_query($conexion, $actualizar);
        }
    }
    
    if ($password_valida) {
        // Login exitoso - Validar roles y establecer sesión
        session_start();
        $_SESSION['user'] = $correo;
        $_SESSION['numDocumento'] = $usuario['numDocumento'];
        $_SESSION['nombres'] = $usuario['nombres'];
        $_SESSION['apellidos'] = $usuario['apellidos'];
        $_SESSION['roles'] = $usuario['roles'];
        $_SESSION['login_time'] = time(); // Para tracking de sesión
        
        // Validación y redirección por roles (basado en ENUM de la BD)
        $rol_usuario = trim($usuario['roles']); // No convertir a minúsculas para mantener formato ENUM
        
        switch ($rol_usuario) {
            case 'Administrador':
                $destino = "homepage.php";
                $_SESSION['permisos'] = [
                    'crear', 'leer', 'actualizar', 'eliminar', 
                    'gestionar_usuarios', 'gestionar_reservas', 
                    'gestionar_habitaciones', 'ver_reportes',
                    'configurar_sistema'
                ];
                $_SESSION['nivel_acceso'] = 3; // Nivel más alto
                break;
                
            case 'Colaborador':
                $destino = "homepage.php";
                $_SESSION['permisos'] = [
                    'leer', 'actualizar', 'crear_reservas', 
                    'gestionar_checkin', 'gestionar_checkout',
                    'ver_disponibilidad', 'gestionar_servicios'
                ];
                $_SESSION['nivel_acceso'] = 2; // Nivel medio
                break;
                
            case 'Usuario':
                $destino = "homeUsuario.php";
                $_SESSION['permisos'] = [
                    'leer', 'actualizar_perfil', 'ver_reservas',
                    'crear_reserva_propia', 'cancelar_reserva_propia'
                ];
                $_SESSION['nivel_acceso'] = 1; // Nivel básico
                break;
                
            default:
                // Rol no reconocido - esto no debería pasar con ENUM bien definido
                mysqli_free_result($resultado);
                mysqli_close($conexion);
                error_log("Rol inesperado en base de datos para usuario {$correo}: '{$rol_usuario}'");
                header("location: login.php?mensaje=Error: Rol de usuario no válido");
                exit;
        }
        
        // Los roles están controlados por ENUM, no necesitamos validación adicional
        // pero podemos agregar verificación de estado del rol si fuera necesario

        
        // Logging de login exitoso (opcional - ajustar tabla según tu BD)
        $fecha_login = date('Y-m-d H:i:s');
        $numDoc_escaped = mysqli_real_escape_string($conexion, $usuario['numDocumento']);
        
        // Si tienes una tabla de logs, descomenta estas líneas:
        /*
        $log_query = "INSERT INTO tp_logs_login (numDocumento, correo, fecha_login, ip_address, rol) 
                      VALUES ('$numDoc_escaped', '$correo_escaped', '$fecha_login', 
                              '{$_SERVER['REMOTE_ADDR']}', '$rol_usuario')";
        mysqli_query($conexion, $log_query);
        */
        
        mysqli_free_result($resultado);
        mysqli_close($conexion);
        
        // Redirección final basada en el rol
        header("location: $destino");
        exit;
        
    } else {
        // Contraseña incorrecta
        mysqli_free_result($resultado);
        mysqli_close($conexion);
        
        // Log de intento fallido (opcional)
        $fecha_intento = date('Y-m-d H:i:s');
        error_log("Intento de login fallido para: $correo en $fecha_intento desde {$_SERVER['REMOTE_ADDR']}");
        
        header("location: login.php?mensaje=Correo o contraseña incorrectos");
        exit;
    }
} else {
    // Usuario no encontrado o sesión caducada
    mysqli_free_result($resultado);
    mysqli_close($conexion);
    header("location: login.php?mensaje=Correo o contraseña incorrectos");
    exit;
}
?>