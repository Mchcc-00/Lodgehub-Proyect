<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear reserva</title>
</head>
<body>
    <h2>Nueva Reserva</h2>
    <h3>Huesped</h3>
    <div id="buscadorHuesped">
        <input type="text" id="buscarHuesped" placeholder="Documento Huesped" name="buscarHuesped" pattern="\d+" maxlength="10" required>
        <button id="btnBuscarHuesped" type="button" name="btnBuscarHuesped">Buscar</button>
    </div>
    <button type="button" id="registrarHuesped" name="registrarHuesped">Nuevo Huesped</button>
    
</body>
    <script src="scripts.js"></script>
</html>