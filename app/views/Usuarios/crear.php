<?php
session_start();
$baseURL = '/lodgehub/public';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../Controllers/UsuarioController.php';
    $controller = new UsuarioController();
    $controller->guardar();

    exit();
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nuevo Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="<?php echo $baseURL; ?>/assets/css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a id="lodgebub-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            LODGEHUB
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../../app/views/homepage/homepage.php">Home</a></li>
                            <li><a class="dropdown-item active" href="mainReservas.php">Reservas</a></li>
                            <li><a class="dropdown-item" href="../../../HABITACIONES/views/dashboard.php">Habitaciones</a></li>
                            <li><a class="dropdown-item" href="../../../MANTENIMIENTO/views/dashboard.php">Mantenimiento</a></li>
                            <li><a class="dropdown-item" href="../../app/views/Usuarios/lista.php">Usuarios</a></li>
                            <li><a class="dropdown-item" href="../../../PQRS/views/dashboard.php">PQRS</a></li>
                        </ul>
                    </li>

                </ul>
                <form class="d-flex" role="perfil">

                    <a href="../../app/views/homepage/cerrarSesion.php" class="btn btn-danger">Cerrar sesión</a>
                </form>
            </div>
        </div>
    </nav>
    <h2 class="form-title">NUEVO USUARIO</h2>

    <form action="crear.php" method="post" enctype="multipart/form-data">
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
            <button type="button" class="btn btn-secondary" onclick="window.location.href='lista.php'">Cancelar</button>
            <button type="submit" class="btn btn-primary">Confirmar</button>
        </div>
    </form>

    <footer class="form-footer">
        lodgehubgroup © 2025
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script> 
    <script src="<?php echo $baseURL; ?>/assets/js/form-validation.js"></script> <!-- script para mostrar ocultar RNT y NIT -->
</body>

</html>