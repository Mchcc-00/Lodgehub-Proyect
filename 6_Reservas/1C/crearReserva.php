<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear reserva</title>
    <link rel="stylesheet" href="../styles.css"> 
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
</head>
<body>
    <div class="container">
        <button id="retornarMainReserva" style="cursor:pointer;">
            <img src="../../public/img/FLECHAmini.png" alt="Volver">
        </button>
        <h2>Nueva Reserva</h2>
        <div class="search-container">
            <form id="buscadorHuesped" action="buscarHuesped.php" method="GET">
                <fieldset>
                    <legend>Buscar huesped</legend>
                    <input type="text" id="buscarHuesped" placeholder="Documento Huesped" name="buscarHuesped" minlength="10" maxlength="15" required>
                    <button id="btnBuscarHuesped" type="submit">
                        <img src="../../public/img/LupaLight.png" alt="Lupa" id="iconoLupa">
                    </button>
                    <button type="button" id="registrarHuesped">Nuevo Huesped</button>
                </fieldset>
            </form>
        </div>

        <div id="infoHuesped"></div>

    </div>

</body>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="../scripts.js"></script>
</html>