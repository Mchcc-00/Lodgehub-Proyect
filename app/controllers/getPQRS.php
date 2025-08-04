<?php
require_once '../../config/conexionGlobal.php';

// Función para conectar a la base de datos


header('Content-Type: application/json');

try {
    $conn = conexionDB();
    if (!$conn) {
        echo json_encode(["error" => "Error de conexión a la base de datos"]);
        exit;
    }

    $sql = "SELECT id, fecha, urgencia, categoria, tipo_pqrs, CONCAT(nombre, ' ', apellido) AS solicitante, empleado, estado, fecha_cierre FROM pqrs";
    $stmt = $conn->query($sql);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);
} catch (PDOException $e) {
    echo json_encode(["error" => $e->getMessage()]);
}
?>
