<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Colaborador - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts (nav y sidebar) -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesMisColaboradores.css">
</head>
<body>

    <?php

        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        // --- INICIO: CONTROL DE ACCESO ---
        // Solo los administradores pueden acceder a esta página
        if (!isset($_SESSION['user']['roles']) || $_SESSION['user']['roles'] !== 'Administrador') {
            echo '<div class="container mt-5"><div class="alert alert-danger text-center"><h4><i class="fas fa-lock"></i> Acceso Denegado</h4><p>No tienes los permisos necesarios para crear colaboradores.</p><a href="homepage.php" class="btn btn-primary mt-3">Volver al Inicio</a></div></div>';
            exit(); // Detener la ejecución del script
        }
        // --- FIN: CONTROL DE ACCESO ---

        // VALIDACIÓN: Asegurarse de que un hotel ha sido seleccionado
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_id = $_SESSION['hotel_id'] ?? null;
        $hotel_nombre = $_SESSION['hotel_nombre'] ?? 'No asignado';
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    
    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder crear un nuevo colaborador, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel al que deseas añadir el colaborador.</p>
            </div>
        <?php else: ?>
        <div class="header">
            <h1>Crear Colaborador</h1>
            <p>Registra un nuevo colaborador en el sistema LodgeHub</p>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                <i class="fas fa-user-plus"></i>
                Nuevo Colaborador
            </h2>
            
            <!-- Mensajes -->
            <div id="success-message" class="success-message">
                ✅ <strong>¡Colaborador creado exitosamente!</strong>
                <div style="margin-top: 5px; font-size: 0.9rem;">El colaborador ha sido registrado en el sistema.</div>
            </div>

            <div id="error-message" class="error-message">
                ❌ <strong>Error al crear el colaborador</strong>
                <div id="error-text" style="margin-top: 5px; font-size: 0.9rem;"></div>
            </div>

            <form id="colaborador-form" action="../controllers/misColaboradoresControllers.php" method="POST" enctype="multipart/form-data">
                <!-- Campo oculto para enviar el id_hotel del admin -->
                <input type="hidden" name="id_hotel_admin" value="<?php echo htmlspecialchars($hotel_id); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="hotel_nombre">Hotel Asignado</label>
                        <input type="text" id="hotel_nombre" name="hotel_nombre" value="<?php echo htmlspecialchars($hotel_nombre); ?>" readonly>
                        <small class="form-text text-muted">El colaborador será asignado a este hotel.</small>
                    </div>

                    <div class="form-group">
                        <label for="numDocumento">Número de Documento <span class="required">*</span></label>
                        <input type="text" id="numDocumento" name="numDocumento" required maxlength="15" placeholder="Ej: 1234567890">
                        <small class="form-text text-muted">Número único de identificación</small>
                        <div id="documento-feedback" class="mt-1"></div>
                    </div>

                    <div class="form-group">
                        <label for="tipoDocumento">Tipo de Documento <span class="required">*</span></label>
                        <select id="tipoDocumento" name="tipoDocumento" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                            <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>                            
                            <option value="Cédula de Extranjería">Cédula de Extranjería</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Registro Civil">Registro Civil</option>
                        </select>
                        <small class="form-text text-muted">Tipo de documento de identidad</small>
                    </div>

                    <div class="form-group">
                        <label for="nombres">Nombres <span class="required">*</span></label>
                        <input type="text" id="nombres" name="nombres" required maxlength="50" placeholder="Ej: Juan Carlos">
                        <small class="form-text text-muted">Nombres completos</small>
                    </div>

                    <div class="form-group">
                        <label for="apellidos">Apellidos <span class="required">*</span></label>
                        <input type="text" id="apellidos" name="apellidos" required maxlength="50" placeholder="Ej: García López">
                        <small class="form-text text-muted">Apellidos completos</small>
                    </div>

                    <div class="form-group">
                        <label for="correo">Correo Electrónico <span class="required">*</span></label>
                        <input type="email" id="correo" name="correo" required maxlength="255" placeholder="Ej: usuario@lodgehub.com">
                        <small class="form-text text-muted">Dirección de correo válida</small>
                        <div id="correo-feedback" class="mt-1"></div>
                    </div>

                    <div class="form-group">
                        <label for="numTelefono">Teléfono <span class="required">*</span></label>
                        <input type="tel" id="numTelefono" name="numTelefono" required maxlength="15" placeholder="Ej: 3001234567">
                        <small class="form-text text-muted">Número de contacto</small>
                    </div>

                    <div class="form-group">
                        <label for="sexo">Sexo <span class="required">*</span></label>
                        <select id="sexo" name="sexo" required>
                            <option value="">Seleccione una opción</option>
                            <option value="Hombre">Hombre</option>
                            <option value="Mujer">Mujer</option>
                            <option value="Otro">Otro</option>
                            <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                        </select>
                        <small class="form-text text-muted">Identidad de género</small>
                    </div>

                    <div class="form-group">
                        <label for="fechaNacimiento">Fecha de Nacimiento <span class="required">*</span></label>
                        <input type="date" id="fechaNacimiento" name="fechaNacimiento" required>
                        <small class="form-text text-muted">Fecha de nacimiento del colaborador</small>
                    </div>

                    <div class="form-group">
                        <label for="roles">Rol en el Sistema <span class="required">*</span></label>
                        <select id="roles" name="roles" required>
                            <option value="">Seleccione un rol</option>
                            <option value="Colaborador" selected>Colaborador</option>
                            <option value="Usuario">Usuario</option>
                        </select>
                        <small class="form-text text-muted">Nivel de acceso en el sistema</small>
                    </div>

                    <div class="form-group">
                        <label for="password">Contraseña <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="password" id="password" name="password" required minlength="6" maxlength="255" placeholder="Mínimo 6 caracteres">
                            <button class="btn btn-outline-secondary" type="button" id="toggle-password">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                        <small class="form-text text-muted">Contraseña segura para el acceso</small>
                        <div id="password-strength" class="mt-1"></div>
                    </div>

                    <div class="form-group">
                        <label for="confirmarPassword">Confirmar Contraseña <span class="required">*</span></label>
                        <input type="password" id="confirmarPassword" name="confirmarPassword" required minlength="6" maxlength="255" placeholder="Confirme la contraseña">
                        <small class="form-text text-muted">Debe coincidir con la contraseña</small>
                        <div id="password-match" class="mt-1"></div>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto de Perfil</label>
                        <input type="file" id="foto" name="foto" accept="image/*">
                        <small class="form-text text-muted">Opcional. Formatos: JPG, PNG, GIF (máx. 2MB)</small>
                        <div id="foto-preview" class="mt-2" style="display: none;">
                            <img id="preview-img" src="" alt="Vista previa" style="max-width: 150px; max-height: 150px; border-radius: 8px;">
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="form-check">
                            <input type="checkbox" class="form-check-input" id="solicitarContraseña" name="solicitarContraseña" value="1">
                            <label class="form-check-label" for="solicitarContraseña">
                                Solicitar cambio de contraseña en el primer inicio de sesión
                            </label>
                        </div>
                        <small class="form-text text-muted">Fuerza al usuario a cambiar su contraseña al iniciar sesión</small>
                    </div>
                </div>

                <!-- Vista previa del colaborador -->
                <div class="colaborador-preview" id="colaborador-preview" style="display: none;">
                    <div class="preview-title">
                        <i class="fas fa-eye"></i>
                        Vista Previa del Colaborador
                    </div>
                    <div class="preview-content" id="preview-content">
                        <!-- Contenido dinámico -->
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i>
                        Crear Colaborador
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        <i class="fas fa-eraser"></i>
                        Limpiar Formulario
                    </button>
                    <button type="button" class="btn btn-info" id="preview-btn">
                        <i class="fas fa-eye"></i>
                        Vista Previa
                    </button>
                    <a href="listaMisColaboradores.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i>
                        Ver Colaboradores
                    </a>
                </div>
            </form>
        </div>
        <?php endif; // Fin del bloque de validación ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/colaboradores.js"></script>

</body>
</html>