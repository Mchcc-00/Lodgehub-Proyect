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
            <img src="iconos/iconos/FLECHA.png" alt="Atras" id="flechaNav">
            <h1>LODGEHUB</h1>
        </div>
        <div class="right">
            <img src="iconos/iconos/iconoPerfil.png" alt="Perfil" id="IconUserNav">
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
                            <img src="iconos/iconos/iconoPQRS.png" alt="PQRS" id="IconPQRS">
                        </div>
                        <div class="iconoAjustes">
                            <img src="iconos/iconos/tuercaAjustes.png" alt="Ajustes" id="IconAjustes">
                        </div>
                </div>
            </div>
            </div>
        </aside>

        <div class="content">

<!-- El Modal -->
<div id="miModal" class="modal">


    <div class="division1Informacion">
    
    <h2>Reserva N°</h2>

    <span class="cerrar" onclick="cerrarModal()">&times;</span>

    </div>
    <div class="division2Informacion">
    <h3>Datos del huesped</h3>
    <?php include 'informacionCompletaReserva.php'; ?>
    </div>
    <div class="division3Informacion">
        <h3>Información hospedaje</h3>
        <div class="informacionReservaContenido">
        <div>
            <p>Fecha inicio:</p>
            <p>Fecha fin:</p>
            <p>Habitación:</p>
            <p>Metodo de pago:</p>
        </div>
        <div>
            <p>Numero de personas</p>
            <div>

            </div>
            <p>Total:</p>
        </div>
        </div>
    </div>

    <div class="division4Informacion">
        <div class="informacionReservaContenido">
        <p>Notas extra</p>
        <div>
            <p></p>
        </div>
        </div>
    </div>



</div>

                <img src="iconos/iconos/BotonAgregar.png" id="NuevaReservaForm" alt="Agregar Reserva" style="cursor:pointer;">

            <?php include 'mostrarTablaReservas.php'; ?>
        </div>
    </div>

</body>

    <script src="js/ventanasEmergentes.js"></script>

</html>