<?php
require_once 'validarSesion.php';
$currentPage = 'Mantenimiento'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Mantenimiento - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesMantenimiento.css"> <!-- Reutilizamos estilos de Mantenimiento -->
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
        
        // VALIDACIÓN: Asegurarse de que un hotel ha sido seleccionado
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_id = $_SESSION['hotel_id'] ?? null;
        $hotel_nombre = $_SESSION['hotel_nombre'] ?? 'No asignado';
        $usuario_actual_id = $_SESSION['user']['numDocumento'] ?? null;

    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    
    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder registrar una nueva tarea de mantenimiento, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
        <div class="header">
            <h1>Crear Mantenimiento</h1>
            <p>Registra una nueva tarea de mantenimiento en el sistema.</p>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                <i class="fas fa-tools"></i>
                Nueva Tarea de Mantenimiento
            </h2>
            
            <!-- Mensajes de alerta -->
            <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
            <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

            <form id="form-crear-mantenimiento" novalidate>
                <!-- Campos ocultos -->
                <input type="hidden" name="id_hotel" value="<?php echo htmlspecialchars($hotel_id); ?>">

                <div class="form-grid">
                    <div class="form-group">
                        <label for="hotel_nombre" class="form-label">Hotel Asignado</label>
                        <input type="text" id="hotel_nombre" name="hotel_nombre" class="form-control" value="<?php echo htmlspecialchars($hotel_nombre); ?>" readonly>
                        <small class="form-text text-muted">La tarea de mantenimiento se asignará a este hotel.</small>
                    </div>

                    <div class="form-group">
                        <label for="id_habitacion" class="form-label">Habitación <span class="required">*</span></label>
                        <select id="id_habitacion" name="id_habitacion" class="form-select" required>
                            <option value="">Cargando habitaciones...</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona una habitación.</div>
                    </div>

                    <div class="form-group">
                        <label for="tipo" class="form-label">Tipo de Mantenimiento <span class="required">*</span></label>
                        <select id="tipo" name="tipo" class="form-select" required>
                            <option value="">Selecciona un tipo</option>
                            <option value="Limpieza">Limpieza</option>
                            <option value="Estructura">Estructura</option>
                            <option value="Eléctrico">Eléctrico</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona un tipo.</div>
                    </div>

                    <div class="form-group">
                        <label for="prioridad" class="form-label">Prioridad <span class="required">*</span></label>
                        <select id="prioridad" name="prioridad" class="form-select" required>
                            <option value="Bajo">Bajo</option>
                            <option value="Alto">Alto</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="numDocumento" class="form-label">Responsable <span class="required">*</span></label>
                        <select id="numDocumento" name="numDocumento" class="form-select" required>
                            <option value="">Cargando responsables...</option>
                        </select>
                        <div class="invalid-feedback">Por favor, asigna un responsable.</div>
                    </div>

                    <div class="form-group">
                        <label for="frecuencia" class="form-label">¿Es recurrente? <span class="required">*</span></label>
                        <select id="frecuencia" name="frecuencia" class="form-select" required>
                            <option value="No">No</option>
                            <option value="Sí">Sí</option>
                        </select>
                    </div>

                    <div class="form-group" id="grupo-cantFrecuencia" style="display: none;">
                        <label for="cantFrecuencia" class="form-label">Frecuencia <span class="required">*</span></label>
                        <select id="cantFrecuencia" name="cantFrecuencia" class="form-select">
                            <option value="Diario">Diario</option>
                            <option value="Semanal">Semanal</option>
                            <option value="Quincenal">Quincenal</option>
                            <option value="Mensual">Mensual</option>
                        </select>
                    </div>

                    <div class="form-group full-width">
                        <label for="problemaDescripcion" class="form-label">Descripción del Problema <span class="required">*</span></label>
                        <textarea id="problemaDescripcion" name="problemaDescripcion" class="form-control" rows="3" required maxlength="50" placeholder="Describe brevemente el problema a solucionar..."></textarea>
                        <div class="invalid-feedback">La descripción es obligatoria (máx. 50 caracteres).</div>
                    </div>

                    <div class="form-group full-width">
                        <label for="observaciones" class="form-label">Observaciones Adicionales</label>
                        <textarea id="observaciones" name="observaciones" class="form-control" rows="3" placeholder="Añade notas o detalles adicionales aquí..."></textarea>
                    </div>
                </div>

                <!-- Vista previa -->
                <div class="pqrs-preview" id="mantenimiento-preview" style="display: none;">
                    <div class="preview-title">
                        <i class="fas fa-eye"></i>
                        Vista Previa del Mantenimiento
                    </div>
                    <div class="preview-content" id="preview-content">
                        <!-- Contenido generado dinámicamente por JS -->
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Mantenimiento
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                    <button type="button" class="btn btn-info" id="preview-btn">
                        <i class="fas fa-eye"></i>
                        Vista Previa
                    </button>
                    <a href="listaMantenimiento.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i>
                        Volver a la Lista
                    </a>
                </div>
            </form>
        </div>
        <?php endif; // Fin del bloque de validación ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/crearMantenimiento.js"></script>

</body>
</html>