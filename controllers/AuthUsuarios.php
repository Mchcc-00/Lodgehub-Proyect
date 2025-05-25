<?php
require_once 'database.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $primer_nombre = trim($_POST['primer_nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    if (empty($primer_nombre) || empty($correo)) {
        die("Por favor, completa todos los campos obligatorios.");
    }

    $stmt = $conn->prepare("INSERT INTO empleados (primer_nombre, correo) VALUES (?, ?)");
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("ss", $primer_nombre, $correo);

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
?>