<?php

    require_once '../login/validarSesion.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" href="../../../public/assets/css/styles.css"> <!-- Enlaza el archivo CSS -->

</head>

<body>
    <?php
    include "../layouts/nav.php";
    ?>


    <div class="botoneshomepage">

        <div class="fondoprimeroboton">
            <a href="../../../6_Reservas/2R/mainReservas.php"> <button id="botonreservas"> RESERVAS</button></a>
        </div>
        <div class="fondocomprobanteboton">
            <a href="#"><button id="botonhabitaciones">HABITACIONES </button></a>
        </div>

    </div>

    <div class="botoneshomepage">

        <div class="fondosegundoboton">
            <a href="../views/PQRS/index.html"> <button id="botonmantenimiento"> MANTENIMIENTOS </button></a>
        </div>
        <div class="fondocomprobanteboton">
            <a href="../PQRS/index.html"><button id="botonpqrs">PQRS </button></a>
        </div>

    </div>

    <div class="container">
        <aside>
            <div class="Lateral">
                <div class="ListaElementos">
                    <ul>
                        <li>
                            <a href="../../../6_Reservas/2R/mainReservas.php" class="paginaActual">RESERVAS</a>
                        </li>
                        <li>
                            <a href="../../../HABITACIONES/views/dashboard.php" class="otrasPaginas">HABITACIONES</a>
                        </li>
                        <li>
                            <a href="../views/PQRS/index.html" class="otrasPaginas">MANTENIMIENTO</a>
                        </li>
                    </ul>
                </div>
                <div>
                    <div class="linea-horizontalSeparadorIconos"></div>
                    <div class="ListaElementosIconos">
                        <div class="iconoPQRS">

                            <a href="../PQRS/index.html"><img src="../public/img/iconoPQRS.png" alt="PQRS" id="IconPQRS"> </a>
                        </div>
                        <div class="iconoAjustes">
                            <img src="../public/img/tuercaAjustes.png" alt="Ajustes" id="IconAjustes">
                        </div>
                    </div>
                </div>
            </div>
        </aside>



</body>
</html>