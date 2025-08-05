<?php
require_once '../../config/conexionGlobal.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"] ?? null;
    $fecha = $_POST["fecha"] ?? null;
    $urgencia = $_POST["urgencia"] ?? null;
    $categoria = $_POST["categoria"] ?? null;
    $tipo_pqrs = $_POST["tipo_pqrs"] ?? null;
    $solicitante = $_POST["solicitante"] ?? null;
    $empleado = $_POST["registra"] ?? null; // Cambiado aquí
    $estado = $_POST["estado"] ?? null;

    if (!$id) {
        echo "❌ ID no proporcionado.";
        exit;
    }

    try {
        $conn = conexionDB();

        $sql = "UPDATE pqrs SET 
                    fecha = ?, 
                    urgencia = ?, 
                    categoria = ?, 
                    tipo_pqrs = ?, 
                    solicitante = ?, 
                    empleado = ?, 
                    estado = ?
                WHERE id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            $fecha,
            $urgencia,
            $categoria,
            $tipo_pqrs,
            $solicitante,
            $empleado,
            $estado,
            $id
        ]);

        // ✅ Redirecciona al CRUD principal después de editar
        header('Location: ../views/PQRS/crud.php');
        exit;

    } catch (PDOException $e) {
        echo "❌ Error al actualizar: " . $e->getMessage();
    }
}
?>
