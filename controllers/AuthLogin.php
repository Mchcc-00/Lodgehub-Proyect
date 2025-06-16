<?php
$correo = $_POST['correo'];
$password = $_POST['password'];
session_start();

$conexion = mysqli_connect("localhost", "root", "", "lodgehub");

// Consulta solo por el correo
$consulta = "SELECT * FROM tp_empleados WHERE correo = ?";
$stmt = mysqli_prepare($conexion, $consulta);
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$resultado = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($resultado)) {
    // Si la contraseña está hasheada, usa password_verify
    if (password_verify($password, $user['password'])) {
        $_SESSION['correo'] = $correo;
        header("Location: ../views/Homepage/index.php");
        exit();
    } else {
        // Contraseña incorrecta
        $_SESSION['login_error'] = "Contraseña incorrecta.";
        header("Location: ../views/Homepage/index.php");
        exit();
    }
} else {
    // Usuario no encontrado
    $_SESSION['login_error'] = "Usuario no encontrado.";
    header("Location: ../views/Homepage/index.php");
    exit();
}

mysqli_free_result($resultado);
mysqli_close($conexion);

