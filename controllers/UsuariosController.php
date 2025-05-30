<?php
require_once '../database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombres = trim($_POST['nombres'] ?? '');
    $tipoDocumento = trim($_POST['tipoDocumento'] ?? '');
    $numDocumento = trim($_POST['numDocumento'] ?? '');
    $apellidos = trim($_POST['apellidos'] ?? '');
    $fechaNacimiento = trim($_POST['fechaNacimiento'] ?? '');
    $sexo = trim($_POST['sexo'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
    $contrasena = ($_POST['contrasena'] ?? '');
    $numTelefono = trim($_POST['numTelefono'] ?? '');
    $contactoPersonal = trim($_POST['contactoPersonal'] ?? '');
    $direccion = trim($_POST['direccion'] ?? '');
    $rol = trim($_POST['rol'] ?? '');


    // Validaciones básicas

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombres = trim($_POST['nombres']);
        $tipoDocumento = trim($_POST['tipoDocumento']);
        $numDocumento = trim($_POST['numDocumento']);
        $apellidos = trim($_POST['apellidos']);
        $fechaNacimiento = trim($_POST['fechaNacimiento']);
        $sexo = trim($_POST['sexo']);
        $numTelefono = trim($_POST['numTelefono']);
        $contactoPersonal = trim($_POST['contactoPersonal']);
        $direccion = trim($_POST['direccion']);
        $rol = trim($_POST['rol']);        
        $correo = trim($_POST['correo']);
        $contrasena = $_POST['contrasena'];
        //validaciones de nombre
        switch($nombres){
            case 0:
        }
        if (empty($nombres)) {
            die('Inserte al menos el primer nombre');
        }
        if (empty($apellidos)) {
            die('Inserte al menos el primer nombre');
        }
        if (empty($tipo_documento)) {
            die('El tipo de documento es obligatorio.');
        }
        if (empty($numDocumento)) {
            die('El número de documento es obligatorio.');
        }
        if (empty($fecha_nacimiento)) {
            die('La fecha de nacimiento es obligatoria.');
        }
        if (empty($sexo)) {
            die('El sexo es obligatorio.');
        }
        if (empty($correo)) {
            die('El correo electrónico es obligatorio.');
        }
        //validaciones de contraseña
        if (empty($contrasena)) {
            die('La contraseña es obligatoria.');
        }
        if (empty($confirmarContrasena)) {
            die('La confirmación de la contraseña es obligatoria.');
        }
        if (strlen($contrasena) < 8) {
            die('La contraseña debe tener al menos 8 caracteres.');
        }
        if (!preg_match('/[A-Z]/', $contrasena)) {
            die('La contraseña debe contener al menos una letra mayúscula.');
        }
        if (!preg_match('/[a-z]/', $contrasena)) {
            die('La contraseña debe contener al menos una letra minúscula.');
        }
        if (!preg_match('/[0-9]/', $contrasena)) {
            die('La contraseña debe contener al menos un número.');
        }
        if (!preg_match('/[\W_]/', $contrasena)) {
            die('La contraseña debe contener al menos un carácter especial.');
        }
        if (preg_match('/\s/', $contrasena)) {
            die('La contraseña no debe contener espacios en blanco.');
        }
        if ($contrasena !== $confirmarContrasena) {
            die('Las contraseñas no coinciden.');
        }
        //validaciones de contacto
        if (empty($telefono)) {
            die('El teléfono es obligatorio.');
        }
        if (empty($tel_emergencia)) {
            die('El teléfono de emergencia es obligatorio.');
        }
        if (empty($direccion)) {
            die('La dirección es obligatoria.');
        }
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            die('El correo electrónico no es válido.');
        }
        if (empty($rol)) {
            die('El rol es obligatorio.');
        }
        if ($rol == 'admin' && empty($rnt)) {
            die('El RNT es obligatorio para el rol de administrador.');
        }
        if ($rol == 'admin' && empty($nit)) {
            die('El NIT es obligatorio para el rol de administrador.');
        }
    }



    // Procesa los datos si son válidos


    // Inserta los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO empleados (primer_nombre, segundo_nombre, tipo_documento, numDocumento, primer_apellido, segundo_apellido, fecha_nacimiento, sexo, correo, contrasena, confirmar_contrasena, telefono, tel_emergencia, direccion, rol, rnt, nit) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("sssssssssssssssss", $primer_nombre, $segundo_nombre, $tipo_documento, $numDocumento, $primer_apellido, $segundo_apellido, $fecha_nacimiento, $sexo, $correo, $contrasena, $confirmar_contrasena, $telefono, $tel_emergencia, $direccion, $rol, $rnt, $nit);

    if ($stmt->execute()) {
        // Redirige al formulario con un mensaje de éxito
        header("Location: /lodgehub/crudUsuarios/crudUsuarios.php?mensaje=Usuario registrado exitosamente");
        exit();;
    } else {
        echo "Error al registrar el usuario: " . $stmt->error . "<br>";
    }
    $stmt->close();
    $conn->close();
}
//Consulta/Leer/Read



// Cerrar la conexión
