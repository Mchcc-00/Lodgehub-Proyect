<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/assets/css/editarHab.css">
    <title>Nueva Habitación</title>
</head>
<body>
<div class="form-container">
    <div class="titulo">Nueva habitación</div>
    <hr>
    <form action="../controllers/habitacionController.php" method="POST" autocomplete="off">
        <input type="hidden" name="accion" value="crear">
        <div class="formulario__grupos">
            <div class="formulario__grupo">
                <label for="numero">Número de la habitación</label>
                <input type="number" id="numero" name="numero" min="1" required placeholder="Ej: 101">
            </div>
            <div class="formulario__grupo">
                <label for="precio">Costo de reserva</label>
                <input type="number" id="precio" name="precio" min="0" step="1000" required placeholder="Ej: 150000">
            </div>
            <div class="formulario__grupo">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Seleccione</option>
                    <option value="Individual">Individual</option>
                    <option value="Doble">Doble</option>
                    <option value="Suite">Suite</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="tamano">Tamaño</label>
                <select id="tamano" name="tamano" required>
                    <option value="">Seleccione</option>
                    <option value="Pequeña">Pequeña</option>
                    <option value="Mediana">Mediana</option>
                    <option value="Grande">Grande</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="capacidad">Capacidad</label>
                <input type="number" id="capacidad" name="capacidad" min="1" max="10" required placeholder="Ej: 2">
            </div>
            <div class="formulario__grupo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="disponible">Disponible</option>
                    <option value="reservada">Reservada</option>
                    <option value="en-uso">En uso</option>
                </select>
            </div>
        </div>
        <div class="formulario__grupos">
            <div class="formulario__grupo info-adicional" style="flex:1;">
                <label for="info">Información adicional</label>
                <textarea id="info" name="info" rows="6" maxlength="500" placeholder="Detalles, observaciones, etc."></textarea>
            </div>
        </div>
        <div class="formulario__acciones">
            <button type="button" class="cancelar-boton" onclick="window.location.href='../../../../controllers/habitacionController.php?accion=listar'">Cancelar</button>
            <button type="submit" class="create-boton">Crear</button>
        </div>
    </form>
    <script src="../public/js/index.js"></script>
</div>
</body>
</html>