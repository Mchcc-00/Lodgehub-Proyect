<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Huésped - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts (nav y sidebar) -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHuesped.css">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    
    <div class="container">
        <div class="header">
            <h1>Agregar Huésped</h1>
            <p>Registra un nuevo huésped en el sistema</p>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                <i class="fas fa-user-plus"></i>
                Nuevo Huésped
            </h2>
            
            <!-- Mensajes -->
            <div id="success-message" class="success-message">
                ✅ <strong>¡Huésped creado exitosamente!</strong>
                <div style="margin-top: 5px; font-size: 0.9rem;">El huésped ha sido registrado en el sistema.</div>
            </div>

            <div id="error-message" class="error-message">
                ❌ <strong>Error al crear el huésped</strong>
                <div id="error-text" style="margin-top: 5px; font-size: 0.9rem;"></div>
            </div>

            <form id="huesped-form" action="../controllers/huespedController.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tipoDocumento">Tipo de Documento <span class="required">*</span></label>
                        <select id="tipoDocumento" name="tipoDocumento" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="Cedula de Ciudadania">Cédula de Ciudadanía</option>
                            <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                            <option value="Cedula de Extranjeria">Cédula de Extranjería</option>
                            <option value="Pasaporte">Pasaporte</option>
                            <option value="Registro Civil">Registro Civil</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="numDocumento">Número de Documento <span class="required">*</span></label>
                        <input type="text" id="numDocumento" name="numDocumento" required maxlength="15" placeholder="Ej: 1234567890">
                        <small class="form-text text-muted">Entre 5 y 15 caracteres, solo letras y números</small>
                    </div>

                    <div class="form-group">
                        <label for="nombres">Nombres <span class="required">*</span></label>
                        <input type="text" id="nombres" name="nombres" required maxlength="50" placeholder="Ej: Juan Carlos">
                        <small class="form-text text-muted">Entre 2 y 50 caracteres</small>
                    </div>

                    <div class="form-group">
                        <label for="apellidos">Apellidos <span class="required">*</span></label>
                        <input type="text" id="apellidos" name="apellidos" required maxlength="50" placeholder="Ej: Pérez García">
                        <small class="form-text text-muted">Entre 2 y 50 caracteres</small>
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
                    </div>

                    <div class="form-group">
                        <label for="numTelefono">Número de Teléfono <span class="required">*</span></label>
                        <input type="tel" id="numTelefono" name="numTelefono" required maxlength="15" placeholder="Ej: 3001234567">
                        <small class="form-text text-muted">Entre 7 y 15 caracteres</small>
                    </div>

                    <div class="form-group full-width">
                        <label for="correo">Correo Electrónico <span class="required">*</span></label>
                        <input type="email" id="correo" name="correo" required maxlength="30" placeholder="Ej: usuario@ejemplo.com">
                        <small class="form-text text-muted">Máximo 30 caracteres</small>
                    </div>
                </div>

                <!-- Vista previa del huésped -->
                <div class="huesped-preview" id="huesped-preview" style="display: none;">
                    <div class="preview-title">
                        <i class="fas fa-eye"></i>
                        Vista Previa del Huésped
                    </div>
                    <div class="preview-content" id="preview-content">
                        <!-- Contenido dinámico -->
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Huésped
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        <i class="fas fa-eraser"></i>
                        Limpiar Formulario
                    </button>
                    <button type="button" class="btn btn-info" id="preview-btn">
                        <i class="fas fa-eye"></i>
                        Vista Previa
                    </button>
                    <a href="listaHuesped.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i>
                        Ver Huéspedes
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/huesped.js"></script>

</body>
</html>