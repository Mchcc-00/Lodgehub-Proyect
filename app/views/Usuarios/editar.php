<h2 class="form-title">Editar Usuario</h2>

<form action="<?php echo BASE_URL; ?>/usuarios/actualizar" method="post" enctype="multipart/form-data">
    
    <input type="hidden" name="numDocumento" value="<?php echo htmlspecialchars($usuario['numDocumento']); ?>">
    
    <div class="form-grid">
        <div class="form-group">
            <label>Número de documento (No editable)</label>
            <input type="text" value="<?php echo htmlspecialchars($usuario['numDocumento']); ?>" disabled>
        </div>

        <div class="form-group">
            <label>Nombres (No editable)</label>
            <input type="text" value="<?php echo htmlspecialchars($usuario['nombres']); ?>" disabled>
        </div>
        
        <div class="form-group">
            <label>Apellidos (No editable)</label>
            <input type="text" value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" disabled>
        </div>

        <div class="form-group">
            <label for="numTelefono">Número de teléfono</label>
            <input type="tel" id="numTelefono" name="numTelefono" value="<?php echo htmlspecialchars($usuario['numTelefono']); ?>">
        </div>
        <div class="form-group">
            <label for="telEmergencia">Teléfono de emergencia</label>
            <input type="tel" id="telEmergencia" name="telEmergencia" value="<?php echo htmlspecialchars($usuario['telEmergencia']); ?>">
        </div>
         <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($usuario['correo']); ?>">
        </div>
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>">
        </div>
        <div class="form-group">
            <label for="sexo">Sexo</label>
            <select id="sexo" name="sexo">
                <option value="1" <?php if ($usuario['sexo'] == '1') echo 'selected'; ?>>Hombre</option>
                <option value="2" <?php if ($usuario['sexo'] == '2') echo 'selected'; ?>>Mujer</option>
                <option value="3" <?php if ($usuario['sexo'] == '3') echo 'selected'; ?>>Otro</option>
            </select>
        </div>
        
        </div>
    
    <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='<?php echo BASE_URL; ?>/usuarios/lista'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
    </div>
</form>

<footer class="form-footer">
    lodgehubgroup © 2025
</footer>