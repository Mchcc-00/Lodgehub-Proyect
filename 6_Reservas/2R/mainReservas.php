<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Reservas</title>
    <link rel="stylesheet" href="../styles.css"> <!-- Enlaza el archivo CSS -->
</head>

<body>
        <div class="content">
            <button id="agregarNewReserva" style="cursor:pointer;">
                <img src="../../public/img/BotonAgregarTW.png" alt="Agregar Reserva">
            </button>
            

            <?php require_once 'ReadReservas.php';?>


            <?php require_once 'ModalReservas.php';?>
        </div>
</body>

    <script src="../scripts.js"></script>

</html>