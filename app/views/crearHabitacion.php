<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Habitación</title>
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitacion.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <?php
        include 'layouts/sidebar.php'; 
        include 'layouts/navbar.php'; 
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="wrapper">  
        <!-- Contenido Principal -->
            <main class="content">
                <div class="page-header">
                    <h1><i class="fas fa-plus-circle"></i> Nueva Habitación</h1>
                    <div class="page-actions">
                        <a href="index.php?controller=room&action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>

                <!-- Mensajes de error -->
                <?php if (isset($_SESSION['errors'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <ul class="error-list">
                            <?php foreach ($_SESSION['errors'] as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php unset($_SESSION['errors']); ?>
                <?php endif; ?>

                <!-- Formulario -->
                <div class="form-container">
                    <form action="index.php?controller=room&action=store" method="POST" enctype="multipart/form-data" id="roomForm">
                        <div class="form-grid">
                            <!-- Información Básica -->
                            <div class="form-section">
                                <h3><i class="fas fa-info-circle"></i> Información Básica</h3>
                                
                                <div class="form-group">
                                    <label for="numero"><i class="fas fa-hashtag"></i> Número de Habitación *</label>
                                    <input type="text" 
                                           id="numero" 
                                           name="numero" 
                                           value="<?php echo $_SESSION['form_data']['numero'] ?? ''; ?>"
                                           placeholder="Ej: 101, A-15, Suite1"
                                           required>
                                    <small class="form-help">Identificador único de la habitación</small>
                                </div>

                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="costo"><i class="fas fa-dollar-sign"></i> Costo por Noche *</label>
                                        <input type="number" 
                                               id="costo" 
                                               name="costo" 
                                               value="<?php echo $_SESSION['form_data']['costo'] ?? ''; ?>"
                                               step="0.01" 
                                               min="0" 
                                               placeholder="0.00"
                                               required>
                                    </div>

                                    <div class="form-group">
                                        <label for="capacidad"><i class="fas fa-users"></i> Capacidad *</label>
                                        <input type="number" 
                                               id="capacidad" 
                                               name="capacidad" 
                                               value="<?php echo $_SESSION['form_data']['capacidad'] ?? ''; ?>"
                                               min="1" 
                                               max="20"
                                               placeholder="1"
                                               required>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="tipoHabitacion"><i class="fas fa-tag"></i> Tipo de Habitación *</label>
                                    <select id="tipoHabitacion" name="tipoHabitacion" required>
                                        <option value="">Seleccionar tipo...</option>
                                        <?php foreach ($roomTypes as $type): ?>
                                            <option value="<?php echo $type['id']; ?>" 
                                                    <?php echo (isset($_SESSION['form_data']['tipoHabitacion']) && $_SESSION['form_data']['tipoHabitacion'] == $type['id']) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($type['descripcion']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>

                            <!-- Imagen y Descripción -->
                            <div class="form-section">
                                <h3><i class="fas fa-image"></i> Imagen y Descripción</h3>
                                
                                <div class="form-group">
                                    <label for="foto"><i class="fas fa-camera"></i> Foto de la Habitación</label>
                                    <div class="file-input-container">
                                        <input type="file" 
                                               id="foto" 
                                               name="foto" 
                                               accept="image/jpeg,image/jpg,image/png,image/gif"
                                               class="file-input">
                                        <label for="foto" class="file-label">
                                            <i class="fas fa-upload"></i>
                                            <span id="fileText">Seleccionar imagen</span>
                                        </label>
                                    </div>
                                    <div class="image-preview" id="imagePreview" style="display: none;">
                                        <img id="previewImg" src="" alt="Vista previa">
                                        <button type="button" class="remove-image" id="removeImage">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <small class="form-help">Formatos permitidos: JPG, JPEG, PNG, GIF. Máximo 5MB</small>
                                </div>

                                <div class="form-group">
                                    <label for="descripcion"><i class="fas fa-align-left"></i> Descripción</label>
                                    <textarea id="descripcion" 
                                              name="descripcion" 
                                              rows="4" 
                                              placeholder="Describe las características de la habitación..."><?php echo $_SESSION['form_data']['descripcion'] ?? ''; ?></textarea>
                                </div>
                            </div>

                            <!-- Estado y Mantenimiento -->
                            <div class="form-section full-width">
                                <h3><i class="fas fa-cog"></i> Estado y Mantenimiento</h3>
                                
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="estado"><i class="fas fa-flag"></i> Estado</label>
                                        <select id="estado" name="estado">
                                            <option value="Disponible" <?php echo (isset($_SESSION['form_data']['estado']) && $_SESSION['form_data']['estado'] == 'Disponible') ? 'selected' : 'selected'; ?>>Disponible</option>
                                            <option value="Reservada" <?php echo (isset($_SESSION['form_data']['estado']) && $_SESSION['form_data']['estado'] == 'Reservada') ? 'selected' : ''; ?>>Reservada</option>
                                            <option value="Ocupada" <?php echo (isset($_SESSION['form_data']['estado']) && $_SESSION['form_data']['estado'] == 'Ocupada') ? 'selected' : ''; ?>>Ocupada</option>
                                            <option value="Mantenimiento" <?php echo (isset($_SESSION['form_data']['estado']) && $_SESSION['form_data']['estado'] == 'Mantenimiento') ? 'selected' : ''; ?>>Mantenimiento</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="estadoMantenimiento"><i class="fas fa-toggle-on"></i> Estado de Mantenimiento</label>
                                        <select id="estadoMantenimiento" name="estadoMantenimiento">
                                            <option value="Activo" <?php echo (isset($_SESSION['form_data']['estadoMantenimiento']) && $_SESSION['form_data']['estadoMantenimiento'] == 'Activo') ? 'selected' : 'selected'; ?>>Activo</option>
                                            <option value="Inactivo" <?php echo (isset($_SESSION['form_data']['estadoMantenimiento']) && $_SESSION['form_data']['estadoMantenimiento'] == 'Inactivo') ? 'selected' : ''; ?>>Inactivo</option>
                                        </select>
                                    </div>
                                </div>

                                <div class="form-group maintenance-description" style="display: none;">
                                    <label for="descripcionMantenimiento"><i class="fas fa-tools"></i> Descripción del Mantenimiento</label>
                                    <textarea id="descripcionMantenimiento" 
                                              name="descripcionMantenimiento" 
                                              rows="3" 
                                              placeholder="Describe el tipo de mantenimiento requerido..."><?php echo $_SESSION['form_data']['descripcionMantenimiento'] ?? ''; ?></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="form-actions">
                            <a href="index.php?controller=room&action=index" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Guardar Habitación
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/habitaciones.js"></script>
    
    <?php if (isset($_SESSION['form_data'])) unset($_SESSION['form_data']); ?>
</body>
</html>