<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva reserva</title>
</head>
<body>
    <h2>Nueva Reserva</h2>
    
    <form id="formRegistrarReserva" action="" method="POST">
        <div id="formularioHuesped">
            <h3>INFORMACIÓN HUESPED</h3>
            <fieldset id="campo1">
                <label for="nombresHuesped">Nombres<input id="nombresHuesped" type="text" name="nombresHuesped" placeholder="Ingrese los nombres del huesped" required></label>
                <label for="apellidosHuesped">Apellidos<input id="apellidosHuesped" type="text" name="apellidosHuesped" placeholder="Ingrese los apellidos del huesped" required></label>
            </fieldset>
            <fieldset id="campo2">
                <label for="tipoDocumento">
                    Tipo Documento
                    <select name="tipoDocumento" id="tipoDocumento" required>
                        <option value="" disabled selected>Seleccione</option>
                        <option value="1">Cedula de Ciudadania</option>
                        <option value="2">Tarjeta de Identidad</option>
                        <option value="3">Cedula de Extranjeria</option>
                        <option value="4">Pasaporte</option>
                        <option value="5">Registro Civil</option>
                    </select>
                </label>
                <label for="numDocumento">Nº Documento<input id="numDocumento" type="text" name="numDocumento" minlength="10" maxlength="15" placeholder="Documento del huesped" required></label>
                <label for="contacto">Contacto<input id="contacto" type="text" name="contacto" minlength="10" maxlength="15" placeholder="Contacto del huesped" required></label>
                <label for="estadoCivil">
                    Estado Civil
                    <select name="estadoCivil" id="estadoCivil" required>
                        <option value="" disabled selected>Seleccione</option>
                        <option value="1">Soltero/a</option>
                        <option value="2">Casado/a</option>
                        <option value="3">Viudo/a</option>
                        <option value="4">Unión libre</option>
                    </select>
                </label>
            </fieldset>
            <fieldset id="campo3">
                <label for="email">Correo<input id="email" type="email" name="email" placeholder="Correo electronico del huesped" required></label>
            </fieldset>
            <fieldset id="campo4">
                <label for="sexo">
                    Sexo
                    <select name="sexo" id="sexo" required>
                        <option value="" disabled selected>Seleccione</option>
                        <option value="1">Hombre</option>
                        <option value="2">Mujer</option>
                        <option value="3">Otro</option>
                    </select>
                </label>
            </fieldset>
        </div>
        <div id="formularioHospedaje">
            <h3>INFORMACIÓN HOSPEDAJE</h3>
            <div id="line"></div>
                <fieldset id="campo5">
                    <label for="fechaInicio">Fecha inicio<input id="fechaInicio" type="date" name="fechaInicio" required></label>
                    <label for="fechaFin">Fecha salida<input id="fechaFin" type="date" name="fechaFin" required></label>
                </fieldset>
                <fieldset id="campo6">
                    <legend>Habitación</legend>
                    <label for="numHabitacion">Nº<input id="numHabitacion" type="text" name="numHabitacion" maxlength="3" placeholder="Numero de habitacion a reservar" required></label>
                </fieldset>
                <fieldset id="campo7">
                    <legend>Número de personas</legend>
                    <label for="numAdultos">Adultos<input id="numAdultos" type="number" name="numAdultos" min="0" max="10" maxlength="2"></label>
                    <label for="numNinos">Niños<input id="numNinos" type="number" name="numNinos" min="0" max="10" maxlength="2"></label>
                    <label for="numDiscapacitados">Discapacitados<input id="numDiscapacitados" type="number" name="numDiscapacitados" min="0" max="10" maxlength="2"></label>
                </fieldset>
                <fieldset id="campo8">
                    <legend>Información adicional</legend>
                    <textarea name="infoAdicional" id="infoAdicional" rows="7" placeholder="Información necesaria a tener en cuenta o sugerencias"></textarea>
                </fieldset>
                <fieldset id="campo9">
                    <label for="metodoPago">
                        Metodo de pago
                        <select name="metodoPago" id="metodoPago" required>
                            <option value="" disabled selected>Seleccione</option>
                            <option value="1">Tarjeta</option>
                            <option value="2">Efectivo</option>
                            <option value="3">PSE</option>
                        </select>
                    </label>
                    <label for="numEmpleado">Empleado que registra<input id="numEmpleado" type="text" name="numEmpleado" minlength="10" maxlength="15" placeholder="Ingrese su numero de documento" required></label>
                    <label for="totalPago">Total a pagar<input id="totalPago" type="text" name="totalPago" required></label>
                </fieldset>
            <div id="line"></div>
        </div>
        <button type="submit" id="btnRegistrarReserva">Reservar</button>
    </form>

    
    <button type="button" id="btnCancelarReserva">Cancelar</button>

</body>
    <script src="scripts.js"></script>
</html>