<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva reserva</title>
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a id="lodgebub-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            LODGEHUB
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../../app/views/homepage/homepage.php">Home</a></li>
                            <li><a class="dropdown-item active" href="mainReservas.php">Reservas</a></li>
                            <li><a class="dropdown-item" href="../../../HABITACIONES/views/dashboard.php">Habitaciones</a></li>
                            <li><a class="dropdown-item" href="../../../MANTENIMIENTO/views/dashboard.php">Mantenimiento</a></li>
                            <li><a class="dropdown-item" href="../../../PQRS/views/dashboard.php">PQRS</a></li>
                        </ul>
                    </li>

                </ul>
                <form class="d-flex" role="perfil">

                    <a href="../../app/views/homepage/cerrarSesion.php" class="btn btn-danger">Cerrar sesión</a>
                </form>
            </div>
        </div>
    </nav>

    <div class="contenedorReservas">
        <div class="subContenedorFormsReservasNew">
            <h2>Nueva Reserva</h2>
            <form class="contenedorformsFlex contenedorformsAlign" id="formRegistrarReserva" action="proceFormNew.php" method="POST">
                <div class="fichaInfoHuespedNew" id="formularioHuesped">
                    <h3>INFORMACIÓN HUESPED</h3>
                    <div class="fichaFormHuesped">
                        <fieldset class="estiloFieldsetReservas" id="campo1">
                            <label for="nombresHuespedReservas">Nombres<input class="estilosFichaInputsHue especialestilosFichaInputsInfoHue" id="nombresHuespedReservas" type="text" name="nombresHuesped" placeholder="Ingrese los nombres del huesped" required></label>
                            <label for="apellidosHuesped">Apellidos<input class="estilosFichaInputsHue especialestilosFichaInputsInfoHue" id="apellidosHuespedReservas" type="text" name="apellidosHuesped" placeholder="Ingrese los apellidos del huesped" required></label>
                        </fieldset>
                        <fieldset class="estiloFieldsetReservas labelArriba" id="campo2">
                            <label for="tipoDocumentoHuesped">
                                Tipo Documento
                                <select class="estilosFichaInputsHue" name="tipoDocumentoHuesped" id="tipoDocumentoHuesped" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="1">Cedula de Ciudadania</option>
                                    <option value="2">Tarjeta de Identidad</option>
                                    <option value="3">Cedula de Extranjeria</option>
                                    <option value="4">Pasaporte</option>
                                    <option value="5">Registro Civil</option>
                                </select>
                            </label>
                            <label for="numDocumentoHuesped">Nº Documento<input class="estilosFichaInputsHue" id="numDocumentoHuesped" type="text" name="numDocumentoHuesped" minlength="10" maxlength="15" placeholder="Documento del huesped" required></label>
                            <label for="contactoHuesped">Contacto<input class="estilosFichaInputsHue" id="contactoHuesped" type="text" name="contactoHuesped" minlength="10" maxlength="15" placeholder="Contacto del huesped" required></label>
                            <label for="estadoCivilHuesped">
                                Estado Civil
                                <select class="estilosFichaInputsHue" name="estadoCivilHuesped" id="estadoCivilHuesped" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="1">Soltero/a</option>
                                    <option value="2">Casado/a</option>
                                    <option value="3">Viudo/a</option>
                                    <option value="4">Unión libre</option>
                                </select>
                            </label>
                        </fieldset>
                        <fieldset class="estiloFieldsetReservas" id="campo3">
                            <label for="emailHuesped">Correo<input class="estilosFichaInputsHue especialestilosFichaInputsInfoHue" id="emailHuesped" type="email" name="emailHuesped" placeholder="Correo electronico del huesped" required></label>
                        </fieldset>
                        <fieldset class="estiloFieldsetReservas labelArriba" id="campo4">
                            <label for="sexoHuesped">
                                Sexo
                                <select class="estilosFichaInputsHue" name="sexoHuesped" id="sexoHuesped" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="1">Hombre</option>
                                    <option value="2">Mujer</option>
                                    <option value="3">Otro</option>
                                    <option value="4">Prefiero no decirlo</option>
                                </select>
                            </label>
                        </fieldset>
                    </div>
                </div>
                <div class="FormMarginReserva" id="formularioHospedaje">
                    <h3>INFORMACIÓN HOSPEDAJE</h3>
                    <div class="lineaEncuadre" id="line"></div>
                    <div class="formReservaBloque">
                        <fieldset class="estiloFieldsetReservas labelArriba" id="campo5">
                            <label for="fechaInicio">Fecha inicio<input class="estiloDefaultInput" id="fechaInicio" type="date" name="fechaInicio" required></label>
                            <label for="fechaFin">Fecha salida<input class="estiloDefaultInput" id="fechaFin" type="date" name="fechaFin" required></label>
                            <label for="motivoReserva">
                                Motivo de la reserva
                                <select class="estiloDefaultInput" name="motivoReserva" id="motivoReserva" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="1">Negocios</option>
                                    <option value="2">Personal</option>
                                    <option value="3">Viaje</option>
                                    <option value="4">Familiar</option>
                                </select>
                            </label>
                        </fieldset>
                        <fieldset class="estiloFieldsetReservas" id="campo6">
                            <legend>Habitación</legend>
                            <label for="numHabitacionReserva">Nº<input class="estiloDefaultInput estiloInputHabitacionReserva espacioInputleft" id="numHabitacionReserva" type="text" name="numHabitacionReserva" maxlength="3" placeholder="Numero de habitacion a reservar" required></label>
                        </fieldset>
                        <fieldset class="estiloFieldsetReservas" id="campo7">
                            <legend>Número de personas</legend>
                            <label for="numAdultos">Adultos<input class="estiloDefaultInput estiloInputNumPersonas espacioInputleft" id="numAdultos" type="number" name="numAdultos" min="1" max="10"></label>
                            <label for="numNinos">Niños<input class="estiloDefaultInput estiloInputNumPersonas espacioInputleft" id="numNinos" type="number" name="numNinos" min="1" max="10"></label>
                            <label for="numDiscapacitados">Discapacitados<input class="estiloDefaultInput estiloInputNumPersonas espacioInputleft" id="numDiscapacitados" type="number" name="numDiscapacitados" min="1" max="10"></label>
                        </fieldset>
                        <fieldset class="estiloFieldsetReservas" id="campo8">
                            <legend>Información adicional</legend>
                            <textarea class="estiloDefaultInput especialestilosFichaInputsHue" name="infoAdicionalReserva" id="infoAdicionalReserva" rows="4" placeholder="Información necesaria a tener en cuenta o sugerencias"></textarea>
                        </fieldset>
                        <fieldset class="estiloFieldsetReservas labelArriba" id="campo9">
                            <label for="metodoPago">
                                Metodo de pago
                                <select class="estiloDefaultInput" name="metodoPago" id="metodoPago" required>
                                    <option value="" disabled selected>Seleccione</option>
                                    <option value="1">Tarjeta</option>
                                    <option value="2">Efectivo</option>
                                    <option value="3">PSE</option>
                                </select>
                            </label>
                            <label for="numEmpleadoReserva">Empleado que registra<input class="estiloDefaultInput" id="numEmpleadoReserva" type="text" name="numEmpleadoReserva" minlength="10" maxlength="15" placeholder="Ingrese su numero de documento" required></label>
                            <label for="totalPago">Total a pagar<input class="estiloDefaultInput" id="totalPago" type="text" name="totalPago" required></label>
                        </fieldset>
                    </div>
                    <div class="lineaEncuadre" id="line2"></div>
                    <div class="btnsFormReservasbloque" id="btnsFormRegistrarReserva">
                        <button class="estiloBtnFormReservas estiloBtnCleanReservas" type="button" id="btnLimpiarFormulario">Limpiar formulario</button>
                        <button class="estiloBtnFormReservas estiloBtnCancelReservas" type="button" id="btnCancelarReserva">Cancelar</button>
                        <button class="estiloBtnFormReservas estiloBtnConfirmReservas" type="submit" id="btnRegistrarReserva">Reservar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="../scripts.js"></script>
</body>

</html>