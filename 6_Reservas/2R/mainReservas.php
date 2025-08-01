<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Reservas</title>
    <link rel="stylesheet" href="../styles.css"> 
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
</head>
<body>
    <div class="container">
        <button id="agregarNewReserva" style="cursor:pointer;">
            <img src="../../public/img/BotonAgregarTW.png" alt="Agregar Reserva">
        </button>
        
        <?php 
            require_once 'ReadReservas.php'; 
        ?>

        <?php require_once 'ModalReservas.php';?>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="../scripts.js"></script>
</body>
</html>