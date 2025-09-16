<?php
require_once 'validarSesion.php';
$currentPage = 'Habitaciones'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Habitación - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitaciones.css">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        // VALIDACIÓN: Asegurarse de que un hotel ha sido seleccionado
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_id = $_SESSION['hotel_id'] ?? null;
        $hotel_nombre = $_SESSION['hotel_nombre'] ?? 'No asignado';

        // Cargar datos para los selects si hay un hotel seleccionado
        $tiposHabitacion = [];
        if ($hotelSeleccionado) {
            require_once '../models/habitacionesModel.php';
            $habitacionesModel = new HabitacionesModel();
            $tiposHabitacion = $habitacionesModel->obtenerTiposHabitacion($hotel_id);
        }
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder crear una nueva habitación, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
        <div class="header">
            <h1>Crear Habitación</h1>
            <p>Registra una nueva habitación en el sistema para el hotel <?php echo htmlspecialchars($hotel_nombre); ?>.</p>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                <i class="fas fa-door-open"></i>
                Nueva Habitación
            </h2>

            <!-- Mensajes de alerta -->
            <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
            <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

            <form id="form-crear-habitacion" novalidate enctype="multipart/form-data">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="hotel_nombre" class="form-label">Hotel Asignado</label>
                        <input type="text" id="hotel_nombre" name="hotel_nombre" class="form-control" value="<?php echo htmlspecialchars($hotel_nombre); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="numero" class="form-label">Número de Habitación <span class="required">*</span></label>
                        <input type="text" id="numero" name="numero" class="form-control" required placeholder="Ej: 101, 20A, PH1">
                        <div class="invalid-feedback">El número es obligatorio.</div>
                    </div>

                    <div class="form-group">
                        <label for="tipoHabitacion" class="form-label">Tipo de Habitación <span class="required">*</span></label>
                        <select id="tipoHabitacion" name="tipoHabitacion" class="form-select" required>
                            <option value="">Selecciona un tipo</option>
                            <?php if (!empty($tiposHabitacion)): ?>
                                <?php foreach ($tiposHabitacion as $tipo): ?>
                                    <option value="<?php echo htmlspecialchars($tipo['id']); ?>">
                                        <?php echo htmlspecialchars($tipo['descripcion']); ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <option value="" disabled>No hay tipos de habitación definidos.</option>
                            <?php endif; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona un tipo de habitación.</div>
                    </div>

                    <div class="form-group">
                        <label for="costo" class="form-label">Costo por Noche (COP) <span class="required">*</span></label>
                        <input type="number" id="costo" name="costo" class="form-control" required min="0" step="1000" placeholder="Ej: 150000">
                        <div class="invalid-feedback">Ingresa un costo válido.</div>
                    </div>

                    <div class="form-group">
                        <label for="capacidad" class="form-label">Capacidad (Personas) <span class="required">*</span></label>
                        <input type="number" id="capacidad" name="capacidad" class="form-control" required min="1" max="20" placeholder="Ej: 2">
                        <div class="invalid-feedback">Ingresa una capacidad válida (1-20).</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea id="descripcion" name="descripcion" class="form-control" rows="3" placeholder="Describe las características de la habitación..."></textarea>
                    </div>

                    <div class="form-group full-width">
                        <label for="foto" class="form-label">Foto de la Habitación</label>
                        <input type="file" id="foto" name="foto" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Opcional. Sube una imagen representativa.</small>
                    </div>

                    <div class="form-group full-width" id="preview-container" style="display: none;">
                        <label class="form-label">Vista Previa de la Imagen</label>
                        <img id="image-preview" src="#" alt="Vista previa" style="max-width: 200px; max-height: 200px; border-radius: 8px; object-fit: cover;">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Habitación
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                    <a href="listaHabitaciones.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i>
                        Volver a la Lista
                    </a>
                </div>
            </form>
        </div>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/crearHabitacion.js"></script>

</body>
</html>