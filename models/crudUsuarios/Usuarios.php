<?php

require_once '../database.php';
// Insertar/Crear
// Crear un nuevo usuario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nombres = trim($_POST['nombres'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    if (empty($nombres) || empty($correo)) {
        die("Por favor, completa todos los campos obligatorios.");
    }

    $stmt = $conn->prepare("INSERT INTO tp_empleados (nombres, correo) VALUES (?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("ss", $nombres, $correo);

    if ($stmt->execute()) {
        // Redirige al formulario con un mensaje de éxito
        header("Location: crearUsuario.html?mensaje=Usuario registrado exitosamente");
        exit();
    } else {
        echo "Error al registrar el usuario: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
//Eliminar/Delete
// Eliminar un usuario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $numDocumento = ($_POST['numDocumento'] ?? null);

    if ($numDocumento) {
        // Preparar la consulta para eliminar el registro
        $stmt = $conn->prepare("DELETE FROM empleados WHERE numDocumento = ?");
        if ($stmt) {
            $stmt->bind_param("s", $numDocumento);
            if ($stmt->execute()) {
                // Redirigir con un mensaje de éxito
                header("Location: ../crudUsuarios/crudUsuarios.php?mensaje=Usuario eliminado exitosamente");
                exit();
            } else {
                echo "Error al eliminar el usuario: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error en la preparación de la consulta: " . $conn->error;
        }
    } else {
        echo "ID de usuario no proporcionado.";
    }
}


$conn->close();

if (isset($_GET['emp_numDocumento'])) {
    $numDocumento = intval($_GET['emp_numDocumento']);

    // Consulta para obtener los datos del usuario
    $stmt = $conn->prepare("SELECT * FROM tp_empleados WHERE emp_numDocumento = ?");
    $stmt->bind_param("i", $tp_emp_numDocumento);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
    } else {
        echo "Usuario no encontrado.";
        exit;
    }

    $stmt->close();
} else {
    echo "ID de usuario no proporcionado.";
    exit;
}
//Editar/Actualizar
// Procesar el formulario de edición
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $emp_direccion = $_POST['emp_direccion'];
    $emp_numTelefono = $_POST['emp_numTelefono'];
    $emp_contactoPersonal = $_POST['emp_contactoPersonal'];
    $emp_correo = $_POST['emp_correo'];
    $emp_rol_roles = $_POST['emp_rol_roles'];
    $emp_estcivemp_estadoCivil = $_POST['emp_estcivemp_estadoCivil'];

    // Validar los datos (puedes agregar más validaciones según sea necesario)
    if (!empty($emp_direccion) && !empty($emp_numTelefono) && !empty($emp_contactoPersonal) && filter_var($emp_correo, FILTER_VALIDATE_EMAIL) && !empty($emp_rol_roles) && !empty($emp_estcivemp_estadoCivil)) {
        // Actualizar los datos en la base de datos
        $stmt = $conn->prepare("UPDATE tp_empleados SET emp_direccion = ?, emp_numTelefono = ?,  emp_contactoPersonal = ?, emp_correo = ?, emp_rol_roles = ?,  emp_estcivemp_estadosCivil WHERE emp_numDocumento = ?");
        $stmt->bind_param("ssssssi", $emp_direccion, $emp_numTelefono, $emp_contactoPersonal, $emp_correo, $emp_rol_roles, $emp_estcivemp_estadoCivil, $emp_numDocumento);
        

        if ($stmt->execute()) {
            echo "Usuario actualizado correctamente.";
        } else {
            echo "Error al actualizar el usuario.";
        }

        $stmt->close();
    } else {
        echo "Por favor, completa todos los campos correctamente.";
    }
}

$conn->close();
?>