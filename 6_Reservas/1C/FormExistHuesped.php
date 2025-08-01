<?php
require_once '../../config/conexionGlobal.php';

$huesped = null; // Inicializa la variable huesped

//Verificar si se recibió el documento del huésped por POST
if (isset($_POST['documentoHuesped'])) {
    $documentoHuesped = trim($_POST['documentoHuesped']);
    
    //Realizar la consulta a la base de datos (similar a buscarHuespedExist.php)
    $db = conexionDB();
    $sql = "SELECT h.numDocumento, h.numTelefono, h.correo, h.nombres, h.apellidos, td.descripcion as tipoDocumento, s.descripcion as sexo, ec.descripcion as estadoCivil FROM tp_huespedes AS h

    INNER JOIN td_tipoDocumento AS td ON h.tipoDocumento = td.id
    INNER JOIN td_sexo AS s ON h.sexo = s.id   
    INNER JOIN td_estadoCivil AS ec ON h.estadoCivil = ec.id
    
    WHERE numDocumento = (:numDocumento)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':numDocumento', $documentoHuesped, PDO::PARAM_STR);
    $stmt->execute();
    $huesped = $stmt->fetch(PDO::FETCH_ASSOC);

    // Si no se encuentra el huésped (aunque no debería pasar si viene del buscador exitoso)
    if (!$huesped) {
        // Redirigir o mostrar un error si el huésped no se encuentra

        echo "Error: No se encontró ningún huésped con el documento proporcionado.";
        exit(); // Detener la ejecución si hay un error crítico
    }   
} else {
    // Si no se recibió el documento (ej. alguien accede directamente a la URL)
    echo "Error: Documento del huésped no proporcionado.";
    exit(); // Detener la ejecución
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Reserva</title>
    <link rel="stylesheet" href="../styles.css"> 
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
    </head>
<body>
    <div class="container">
        <h2>Nueva Reserva</h2>
    <div class="mostrarInfoHuesped">
        <div class="tablaInfoHuesped">
            <h3>INFORMACIÓN DEL HUÉSPED</h3>
            <p><strong>Nombres: </strong><?php echo htmlspecialchars($huesped['nombres'] ?? '');?></p>
            <p><strong>Apellidos: </strong><?php echo htmlspecialchars($huesped['apellidos'] ?? '');?></p>
            <p><strong>Tipo Documento: </strong><?php echo htmlspecialchars($huesped['tipoDocumento'] ?? '');?></p>
            <p><strong>Documento: </strong><?php echo htmlspecialchars($huesped['numDocumento'] ?? '');?></p>
            <p><strong>Sexo: </strong><?php echo htmlspecialchars($huesped['sexo'] ?? '');?></p>
            <p><strong>Estado Civil: </strong><?php echo htmlspecialchars($huesped['estadoCivil'] ?? '');?></p>
            <p><strong>Contacto: </strong><?php echo htmlspecialchars($huesped['numTelefono'] ?? '');?></p>
            <p><strong>Correo: </strong><?php echo htmlspecialchars($huesped['correo'] ?? '');?></p>
        </div>

        <form id="formRegistrarReservaExist" action="proceFormExist.php" method="POST">
            <div id="formularioHospedajeExist">
                <h3>INFORMACIÓN DE HOSPEDAJE</h3>
                <div id="lineExist"></div>
                    <fieldset class="label-arriba" id="campo5Exist">
                        <label for="fechaInicioExist">Fecha inicio<input id="fechaInicioExist" type="date" name="fechaInicioExist" required></label>
                        <label for="fechaFinExist">Fecha salida<input id="fechaFinExist" type="date" name="fechaFinExist" required></label>
                        <label for="motivoReservaExist">
                            Motivo de la reserva
                            <select name="motivoReservaExist" id="motivoReservaExist" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="1">Negocios</option>
                                <option value="2">Personal</option>
                                <option value="3">Viaje</option>
                                <option value="4">Familiar</option>
                            </select>
                        </label>
                    </fieldset>
                    <fieldset id="campo6Exist">
                        <legend>Habitación</legend>
                        <label for="numHabitacionReservaExist">Nº<input id="numHabitacionReservaExist" type="text" name="numHabitacionReservaExist" maxlength="3" placeholder="Número de habitación a reservar" required></label>
                    </fieldset>
                    <fieldset id="campo7Exist">
                        <legend>Número de personas</legend>
                        <label for="numAdultosExist">Adultos<input id="numAdultosExist" type="number" name="numAdultosExist" min="1" max="10"></label>
                        <label for="numNinosExist">Niños<input id="numNinosExist" type="number" name="numNinosExist" min="0" max="10"></label>
                        <label for="numDiscapacitadosExist">Discapacitados<input id="numDiscapacitadosExist" type="number" name="numDiscapacitadosExist" min="0" max="10"></label>
                    </fieldset>
                    <fieldset id="campo8Exist">
                        <legend>Información adicional</legend>
                        <textarea name="infoAdicionalReservaExist" id="infoAdicionalReservaExist" rows="7" placeholder="Información necesaria a tener en cuenta o sugerencias"></textarea>
                    </fieldset>
                    <fieldset class="label-arriba" id="campo9Exist">
                        <label for="metodoPagoExist">
                            Método de pago
                            <select name="metodoPagoExist" id="metodoPagoExist" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="1">Tarjeta</option>
                                <option value="2">Efectivo</option>
                                <option value="3">PSE</option>
                            </select>
                        </label>
                        <label for="numEmpleadoReservaExist">Empleado que registra<input id="numEmpleadoReservaExist" type="text" name="numEmpleadoReservaExist" minlength="10" maxlength="15" placeholder="Ingrese su número de documento" required></label>
                        <label for="totalPagoExist">Total a pagar<input id="totalPagoExist" type="text" name="totalPagoExist" required></label>
                    </fieldset>
                <div id="lineExist2"></div>
            </div>
            <div id="botonesFormularioExist">
                <button type="button" id="btnLimpiarFormularioExist">Limpiar formulario</button>
                <button type="button" id="btnCancelarReservaExist">Cancelar</button>
                <button type="submit" id="btnRegistrarReservaExist">Reservar</button>
            </div>
            <input type="hidden" name="documentoHuespedExist" value="<?php echo htmlspecialchars($huesped['numDocumento'] ?? ''); ?>">
        </form>
    </div>   
        
    </div>
</body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="../scripts.js"></script>
</html>