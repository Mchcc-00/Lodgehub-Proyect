<?php
require_once('../config/conexionGlobal.php');
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

$conexion = mysqli_connect("localhost", "root", "", "lodgehub");

if (!$conexion) {
    header("location: login.php?mensaje=Error: No se pudo conectar a la base de datos");
    exit;
}

// Consulta modificada para sistema multi-hotel
$consulta = "SELECT 
                u.numDocumento, 
                u.correo, 
                u.password, 
                u.nombres, 
                u.apellidos, 
                u.roles,
                h.id AS hotel_id,
                h.nombre AS hotel_nombre,
                h.nit AS hotel_nit,
                h.direccion AS hotel_direccion,
                h.telefono AS hotel_telefono,
                h.correo AS hotel_correo,
                h.foto AS hotel_foto,
                h.descripcion AS hotel_descripcion,
                p.roles as roles_especificos
             FROM tp_usuarios u
             LEFT JOIN ti_personal p ON u.numDocumento = p.numDocumento -- Corregido: LEFT JOIN para incluir usuarios sin hotel
             LEFT JOIN tp_hotel h ON p.id_hotel = h.id
             WHERE u.correo = ? AND u.sesionCaducada = '1'";

$stmt = mysqli_prepare($conexion, $consulta);
if (!$stmt) {
    mysqli_close($conexion);
    header("location: login.php?mensaje=Error: Fallo al preparar la consulta.");
    exit;
}

mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($resultado && mysqli_num_rows($resultado) > 0) {
    // El usuario existe, ahora procesamos sus datos y hoteles
    $hoteles_asignados = [];
    while ($fila = mysqli_fetch_assoc($resultado)) {
        $hoteles_asignados[] = $fila;
    }
    $usuario = $hoteles_asignados[0]; // Tomamos los datos del usuario de la primera fila

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
            $update_stmt = mysqli_prepare($conexion, "UPDATE tp_usuarios SET password = ? WHERE numDocumento = ?");
            mysqli_stmt_bind_param($update_stmt, "ss", $nueva_hash, $usuario['numDocumento']);
            mysqli_stmt_execute($update_stmt);
            mysqli_stmt_close($update_stmt);
        }
    }

    if ($password_valida) {
        // Login exitoso - Establecer sesión con información del hotel
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['correo'] = $correo;

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

        // Guardar todos los hoteles asignados en la sesión
        $hoteles_para_sesion = [];
        foreach ($hoteles_asignados as $hotel_data) {
            if ($hotel_data['hotel_id']) {
                $hoteles_para_sesion[] = [
                    'id' => $hotel_data['hotel_id'],
                    'nombre' => $hotel_data['hotel_nombre'],
                    'nit' => $hotel_data['hotel_nit'],
                    'roles' => $hotel_data['roles_especificos'],
                    'direccion' => $hotel_data['hotel_direccion'],
                    'telefono' => $hotel_data['hotel_telefono'],
                    'correo' => $hotel_data['hotel_correo'],
                    'foto' => $hotel_data['hotel_foto'],
                    'descripcion' => $hotel_data['hotel_descripcion']
                ];
            }
        }
        $_SESSION['hoteles_asignados'] = $hoteles_para_sesion;

        // Establecer el primer hotel como el activo por defecto
        if (!empty($hoteles_para_sesion)) {
            $hotel_activo = $hoteles_para_sesion[0];
            $_SESSION['hotel'] = [
                'id' => $hotel_activo['id'],
                'nombre' => $hotel_activo['nombre'],
                'nit' => $hotel_activo['nit'],
                'direccion' => $hotel_activo['direccion'],
                'telefono' => $hotel_activo['telefono'],
                'correo' => $hotel_activo['correo'],
                'foto' => $hotel_activo['foto'],
                'descripcion' => $hotel_activo['descripcion']
            ];
            $_SESSION['hotel_id'] = $hotel_activo['id'];
            $_SESSION['hotel_nombre'] = $hotel_activo['nombre'];
            $_SESSION['roles_especificos'] = $hotel_activo['roles'];
        }

        // Validación y redirección por roles
        $rol_usuario = trim($usuario['roles']);

        switch ($rol_usuario) {
            case 'Administrador':
                // Los administradores pueden ser:
                // 1. Super Admin (sin hotel asignado) - Ve todos los hoteles
                // 2. Admin de Hotel (con hotel asignado) - Ve solo su hotel

                if (!empty($_SESSION['hotel_id'])) {
                    // Administrador de hotel específico
                    $destino = "homepage.php";
                    $_SESSION['tipo_admin'] = 'hotel';
                    $_SESSION['permisos'] = [
                        'crear',
                        'leer',
                        'actualizar',
                        'eliminar',
                        'gestionar_usuarios_hotel',
                        'gestionar_reservas_hotel',
                        'gestionar_habitaciones_hotel',
                        'ver_reportes_hotel',
                        'configurar_hotel'
                    ];
                } else {
                    // Super administrador (puede manejar múltiples hoteles)
                    $destino = "homepage.php";
                    $_SESSION['tipo_admin'] = 'super';
                    $_SESSION['permisos'] = [
                        'crear',
                        'leer',
                        'actualizar',
                        'eliminar',
                        'gestionar_usuarios',
                        'gestionar_reservas',
                        'gestionar_habitaciones',
                        'ver_reportes',
                        'configurar_sistema',
                        'gestionar_hoteles'
                    ];
                }
                $_SESSION['nivel_acceso'] = 3;
                break;

            case 'Colaborador':
                // Los colaboradores siempre deben estar asignados a un hotel
                if (empty($_SESSION['hotel_id'])) {
                    mysqli_free_result($resultado);
                    mysqli_close($conexion);
                    header("location: login.php?mensaje=Error: Colaborador sin hotel asignado");
                    exit;
                }

                $destino = "homepage.php";
                $_SESSION['permisos'] = [
                    'leer',
                    'actualizar',
                    'crear_reservas',
                    'gestionar_checkin',
                    'gestionar_checkout',
                    'ver_disponibilidad',
                    'gestionar_servicios',
                    'gestionar_mantenimiento'
                ];
                $_SESSION['nivel_acceso'] = 2;
                break;

            case 'Usuario':
                // Los usuarios finales no necesitan hotel asignado
                $destino = "../../index.php";
                $_SESSION['permisos'] = [
                    'leer',
                    'actualizar_perfil',
                    'ver_reservas',
                    'crear_reserva_propia',
                    'cancelar_reserva_propia'
                ];
                $_SESSION['nivel_acceso'] = 1;
                break;

            default:
                if ($resultado) mysqli_free_result($resultado);
                mysqli_close($conexion);
                error_log("Rol inesperado en base de datos para usuario {$correo}: '{$rol_usuario}'");
                header("location: login.php?mensaje=Error: Rol de usuario no válido");
                exit;
        }

        // Logging de login exitoso
        $fecha_login = date('Y-m-d H:i:s');
        $hotel_info = !empty($_SESSION['hotel_id']) ? "Hotel ID: {$_SESSION['hotel_id']}" : "Sin hotel asignado";

        error_log("Login exitoso: $correo - $rol_usuario - $hotel_info - {$_SERVER['REMOTE_ADDR']} - $fecha_login");

        if ($resultado) mysqli_free_result($resultado);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);

        // Redirección final basada en el rol
        header("location: $destino");
        exit;
    } else {
        // Contraseña incorrecta
        if ($resultado) mysqli_free_result($resultado);
        mysqli_stmt_close($stmt);
        mysqli_close($conexion);

        $fecha_intento = date('Y-m-d H:i:s');
        error_log("Intento de login fallido para: $correo en $fecha_intento desde {$_SERVER['REMOTE_ADDR']}");

        header("location: login.php?mensaje=Correo o contraseña incorrectos");
        exit;
    }
} else {
    // Usuario no encontrado o sesión caducada
    if ($resultado) mysqli_free_result($resultado);
    if (isset($stmt)) mysqli_stmt_close($stmt);
    mysqli_close($conexion);
    header("location: login.php?mensaje=Correo o contraseña incorrectos");
    exit;
}
