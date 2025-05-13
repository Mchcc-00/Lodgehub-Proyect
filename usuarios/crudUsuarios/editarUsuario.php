<?php
<<<<<<< HEAD

require_once '../database.php';

if (isset($_GET['emp_numDocumento'])) {
    $dni = intval($_GET['emp_numDocumento']);

    // Consulta para obtener los datos del usuario
    $stmt = $conn->prepare("SELECT * FROM tp_empleados WHERE emp_numDocumento = ?");
    $stmt->bind_param("i", $tp_emp_numDocumento);
=======
require_once '../database.php';

if (isset($_GET['dni'])) {
    $dni = intval($_GET['dni']);

    // Consulta para obtener los datos del usuario
    $stmt = $conn->prepare("SELECT * FROM empleados WHERE dni = ?");
    $stmt->bind_param("i", $dni);
>>>>>>> 339a6896b6f89119da69892257db6648299ed025
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
<<<<<<< HEAD
    } else {
        echo "Usuario no encontrado.";
        exit;
=======
        // Aquí puedes cargar los datos en el formulario para editar
    } else {
        echo "Usuario no encontrado.";
>>>>>>> 339a6896b6f89119da69892257db6648299ed025
    }

    $stmt->close();
} else {
    echo "ID de usuario no proporcionado.";
<<<<<<< HEAD
    exit;
}

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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuario</title>
</head>
<body>
    <h1>Editar Usuario</h1>
    <form method="POST">
        <label for="nombre">Nombre:</label>
        <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required><br>

        <label for="apellido">Apellido:</label>
        <input type="text" id="apellido" name="apellido" value="<?php echo htmlspecialchars($usuario['apellido']); ?>" required><br>

        <label for="email">Email:</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($usuario['email']); ?>" required><br>

        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>
=======
}

$conn->close();
?>
>>>>>>> 339a6896b6f89119da69892257db6648299ed025
