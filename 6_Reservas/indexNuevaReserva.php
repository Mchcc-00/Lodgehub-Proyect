<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Reservas</title>
</head>

<body>

    <div class="navbar">
        <div class="left">
            <img src="../public/img/FLECHA.png" alt="Atras" id="flechaNav">
            <h1>LODGEHUB</h1>
        </div>
        <div class="right">
            <img src="../public/img/iconoPerfil.png" alt="Perfil" id="IconUserNav">
        </div>
    </div>

    <div class="container">
        <aside>
            <div class="Lateral">
                <div class="ListaElementos">
                    <ul>
                        <li>
                            <a href="indexReservasmain.php" class="paginaActual">RESERVAS</a>
                        </li>
                        <li>
                            <a href="indexHabitaciones.html" class="otrasPaginas">HABITACIONES</a>
                        </li>
                        <li>
                            <a href="indexMantenimiento.html" class="otrasPaginas">MANTENIMIENTO</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="linea-horizontalSeparadorIconos"></div>
                    <div class="ListaElementosIconos">
                        <div class="iconoPQRS">
                            <img src="../public/img/iconoPQRS.png" alt="PQRS" id="IconPQRS">
                        </div>
                        <div class="iconoAjustes">
                            <img src="../public/img/tuercaAjustes.png" alt="Ajustes" id="IconAjustes">
                        </div>
                    </div>
                </div>
            </div>
        </aside>

        <div class="content">
            <form action="procesar_formulario.php" method="POST">
                <h2>Nueva Reserva</h2>

                <div class="linea-horizontalContent"></div>

                <h3>DATOS PERSONALES</h3>
                <div class="campos">

                    <div class="campo1Personal">
                        <div class="campo">
                            <label for="nombreHuesped">Nombres</label>
                            <input type="text" id="nombreHuesped" name="NombresHuesped" required>
                        </div>
                    </div>
                    <div class="campo2Personal">
                        <div class="campo">
                            <label for="ApellidoHuesped">Apellidos</label>
                            <input type="text" id="ApellidoHuesped" name="ApellidosHuesped" required>
                        </div>
                    </div>
                    <div class="campo3Personal">
                        <div class="campo">
                            <label for="tipoDocumentoHuesped">Tipo Documento</label>
                            <select id="tipoDocumentoHuesped" name="TipDocumentoHuesped" required>
                                <option value="" disabled selected></option>
                                <option value="1">CC</option>
                                <option value="2">TI</option>
                                <option value="3">CE</option>
                                <option value="4">PAS</option>
                                <option value="5">RC</option>
                            </select>
                        </div>
                    </div>
                    <div class="campo4Personal">
                        <div class="campo">
                            <label for="NumDocumentoHuesped">Nº Documento</label>
                            <input type="text" id="NumDocumentoHuesped" maxlength="10" name="NumeroDocumentoHuesped"
                                required>
                        </div>
                    </div>
                    <div>
                        <div class="campo">
                            <label for="SexHuesped">Sexo</label>
                            <select name="SexoHuesped" id="SexHuesped">
                                <option value="" disabled selected></option>
                                <option value="1">Hombre</option>
                                <option value="2">Mujer</option>
                                <option value="3">Otro</option>
                                <option value="4">Prefiero no decir</option>
                            </select>
                        </div>
                    </div>
                    <div>
                        <div class="campo">
                            <label for="EstCivHuespedes">Estado civil</label>
                            <select name="EstadoCivilHuespedes" id="EstCivHuespedes">
                                <option value="" disabled selected></option>
                                <option value="1">Soltero/a</option>
                                <option value="2">Casado/a</option>
                                <option value="3">Viudo/a</option>
                                <option value="4">Union libre</option>
                            </select>
                        </div>
                    </div>
                    <div class="campo5Personal">
                        <div class="campo">
                            <label for="NumContactoHuesped">Número de contacto</label>
                            <input type="text" id="NumContactoHuesped" maxlength="10" name="ContactoHuesped"
                                pattern="\d{7,10}" required>
                        </div>
                    </div>
                    <div class="campo6Personal">
                        <div class="campo">
                            <label for="CorreoHuesped">Correo electronico</label>
                            <input type="text" id="CorreoHuesped" name="CorrHuesped" required>
                        </div>
                    </div>
                </div>

                <h3>INFORMACIÓN HOSPEDAJE</h3>

                <div class="linea-horizontalContent2"></div>

                <div class="campos">
                    <div class="campo">
                        <label for="fechaInicioReserva">Fecha inicio</label>
                        <input type="date" id="fechaInicioReserva" name="FechaIniReserva" required>
                    </div>
                    <div class="campo">
                        <label for="fechaFinReserva">Fecha salida</label>
                        <input type="date" id="fechaFinReserva" name="FecFinReserva" required>
                    </div>

                    <div class="CamposCapacidades">
                        <div class="campos2">
                            <h4>Número de personas</h4>
                            <label for="CantidadAdultos">Adultos</label>
                            <input type="text" id="CantidadAdultos" name="CantidadAdultosRes" maxlength="2" required>
                            <label for="CantidadMenores">Menores</label>
                            <input type="text" id="CantidadMenores" name="CantidadMenoresRes" maxlength="2" required>
                            <label for="CantidadDiscapacitados">Discapacitados</label>
                            <input type="text" id="CantidadDiscapacitados" name="CantidadDiscapacitadosRes"
                                maxlength="2" required>
                        </div>
                    </div>
                    <div class="campo">
                        <label for="MotReservaHuesped">Motivo reserva</label>
                        <select name="MotivoReservaHuesped" id="MotReservaHuesped" required>
                            <option value="" disabled selected></option>
                            <option value="1">Negocios</option>
                            <option value="2">Personal</option>
                            <option value="3">Viaje</option>
                            <option value="4">Familiar</option>
                        </select>
                    </div>

                    <div class="campoPago">
                        <div>
                            <div class="campos2">
                                <h4>Habitación</h4>
                                <label for="NumeroHabitacion">Nº</label>
                                <input type="text" id="NumeroHabitacion" name="NumeroHabitacionHuesped" maxlength="4"
                                    required>
                            </div>
                            <div class="camposPago">
                                <div class="campo">
                                    <label for="MetodosPago">Metodo de pago</label>
                                    <select id="MetodosPago" name="TipoMetodoPago" required>
                                        <option value="" disabled selected></option>
                                        <option value="1">Tarjeta</option>
                                        <option value="2">Efectivo</option>
                                        <option value="3">PSE</option>
                                    </select>
                                </div>
                                <div class="campo">
                                    <label for="">Total a pagar</label>
                                    <input type="text" id="TotalPagoHuespued" name="PagoTotalReserva" required>
                                </div>
                            </div>
                            <div class="campoEmpleado">
                                <div class="campo">
                                    <label for="NumDocumentoEmpleado">Empleado que registra</label>
                                    <input type="text" id="NumDocumentoEmpleado" name="NumeroDocumentoEmpleado"
                                        maxlength="10" pattern="\d{7,10}" required>
                                </div>
                            </div>
                        </div>
                        <div>
                            <div class="campoTextArea">
                                <div class="campo">
                                    <h4>Información adicional</h4>
                                    <textarea name="InformacionAdicionalReserva" id="InformacionAdicional"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="botones-derecha">
                    <input type="button" value="Cancelar" id="BotonCancelarReserva" name="BotonCancelarRes">
                    <input type="button" value="Reservar" id="BotonReservarReserva" name="BotonReservarRes">
                </div>
                <div class="linea-horizontalContent"></div>
            </form>
        </div>
</body>

<script src="js/scripts.js"></script>

</html>