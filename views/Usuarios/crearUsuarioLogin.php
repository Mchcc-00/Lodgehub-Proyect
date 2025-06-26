<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="../../public/assets/css/loginStyles.css">
</head>

<body>
    <div class="page-background">
        <div class="borde-container">
            <div class="form-container">
                <header class="form-header">
                    <h2 class="form-title">CREAR USUARIO</h2>
                    <div class="logo-placeholder">
                        <img src="../../public/assets/img/LogoClaroLH.png" alt="LogoClaroLH" width="80px" height="auto">
                    </div>
                </header>

                <form action="../../models/crudUsuarios/Usuarios.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="formulario" value="crearPerfil">
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="primer_nombre">Primer nombre</label>
                            <input type="text" id="primer_nombre" name="primer_nombre">
                        </div>
                        <div class="form-group">
                            <label for="segundo_nombre">Segundo nombre</label>
                            <input type="text" id="segundo_nombre" name="segundo_nombre">
                        </div>
                        <div class="form-group">
                            <label for="tipo_documento">Tipo de documento</label>
                            <select id="tipo_documento" name="tipo_documento">
                                <option value="cedula">Cédula</option>
                                <option value="pasaporte">Pasaporte</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="documento">Número de documento</label>
                            <input type="text" id="documento" name="documento">
                        </div>
                        <div class="form-group">
                            <label for="primer_apellido">Primer apellido</label>
                            <input type="text" id="primer_apellido" name="primer_apellido">
                        </div>
                        <div class="form-group">
                            <label for="segundo_apellido">Segundo apellido</label>
                            <input type="text" id="segundo_apellido" name="segundo_apellido">
                        </div>

                        <div class="form-group">
                            <label for="fecha_nacimiento">Fecha de nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento">
                        </div>
                        <div class="form-group">
                            <label for="sexo">Sexo</label>
                            <select id="sexo" name="sexo">
                                <option value="" disabled selected>Seleccionar...</option>
                                <option value="masculino">Masculino</option>
                                <option value="femenino">Femenino</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="correo">Correo electrónico</label>
                            <input type="email" id="correo" name="correo">
                        </div>
                        <div class="form-group password-wrapper">
                            <label for="contrasena">Contraseña</label>
                            <input type="password" id="contrasena" name="contrasena">
                        </div>
                        <div class="form-group">
                            <label for="confirmar_contrasena">Confirmar contraseña</label>
                            <input type="password" id="confirmar_contrasena" name="confirmar_contrasena">
                        </div>

                        <div class="form-group">
                            <label for="telefono">Número de teléfono</label>
                            <input type="tel" id="telefono" name="telefono">
                        </div>
                        <div class="form-group">
                            <label for="tel_emergencia">Teléfono de emergencia</label>
                            <input type="tel" id="tel_emergencia" name="tel_emergencia">
                        </div>
                        <div class="form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" id="direccion" name="direccion">
                        </div>


                        <div class="form-group">
                            <label for="rol">Rol</label>
                            <select id="rol" name="rol">
                                <option value="" disabled selected>Seleccionar...</option>
                                <option value="1">Administrador</option>
                                <option value="2">Recepcionista</option>
                                <option value="3">Atención al cliente</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="rnt">RNT</label>
                            <input type="text" id="rnt" name="rnt">
                        </div>
                        <div class="form-group">
                            <label for="rnt">NIT</label>
                            <input type="text" id="nit" name="nit">
                        </div>

                        <div class="form-group photo-upload-area">
                            <label for="foto_perfil" class="photo-upload-label">
                                SUBIR FOTO
                                <input type="file" id="foto_perfil" name="foto_perfil" accept="image/*"
                                    class="photo-input-hidden">
                            </label>
                        </div>
                    </div>

                    <!-- Espacio para mostrar mensajes -->
                    <?php if (isset($_GET['mensaje'])): ?>
                        <p style="color: green;"><?php echo htmlspecialchars($_GET['mensaje']); ?></p>
                    <?php endif; ?>


                    <div class="form-actions">
                        <button type="button" class="btn btn-secondary">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>

                <footer class="form-footer">
                    lodgehubgroup © 2025
                </footer>

            </div>
        </div>
        <!-- Script para habilitar/deshabilitar RNT y NIT según el rol -->
    <script>
    document.getElementById('rol').addEventListener('change', function() {
        const rnt = document.getElementById('rnt');
        const nit = document.getElementById('nit');
        if (this.value === '1') { // 1 = rol Administrador
            rnt.disabled = false;
            nit.disabled = false;
            rnt.required = true;
            nit.required = true;
        } else {
            rnt.disabled = true;
            nit.disabled = true;
            rnt.required = false;
            nit.required = false;
            rnt.value = '';
            nit.value = '';
        }
    });
    document.getElementById('rol').dispatchEvent(new Event('change'));
    </script>
        <script src="../assets/js/formScripts.js"></script>
</body>

</html>