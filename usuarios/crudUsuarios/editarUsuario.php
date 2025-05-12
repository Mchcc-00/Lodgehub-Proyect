<?php
require_once '../database.php';

if (isset($_GET['dni'])) {
    $dni = intval($_GET['dni']);

    // Consulta para obtener los datos del usuario
    $stmt = $conn->prepare("SELECT * FROM empleados WHERE dni = ?");
    $stmt->bind_param("i", $dni);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        // Aquí puedes cargar los datos en el formulario para editar
    } else {
        echo "Usuario no encontrado.";
    }

    $stmt->close();
} else {
    echo "ID de usuario no proporcionado.";
}

$conn->close();
?>