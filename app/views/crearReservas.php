<?php
require_once 'validarSesion.php';
$currentPage = 'Reservas'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Reserva - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesMantenimiento.css"> <!-- Reutilizamos estilos -->
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_id = $_SESSION['hotel_id'] ?? null;
        $hotel_nombre = $_SESSION['hotel_nombre'] ?? 'No asignado';
        $us_numDocumento = $_SESSION['user']['numDocumento'] ?? null;
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    
    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder crear una reserva, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
        <div class="header">
            <h1><i class="fas fa-calendar-plus"></i> Crear Nueva Reserva</h1>
            <p>Registra una nueva reserva en el sistema para el hotel seleccionado.</p>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                <i class="fas fa-book"></i>
                Detalles de la Reserva
            </h2>
            
            <!-- Mensajes de alerta -->
            <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
            <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

            <form id="form-crear-reserva" novalidate>
                <!-- Campos ocultos -->
                <input type="hidden" name="id_hotel" value="<?php echo htmlspecialchars($hotel_id); ?>">
                <input type="hidden" name="us_numDocumento" value="<?php echo htmlspecialchars($us_numDocumento); ?>">

                <div class="form-grid">
                    <!-- Búsqueda de Huésped -->
                    <div class="form-group full-width">
                        <label for="buscar-huesped" class="form-label">Buscar Huésped <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="text" id="buscar-huesped" class="form-control" placeholder="Buscar por nombre, apellido o documento...">
                            <a href="crearHuesped.php" class="btn btn-outline-success" target="_blank" title="Crear Nuevo Huésped">
                                <i class="fas fa-user-plus"></i>
                            </a>
                        </div>
                        <div id="sugerencias-huesped" class="list-group" style="position: absolute; z-index: 1000; width: 95%;"></div>
                        <input type="hidden" id="hue_numDocumento" name="hue_numDocumento" required>
                        <div class="invalid-feedback">Por favor, selecciona un huésped.</div>
                        <div id="huesped-seleccionado" class="alert alert-info mt-2" style="display: none;"></div>
                    </div>

                    <!-- Selección de Fechas -->
                    <div class="form-group">
                        <label for="fechainicio" class="form-label">Fecha Inicio <span class="required">*</span></label>
                        <input type="date" id="fechainicio" name="fechainicio" class="form-control" required>
                        <div class="invalid-feedback">Por favor, selecciona una fecha de inicio.</div>
                    </div>
                    <div class="form-group">
                        <label for="fechaFin" class="form-label">Fecha Fin <span class="required">*</span></label>
                        <input type="date" id="fechaFin" name="fechaFin" class="form-control" required>
                        <div class="invalid-feedback">Por favor, selecciona una fecha de fin.</div>
                    </div>

                    <!-- Selección de Habitación -->
                    <div class="form-group">
                        <label for="id_habitacion" class="form-label">Habitación Disponible <span class="required">*</span></label>
                        <select id="id_habitacion" name="id_habitacion" class="form-select" required disabled>
                            <option value="">Selecciona las fechas primero</option>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona una habitación.</div>
                    </div>

                    <!-- Detalles de la Habitación (se rellena con JS) -->
                    <div id="detalles-habitacion" class="form-group" style="display: none;">
                        <label class="form-label">Detalles de la Habitación</label>
                        <div class="alert alert-secondary">
                            <strong>Tipo:</strong> <span id="hab-tipo"></span><br>
                            <strong>Capacidad:</strong> <span id="hab-capacidad"></span> personas<br>
                            <strong>Costo/noche:</strong> $<span id="hab-costo"></span>
                        </div>
                    </div>

                    <!-- Cantidad de Personas -->
                    <div class="form-group">
                        <label for="cantidadAdultos" class="form-label">Adultos <span class="required">*</span></label>
                        <input type="number" id="cantidadAdultos" name="cantidadAdultos" class="form-control" min="1" value="1" required>
                    </div>
                    <div class="form-group">
                        <label for="cantidadNinos" class="form-label">Niños</label>
                        <input type="number" id="cantidadNinos" name="cantidadNinos" class="form-control" min="0" value="0">
                    </div>

                    <!-- Motivo y Método de Pago -->
                    <div class="form-group">
                        <label for="motivoReserva" class="form-label">Motivo de la Reserva <span class="required">*</span></label>
                        <select id="motivoReserva" name="motivoReserva" class="form-select" required>
                            <option value="Personal">Personal</option>
                            <option value="Negocios">Negocios</option>
                            <option value="Viaje">Viaje</option>
                            <option value="Familiar">Familiar</option>
                            <option value="Otro">Otro</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="metodoPago" class="form-label">Método de Pago <span class="required">*</span></label>
                        <select id="metodoPago" name="metodoPago" class="form-select" required>
                            <option value="Efectivo">Efectivo</option>
                            <option value="Tarjeta">Tarjeta</option>
                            <option value="PSE">PSE</option>
                        </select>
                    </div>

                    <!-- Información Adicional -->
                    <div class="form-group full-width">
                        <label for="informacionAdicional" class="form-label">Información Adicional</label>
                        <textarea id="informacionAdicional" name="informacionAdicional" class="form-control" rows="3" placeholder="Añade notas o peticiones especiales aquí..."></textarea>
                    </div>
                </div>

                <!-- Vista previa y Costo Total -->
                <div class="pqrs-preview mt-4" id="reserva-preview" style="display: none;">
                    <div class="preview-title">
                        <i class="fas fa-file-invoice-dollar"></i>
                        Resumen de la Reserva
                    </div>
                    <div class="preview-content" id="preview-content">
                        <!-- Contenido generado por JS -->
                    </div>
                    <div class="preview-footer">
                        <h4>Costo Total: $<span id="costo-total">0.00</span></h4>
                        <input type="hidden" id="pagoFinal" name="pagoFinal" value="0">
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Guardar Reserva
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        <i class="fas fa-eraser"></i>
                        Limpiar
                    </button>
                    <button type="button" class="btn btn-info" id="preview-btn">
                        <i class="fas fa-eye"></i>
                        Calcular y Ver Resumen
                    </button>
                    <div class="form-group">
                    <a href="listaReservas.php" class="btn btn-outline-primary">
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
    <script src="../../public/assets/js/crearReservas.js"></script>

</body>
</html>