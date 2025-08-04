<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Listado Reservas</title>
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
                            <li><a class="dropdown-item" href="../../app/views/Usuarios/lista.php">Usuarios</a></li>
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

    <style>
        .container-fluid {
            background: #437bafff;
            padding: 20px;
        }

        #lodgebub-dropdown {
            color: #ffffffff;
            margin: 0;
            padding: 0;
            list-style: none;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>

    <div class="contenedorReservas">
        <button id="agregarNewReserva" style="cursor:pointer;">
            <img src="../../public/img/BotonAgregarTW.png" alt="Agregar Reserva">
        </button>

        <?php
        require_once 'ReadReservas.php';
        ?>

        <?php require_once 'ModalReservas.php'; ?>
    </div>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="../scripts.js"></script>
</body>

</html>