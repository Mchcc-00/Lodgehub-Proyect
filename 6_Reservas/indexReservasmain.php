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
                            <a href="indexMantenimiento.html" class="otrasPaginas">MANTENIMIENT3re</a>
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


            <img src="../public/img/BotonAgregar.png" id="NuevaReservaForm" alt="Agregar Reserva" style="cursor:pointer;">

            <?php require_once 'mostrarTablaReservas.php';?>


            <?php require_once 'informacionCompletaReserva.php';?>
        </div>
    </div>

</body>

    <script src="js/ventanasEmergentes.js"></script>

</html>