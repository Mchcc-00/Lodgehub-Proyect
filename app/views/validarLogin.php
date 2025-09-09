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

// Consulta modificada para sistema multi-hotel
$consulta = "SELECT 
                u.numDocumento, 
                u.correo, 
                u.password, 
                u.nombres, 
                u.apellidos, 
                u.roles,
                h.id as hotel_id,
                h.nombre as hotel_nombre,
                h.nit as hotel_nit,
                p.roles as roles_especificos
             FROM tp_usuarios u
             LEFT JOIN ti_personal p ON u.numDocumento = p.numDocumento
             LEFT JOIN tp_hotel h ON p.id_hotel = h.id
             WHERE u.correo = '$correo_escaped' AND u.sesionCaducada = 1";

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
    $password_bd = $usuario['password'];
    $password_valida = false;
    
    if (substr($password_bd, 0, 3) === '$2y') {
        // Contraseña encriptada - usar password_verify
        $password_valida = password_verify($password, $password_bd);
    } else {
        // Contraseña en texto plano (para compatibilidad con contraseñas viejas)
        $password_valida = ($password === $password_bd);
        
        // Actualizar a contraseña encriptada automáticamente
        if ($password_valida) {
            $nueva_hash = password_hash($password, PASSWORD_DEFAULT);
            $nueva_hash_escaped = mysqli_real_escape_string($conexion, $nueva_hash);
            $numDoc_escaped = mysqli_real_escape_string($conexion, $usuario['numDocumento']);
            
            $actualizar = "UPDATE tp_usuarios SET password = '$nueva_hash_escaped' WHERE numDocumento = '$numDoc_escaped'";
            mysqli_query($conexion, $actualizar);
        }
    }
    
    if ($password_valida) {
        // Login exitoso - Establecer sesión con información del hotel
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Información básica del usuario
        $_SESSION['user'] = [
            'numDocumento' => $usuario['numDocumento'],
            'correo' => $usuario['correo'],
            'nombres' => $usuario['nombres'],
            'apellidos' => $usuario['apellidos'],
            'roles' => $usuario['roles']
        ];
        
        // Para compatibilidad con código existente
        $_SESSION['numDocumento'] = $usuario['numDocumento'];
        $_SESSION['nombres'] = $usuario['nombres'];
        
        // Información del hotel (si aplica)
        if ($usuario['hotel_id']) {
            $_SESSION['hotel'] = [
                'id' => $usuario['hotel_id'],
                'nombre' => $usuario['hotel_nombre'],
                'nit' => $usuario['hotel_nit']
            ];
            $_SESSION['hotel_id'] = $usuario['hotel_id'];
            $_SESSION['hotel_nombre'] = $usuario['hotel_nombre'];
        }
        
        // Roles específicos del hotel (si existen)
        if ($usuario['roles_especificos']) {
            $_SESSION['roles_especificos'] = $usuario['roles_especificos'];
        }
        
        // Validación y redirección por roles
        $rol_usuario = trim($usuario['roles']);
        
        switch ($rol_usuario) {
            case 'Administrador':
                // Los administradores pueden ser:
                // 1. Super Admin (sin hotel asignado) - Ve todos los hoteles
                // 2. Admin de Hotel (con hotel asignado) - Ve solo su hotel
                
                if ($usuario['hotel_id']) {
                    // Administrador de hotel específico
                    $destino = "homepage.php";
                    $_SESSION['tipo_admin'] = 'hotel';
                    $_SESSION['permisos'] = [
                        'crear', 'leer', 'actualizar', 'eliminar', 
                        'gestionar_usuarios_hotel', 'gestionar_reservas_hotel', 
                        'gestionar_habitaciones_hotel', 'ver_reportes_hotel',
                        'configurar_hotel'
                    ];
                } else {
                    // Super administrador (puede manejar múltiples hoteles)
                    $destino = "homepage.php";
                    $_SESSION['tipo_admin'] = 'super';
                    $_SESSION['permisos'] = [
                        'crear', 'leer', 'actualizar', 'eliminar', 
                        'gestionar_usuarios', 'gestionar_reservas', 
                        'gestionar_habitaciones', 'ver_reportes',
                        'configurar_sistema', 'gestionar_hoteles'
                    ];
                }
                $_SESSION['nivel_acceso'] = 3;
                break;
                
            case 'Colaborador':
                // Los colaboradores siempre deben estar asignados a un hotel
                if (!$usuario['hotel_id']) {
                    mysqli_free_result($resultado);
                    mysqli_close($conexion);
                    header("location: login.php?mensaje=Error: Colaborador sin hotel asignado");
                    exit;
                }
                
                $destino = "homepage.php";
                $_SESSION['permisos'] = [
                    'leer', 'actualizar', 'crear_reservas', 
                    'gestionar_checkin', 'gestionar_checkout',
                    'ver_disponibilidad', 'gestionar_servicios',
                    'gestionar_mantenimiento'
                ];
                $_SESSION['nivel_acceso'] = 2;
                break;
                
            case 'Usuario':
                // Los usuarios finales no necesitan hotel asignado
                $destino = "../../index.php";
                $_SESSION['permisos'] = [
                    'leer', 'actualizar_perfil', 'ver_reservas',
                    'crear_reserva_propia', 'cancelar_reserva_propia'
                ];
                $_SESSION['nivel_acceso'] = 1;
                break;
                
            default:
                mysqli_free_result($resultado);
                mysqli_close($conexion);
                error_log("Rol inesperado en base de datos para usuario {$correo}: '{$rol_usuario}'");
                header("location: login.php?mensaje=Error: Rol de usuario no válido");
                exit;
        }
        
        // Verificar si el usuario tiene múltiples hoteles (para casos especiales)
        if ($rol_usuario !== 'Usuario') {
            $consulta_hoteles = "SELECT h.id, h.nombre, h.nit, p.roles 
                                FROM tp_hotel h
                                INNER JOIN ti_personal p ON h.id = p.id_hotel
                                WHERE p.numDocumento = '{$usuario['numDocumento']}'
                                ORDER BY h.nombre";
            
            $resultado_hoteles = mysqli_query($conexion, $consulta_hoteles);
            
            if ($resultado_hoteles && mysqli_num_rows($resultado_hoteles) > 0) {
                $hoteles_asignados = [];
                while ($hotel = mysqli_fetch_assoc($resultado_hoteles)) {
                    $hoteles_asignados[] = $hotel;
                }
                $_SESSION['hoteles_asignados'] = $hoteles_asignados;
                mysqli_free_result($resultado_hoteles);
            }
        }
        
        // Logging de login exitoso
        $fecha_login = date('Y-m-d H:i:s');
        $numDoc_escaped = mysqli_real_escape_string($conexion, $usuario['numDocumento']);
        $hotel_info = $usuario['hotel_id'] ? "Hotel ID: {$usuario['hotel_id']}" : "Sin hotel asignado";
        
        error_log("Login exitoso: $correo - $rol_usuario - $hotel_info - {$_SERVER['REMOTE_ADDR']} - $fecha_login");
        
        mysqli_free_result($resultado);
        mysqli_close($conexion);
        
        // Redirección final basada en el rol
        header("location: $destino");
        exit;
        
    } else {
        // Contraseña incorrecta
        mysqli_free_result($resultado);
        mysqli_close($conexion);
        
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