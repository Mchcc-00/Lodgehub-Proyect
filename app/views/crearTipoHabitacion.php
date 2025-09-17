<?php
require_once 'validarSesion.php';
$currentPage = 'Habitaciones'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Tipo de Habitación - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitaciones.css"> <!-- Reutilizamos estilos -->
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

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
                <p>Para poder crear un nuevo tipo de habitación, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
        <div class="header">
            <h1><i class="fas fa-plus-circle"></i> Crear Tipo de Habitación</h1>
            <p>Registra un nuevo tipo de habitación para el hotel <strong><?php echo htmlspecialchars($hotel_nombre); ?></strong>.</p>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                <i class="fas fa-door-closed"></i>
                Nuevo Tipo
            </h2>

            <!-- Mensajes de alerta -->
            <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
            <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

            <form id="form-crear-tipo-habitacion" novalidate>
                <!-- Campos ocultos -->
                <input type="hidden" name="id_hotel" value="<?php echo htmlspecialchars($hotel_id); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="hotel_nombre" class="form-label">Hotel Asignado</label>
                        <input type="text" id="hotel_nombre" name="hotel_nombre" class="form-control" value="<?php echo htmlspecialchars($hotel_nombre); ?>" readonly>
                    </div>

                    <div class="form-group">
                        <label for="descripcion" class="form-label">Descripción <span class="required">*</span></label>
                        <input type="text" id="descripcion" name="descripcion" class="form-control" required maxlength="20" placeholder="Ej: Suite, Doble, Individual">
                        <div class="invalid-feedback">La descripción es obligatoria (máx. 20 caracteres).</div>
                        <small class="form-text text-muted">Nombre que identificará el tipo de habitación.</small>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Tipo
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                    <a href="listaTipoHabitacion.php" class="btn btn-outline-primary">
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
    <script src="../../public/assets/js/crearTipoHabitacion.js"></script>

</body>
</html>