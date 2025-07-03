<?php
echo "<p>Debug: El archivo Usuarios.php se está ejecutando</p>";
// Habilitar la visualización de errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../../config/conexionGlobal.php';

// Debug: Mostrar método y POST
echo "<p>Debug: Método: ". $_SERVER["REQUEST_METHOD"] ."</p>";
echo "<pre>Debug: POST: ".print_r($_POST, true)."</pre>";

$db = conexionDB(); // Usamos $db como la conexión PDO
if (!$db) {
    die("Error al conectar a la base de datos.");
}

// Función para limpiar datos
function limpiar($dato)
{
    return htmlspecialchars(trim($dato));
}
// Insertar usuario
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['accion']) && $_POST['accion'] === 'insertar') {

    // Validar y limpiar datos
    $numDocumento = limpiar($_POST['numDocumento'] ?? '');
    $nombres = trim((($_POST['primer_nombre'] ?? '') . ' ' . ($_POST['segundo_nombre'] ?? '')));
    $apellidos = trim((($_POST['primer_apellido'] ?? '') . ' ' . ($_POST['segundo_apellido'] ?? '')));
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
        || empty($correo) || empty($sexo) || empty($roles) || empty($estadoCivil)
    ) {
        die("<p>Debug: Faltan campos obligatorios</p>");
    }
    // Validación específica para ADMINISTRADOR
    if ($roles === '1') { // Asumiendo que '1' es el ID del rol ADMINISTRADOR
        if (empty($rnt) || empty($nit)) {
            die("<p>Debug: Faltan RNT o NIT para administrador</p>");
        }
    }

    try {
        $stmt = $db->prepare(
            "INSERT INTO tp_empleados 
        (numDocumento, nombres, apellidos, direccion, fechaNacimiento, numTelefono, contactoPersonal, 
        password, correo, rnt, nit, sexo, tipoDocumento, roles, estadoCivil, foto)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
        );
        echo "<p>Debug: Consulta preparada</p>";

        $ejecutado = $stmt->execute([
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
        echo "<p>Debug: Consulta ejecutada</p>";

        if ($ejecutado) {
            echo "<h2 style='color:green'>Usuario registrado correctamente. Puedes volver atrás.</h2>";
             header("Location: ../../views/Usuarios/crearUsuario.php?mensaje=¡Usuario registrado exitosamente!");
            // exit();
        } else {
            echo "<p>Debug: Error al registrar el usuario (execute devolvió false)</p>";
        }
    } catch (PDOException $e) {
        echo "<p>Debug: Error en PDO - " . $e->getMessage() . "</p>";
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
?>