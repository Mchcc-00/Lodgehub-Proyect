<?php
require_once ('../../../config/conexionGlobal.php');
session_start();

// Verificar que los datos lleguen por POST
if (!isset($_POST['numDocumento']) || !isset($_POST['new_password'])) {
    header("location: ../login/login.php?mensaje=Error: Datos no recibidos");
    exit;
}

$numDocumento = trim($_POST['numDocumento']);
$new_password = trim($_POST['new_password']);

// Validar que no estén vacíos
if (empty($numDocumento) || empty($new_password)) {
    header("location: ../login/login.php?mensaje=Error: Número de documento y contraseña son obligatorios");
    exit;
}

// Conectar a la base de datos
$conexion = mysqli_connect("localhost", "root", "", "lodgehub");

if (!$conexion) {
    header("location: ../login/login.php?mensaje=Error: No se pudo conectar a la base de datos");
    exit;
}

// Verificar que el usuario existe
$numDocumento_escaped = mysqli_real_escape_string($conexion, $numDocumento);
$consulta_verificar = "SELECT numDocumento FROM tp_empleados WHERE numDocumento = '$numDocumento_escaped'";
$resultado_verificar = mysqli_query($conexion, $consulta_verificar);

if (!$resultado_verificar) {
    mysqli_close($conexion);
    header("location: ../login/login.php?mensaje=Error en la consulta: " . mysqli_error($conexion));
    exit;
}

if (mysqli_num_rows($resultado_verificar) == 0) {
    mysqli_close($conexion);
    header("location: ../login/login.php?mensaje=Error: No se encontró un usuario con ese número de documento");
    exit;
}

// Encriptar la nueva contraseña
$password_hash = password_hash($new_password, PASSWORD_DEFAULT);
$password_escaped = mysqli_real_escape_string($conexion, $password_hash);

// Actualizar la contraseña
$consulta_update = "UPDATE tp_empleados SET password = '$password_escaped', solicitarContraseña = '0' WHERE numDocumento = '$numDocumento_escaped'";

// EJECUTAR la consulta (esto faltaba en tu código original)
$resultado_update = mysqli_query($conexion, $consulta_update);

if (!$resultado_update) {
    mysqli_close($conexion);
    header("location: ../login/login.php?mensaje=Error al actualizar: " . mysqli_error($conexion));
    exit;
}

// Verificar si se actualizó alguna fila
$filas_afectadas = mysqli_affected_rows($conexion);

if ($filas_afectadas > 0) {
    mysqli_close($conexion);
    header("location: ../login/login.php?mensaje=Contraseña actualizada correctamente");
    exit;
} else {
    mysqli_close($conexion);
    header("location: ../login/login.php?mensaje=Error: No se pudo actualizar la contraseña");
    exit;
}
?>