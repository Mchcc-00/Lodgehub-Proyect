<?php

require_once '../config/conexionGlobal.php';


nuevaReserva();


    function nuevaReserva(){

    $db = conexionDB();

    try{

    $db->beginTransaction();

    $documentoHuesped = $_POST["NumeroDocumentoHuesped"];
    $contactoHuesped = $_POST["ContactoHuesped"];
    $correoHuesped = $_POST["CorrHuesped"];
    $nombreHuesped = $_POST["NombresHuesped"];
    $apellidosHuesped = $_POST["ApellidosHuesped"];
    $tipDocumentoHuesped = $_POST["TipDocumentoHuesped"];
    $sexoHuesped = $_POST["SexoHuesped"];
    $estadoCivilHuesped = $_POST["EstadoCivilHuespedes"];


    $sql = "INSERT INTO tp_huespedes (numDocumento, numTelefono, correo, nombres, apellidos, tipoDocumento, sexo, estadoCivil) VALUES (:numDocumento, :numTelefono, :correo, :nombres, :apellidos, :tipoDocumento, :sexo, :estadoCivil)";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':numDocumento', $documentoHuesped, PDO::PARAM_INT);
    $stmt->bindParam(':numTelefono', $contactoHuesped, PDO::PARAM_INT);
    $stmt->bindParam(':correo', $correoHuesped, PDO::PARAM_STR);
    $stmt->bindParam(':nombres', $nombreHuesped, PDO::PARAM_STR);
    $stmt->bindParam(':apellidos', $apellidosHuesped, PDO::PARAM_STR);
    $stmt->bindParam(':tipoDocumento', $tipDocumentoHuesped, PDO::PARAM_INT);
    $stmt->bindParam(':sexo', $sexoHuesped, PDO::PARAM_INT);
    $stmt->bindParam(':estadoCivil', $estadoCivilHuesped, PDO::PARAM_INT);
    $stmt->execute();

    $costoReserva = $_POST["PagoTotalReserva"];
    $fechaInicioReserva = $_POST["FechaIniReserva"];
    $fechaFinReserva = $_POST["FecFinReserva"];
    $canAdultosReserva = $_POST["CantidadAdultosRes"];
    $canNinosReserva = $_POST["CantidadMenoresRes"];
    $canDiscapacidadReserva = $_POST["CantidadDiscapacitadosRes"];
    $motivoReserva = $_POST["MotivoReservaHuesped"];
    $habitacionReserva = $_POST["NumeroHabitacionHuesped"];
    $metodoPagoReserva = $_POST["TipoMetodoPago"];
    $infoAdicionalReserva = $_POST["InformacionAdicionalReserva"];
    $documentoEmpleado = $_POST["NumeroDocumentoEmpleado"];
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
    $stmt2->bindParam(':numeroHabitacion', $habitacionReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':metodoPago', $metodoPagoReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':informacionAdicional', $infoAdicionalReserva, PDO::PARAM_STR);
    $stmt2->bindParam(':emp_numdocumento', $documentoEmpleado, PDO::PARAM_INT);
    $stmt2->bindParam(':estado', $estadoReserva, PDO::PARAM_INT);
    $stmt2->bindParam(':hue_numdocumento', $documentoHuesped, PDO::PARAM_INT);
    $stmt2->execute();


    $db->commit();

    header("Location: indexReservasmain.php");
    exit();


}catch (PDOException $e){

        $db->rollBack();
    echo "Error al realizar la reserva: " . $e->getMessage();


}
}

// Cerrar la conexión

?>
