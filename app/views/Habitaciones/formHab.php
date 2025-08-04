<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../../public/assets/css/dashboardHab.css">
    <link rel="stylesheet" href="../../../public/assets/css/editarHab.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Nueva Habitación</title>
</head>
<body>
    
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lodgehub/app/views/layouts/nav.php";
 if (isset($_SESSION['errores']) && count($_SESSION['errores']) > 0): ?>
    <div class="alert alert-danger">
        <?php foreach ($_SESSION['errores'] as $error): ?>
            <p><?php echo htmlspecialchars($error); ?></p>
        <?php endforeach; unset($_SESSION['errores']); ?>
    </div>
<?php endif; ?>
<div class="form-container">
    <div class="titulo">Nueva habitación</div>
    <hr>
    <form action="../../controllers/habitacionController.php" method="POST" autocomplete="off">
        <input type="hidden" name="accion" value="crear">
        <div class="formulario__grupos">
            <div class="formulario__grupo">
                <label for="numero">Número de la habitación</label>
                <input type="number" id="numero" name="numero" min="1" required placeholder="Ej: 101">
            </div>
            <div class="formulario__grupo">
                <label for="costo">Costo de reserva</label>
                <input type="number" id="costo" name="costo" min="0" step="1000" required placeholder="Ej: 150000">
            </div>
            <div class="formulario__grupo">
                <label for="tipoHabitacion">Tipo</label>
                <select id="tipoHabitacion" name="tipoHabitacion" required>
                    <option value="">Seleccione</option>
                    <option value="1">Individual</option>
                    <option value="2">Doble</option>
                    <option value="3">Triple</option>
                    <option value="4">Suite</option>
                    <option value="5">Confort</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="tamano">Tamaño</label>
                <select id="tamano" name="tamano" required>
                    <option value="">Seleccione</option>
                    <option value="1">Pequeño</option>
                    <option value="2">Medio</option>
                    <option value="3">Grande</option>
                    <option value="4">Extra Grande</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="capacidad">Capacidad</label>
                <input type="number" id="capacidad" name="capacidad" min="1" max="10" required placeholder="Ej: 2">
            </div>
            <div class="formulario__grupo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="1">Activo</option>
                    <option value="2">Inactivo</option>
                    <option value="3">En uso</option>
                    <option value="4">Finalizado</option>
                    <option value="5">Pendiente</option>
                    <option value="6">Cancelado</option>
                </select>
            </div>
        </div>
        <div class="formulario__grupos">
            <div class="formulario__grupo info-adicional" style="flex:1;">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="6" maxlength="500" placeholder="Detalles, observaciones, etc."></textarea>
            </div>
        </div>
        <div class="formulario__acciones">
            <button type="button" class="cancelar-boton" onclick="window.location.href='../../../app/views/Habitaciones/dashboardHab.php'">Cancelar</button>
            <button type="submit" class="create-boton">Crear</button>
        </div>
    </form>
    <script src="../public/js/index.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</div>
</body>
</html>