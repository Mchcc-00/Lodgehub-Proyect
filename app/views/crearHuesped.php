<?php
require_once 'validarSesion.php';
$currentPage = 'Huespedes'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Huésped - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHuesped.css">
</head>

<body>

    <?php
    include "layouts/sidebar.php";
    include "layouts/navbar.php";

    $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
    $hotel_id = $_SESSION['hotel_id'] ?? null;
    $hotel_nombre = $_SESSION['hotel_nombre'] ?? 'No asignado';
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder registrar un nuevo huésped, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
            <div class="header">
                <h1><i class="fas fa-user-plus"></i> Crear Nuevo Huésped</h1>
                <p>Registra un nuevo huésped en el sistema para el hotel "<?php echo htmlspecialchars($hotel_nombre); ?>".</p>
            </div>

            <div class="form-section">
                <h2 class="form-title">
                    <i class="fas fa-id-card"></i>
                    Información del Huésped
                </h2>

                <!-- Mensajes de alerta -->
                <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
                <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

                <form id="form-crear-huesped" novalidate>
                    <!-- Campos ocultos -->
                    <input type="hidden" name="id_hotel" value="<?php echo htmlspecialchars($hotel_id); ?>">

                    <div class="form-grid">

                        <div class="form-group">
                            <label for="hotel_nombre" class="form-label">Hotel Asignado</label>
                            <input type="text" id="hotel_nombre" class="form-control" value="<?php echo htmlspecialchars($hotel_nombre); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label for="tipoDocumento" class="form-label">Tipo de Documento <span class="required">*</span></label>
                            <select id="tipoDocumento" name="tipoDocumento" class="form-select" required>
                                <option value="">Selecciona un tipo</option>
                                <option value="Cédula de Ciudadanía">Cédula de Ciudadanía</option>
                                <option value="Tarjeta de Identidad">Tarjeta de Identidad</option>
                                <option value="Cedula de Extranjeria">Cédula de Extranjería</option>
                                <option value="Pasaporte">Pasaporte</option>
                                <option value="Registro Civil">Registro Civil</option>
                            </select>
                            <div class="invalid-feedback">Selecciona un tipo de documento.</div>
                        </div>

                        <div class="form-group">
                            <label for="numDocumento" class="form-label">Número de Documento <span class="required">*</span></label>
                            <input type="text" id="numDocumento" name="numDocumento" class="form-control" required>
                            <div class="invalid-feedback">Ingresa un número de documento.</div>
                            <div id="documento-feedback" class="documento-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="nombres" class="form-label">Nombres <span class="required">*</span></label>
                            <input type="text" id="nombres" name="nombres" class="form-control" required>
                            <div class="invalid-feedback">Ingresa los nombres.</div>
                        </div>

                        <div class="form-group">
                            <label for="apellidos" class="form-label">Apellidos <span class="required">*</span></label>
                            <input type="text" id="apellidos" name="apellidos" class="form-control" required>
                            <div class="invalid-feedback">Ingresa los apellidos.</div>
                        </div>

                        <div class="form-group">
                            <label for="correo" class="form-label">Correo Electrónico <span class="required">*</span></label>
                            <input type="email" id="correo" name="correo" class="form-control" required>
                            <div class="invalid-feedback">Ingresa un correo válido.</div>
                            <div id="correo-feedback" class="correo-feedback" style="display: none;"></div>
                        </div>

                        <div class="form-group">
                            <label for="numTelefono" class="form-label">Teléfono <span class="required">*</span></label>
                            <input type="tel" id="numTelefono" name="numTelefono" class="form-control" required>
                            <div class="invalid-feedback">Ingresa un número de teléfono.</div>
                        </div>

                        <div class="form-group">
                            <label for="sexo" class="form-label">Sexo <span class="required">*</span></label>
                            <select id="sexo" name="sexo" class="form-select" required>
                                <option value="">Selecciona un sexo</option>
                                <option value="Hombre">Hombre</option>
                                <option value="Mujer">Mujer</option>
                                <option value="Otro">Otro</option>
                                <option value="Prefiero no decirlo">Prefiero no decirlo</option>
                            </select>
                            <div class="invalid-feedback">Selecciona una opción.</div>
                        </div>


                    </div>

                    <!-- Vista previa -->
                    <div class="huesped-preview" id="huesped-preview" style="display: none;">
                        <div class="preview-title">
                            <i class="fas fa-eye"></i>
                            Vista Previa del Huésped
                        </div>
                        <div class="preview-content" id="preview-content">
                            <!-- Contenido generado dinámicamente por JS -->
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i>
                            Guardar Huésped
                        </button>
                        <button type="button" class="btn btn-secondary" id="reset-btn">
                            <i class="fas fa-eraser"></i>
                            Limpiar
                        </button>
                        <button type="button" class="btn btn-primary" id="preview-btn">
                            <i class="fas fa-eye"></i>
                            Vista Previa
                        </button>
                        <a href="listaHuesped.php" class="btn btn-outline-primary">
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
    <script src="../../public/assets/js/crearHuesped.js"></script>

</body>

</html>