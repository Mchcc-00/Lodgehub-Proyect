<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Habitación</title>
    <link rel="stylesheet" href="../../../public/assets/css/editarHab.css">
</head>
<body>
<div class="form-container">
    <h2>Editar Habitación N° <?php echo htmlspecialchars($habitacion['numero']); ?></h2>
    <form action="../controllers/habitacionController.php" method="POST" autocomplete="off">
        <input type="hidden" name="accion" value="actualizar">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($habitacion['id']); ?>">
        <div class="formulario__grupos">
            <div class="formulario__grupo">
                <label for="numero">Número</label>
                <input type="number" id="numero" name="numero" required value="<?php echo htmlspecialchars($habitacion['numero']); ?>">
            </div>
            <div class="formulario__grupo">
                <label for="precio">Costo de reserva</label>
                <input type="number" id="precio" name="precio" required value="<?php echo htmlspecialchars($habitacion['precio']); ?>">
            </div>
            <div class="formulario__grupo">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo" required>
                    <option value="">Seleccione</option>
                    <option value="Individual" <?php if($habitacion['tipo']=='Individual') echo 'selected'; ?>>Individual</option>
                    <option value="Doble" <?php if($habitacion['tipo']=='Doble') echo 'selected'; ?>>Doble</option>
                    <option value="Suite" <?php if($habitacion['tipo']=='Suite') echo 'selected'; ?>>Suite</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="tamano">Tamaño</label>
                <select id="tamano" name="tamano" required>
                    <option value="">Seleccione</option>
                    <option value="Pequeña" <?php if($habitacion['tamano']=='Pequeña') echo 'selected'; ?>>Pequeña</option>
                    <option value="Mediana" <?php if($habitacion['tamano']=='Mediana') echo 'selected'; ?>>Mediana</option>
                    <option value="Grande" <?php if($habitacion['tamano']=='Grande') echo 'selected'; ?>>Grande</option>
                </select>
            </div>
            <div class="formulario__grupo">
                <label for="capacidad">Capacidad</label>
                <input type="number" id="capacidad" name="capacidad" required value="<?php echo htmlspecialchars($habitacion['capacidad']); ?>">
            </div>
            <div class="formulario__grupo">
                <label for="estado">Estado</label>
                <select id="estado" name="estado" required>
                    <option value="Disponible" <?php if($habitacion['estado']=='Disponible') echo 'selected'; ?>>Disponible</option>
                    <option value="Reservada" <?php if($habitacion['estado']=='Reservada') echo 'selected'; ?>>Reservada</option>
                    <option value="En uso" <?php if($habitacion['estado']=='En uso') echo 'selected'; ?>>En uso</option>
                </select>
            </div>
        </div>
        <div class="formulario__grupos">
            <div class="formulario__grupo info-adicional" style="flex:1;">
                <label for="informacionAdicional">Información adicional</label>
                <textarea id="informacionAdicional" name="informacionAdicional" rows="6"><?php echo htmlspecialchars($habitacion['informacionAdicional']); ?></textarea>
            </div>
        </div>
        <div class="formulario__acciones">
            <button type="button" class="cancelar-boton" onclick="window.location.href='../controllers/habitacionController.php?accion=listar'">Cancelar</button>
            <button type="submit" class="create-boton">Actualizar</button>
        </div>
    </form>
</div>
</body>
</html>