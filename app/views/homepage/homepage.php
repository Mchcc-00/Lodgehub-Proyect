<?php

    require_once '../login/validarSesion.php';

?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" href="../../../public/assets/css/homepage.css"> <!-- Enlaza el archivo CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">

    
</head>
    <?php
        include "../layouts/nav.php";
    ?>


<body>
    

    <div  class="d-flex justify-content-center">
        <div id="primercuadro" class="cuadro-principal">
            <!-- Primer cuadro -->
            
            <h4>NUEVOS | diarios</h4>
            <div class="row mt-1">
                <div class="col--4">
                    <div id="reservas" class="item">RESERVAS</div>
                </div>
                <div class="col--4">
                    <div id="mantenimiento" class="item">MANTENIMIENTO</div>
                </div>
                <div class="col--4">
                    <div id="pqrs" class="item">PQRS</div>
                </div>
            </div>
        </div>
        <div id="segundocuadro" class="cuadro-principal">
            <!-- Segundo cuadro -->
            <a href="../../../6_Reservas/2R/mainReservas.php"><h4>RESERVAS</h4></a>
            <div class="row">
                <div class="col-6">
                    <div class="item">Reservas que inician el dia de hoy</div>
                </div>
                <div class="col-6">
                    <div class="item">Reservas que finalizan el dia de hoy</div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-4">
                    <div class="item">Activas</div>
                </div>
                <div class="col-4">
                    <div class="item">Pendientes</div>
                </div>
                <div class="col-4">
                    <div class="item">Inactivas</div>
                </div>
            </div>
        </div>

    </div>

    <!-- fila de abajo -->


        <div class="d-flex justify-content-center">
        <div id="tercercuadro" class="cuadro-principal">
            <!-- Tercer cuadro -->
            <a href="../../../MANTENIMIENTO/views/dashboard.php"><h4>MANTENIMIENTO</h4></a>
            <div class="row">
                <div class="col-6">
                    <div class="item">Pendientes</div> 
                </div>
                <div class="col-6">
                    <div class="item">En proceso</div>
                </div>
            </div>

        </div>
        <div id="cuartocuadro" class="cuadro-principal">
            <!-- Cuarto cuadro -->
            <a href="../PQRS/index.php"><h4>PQRS</h4></a>
            <h6>PQRS pendientes</h6>
            <div class="row mt-1">
                <div class="col-4">
                    <div class="item">Alto</div>
                </div>
                <div class="col-4">
                    <div class="item">Medio</div>
                </div>
                <div class="col-4">
                    <div class="item">Bajo</div>
                </div>
                <a href="#" id="pqrsboton" > <button class="btn btn-info">Ver historial PQRS</button> </a>
            </div>
        </div>
    </div>
</div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
</body>

</html>