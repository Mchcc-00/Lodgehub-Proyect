<?php
require_once ('../../../config/conexionGlobal.php');
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
             FROM tp_empleados 
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
            
            $actualizar = "UPDATE tp_empleados SET password = '$nueva_hash_escaped' WHERE numDocumento = '$numDoc_escaped'";
            mysqli_query($conexion, $actualizar);
        }
    }
    
    if ($password_valida) {
        // Login exitoso
        session_start();
        $_SESSION['user'] = $correo;
        $_SESSION['numDocumento'] = $usuario['numDocumento'];
        $_SESSION['nombres'] = $usuario['nombres'];
        $_SESSION['apellidos'] = $usuario['apellidos'];
        $_SESSION['roles'] = $usuario['roles'];
        
        mysqli_free_result($resultado);
        mysqli_close($conexion);
        
        header("location: ../homepage/homepage.php");
        exit;
    } else {
        // Contraseña incorrecta
        mysqli_free_result($resultado);
        mysqli_close($conexion);
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

<!-- <?php
// codigo antiguo
// require_once '../../config/conexionGlobal.php';
// $db = conexionDB();


$correo=$_POST['correo'];
$password=$_POST['password'];
session_start();
$_SESSION['correo']=$correo;



$conexion=mysqli_connect("localhost","root","","lodgehub");

$consulta="SELECT*FROM tp_empleados where correo= '$correo' and password= '$password' and sesionCaducada = 1";
$resultado=mysqli_query($conexion,$consulta);

$filas=mysqli_num_rows($resultado);

if($filas){

    session_start();
    $_SESSION['user'] = $correo;
    
    header("location: ../homepage/homepage.php");

}else {
    header("location: login.php");
    ?>
    <?php
}

mysqli_free_result($resultado);
mysqli_close($conexion);

?> -->
