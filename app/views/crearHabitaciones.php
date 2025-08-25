<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Habitación - Sistema de Gestión Hotelera</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- CSS personalizado -->
    <link href="../css/habitaciones.css" rel="stylesheet">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitaciones.css">
</head>
<body>
    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Crear Nueva Habitación</h1>
            <p>Complete el formulario para agregar una nueva habitación al sistema</p>
        </div>

        <!-- Mensajes -->
        <div id="success-message" class="success-message" style="display: none;">
            <i class="fas fa-check-circle"></i> <span id="success-text">Habitación creada exitosamente</span>
        </div>

        <div id="error-message" class="error-message" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i> <span id="error-text"></span>
        </div>

        <!-- Formulario de Creación -->
        <div class="form-section">
            <form id="habitaciones-form" action="../controllers/HabitacionesController.php?action=crear" method="POST">
                <div class="form-title">
                    <i class="fas fa-bed"></i>
                    Información de la Habitación
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label for="numero">Número de Habitación <span class="required">*</span></label>
                        <input type="text" id="numero" name="numero" class="form-control" required maxlength="5" placeholder="Ej: 101, A1, 2A">
                        <div class="form-text">Máximo 5 caracteres (letras y números)</div>
                    </div>

                    <div class="form-group">
                        <label for="tipoHabitacion">Tipo de Habitación <span class="required">*</span></label>
                        <select id="tipoHabitacion" name="tipoHabitacion" class="form-control" required>
                            <option value="">Seleccionar tipo</option>
                            <!-- Se llenan dinámicamente via JS -->
                        </select>
                        <div class="form-text">Seleccione el tipo de habitación</div>
                    </div>

                    <div class="form-group">
                        <label for="capacidad">Capacidad <span class="required">*</span></label>
                        <input type="number" id="capacidad" name="capacidad" class="form-control" required min="1" max="20" placeholder="Número de personas">
                        <div class="form-text">Capacidad máxima: 20 personas</div>
                    </div>

                    <div class="form-group">
                        <label for="costo">Costo por Noche <span class="required">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text">$</span>
                            <input type="number" id="costo" name="costo" class="form-control" required min="0" step="0.01" placeholder="0.00">
                        </div>
                        <div class="form-text">Precio en pesos colombianos</div>
                    </div>

                    <div class="form-group">
                        <label for="foto">URL de la Foto</label>
                        <input type="url" id="foto" name="foto" class="form-control" placeholder="https://ejemplo.com/imagen.jpg">
                        <div class="form-text">URL de la imagen de la habitación (opcional)</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="4" maxlength="500" placeholder="Describe las características, amenidades y detalles especiales de la habitación..."></textarea>
                        <div class="form-text">Máximo 500 caracteres. <span id="char-count">0/500</span></div>
                    </div>
                </div>

                <!-- Vista Previa -->
                <div id="habitacion-preview" class="habitacion-preview" style="display: none;">
                    <div class="preview-title">
                        <i class="fas fa-eye"></i>
                        Vista Previa de la Habitación
                    </div>
                    <div class="preview-content" id="preview-content">
                        <!-- Se llena dinámicamente -->
                    </div>
                </div>

                <!-- Botones de Acción -->
                <div class="form-actions">
                    <button type="button" id="preview-btn" class="btn btn-info">
                        <i class="fas fa-eye"></i> Vista Previa
                    </button>
                    <button type="button" id="reset-btn" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Limpiar Formulario
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Crear Habitación
                    </button>
                    <a href="listaHabitaciones.php" class="btn btn-primary">
                        <i class="fas fa-list"></i> Ver Lista de Habitaciones
                    </a>
                </div>
            </form>
        </div>

        <!-- Información adicional -->
        <div class="mt-4">
            <div class="row">
                <div class="col-md-6">
                    <div class="card border-info">
                        <div class="card-header bg-info text-white">
                            <i class="fas fa-info-circle"></i> Información
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-check text-success"></i> Los campos marcados con <span class="text-danger">*</span> son obligatorios</li>
                                <li><i class="fas fa-check text-success"></i> El número de habitación debe ser único</li>
                                <li><i class="fas fa-check text-success"></i> La capacidad debe estar entre 1 y 20 personas</li>
                                <li><i class="fas fa-check text-success"></i> El estado inicial será "Disponible"</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-warning">
                        <div class="card-header bg-warning text-dark">
                            <i class="fas fa-lightbulb"></i> Consejos
                        </div>
                        <div class="card-body">
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-arrow-right text-primary"></i> Use números claros como 101, 102, 201</li>
                                <li><i class="fas fa-arrow-right text-primary"></i> Describa las amenidades importantes</li>
                                <li><i class="fas fa-arrow-right text-primary"></i> Asegúrese de que la URL de la foto sea válida</li>
                                <li><i class="fas fa-arrow-right text-primary"></i> Use la vista previa antes de crear</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../js/habitaciones.js"></script>
    
    <script>
        // Contador de caracteres para descripción
        document.addEventListener('DOMContentLoaded', function() {
            const descripcionTextarea = document.getElementById('descripcion');
            const charCount = document.getElementById('char-count');
            
            if (descripcionTextarea && charCount) {
                descripcionTextarea.addEventListener('input', function() {
                    const count = this.value.length;
                    charCount.textContent = `${count}/500`;
                    
                    if (count > 450) {
                        charCount.className = 'text-warning';
                    } else if (count > 500) {
                        charCount.className = 'text-danger';
                    } else {
                        charCount.className = 'text-muted';
                    }
                });
            }
        });
    </script>
</body>
</html>