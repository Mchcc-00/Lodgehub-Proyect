<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear reserva</title>
    <link rel="stylesheet" href=""> <!-- Enlaza el archivo CSS -->
</head>
<body>
    <div class="container">
        <h2>Nueva Reserva</h2>
        <div class="search-container">
            <form id="buscadorHuesped" action="buscarHuespedExist.php" method="GET">
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
    <script src="scripts.js"></script>
</html>