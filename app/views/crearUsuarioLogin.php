<?php
/**
 * Este bloque de código solo se ejecuta cuando el formulario se envía (petición POST).
 */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once __DIR__ . '/../../Controllers/UsuarioController.php';
    $controller = new UsuarioController();
    $controller->registrarPublico();
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
    <link rel="stylesheet" href="<?php echo $baseURL; ?>/assets/css/stylesUsuarios.css">
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
                <img src="<?php echo $baseURL; ?>/img/LogoClaroLH.png" alt="LogoClaroLH">
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

    <script src="<?php echo $baseURL; ?>/assets/js/form-validation.js"></script>
    <script>
        // JavaScript para navegación entre secciones
        let currentSection = 1;
        const totalSections = 4;

        function showSection(sectionNumber) {
            // Ocultar todas las secciones
            document.querySelectorAll('.form-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Mostrar la sección actual
            document.getElementById(`section-${sectionNumber}`).classList.add('active');
            
            // Actualizar indicador de progreso
            document.querySelectorAll('.step').forEach((step, index) => {
                if (index + 1 <= sectionNumber) {
                    step.classList.add('active');
                } else {
                    step.classList.remove('active');
                }
            });
            
            // Controlar botones de navegación
            document.getElementById('prevBtn').style.display = sectionNumber > 1 ? 'inline-block' : 'none';
            document.getElementById('nextBtn').style.display = sectionNumber < totalSections ? 'inline-block' : 'none';
            document.getElementById('submitBtn').style.display = sectionNumber === totalSections ? 'inline-block' : 'none';
        }

        function validateCurrentSection() {
            const currentSectionEl = document.getElementById(`section-${currentSection}`);
            const requiredFields = currentSectionEl.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    field.classList.add('error');
                    isValid = false;
                } else {
                    field.classList.remove('error');
                }
            });
            
            return isValid;
        }

        // Event listeners para navegación
        document.getElementById('nextBtn').addEventListener('click', function() {
            if (validateCurrentSection() && currentSection < totalSections) {
                currentSection++;
                showSection(currentSection);
            }
        });

        document.getElementById('prevBtn').addEventListener('click', function() {
            if (currentSection > 1) {
                currentSection--;
                showSection(currentSection);
            }
        });

        // Validación de contraseñas
        document.getElementById('confirmar_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            const indicator = document.querySelector('.password-match-indicator');
            
            if (confirmPassword) {
                if (password === confirmPassword) {
                    indicator.textContent = '✓ Las contraseñas coinciden';
                    indicator.style.color = '#22c55e';
                } else {
                    indicator.textContent = '✗ Las contraseñas no coinciden';
                    indicator.style.color = '#ef4444';
                }
            } else {
                indicator.textContent = '';
            }
        });

        // Preview de imagen
        document.getElementById('foto').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const preview = document.querySelector('.file-preview');
            const fileText = document.querySelector('.file-text');
            
            if (file) {
                if (file.size > 2 * 1024 * 1024) {
                    alert('El archivo es muy grande. Máximo 2MB.');
                    this.value = '';
                    return;
                }
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; max-height: 100px; border-radius: 8px;">`;
                };
                reader.readAsDataURL(file);
                fileText.textContent = file.name;
            } else {
                preview.innerHTML = '';
                fileText.textContent = 'Seleccionar imagen';
            }
        });

        // Inicializar formulario
        showSection(1);
    </script>
</body>
</html>