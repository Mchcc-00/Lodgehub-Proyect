<?php
require_once ('../../../config/conexionGlobal.php');
$db = conexionDB();

session_start(); // Inicia la sesión antes de usar $_SESSION

$id = $_POST['id'];
$password = $_POST['new_password'];

if (!$id) {
    header("location: ../login/login.php?mensaje=Error: ID de usuario no encontrado");
    exit;
}

$conexion = mysqli_connect("localhost", "root", "", "lodgehub");

// Verifica el nombre correcto de la columna ID en tu tabla
$consulta = "UPDATE tp_empleados SET password = '$password' WHERE numDocumento = '$id'";
$resultado = mysqli_query($conexion, $consulta);

    header("location: ../login/login.php?mensaje=Contraseña actualizada correctamente");


mysqli_close($conexion);
?>

