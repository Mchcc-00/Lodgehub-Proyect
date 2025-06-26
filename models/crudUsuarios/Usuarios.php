<?php
// filepath: c:\xampp\htdocs\loch\usuarios.php

require_once __DIR__ . '/../../config/conexionGlobal.php';

// Función para limpiar datos
function limpiar($dato)
{
    return htmlspecialchars(trim($dato));
}

$db = conexionDB(); // Usamos $db como la conexión PDO

// Insertar usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'insertar') {
    // Validar y limpiar datos
    $numDocumento = limpiar($_POST['numDocumento'] ?? '');
    $nombres = limpiar($_POST['nombres'] ?? '');
    $apellidos = limpiar($_POST['apellidos'] ?? '');
    $direccion = limpiar($_POST['direccion'] ?? '');
    $fechaNacimiento = limpiar($_POST['fechaNacimiento'] ?? '');
    $numTelefono = limpiar($_POST['numTelefono'] ?? '');
    $contactoPersonal = limpiar($_POST['contactoPersonal'] ?? '');
    $password = limpiar($_POST['password'] ?? '');
    $correo = limpiar($_POST['correo'] ?? '');
    $rnt = limpiar($_POST['rnt'] ?? '');
    $nit = limpiar($_POST['nit'] ?? '');
    $foto = $_FILES['foto'] ?? null;
    $sexo = limpiar($_POST['sexo'] ?? '');
    $tipoDocumento = limpiar($_POST['tipoDocumento'] ?? '');
    $roles = limpiar($_POST['roles'] ?? '');
    $estadoCivil = limpiar($_POST['estadoCivil'] ?? '');

    // Validación de campos obligatorios GENERAL
    if (
        empty($numDocumento) || empty($tipoDocumento) || empty($nombres) || empty($apellidos) || empty($direccion)
        || empty($fechaNacimiento) || empty($numTelefono) || empty($contactoPersonal) || empty($password)
        || empty($correo) || empty($foto) || empty($sexo) || empty($roles) || empty($estadoCivil)
    ) {
        die("Por favor, completa todos los campos obligatorios.");
    }
    // Validación específica para ADMINISTRADOR
    if ($roles === '1') { // Asumiendo que '1' es el ID del rol ADMINISTRADOR
        if (empty($rnt) || empty($nit)) {
            die("RNT y NIT son obligatorios para el rol ADMINISTRADOR.");
        }
    }

    $stmt = $db->prepare(
        "INSERT INTO tp_empleados 
    (numDocumento, nombres, apellidos, direccion, fechaNacimiento, numTelefono, contactoPersonal, 
    password, correo, rnt, nit, sexo, tipoDocumento, roles, estadoCivil, foto)
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );

    $stmt->execute([
        $numDocumento,
        $nombres,
        $apellidos,
        $direccion,
        $fechaNacimiento,
        $numTelefono,
        $contactoPersonal,
        $password,
        $correo,
        $rnt,
        $nit,
        $sexo,
        $tipoDocumento,
        $roles,
        $estadoCivil,
        $foto ? file_get_contents($foto['tmp_name']) : null // o guarda la ruta si subes el archivo
    ]);

    if ($stmt) {
        header("Location: ../../views/Usuarios/crearUsuario.php?mensaje=¡Usuario registrado exitosamente!");
        exit();
    } else {
        echo "Error al registrar el usuario.";
    }
}

// Actualizar usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'actualizar') {
    $emp_numDocumento = limpiar($_POST['emp_numDocumento'] ?? '');
    $emp_direccion = limpiar($_POST['emp_direccion'] ?? '');
    $emp_numTelefono = limpiar($_POST['emp_numTelefono'] ?? '');
    $emp_contactoPersonal = limpiar($_POST['emp_contactoPersonal'] ?? '');
    $emp_correo = limpiar($_POST['emp_correo'] ?? '');
    $emp_rol_roles = limpiar($_POST['emp_rol_roles'] ?? '');
    $emp_estcivemp_estadoCivil = limpiar($_POST['emp_estcivemp_estadoCivil'] ?? '');

    if (
        empty($emp_numDocumento) || empty($emp_direccion) || empty($emp_numTelefono) ||
        empty($emp_contactoPersonal) || empty($emp_correo) || empty($emp_rol_roles) || empty($emp_estcivemp_estadoCivil) ||
        !filter_var($emp_correo, FILTER_VALIDATE_EMAIL)
    ) {
        die("Por favor, completa todos los campos correctamente.");
    }

    $stmt = $db->prepare("UPDATE tp_empleados SET emp_direccion = ?, emp_numTelefono = ?, emp_contactoPersonal = ?, emp_correo = ?, emp_rol_roles = ?, emp_estcivemp_estadoCivil = ? WHERE emp_numDocumento = ?");
    if ($stmt->execute([$emp_direccion, $emp_numTelefono, $emp_contactoPersonal, $emp_correo, $emp_rol_roles, $emp_estcivemp_estadoCivil, $emp_numDocumento])) {
        echo "Usuario actualizado correctamente.";
    } else {
        echo "Error al actualizar el usuario.";
    }
}

// Eliminar usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'eliminar') {
    $emp_numDocumento = limpiar($_POST['emp_numDocumento'] ?? '');

    if (empty($emp_numDocumento)) {
        die("ID de usuario no proporcionado.");
    }

    $stmt = $db->prepare("DELETE FROM tp_empleados WHERE emp_numDocumento = ?");
    if ($stmt->execute([$emp_numDocumento])) {
        header("Location: ../crudUsuarios/crudUsuarios.php?mensaje=Usuario eliminado exitosamente");
        exit();
    } else {
        echo "Error al eliminar el usuario.";
    }
}
