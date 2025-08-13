<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear PQRS - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts (nav y sidebar) -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesPqrs.css">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    
    <div class="container">
        <div class="header">
            <h1>Crear PQRS</h1>
            <p>Registra una nueva Petición, Queja, Reclamo, Sugerencia o Felicitación</p>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                <i class="fas fa-clipboard-list"></i>
                Nueva PQRS
            </h2>
            
            <!-- Mensajes -->
            <div id="success-message" class="success-message">
                ✅ <strong>¡PQRS creada exitosamente!</strong>
                <div style="margin-top: 5px; font-size: 0.9rem;">La PQRS ha sido registrada en el sistema.</div>
            </div>

            <div id="error-message" class="error-message">
                ❌ <strong>Error al crear la PQRS</strong>
                <div id="error-text" style="margin-top: 5px; font-size: 0.9rem;"></div>
            </div>

            <form id="pqrs-form" action="../controllers/pqrsController.php" method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="tipo">Tipo de PQRS <span class="required">*</span></label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="Peticiones">Petición</option>
                            <option value="Quejas">Queja</option>
                            <option value="Reclamos">Reclamo</option>
                            <option value="Sugerencias">Sugerencia</option>
                            <option value="Felicitaciones">Felicitación</option>
                        </select>
                        <small class="form-text text-muted">Selecciona el tipo de solicitud</small>
                    </div>

                    <div class="form-group">
                        <label for="prioridad">Prioridad <span class="required">*</span></label>
                        <select id="prioridad" name="prioridad" required>
                            <option value="">Seleccione una prioridad</option>
                            <option value="Bajo">Baja</option>
                            <option value="Alto">Alta</option>
                        </select>
                        <small class="form-text text-muted">Nivel de prioridad de la solicitud</small>
                    </div>

                    <div class="form-group">
                        <label for="categoria">Categoría <span class="required">*</span></label>
                        <select id="categoria" name="categoria" required>
                            <option value="">Seleccione una categoría</option>
                            <option value="Servicio">Servicio</option>
                            <option value="Habitación">Habitación</option>
                            <option value="Atención">Atención al Cliente</option>
                            <option value="Otro">Otro</option>
                        </select>
                        <small class="form-text text-muted">Categoría relacionada con la solicitud</small>
                    </div>

                    <div class="form-group">
                        <label for="numDocumento">Documento del Usuario <span class="required">*</span></label>
                        <div class="input-group">
                            <input type="text" id="numDocumento" name="numDocumento" required maxlength="15" placeholder="Ej: 1234567890">
                            <button type="button" class="btn btn-outline-secondary" id="validar-usuario-btn">
                                <i class="fas fa-search"></i> Validar
                            </button>
                        </div>
                        <small class="form-text text-muted">Número de documento del usuario que realiza la PQRS</small>
                        <div id="usuario-info" class="mt-2" style="display: none;">
                            <div class="alert alert-success">
                                <i class="fas fa-user-check"></i>
                                <strong id="usuario-nombre">Usuario válido</strong>
                            </div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="descripcion">Descripción <span class="required">*</span></label>
                        <textarea id="descripcion" name="descripcion" required maxlength="1000" rows="5" placeholder="Describa detalladamente su petición, queja, reclamo, sugerencia o felicitación..."></textarea>
                        <small class="form-text text-muted">Máximo 1000 caracteres. <span id="contador-chars">0/1000</span></small>
                    </div>
                </div>

                <!-- Vista previa de la PQRS -->
                <div class="pqrs-preview" id="pqrs-preview" style="display: none;">
                    <div class="preview-title">
                        <i class="fas fa-eye"></i>
                        Vista Previa de la PQRS
                    </div>
                    <div class="preview-content" id="preview-content">
                        <!-- Contenido dinámico -->
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i>
                        Crear PQRS
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        <i class="fas fa-eraser"></i>
                        Limpiar Formulario
                    </button>
                    <button type="button" class="btn btn-info" id="preview-btn">
                        <i class="fas fa-eye"></i>
                        Vista Previa
                    </button>
                    <a href="listaPqrs.php" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i>
                        Ver PQRS
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/pqrs.js"></script>

</body>
</html>