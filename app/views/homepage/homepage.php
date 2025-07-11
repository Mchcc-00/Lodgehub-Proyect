<?php

    require_once '../login/validarSesion.php';

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>home</title>
    <link rel="stylesheet" href="../../../6_Reservas/styles.css"> <!-- Enlaza el archivo CSS -->

</head>
<body>

        <div class="navbar">
        <div class="left">
            <img src="../../public/img/FLECHA.png" alt="Atras" id="flechaNav">
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
                            <a href="../../../6_Reservas/indexReservasmain.php" class="paginaActual">RESERVAS</a>
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
        <div>
            <?php include '../login/navbar.php'?>
        </div>


</body>
</html>