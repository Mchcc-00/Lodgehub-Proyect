<?php
/**
 * Este bloque de código solo se ejecuta cuando el formulario se envía (petición POST).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '../../Controllers/UsuarioController.php';
    $controller = new UsuarioController();
    $controller->registrarPublico();
    // Redirige a login.php con mensaje
    header('Location: /lodgehub/app/views/login.php?mensaje=Usuario+registrado+correctamente');
    exit();
    exit();
}

$baseURL = '/lodgehub/public'; // Ajusta si el nombre de tu carpeta principal es diferente
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Usuario</title>
    <link rel="stylesheet" href="<?php echo $baseURL; ?>/public/assets/css/stylesUsuarios.css">
</head>
<body>
    <div class="page-background">
        <!-- Círculos decorativos flotantes -->
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
        <div class="circle circle-3"></div>
        <div class="circle circle-4"></div>

        <div class="borde-container-login">
            <!-- Contenedor del logo -->
            <div class="logo-container">
                <img src="<?php echo $baseURL; ?>/public/img/LogoClaroLH.png" alt="LogoClaroLH">
                <h6>Lodgehub</h6>
            </div>

            <div class="user-container">
                <header class="form-header">
                    <h2 class="form-title">CREAR USUARIO</h2>
                </header>

                <!-- Indicador de progreso -->
                <div class="progress-indicator">
                    <div class="step active" data-step="1">1</div>
                    <div class="step" data-step="2">2</div>
                    <div class="step" data-step="3">3</div>
                    <div class="step" data-step="4">4</div>
                </div>

                <form action="crearUsuarioLogin.php" method="post" enctype="multipart/form-data" id="userForm">
                    <input type="hidden" name="formulario" value="crearUsuario">
                    
                    <!--INFORMACIÓN DE IDENTIFICACIÓN -->
                    <div class="form-section active" id="section-1">
                        <h3 class="section-title">Información de Identificación</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="tipoDocumento">Tipo de documento *</label>
                                <select id="tipoDocumento" name="tipoDocumento" required>
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                    <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                    <option value="Cedula de Extranjeria">Cédula de Extranjería</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Registro Civil">Registro Civil</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="numDocumento">Número de documento *</label>
                                <input type="text" id="numDocumento" name="numDocumento" 
                                       placeholder="Ingrese su número de documento" 
                                       maxlength="15" required>
                            </div>
                        </div>
                    </div>

                    <!--INFORMACIÓN PERSONAL -->
                    <div class="form-section" id="section-2">
                        <h3 class="section-title">Información Personal</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="nombres">Nombres *</label>
                                <input type="text" id="nombres" name="nombres" 
                                       placeholder="Ingrese sus nombres completos" 
                                       maxlength="50" required>
                            </div>
                            <div class="form-group">
                                <label for="apellidos">Apellidos *</label>
                                <input type="text" id="apellidos" name="apellidos" 
                                       placeholder="Ingrese sus apellidos completos" 
                                       maxlength="50" required>
                            </div>
                            <div class="form-group">
                                <label for="fechaNacimiento">Fecha de nacimiento *</label>
                                <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>
                            </div>
                            <div class="form-group">
                                <label for="sexo">Sexo *</label>
                                <select id="sexo" name="sexo" required>
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="Hombre">Hombre</option>
                                    <option value="Mujer">Mujer</option>
                                    <option value="Otro">Otro</option>
                                    <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- INFORMACIÓN DE CONTACTO -->
                    <div class="form-section" id="section-3">
                        <h3 class="section-title">Información de Contacto</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="correo">Correo electrónico *</label>
                                <input type="email" id="correo" name="correo" 
                                       placeholder="ejemplo@correo.com" 
                                       maxlength="30" required>
                            </div>
                            <div class="form-group">
                                <label for="numTelefono">Número de teléfono *</label>
                                <input type="tel" id="numTelefono" name="numTelefono" 
                                       placeholder="Ej: +57 300 123 4567" 
                                       maxlength="15" required>
                            </div>
                        </div>
                    </div>

                    <!-- CONFIGURACIÓN DE CUENTA -->
                    <div class="form-section" id="section-4">
                        <h3 class="section-title">Configuración de Cuenta</h3>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="password">Contraseña *</label>
                                <div class="password-wrapper">
                                    <input type="password" id="password" name="password" 
                                           placeholder="Mínimo 8 caracteres" required>
                                    <div class="password-strength">
                                        <div class="strength-bar"></div>
                                        <span class="strength-text">Fortaleza de contraseña</span>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="confirmar_password">Confirmar contraseña *</label>
                                <input type="password" id="confirmar_password" name="confirmar_password" 
                                       placeholder="Repita su contraseña" required>
                                <div class="password-match-indicator"></div>
                            </div>
                            <div class="form-group">
                                <label for="roles">Rol del usuario *</label>
                                <select id="roles" name="roles" required>
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <option value="Administrador">Administrador</option>
                                    <option value="Colaborador">Colaborador</option>
                                    <option value="Usuario">Usuario</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="foto">Foto de perfil</label>
                                <div class="file-upload-wrapper">
                                    <input type="file" id="foto" name="foto" accept="image/*" class="file-input">
                                    <label for="foto" class="file-label">
                                        <span class="file-text">Seleccionar imagen</span>
                                        <span class="file-button">Buscar</span>
                                    </label>
                                    <div class="file-preview"></div>
                                </div>
                                <small class="file-info">Formatos: JPG, PNG, GIF. Máximo 2MB</small>
                            </div>
                        </div>
                    </div>

                    <!-- Mensajes de feedback -->
                    <?php if (isset($_GET['mensaje'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_GET['mensaje']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error'])): ?>
                        <div class="alert alert-error">
                            <?php echo htmlspecialchars($_GET['error']); ?>
                        </div>
                    <?php endif; ?>

                    <!-- Navegación del formulario -->
                    <div class="form-navigation">
                        <button type="button" class="btn-secondary" id="prevBtn" style="display: none;">
                            Anterior
                        </button>
                        <button type="button" class="btn-secondary" onclick="window.location.href='login.php'">
                            Cancelar
                        </button>
                        <button type="button" class="btn-primary" id="nextBtn">
                            Siguiente
                        </button>
                        <button type="submit" class="btn-primary" id="submitBtn" style="display: none;">
                            Crear Usuario
                        </button>
                    </div>
                </form>

                <footer class="form-footer">
                    lodgehubgroup © 2025
                </footer>
            </div>
        </div>
    </div>

    <!-- Scripts de validación y navegación -->
    <script src="<?php echo $baseURL; ?>/assets/js/form-validation.js"></script>
    <script src="<?php echo $baseURL; ?>/assets/js/navigation.js"></script>

    <!-- SOLUCIÓN: Manejar la tecla Enter para que funcione como "Siguiente" -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('userForm');
            const nextBtn = document.getElementById('nextBtn');

            // Escuchar el evento 'keydown' en todo el formulario
            form.addEventListener('keydown', function(event) {
                // Si la tecla presionada es "Enter"
                if (event.key === 'Enter') {
                    // Prevenir la acción por defecto (enviar el formulario)
                    event.preventDefault();

                    // Si el botón "Siguiente" está visible, simular un clic
                    if (nextBtn.style.display !== 'none') {
                        nextBtn.click();
                    }
                }
            });
        });
    </script>
    
    <!-- Script para compatibilidad de validación de contraseñas  -->
    <script>
        // Validación de contraseñas - Mantenido para compatibilidad
        document.addEventListener('DOMContentLoaded', function() {
            const confirmPasswordField = document.getElementById('confirmar_password');
            if (confirmPasswordField) {
                confirmPasswordField.addEventListener('input', function() {
                    const password = document.getElementById('password').value;
                    const confirmPassword = this.value;
                    const indicator = document.querySelector('.password-match-indicator');
                    
                    if (indicator) {
                        if (confirmPassword) {
                            if (password === confirmPassword) {
                                indicator.textContent = '✓ Las contraseñas coinciden';
                                indicator.style.color = '#22c55e';
                                indicator.className = 'password-match-indicator success';
                            } else {
                                indicator.textContent = '✗ Las contraseñas no coinciden';
                                indicator.style.color = '#ef4444';
                                indicator.className = 'password-match-indicator error';
                            }
                        } else {
                            indicator.textContent = '';
                            indicator.className = 'password-match-indicator';
                        }
                    }
                });
            }

            // Preview de imagen - Mantenido para compatibilidad
            const fotoField = document.getElementById('foto');
            if (fotoField) {
                fotoField.addEventListener('change', function(e) {
                    const file = e.target.files[0];
                    const preview = document.querySelector('.file-preview');
                    const fileText = document.querySelector('.file-text');
                    
                    if (file) {
                        if (file.size > 2 * 1024 * 1024) {
                            alert('El archivo es muy grande. Máximo 2MB.');
                            this.value = '';
                            if (preview) preview.innerHTML = '';
                            if (fileText) fileText.textContent = 'Seleccionar imagen';
                            return;
                        }
                        
                        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                        if (!allowedTypes.includes(file.type)) {
                            alert('Solo se permiten archivos JPG, PNG y GIF.');
                            this.value = '';
                            if (preview) preview.innerHTML = '';
                            if (fileText) fileText.textContent = 'Seleccionar imagen';
                            return;
                        }
                        
                        if (preview && fileText) {
                            const reader = new FileReader();
                            reader.onload = function(e) {
                                preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; max-height: 100px; border-radius: 8px; object-fit: cover;">`;
                            };
                            reader.readAsDataURL(file);
                            fileText.textContent = file.name;
                        }
                    } else {
                        if (preview) preview.innerHTML = '';
                        if (fileText) fileText.textContent = 'Seleccionar imagen';
                    }
                });
            }
        });
    </script>
</body>
</html>