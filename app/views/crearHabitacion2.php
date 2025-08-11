<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Habitación - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

    <!-- Estilos de los layouts (nav y sidebar) -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitacion.css">
</head>

<body>

    <?php
    include "layouts/sidebar.php";
    include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>


    <div class="contenedorCrearHab">
        <div class="headerFormCrearHab">
            <h1>Agregar Habitación</h1>
        </div>
        <div class="contenidoFormCrearHab">

            <h2 class="formTitleCrearHab">
                Nueva Habitación
            </h2>
            <!-- Mensajes
            <div id="success-message" class="success-message">
                ✅ <strong>¡Habitación creada exitosamente!</strong>
                <div style="margin-top: 5px; font-size: 0.9rem;">La habitación ha sido registrada en el sistema.</div>
            </div>

            <div id="error-message" class="error-message">
                ❌ <strong>Error al crear la habitación</strong>
                <div id="error-text" style="margin-top: 5px; font-size: 0.9rem;"></div>
            </div> -->

            <form id="habitacionFormCrear" action="../models/guardarHabitacion.php" method="POST" enctype="multipart/form-data">
                <div class="formGrid">
                    <div class="formGroupHab">
                        <label for="numeroNewHab">Número de Habitación <span class="required">*</span><input type="text" id="numeroNewHab" name="numeroNewHab" required placeholder="Ej: 101, 201A"></label>
                    </div>

                    <div class="formGroupHab">
                        <label for="costoNewHab">Costo por Noche <span class="required">*</span><input type="text" id="costoNewHab" name="costoNewHab" required min="1" placeholder="Ej: $500.000"></label>
                    </div>

                    <div class="formGroupHab">
                        <label for="capacidadPersonasNewHab">Capacidad (Personas) <span class="required">*</span><input type="number" id="capacidadPersonasNewHab" name="capacidadPersonasNewHab" required min="1" max="20" placeholder="Ej: 2"></label>
                    </div>

                    <div class="formGroupHab">
                        <label for="tipoNewHab">Tipo de Habitación <span class="required">*</span>
                            <select id="tipoNewHab" name="tipoNewHab" required>
                                <option value="" disabled selected>Seleccione</option>
                                <option value="1">Individual</option>
                                <option value="2">Doble</option>
                                <option value="3">Suite</option>
                            </select>
                        </label>
                    </div>

                    <div class="formGroupHab">
                        <label for="fotoNewHab">Foto de la Habitación</label>
                        <div class="fileUploadContenedor">
                            <input type="file" id="fotoNewHab" name="fotoNewHab" class="fileUploadInputNewHab" accept="image/*">
                            <div class="fileUploadBtn" id="fileUploadBtnNewHab">
                                📷 Seleccionar imagen
                            </div>
                        </div>
                        <div class="imagenPreview" id="imagenPreviewNewHab">
                            <img id="previewImgNewHab" src="" alt="Vista previa">
                            <button type="button" class="removeImage" id="removeImageNewHab">Eliminar imagen</button>
                        </div>
                    </div>
                </div>

                <div class="formGroupHab">
                    <label for="descripcionNewHab">Descripción</label>
                    <textarea id="descripcionNewHab" name="descripcionNewHab" rows="3" placeholder="Descripción detallada de la habitación, servicios incluidos, etc."></textarea>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">
                        Guardar Habitación
                    </button>
                    <button type="button" class="btn btn-secondary" id="resetBtnFormNewHab">
                        Limpiar Formulario
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Base de datos simulada para verificar duplicados
        let habitacionesExistentes = ['101', '201', '301']; // Simula números ya existentes

        const tiposHabitacion = {
            1: 'Individual',
            2: 'Doble',
            3: 'Suite'
        };

        // Referencias DOM
        const form = document.getElementById('habitacion-form');
        const estadoSelect = document.getElementById('estado');
        const mantenimientoGroup = document.getElementById('mantenimiento-group');
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        const habitacionPreview = document.getElementById('habitacion-preview');
        const previewContent = document.getElementById('preview-content');

        // Referencias para manejo de archivos
        const fileInput = document.getElementById('fotoNewHab');
        const fileUploadButton = document.getElementById('fileUploadBtnNewHab');
        const imagePreview = document.getElementById('imagenPreviewNewHab');
        const previewImg = document.getElementById('previewImgNewHab');
        const removeImageBtn = document.getElementById('removeImageNewHab');

        let currentImageFile = null;

        // Manejo del archivo de imagen
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                // Validar que sea una imagen
                if (!file.type.startsWith('image/')) {
                    showError('Por favor seleccione un archivo de imagen válido');
                    fileInput.value = '';
                    return;
                }

                // Validar tamaño (máximo 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    showError('La imagen debe ser menor a 5MB');
                    fileInput.value = '';
                    return;
                }

                currentImageFile = file;

                // Crear URL para vista previa
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    imagePreview.style.display = 'block';
                    fileUploadButton.classList.add('has-file');
                    fileUploadButton.innerHTML = '✅ ' + file.name;
                };
                reader.readAsDataURL(file);
            }
            updatePreview();
        });

        // Remover imagen
        removeImageBtn.addEventListener('click', function() {
            currentImageFile = null;
            fileInput.value = '';
            imagePreview.style.display = 'none';
            fileUploadButton.classList.remove('has-file');
            fileUploadButton.innerHTML = '📷 Seleccionar imagen';
            updatePreview();
        });

        // Manejar envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            hideMessages();
            
            const formData = new FormData(form);
            const numero = formData.get('numero').trim();
            
            // Validar número único
            if (habitacionesExistentes.includes(numero)) {
                showError('Ya existe una habitación con el número "' + numero + '"');
                return;
            }

            // Validar campos requeridos
            if (!numero || !formData.get('costo') || !formData.get('capacidad') || 
                !formData.get('tipoHabitacion') || !formData.get('estado')) {
                showError('Por favor complete todos los campos obligatorios (*)');
                return;
            }
        });

        // Función para resetear la carga de imagen
        function resetImageUpload() {
            currentImageFile = null;
            imagePreview.style.display = 'none';
            fileUploadButton.classList.remove('has-file');
            fileUploadButton.innerHTML = '📷 Seleccionar imagen';
        }

        // Botón limpiar formulario
        document.getElementById('resetBtnFormNewHab').addEventListener('click', function() {
            form.reset();
            hideMessages();
            resetImageUpload();
            habitacionPreview.style.display = 'none';
            mantenimientoGroup.style.display = 'none';
        });

        // Funciones para mostrar mensajes
        function showSuccess() {
            successMessage.style.display = 'block';
            errorMessage.style.display = 'none';
        }

        function showError(message) {
            errorText.textContent = message;
            errorMessage.style.display = 'block';
            successMessage.style.display = 'none';
        }

        function hideMessages() {
            successMessage.style.display = 'none';
            errorMessage.style.display = 'none';
        }

        // Auto-hide success message after 5 seconds
        function autoHideSuccess() {
            setTimeout(() => {
                if (successMessage.style.display === 'block') {
                    successMessage.style.display = 'none';
                }
            }, 5000);
        }

        // Llamar autoHideSuccess cuando se muestre el mensaje de éxito
        const originalShowSuccess = showSuccess;
        showSuccess = function() {
            originalShowSuccess();
            autoHideSuccess();
        };
    </script>
    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebarOverlay');
            const body = document.body;

            sidebar.classList.toggle('show');
            body.classList.toggle('sidebar-open');

            // Solo mostrar overlay en móvil
            if (window.innerWidth < 992) {
                overlay.classList.toggle('show');
            }

            // Cambiar el ícono del botón collapse
            const collapseBtn = document.querySelector('.btn-collapse-sidebar i');
            if (collapseBtn) {
                if (sidebar.classList.contains('show')) {
                    collapseBtn.className = 'fas fa-chevron-left';
                } else {
                    collapseBtn.className = 'fas fa-chevron-right';
                }
            }
        }

        // Cerrar sidebar al hacer clic en un enlace solo en móvil
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');

            sidebarLinks.forEach(link => {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 992) {
                        toggleSidebar();
                    }
                });
            });

            // Manejar resize de ventana
            window.addEventListener('resize', function() {
                const overlay = document.getElementById('sidebarOverlay');

                if (window.innerWidth >= 992) {
                    overlay.classList.remove('show');
                }
            });
        });
    </script>
</body>

</html>