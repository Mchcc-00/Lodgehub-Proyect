<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva reserva</title>
    <link rel="stylesheet" href="styles.css"> <!-- Enlaza el archivo CSS -->
</head>
<body>
    <div class="container">
    <h2>Nueva Reserva</h2>
        <form class="mostrarInfoHuesped" id="formRegistrarReserva" action="procesarFormulario.php" method="POST">
            <div class="tablaInfoHuesped" id="formularioHuesped">
                <h3>INFORMACIÓN HUESPED</h3>
                <fieldset id="campo1">
                    <label for="nombresHuesped">Nombres<input id="nombresHuesped" type="text" name="nombresHuesped" placeholder="Ingrese los nombres del huesped" required></label>
                    <label for="apellidosHuesped">Apellidos<input id="apellidosHuesped" type="text" name="apellidosHuesped" placeholder="Ingrese los apellidos del huesped" required></label>
                </fieldset>
                <fieldset id="campo2">
                    <label for="tipoDocumentoHuesped">
                        Tipo Documento
                        <select name="tipoDocumentoHuesped" id="tipoDocumentoHuesped" required>
                            <option value="" disabled selected>Seleccione</option>
                            <option value="1">Cedula de Ciudadania</option>
                            <option value="2">Tarjeta de Identidad</option>
                            <option value="3">Cedula de Extranjeria</option>
                            <option value="4">Pasaporte</option>
                            <option value="5">Registro Civil</option>
                        </select>
                    </label>
                    <label for="numDocumentoHuesped">Nº Documento<input id="numDocumentoHuesped" type="text" name="numDocumentoHuesped" minlength="10" maxlength="15" placeholder="Documento del huesped" required></label>
                    <label for="contactoHuesped">Contacto<input id="contactoHuesped" type="text" name="contactoHuesped" minlength="10" maxlength="15" placeholder="Contacto del huesped" required></label>
                    <label for="estadoCivilHuesped">
                        Estado Civil
                        <select name="estadoCivilHuesped" id="estadoCivilHuesped" required>
                            <option value="" disabled selected>Seleccione</option>
                            <option value="1">Soltero/a</option>
                            <option value="2">Casado/a</option>
                            <option value="3">Viudo/a</option>
                            <option value="4">Unión libre</option>
                        </select>
                    </label>
                </fieldset>
                <fieldset id="campo3">
                    <label for="emailHuesped">Correo<input id="emailHuesped" type="email" name="emailHuesped" placeholder="Correo electronico del huesped" required></label>
                </fieldset>
                <fieldset id="campo4">
                    <label for="sexoHuesped">
                        Sexo
                        <select name="sexoHuesped" id="sexoHuesped" required>
                            <option value="" disabled selected>Seleccione</option>
                            <option value="1">Hombre</option>
                            <option value="2">Mujer</option>
                            <option value="3">Otro</option>
                            <option value="4">Prefiero no decirlo</option>
                        </select>
                    </label>
                </fieldset>
            </div>
            <div id="formularioHospedaje">
                <h3>INFORMACIÓN HOSPEDAJE</h3>
                <div id="line"></div>
                    <fieldset class="label-arriba" id="campo5">
                        <label for="fechaInicio">Fecha inicio<input id="fechaInicio" type="date" name="fechaInicio" required></label>
                        <label for="fechaFin">Fecha salida<input id="fechaFin" type="date" name="fechaFin" required></label>
                        <label for="motivoReserva">
                            Motivo de la reserva
                            <select name="motivoReserva" id="motivoReserva" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="1">Negocios</option>
                                <option value="2">Personal</option>
                                <option value="3">Viaje</option>
                                <option value="4">Familiar</option>
                            </select>
                        </label>
                    </fieldset>
                    <fieldset id="campo6">
                        <legend>Habitación</legend>
                        <label for="numHabitacionReserva">Nº<input id="numHabitacionReserva" type="text" name="numHabitacionReserva" maxlength="3" placeholder="Numero de habitacion a reservar" required></label>
                    </fieldset>
                    <fieldset id="campo7">
                        <legend>Número de personas</legend>
                        <label for="numAdultos">Adultos<input id="numAdultos" type="number" name="numAdultos" min="1" max="10"></label>
                        <label for="numNinos">Niños<input id="numNinos" type="number" name="numNinos" min="1" max="10"></label>
                        <label for="numDiscapacitados">Discapacitados<input id="numDiscapacitados" type="number" name="numDiscapacitados" min="1" max="10"></label>
                    </fieldset>
                    <fieldset id="campo8">
                        <legend>Información adicional</legend>
                        <textarea name="infoAdicionalReserva" id="infoAdicionalReserva" rows="7" placeholder="Información necesaria a tener en cuenta o sugerencias"></textarea>
                    </fieldset>
                    <fieldset class="label-arriba" id="campo9">
                        <label for="metodoPago">
                            Metodo de pago
                            <select name="metodoPago" id="metodoPago" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="1">Tarjeta</option>
                                <option value="2">Efectivo</option>
                                <option value="3">PSE</option>
                            </select>
                        </label>
                        <label for="numEmpleadoReserva">Empleado que registra<input id="numEmpleadoReserva" type="text" name="numEmpleadoReserva" minlength="10" maxlength="15" placeholder="Ingrese su numero de documento" required></label>
                        <label for="totalPago">Total a pagar<input id="totalPago" type="text" name="totalPago" required></label>
                    </fieldset>
                <div id="line2"></div>
                <div id="botonesFormulario">
                    <button type="button" id="btnLimpiarFormulario">Limpiar formulario</button>
                    <button type="button" id="btnCancelarReserva">Cancelar</button>
                    <button type="submit" id="btnRegistrarReserva">Reservar</button>
                </div>
            </div>

        </form>
    </div>
</body>
    <script src="scripts.js"></script>
</html>