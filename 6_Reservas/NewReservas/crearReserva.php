<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear reserva</title>
</head>
<body>
    <h2>Nueva Reserva</h2>
    <form id="buscadorHuesped" action="buscarHuespedExist.php" method="GET">
        <label for="buscarHuesped">Buscar huesped<input type="text" id="buscarHuesped" placeholder="Documento Huesped" name="buscarHuesped" minlength="10" maxlength="15" required></label>
        <button id="btnBuscarHuesped" type="submit">Buscar</button>
    </form>
    <button type="button" id="registrarHuesped">Nuevo Huesped</button>

    <div id="infoHuesped"></div>

    

    
</body>
    <script src="scripts.js"></script>
</html>