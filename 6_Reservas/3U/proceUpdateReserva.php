<?php

require_once '../../config/conexionGlobal.php';

$db = conexionDB();

    if (isset($_GET['emp_numDocumento'])) {
    $dni = intval($_GET['emp_numDocumento']);
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


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['idReserva'];

    try {

        $costoReserva = $_POST["totalPagoModif"];
        $fechaInicioReserva = $_POST["fechaInicioModif"];
        $fechaFinReserva = $_POST["fechaFinModif"];
        $canAdultosReserva = $_POST["numAdultosModif"];
        $canNinosReserva = $_POST["numNinosModif"];
        $canDiscapacidadReserva = $_POST["numDiscapacitadosModif"];
        $habitacionReserva = $_POST["numHabitacionModif"];
        $infoAdicionalReserva = $_POST["infoAdicionalModif"];
        $estadoReserva = $_POST["estadoReservaModif"];


        $sql = "UPDATE tp_reservas

        SET costo = :costo, fechainicio = :fechainicio, fechaFin = :fechaFin, cantidadAdultos = :cantidadAdultos, cantidadNinos = :cantidadNinos, cantidadDiscapacitados = :cantidadDiscapacitados, numeroHabitacion = :numeroHabitacion, informacionAdicional = :informacionAdicional, estado = :estado
        
        WHERE id = :id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':costo', $costoReserva, PDO::PARAM_STR);
        $stmt->bindParam(':fechainicio', $fechaInicioReserva, PDO::PARAM_STR);
        $stmt->bindParam(':fechaFin', $fechaFinReserva, PDO::PARAM_STR);
        $stmt->bindParam(':cantidadAdultos', $canAdultosReserva, PDO::PARAM_INT);
        $stmt->bindParam(':cantidadNinos', $canNinosReserva, PDO::PARAM_INT);
        $stmt->bindParam(':cantidadDiscapacitados', $canDiscapacidadReserva, PDO::PARAM_INT);
        $stmt->bindParam(':numeroHabitacion', $habitacionReserva, PDO::PARAM_STR);
        $stmt->bindParam(':informacionAdicional', $infoAdicionalReserva, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estadoReserva, PDO::PARAM_INT);
        $stmt->execute();


        // Redirigir después de eliminar
        header("Location: ../2R/mainReservas.php?modificado=ok");
        exit();
    } catch (PDOException $e) {
        echo "Error al modificar la reserva: " . $e->getMessage();
    }
}
?>