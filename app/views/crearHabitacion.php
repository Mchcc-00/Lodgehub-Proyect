<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Habitación - LodgeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link href="../../public/assets/css/stylesHabitaciones.css" rel="stylesheet">

</head>

<body>
    <?php
    include "layouts/sidebar.php";
    include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <main class="main-content">
        <div class="content-wrapper container-fluid"> <!-- Usamos container-fluid para un ancho completo adaptable -->
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-plus-circle"></i> Crear Nueva Habitación</h1>
                <p>Añade una nueva habitación al sistema</p>
            </div>

            <!-- Mensajes -->
            <div id="success-message" class="success-message">
                <i class="fas fa-check-circle"></i>
                <span id="success-text"></span>
            </div>

            <div id="error-message" class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="error-text"></span>
            </div>

            <!-- Formulario -->
            <div class="form-section">
                <div class="form-title">
                    <i class="fas fa-bed"></i>
                    Información de la Habitación
                </div>

                <form id="form-habitacion" enctype="multipart/form-data">
                    <div class="form-grid">
                        <!-- Hotel -->
                        <div class="form-group">
                            <label for="id_hotel">Hotel <span class="required">*</span></label>
                            <select id="id_hotel" name="id_hotel" class="form-select" required>
                                <option value="">Seleccionar hotel</option>
                                <?php foreach ($hoteles as $hotel): ?>
                                    <option value="<?php echo $hotel['id']; ?>"
                                        <?php echo (isset($_GET['hotel']) && $_GET['hotel'] == $hotel['id']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($hotel['nombre']); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-text">Selecciona el hotel al que pertenecerá la habitación</div>
                        </div>

                        <!-- Número -->
                        <div class="form-group">
                            <label for="numero">Número de Habitación <span class="required">*</span></label>
                            <input type="text" id="numero" name="numero" class="form-control" required
                                placeholder="Ej: 101, A1, etc.">
                            <div class="form-text">Número único identificador de la habitación</div>
                        </div>

                        <!-- Tipo de habitación -->
                        <div class="form-group">
                            <label for="tipoHabitacion">Tipo de Habitación <span class="required">*</span></label>
                            <select id="tipoHabitacion" name="tipoHabitacion" class="form-select" required disabled>
                                <option value="">Selecciona primero un hotel</option>
                            </select>
                            <div class="form-text">Tipo de habitación disponible para el hotel seleccionado</div>
                        </div>

                        <!-- Capacidad -->
                        <div class="form-group">
                            <label for="capacidad">Capacidad <span class="required">*</span></label>
                            <input type="number" id="capacidad" name="capacidad" class="form-control" required
                                min="1" max="20" placeholder="Número de personas">
                            <div class="form-text">Número máximo de huéspedes</div>
                        </div>

                        <!-- Costo -->
                        <div class="form-group">
                            <label for="costo">Costo por Noche <span class="required">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" id="costo" name="costo" class="form-control" required
                                    min="0" step="0.01" placeholder="0.00">
                            </div>
                            <div class="form-text">Precio por noche en pesos colombianos</div>
                        </div>

                        <!-- Estado -->
                        <div class="form-group">
                            <label for="estado">Estado <span class="required">*</span></label>
                            <select id="estado" name="estado" class="form-select" required>
                                <option value="Disponible">Disponible</option>
                                <option value="Reservada">Reservada</option>
                                <option value="Ocupada">Ocupada</option>
                                <option value="Mantenimiento">Mantenimiento</option>
                            </select>
                            <div class="form-text">Estado actual de la habitación</div>
                        </div>

                        <!-- Descripción -->
                        <div class="form-group full-width">
                            <label for="descripcion">Descripción</label>
                            <textarea id="descripcion" name="descripcion" class="form-control" rows="4"
                                placeholder="Describe las características especiales de la habitación..."></textarea>
                            <div class="form-text">Información adicional sobre amenidades, vista, etc.</div>
                        </div>

                        <!-- Foto -->
                        <div class="form-group full-width">
                            <label for="foto">Imagen de la Habitación</label>
                            <div class="image-upload-area" id="image-upload-area">
                                <div class="upload-placeholder" id="upload-placeholder">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <p>Arrastra una imagen aquí o haz clic para seleccionar</p>
                                    <small class="text-muted">Formatos: JPG, PNG, WEBP (Máx. 5MB)</small>
                                </div>
                                <div class="image-preview" id="image-preview" style="display: none;">
                                    <img id="preview-img" src="" alt="Vista previa">
                                    <button type="button" class="btn btn-danger btn-sm remove-image" id="remove-image">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <input type="file" id="imagen" name="imagen" class="form-control" accept="image/*" style="display: none;">
                            <input type="hidden" id="foto" name="foto" value="">
                        </div>

                        <!-- Campos de mantenimiento (aparecen solo si estado es "Mantenimiento") -->
                        <div class="form-group full-width" id="grupo-mantenimiento" style="display: none;">
                            <label for="descripcionMantenimiento">Descripción del Mantenimiento</label>
                            <textarea id="descripcionMantenimiento" name="descripcionMantenimiento" class="form-control" rows="3"
                                placeholder="Describe el tipo de mantenimiento requerido..."></textarea>
                            <div class="form-text">Detalla el motivo del mantenimiento</div>
                        </div>
                    </div>

                    <!-- Vista previa -->
                    <div class="pqrs-preview" id="preview-section" style="display: none;">
                        <div class="preview-title">
                            <i class="fas fa-eye"></i> Vista Previa
                        </div>
                        <div class="preview-content" id="preview-content">
                            <!-- Contenido de vista previa generado dinámicamente -->
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="form-actions">
                        <a href="?action=index" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                        <button type="button" id="btn-preview" class="btn btn-info">
                            <i class="fas fa-eye"></i> Vista Previa
                        </button>
                        <button type="submit" id="btn-guardar" class="btn btn-success">
                            <i class="fas fa-save"></i> Crear Habitación
                        </button>
                    </div>
                </form>
            </div>
        </div>
        </div> <!-- Cierre de .content-wrapper -->
    </main> <!-- Cierre de .main-content -->

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../js/habitaciones-form.js"></script>
</body>

</html>