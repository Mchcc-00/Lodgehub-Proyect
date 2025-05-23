<?php
// Datos de conexión
$host = "localhost";
$usuario = "root";
$contrasena = "";
$bd = "reservas_pruebita";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $bd);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$conn->begin_transaction();

try {
    // Capturar los datos del formulario 
    $nombres = $_POST["NombresHuesped"];
    $apellidos = $_POST["ApellidosHuesped"];
    $tipoDocumento = $_POST["TipDocumentoHuesped"];
    $numeroDocumento = $_POST["NumeroDocumentoHuesped"];
    $sexo = $_POST["SexoHuesped"];
    $estadoCivil = $_POST["EstadoCivilHuespedes"];
    $NumeroContacto = $_POST["ContactoHuesped"];
    $correoElectronico = $_POST["CorrHuesped"];

    $numPerAdultos = $_POST["CantidadAdultosRes"];
    $numPerMenores = $_POST["CantidadMenoresRes"];
    $numPerDiscapacitados = $_POST["CantidadDiscapacitadosRes"];

    $fechaInicio = $_POST["FechaIniReserva"];
    $fechaFin = $_POST["FecFinReserva"];
    $motivoReserva = $_POST["MotivoReservaHuesped"];
    $numeroHabitacion = $_POST["NumeroHabitacionHuesped"];
    $metodoPago = $_POST["TipoMetodoPago"];
    $costoReserva = $_POST["PagoTotalReserva"];
    $infoAdicional = $_POST["InformacionAdicionalReserva"];
    $empleadoRegistra = $_POST["NumeroDocumentoEmpleado"];
    $estadoReserva = 1;

    // Insertar datos del número de personas en la tabla td_numeropersonasrerservas
    $sqlnumeroPersonasReserva = "INSERT INTO td_numeropersonasreservas (numPerRes_Adultos,numPerRes_Menores,numPerRes_Discapacitados)
                    VALUES (?,?,?)";
    $stmt = $conn->prepare($sqlnumeroPersonasReserva);
    $stmt->bind_param("iii",$numPerAdultos,$numPerMenores,$numPerDiscapacitados);
    $stmt->execute();

    $numPersonasId = $conn->insert_id;

    // Insertar datos del huésped en la tabla tp_huespedes
    $sqlHuesped = "INSERT INTO tp_huespedes (hue_numDocumento,hue_nombres,hue_apellidos,hue_tipDoc_tipoDocumento,hue_sex_sexo,hue_estCiv_estadoCivil,hue_numContacto,hue_correo)
                    VALUES (?,?,?,?,?,?,?,?)";
    $stmt = $conn->prepare($sqlHuesped);
    $stmt->bind_param("issiiiis",$numeroDocumento,$nombres,$apellidos,$tipoDocumento,$sexo,$estadoCivil,$NumeroContacto,$correoElectronico);
    $stmt->execute();

    // Insertar datos de la reserva en la tabla tp_reservas
    $sqlReserva = "INSERT INTO tp_reservas (res_fechaInicio, res_fechaFin, res_motRes_motivo, res_temhab_numHabitacion,res_metPagRes_metodoPago, res_costoReserva, res_informacionAdicional,res_temEmp_numDocumento, res_hue_numDocumento, res_numPerRes_id,res_estRes_estadoReserva) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sqlReserva);
    $stmt->bind_param("ssiiiisiiis",$fechaInicio, $fechaFin, $motivoReserva, $numeroHabitacion,$metodoPago, $costoReserva, $infoAdicional,$empleadoRegistra, $numeroDocumento, $numPersonasId,$estadoReserva);
    $stmt->execute();

    // Si ambas inserciones fueron exitosas, confirmar transacción
$conn->commit();

    header("Location: indexReservasmain.php");
    exit();



} catch (Exception $e) {
    // Si hay algún error, revertir los cambios
    $conn->rollback();
    echo "Error al realizar la reserva: " . $e->getMessage();
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>
