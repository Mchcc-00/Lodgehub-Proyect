<?php

require_once '../../config/conexionGlobal.php';

$db = conexionDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['idReserva'];

    try {

        $sql = "DELETE FROM tp_reservas WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();


        // Redirigir después de eliminar
        header("Location: ../2R/mainReservas.php?eliminado=ok");
        exit();

    } catch (PDOException $e) {
        echo "Error al eliminar la reserva: " . $e->getMessage();
    }
}
?>