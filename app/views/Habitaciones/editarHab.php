<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Habitaciónes</title>
    <link rel="stylesheet" href="/LODGEHUB/public/assets/css/dashboardHab.css">
    <link rel="stylesheet" href="/LODGEHUB/public/assets/css/editarHab.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/lodgehub/app/views/layouts/nav.php"; ?>
<div class="form-container">
    


    <h2>Editar Habitación N° <?php echo htmlspecialchars($habitacion['numero']); ?></h2>
    <form action="/LODGEHUB/app/controllers/habitacionController.php" method="POST" autocomplete="off">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="numero_original" value="<?php echo htmlspecialchars($habitacion['numero']); ?>">
        <div class="formulario__grupos">
            <div class="formulario__grupo">
                <label for="numero">Número</label>
                <input type="number" id="numero" name="numero" required value="<?php echo htmlspecialchars($habitacion['numero']); ?>">
            </div>
            <div class="formulario__grupo">
                <label for="costo">Costo de reserva</label>
                <input type="number" id="costo" name="costo" required value="<?php echo intval($habitacion['costo']); ?>">
            </div>
            <div class="formulario__grupo">
                <label for="tipoHabitacion">Tipo</label>
                <select id="tipoHabitacion" name="tipoHabitacion" required>
                    <option value="">Seleccione</option>
                    <option value="1" <?php if($habitacion['tipoHabitacion']==1) echo 'selected'; ?>>Individual</option>
                    <option value="2" <?php if($habitacion['tipoHabitacion']==2) echo 'selected'; ?>>Doble</option>
                    <option value="3" <?php if($habitacion['tipoHabitacion']==3) echo 'selected'; ?>>Suite</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="tamano">Tamaño</label>
                <select id="tamano" name="tamano" required>
                    <option value="">Seleccione</option>
                    <option value="1" <?php if($habitacion['tamano']==1) echo 'selected'; ?>>Pequeña</option>
                    <option value="2" <?php if($habitacion['tamano']==2) echo 'selected'; ?>>Mediana</option>
                    <option value="3" <?php if($habitacion['tamano']==3) echo 'selected'; ?>>Grande</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="capacidad">Capacidad</label>
                <input type="number" id="capacidad" name="capacidad" required value="<?php echo htmlspecialchars($habitacion['capacidad']); ?>">
            </div>
            <div class="formulario__grupo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="1" <?php if($habitacion['estado']==1) echo 'selected'; ?>>Disponible</option>
                    <option value="2" <?php if($habitacion['estado']==2) echo 'selected'; ?>>Reservada</option>
                    <option value="3" <?php if($habitacion['estado']==3) echo 'selected'; ?>>En uso</option>
                </select>
            </div>
        </div>
        <div class="formulario__grupos">
            <div class="formulario__grupo info-adicional" style="flex:1;">
                <label for="informacionAdicional">Información adicional</label>
                <textarea id="informacionAdicional" name="informacionAdicional" rows="6"><?php echo htmlspecialchars($habitacion['informacionAdicional']); ?></textarea>
            </div>
        </div>
        <hr style="margin: 30px 0;">
        <div class="formulario__acciones">
            <button type="button" class="cancelar-boton" onclick="window.location.href='/LODGEHUB/app/controllers/habitacionController.php?accion=listar'">Cancelar</button>
            <button type="submit" class="create-boton">Actualizar</button>
        </div>
    </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>