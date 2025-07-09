<h2 class="form-title">NUEVO USUARIO</h2>

<form action="<?php echo BASE_URL; ?>/usuarios/guardar" method="post" enctype="multipart/form-data">
    <input type="hidden" name="formulario" value="crearUsuario">
    <div class="form-grid">
        <div class="form-group">
            <label for="primer_nombre">Primer nombre</label>
            <input type="text" id="primer_nombre" name="primer_nombre" required>
        </div>
        <div class="form-group">
            <label for="segundo_nombre">Segundo nombre</label>
            <input type="text" id="segundo_nombre" name="segundo_nombre">
        </div>
        <div class="form-group">
            <label for="tipoDocumento">Tipo de documento</label>
            <select id="tipoDocumento" name="tipoDocumento" required>
                <option value="" disabled selected>Seleccionar...</option>
                <option value="1">Cédula de ciudadanía</option>
                <option value="2">Tarjeta de identidad</option>
                <option value="3">Cédula de extranjería</option>
                <option value="4">Pasaporte</option>
                <option value="5">Registro civil</option>
            </select>
        </div>
        <div class="form-group">
            <label for="numDocumento">Número de documento</label>
            <input type="text" id="numDocumento" name="numDocumento" required>
        </div>
        <div class="form-group">
            <label for="primer_apellido">Primer apellido</label>
            <input type="text" id="primer_apellido" name="primer_apellido" required>
        </div>
        <div class="form-group">
            <label for="segundo_apellido">Segundo apellido</label>
            <input type="text" id="segundo_apellido" name="segundo_apellido">
        </div>
        <div class="form-group">
            <label for="fechaNacimiento">Fecha de nacimiento</label>
            <input type="date" id="fechaNacimiento" name="fechaNacimiento">
        </div>
        <div class="form-group">
            <label for="sexo">Sexo</label>
            <select id="sexo" name="sexo">
                <option value="" disabled selected>Seleccionar...</option>
                <option value="1">Hombre</option>
                <option value="2">Mujer</option>
                <option value="3">Otro</option>
            </select>
        </div>
        <div class="form-group">
            <label for="correo">Correo electrónico</label>
            <input type="email" id="correo" name="correo" required>
        </div>
        <div class="form-group password-wrapper">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirmar_password">Confirmar contraseña</label>
            <input type="password" id="confirmar_password" name="confirmar_password" required>
        </div>
        <div class="form-group">
            <label for="numTelefono">Número de teléfono</label>
            <input type="tel" id="numTelefono" name="numTelefono">
        </div>
        <div class="form-group">
            <label for="telEmergencia">Teléfono de emergencia</label>
            <input type="tel" id="telEmergencia" name="telEmergencia">
        </div>
        <div class="form-group">
            <label for="direccion">Dirección</label>
            <input type="text" id="direccion" name="direccion">
        </div>
        <div class="form-group">
            <label for="roles">Rol</label>
            <select id="roles" name="roles" required>
                <option value="" disabled selected>Seleccionar...</option>
                <option value="1">Administrador</option>
                <option value="2">Recepcionista</option>
                <option value="3">Atención al cliente</option>
            </select>
        </div>
        <div id="admin-fields" style="display: none;">
            <div class="form-group">
                <label for="rnt">RNT</label>
                <input type="text" id="rnt" name="rnt">
            </div>
            <div class="form-group">
                <label for="nit">NIT</label>
                <input type="text" id="nit" name="nit">
            </div>
        </div>
        <div class="form-group photo-upload-area">
            <label for="foto_perfil" class="photo-upload-label">
                SUBIR FOTO
                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*" class="photo-input-hidden">
            </label>
        </div>
    </div>

    <?php if (isset($_GET['mensaje'])): ?>
        <p class="mensaje-feedback" style="color: green;"><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
    <?php endif; ?>

    <div class="form-actions">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='/lodge/public/index.php?action=mostrarLista'">Cancelar</button>
        <button type="submit" class="btn btn-primary">Confirmar</button>
    </div>
</form>

<footer class="form-footer">
    lodgehubgroup © 2025
</footer>
