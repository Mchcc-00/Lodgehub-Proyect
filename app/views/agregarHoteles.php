<?php
require_once 'validarSesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Hotel</title>
    <!-- CSS personalizado adaptado -->
    <link rel="stylesheet" href="../../public/assets/css/stylesHotel.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNav.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php
    include 'layouts/sidebar.php';
    include 'layouts/navbar.php';
    ?>
     <script src="../../public/assets/js/sidebar.js"></script>

    <!-- Contenido principal adaptado para sidebar y navbar -->
    <div class="main-content" id="main-content">
        <div class="container">


            <div class="header">
                <h1><i class="fas fa-hotel"></i> Agregar hotel</h1>
                <p>¡Agrega y valida la información de tu hotel!</p>
            </div>

            <div class="form-content">
                <form id="hotel-form" action="/api/v1/hotel.php" method="POST" enctype="multipart/form-data">
                    <input type="hidden" id="hotelId" name="id">

                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nit" class="form-label">
                                <i class="fas fa-id-card"></i> NIT
                            </label>
                            <input type="text" id="nit" name="nit" class="form-input" required maxlength="20"
                                placeholder="Ej: 901234567-1">
                            <div class="error" id="nit-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="nombre" class="form-label">
                                <i class="fas fa-hotel"></i> Nombre del Hotel
                            </label>
                            <input type="text" id="nombre" name="nombre" class="form-input" required maxlength="100"
                                placeholder="Nombre completo del hotel">
                            <div class="char-counter" id="nombre-counter">0/100</div>
                            <div class="error" id="nombre-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="numDocumento" class="form-label">
                                <i class="fas fa-user-tie"></i> Documento Administrador
                            </label>
                            <input type="text" id="numDocumento" name="numDocumento" class="form-input" required
                                placeholder="Número de documento del administrador"
                                value="<?php echo htmlspecialchars($_SESSION['user']['numDocumento'] ?? ''); ?>" readonly>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i>
                                Solo puedes registrar hoteles con tu propio número de documento
                            </small>
                            <div class="error" id="numDocumento-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="telefono" class="form-label">
                                <i class="fas fa-phone"></i> Teléfono
                            </label>
                            <input type="tel" id="telefono" name="telefono" class="form-input" maxlength="20"
                                placeholder="Ej: +57 300 123 4567">
                            <div class="error" id="telefono-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="correo" class="form-label">
                                <i class="fas fa-envelope"></i> Correo Electrónico
                            </label>
                            <input type="email" id="correo" name="correo" class="form-input" maxlength="100"
                                placeholder="correo@ejemplo.com">
                            <div class="error" id="correo-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="foto"  class="form-label">
                                <i class="fas fa-image"></i> Foto del Hotel
                            </label>
                            <input type="file" id="foto" name="foto" class="form-control" class="form-input" accept="image/jpeg, image/png, image/gif">
                            <div class="image-preview-container mt-2" id="image-preview-container" style="display:none;">
                                <img id="image-preview" src="#" alt="Vista previa de la imagen" class="image-preview"/>
                                <span class="image-preview-text">Vista previa</span>
                            </div>
                            <div class="error" id="foto-error"></div>
                        </div>
                    </div>

                    <div class="form-group full-width">
                        <label for="direccion" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Dirección
                        </label>
                        <textarea id="direccion" name="direccion" rows="3" class="form-input" maxlength="200"
                            placeholder="Dirección completa del hotel"></textarea>
                        <div class="char-counter" id="direccion-counter">0/200</div>
                        <div class="error" id="direccion-error"></div>
                    </div>

                    <div class="form-group full-width">
                        <label for="descripcion" class="form-label">
                            <i class="fas fa-map-marker-alt"></i> Descripción del Hotel
                        </label>
                        <textarea id="descripcion" name="descripcion" rows="4" class="form-input" maxlength="1000"
                            placeholder="Describe las características, servicios y amenidades del hotel..."></textarea>
                        <div class="char-counter" id="descripcion-counter">0/1000</div>
                        <div class="error" id="descripcion-error"></div>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span id="submitText">Guardar Hotel</span>
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="cancelEdit()"
                            id="cancelBtn" style="display:none;">
                            <i class="fas fa-times"></i>
                            Cancelar
                        </button>
                    </div>

                    <div id="form-messages"></div>
                </form>
            </div>

        </div>
    </div>

    <!-- Scripts -->
    <script src="../../public/assets/js/sidebar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/hotelScript.js"></script>

    <script>
        // Script para manejar el colapso del sidebar
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
            const mainContent = document.getElementById('main-content');

            if (sidebarToggle && mainContent) {
                sidebarToggle.addEventListener('click', function() {
                    mainContent.classList.toggle('sidebar-collapsed');
                });
            }
        });
    </script>
</body>

</html>