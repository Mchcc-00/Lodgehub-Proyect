<?php
require_once __DIR__ . '/../../config/conexionGlobal.php';

$db = conexionDB(); // Usamos $db como la conexión PDO
if (!$db) {
    die("Error al conectar a la base de datos.");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombres = trim($_POST['nombres'] ?? '');
    $tipoDocumento = trim($_POST['tipoDocumento'] ?? '');
    $numDocumento = trim($_POST['numDocumento'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $fechaNacimiento = trim($_POST['fechaNacimiento'] ?? '');
    $sexo = trim($_POST['sexo'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $password = ($_POST['password'] ?? '');
    $numTelefono = trim($_POST['numTelefono'] ?? '');
    $contactoPersonal = trim($_POST['contactoPersonal'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $rol = trim($_POST['rol'] ?? '');


    // Validaciones básicas
    $error = '';
    if (empty($nombres)) {
        $error = 'nombres';
    } elseif (empty($apellidos)) {
        $error = 'apellidos';
    } elseif (empty($tipoDocumento)) {
        $error = 'tipoDocumento';
    } elseif (empty($numDocumento)) {
        $error = 'numDocumento';
    } elseif (empty($fechaNacimiento)) {
        $error = 'fechaNacimiento';
    } elseif (empty($sexo)) {
        $error = 'sexo';
    } elseif (empty($correo)) {
        $error = 'correo';
    } elseif (empty($password)) {
        $error = 'password';
    } elseif (strlen($password) < 8) {
        $error = 'contrasena_corta';
    } elseif (!preg_match('/[A-Z]/', $password)) {
        $error = 'contrasena_mayuscula';
    } elseif (!preg_match('/[a-z]/', $password)) {
        $error = 'contrasena_minuscula';
    } elseif (!preg_match('/[0-9]/', $password)) {
        $error = 'contrasena_numero';
    } elseif (!preg_match('/[\W_]/', $password)) {
        $error = 'contrasena_especial';
    } elseif (preg_match('/\s/', $password)) {
        $error = 'contrasena_espacio';
    } elseif (empty($numTelefono)) {
        $error = 'numTelefono';
    } elseif (empty($contactoPersonal)) {
        $error = 'contactoPersonal';
    } elseif (empty($direccion)) {
        $error = 'direccion';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'correo_invalido';
    } elseif (empty($rol)) {
        $error = 'rol';
    } elseif ($rol == 'admin' && empty($rnt)) {
        $error = 'rnt';
    } elseif ($rol == 'admin' && empty($nit)) {
        $error = 'nit';
    }

    if ($error !== '') {
        switch ($error) {
            case 'nombres':
                die('Inserte al menos el primer nombre');
            case 'apellidos':
                die('Inserte al menos el primer apellido');
            case 'tipoDocumento':
                die('Seleccione un tipo de documento.');
            case 'numDocumento':
                die('Inserte el número de documento.');
            case 'fechaNacimiento':
                die('Inserte la fecha de nacimiento.');
            case 'sexo':
                die('Inserte un sexo.');
            case 'correo':
                die('Inserte el correo electrónico.');
            case 'contrasena':
                die('La contraseña es obligatoria.');
            case 'confirmarContrasena':
                die('La confirmación de la contraseña es obligatoria.');
            case 'contrasena_corta':
                die('La contraseña debe tener al menos 8 caracteres.');
            case 'contrasena_mayuscula':
                die('La contraseña debe contener al menos una letra mayúscula.');
            case 'contrasena_minuscula':
                die('La contraseña debe contener al menos una letra minúscula.');
            case 'contrasena_numero':
                die('La contraseña debe contener al menos un número.');
            case 'contrasena_especial':
                die('La contraseña debe contener al menos un carácter especial.');
            case 'contrasena_espacio':
                die('La contraseña no debe contener espacios en blanco.');
            case 'contrasena_no_coincide':
                die('Las contraseñas no coinciden.');
            case 'numTelefono':
                die('Inserte un número telefónico.');
            case 'contactoPersonal':
                die('Inserte un número en caso de emergencia.');
            case 'direccion':
                die('Inserte su dirección.');
            case 'correo_invalido':
                die('El correo electrónico no es válido.');
            case 'rol':
                die('Inserte un rol.');
            case 'rnt':
                die('El RNT es obligatorio para el rol de administrador.');
            case 'nit':
                die('El NIT es obligatorio para el rol de administrador.');
            default:
                die('Error desconocido.');
        }
    }


}