<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        CREAR USUARIO
    </title>
    <link rel="stylesheet" href="../../public/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

</head>

<body>
    <div class="main-container">

        <header class="top-bar">
            <div class="top-bar-left">
                <img src="../../public/assets/img/LogoClaroLH.png"
                    alt="Logo Lodgehub" class="top-logo">
            </div>
            <div class="top-bar-right">
                <i class="fas fa-user-circle user-icon" title="Perfil Usuario"></i>
            </div>
        </header>

        <div class="content-area">

            <aside class="sidebar left-sidebar">
                <nav>
                    <ul>
                        <li><a href="#">HABITACIONES</a></li>
                        <li><a href="#">RESERVAS</a></li>
                        <li><a href="listaUsuarios.php">USUARIOS</a></li>
                    </ul>
                </nav>
                <div class="sidebar-bottom-icons">
                    <i class="fas fa-headphones"></i>
                    <i class="fas fa-configuration"></i>
                </div>
            </aside>

            <main class="form-content-container">
                <h2 class="form-title">NUEVO USUARIO</h2>

                <form action="../../models/crudUsuarios/Usuarios.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="formulario" value="crearUsuario">
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
                            <label for="tipoDocumento">Tipo de documento</label>
                            <select id="tipoDocumento" name="tipDocumento">
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
                            <input type="text" id="numDocumento" name="numDocumento">
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
                            <label for="fechaNacimiento">Fecha de nacimiento</label>
                            <input type="date" id="fecha_nacimiento" name="fecha_nacimiento"">
                        </div>
                        <div class=" form-group">
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
                            <input type="email" id="correo" name="correo">
                        </div>
                        <div class="form-group password-wrapper">
                            <label for="password">Contraseña</label>
                            <input type="password" id="password" name="password">
                        </div>
                        <div class="form-group">
                            <label for="confirmar_password">Confirmar contraseña</label>
                            <input type="password" id="confirmar_password" name="confirmar_passowrd">
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
                            <select id="roles" name="roles">
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
                            <label for="nit">NIT</label>
                            <input type="text" id="nit" name="nit">
                        </div>

                        <div class="form-group photo-upload-area">
                            <label for="foto" class="photo-upload-label">
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
                        <button type="button" class="btn btn-secondary" onclick="window.location.href='../../views/Usuarios/listaUsuarios.php'">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Registrar</button>
                    </div>
                </form>
                <footer class="form-footer">
                    lodgehubgroup © 2025
                </footer>
        </div>
        </main>
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