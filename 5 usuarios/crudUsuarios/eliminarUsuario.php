<?php

require_once '../database.php';
//Eliminar/Delete
// Eliminar un usuario
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $dni = ($_POST['dni'] ?? null);

    if ($dni) {
        // Preparar la consulta para eliminar el registro
        $stmt = $conn->prepare("DELETE FROM empleados WHERE dni = ?");
        if ($stmt) {
            $stmt->bind_param("s", $dni);
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
?>