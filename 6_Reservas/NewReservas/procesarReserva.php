<?php

require_once '../../config/conexionGlobal.php';


nuevaReserva();


    function nuevaReserva(){

    $db = conexionDB();

    try{

    $db->beginTransaction();

    $documentoHuesped = $_POST["documentoHuespedExist"];
    $costoReserva = $_POST["totalPagoExist"];
    $fechaInicioReserva = $_POST["fechaInicioExist"];
    $fechaFinReserva = $_POST["fechaFinExist"];
    $canAdultosReserva = $_POST["numAdultosExist"];
    $canNinosReserva = $_POST["numNinosExist"];
    $canDiscapacidadReserva = $_POST["numDiscapacitadosExist"];
    $motivoReserva = $_POST["motivoReservaExist"];
    $habitacionReserva = $_POST["numHabitacionReservaExist"];
    $metodoPagoReserva = $_POST["metodoPagoExist"];
    $infoAdicionalReserva = $_POST["infoAdicionalReservaExist"];
    $documentoEmpleado = $_POST["numEmpleadoReservaExist"];
    $estadoReserva = 1;


    $sql2 = "INSERT INTO  tp_reservas (costo, fechainicio, fechaFin, cantidadAdultos, cantidadNinos, cantidadDiscapacitados, motivoReserva, numeroHabitacion, metodoPago, informacionAdicional, emp_numdocumento, estado, hue_numdocumento) VALUES (:costo, :fechainicio, :fechaFin, :cantidadAdultos, :cantidadNinos, :cantidadDiscapacitados, :motivoReserva, :numeroHabitacion, :metodoPago, :informacionAdicional, :emp_numdocumento, :estado, :hue_numdocumento)";
    $stmt2 = $db->prepare($sql2);
    $stmt2->bindParam(':costo', $costoReserva, PDO::PARAM_STR);
    $stmt2->bindParam(':fechainicio', $fechaInicioReserva, PDO::PARAM_STR);
    $stmt2->bindParam(':fechaFin', $fechaFinReserva, PDO::PARAM_STR);
    $stmt2->bindParam(':cantidadAdultos', $canAdultosReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':cantidadNinos', $canNinosReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':cantidadDiscapacitados', $canDiscapacidadReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':motivoReserva', $motivoReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':numeroHabitacion', $habitacionReserva, PDO::PARAM_STR);
    $stmt2->bindParam(':metodoPago', $metodoPagoReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':informacionAdicional', $infoAdicionalReserva, PDO::PARAM_STR);
    $stmt2->bindParam(':emp_numdocumento', $documentoEmpleado, PDO::PARAM_STR);
    $stmt2->bindParam(':estado', $estadoReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':hue_numdocumento', $documentoHuesped, PDO::PARAM_STR);
    $stmt2->execute();


    $db->commit();

    header("Location: ../indexReservasmain.php");
    exit();


}catch (PDOException $e){

        $db->rollBack();
    echo "Error al realizar la reserva: " . $e->getMessage();


}
}

// Cerrar la conexión

?>