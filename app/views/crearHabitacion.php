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


    <div class="container">
        <div class="header">
            <h1>Agregar Habitación</h1>
        </div>

        <div class="form-section">
            <h2 class="form-title">
                Nueva Habitación
            </h2>
            <!-- Mensajes -->
            <div id="success-message" class="success-message">
                ✅ <strong>¡Habitación creada exitosamente!</strong>
                <div style="margin-top: 5px; font-size: 0.9rem;">La habitación ha sido registrada en el sistema.</div>
            </div>

            <div id="error-message" class="error-message">
                ❌ <strong>Error al crear la habitación</strong>
                <div id="error-text" style="margin-top: 5px; font-size: 0.9rem;"></div>
            </div>

            <form id="habitacion-form">
                <div class="form-grid">
                    <div class="form-group">
                        <label for="numero">Número de Habitación <span class="required">*</span></label>
                        <input type="text" id="numero" name="numero" required placeholder="Ej: 101, 201A">
                    </div>

                    <div class="form-group">
                        <label for="costo">Costo por Noche <span class="required">*</span></label>
                        <input type="number" id="costo" name="costo" required step="0.01" min="0" placeholder="Ej: 80000.00">
                    </div>

                    <div class="form-group">
                        <label for="capacidad">Capacidad (Personas) <span class="required">*</span></label>
                        <input type="number" id="capacidad" name="capacidad" required min="1" max="20" placeholder="Ej: 2">
                    </div>

                    <div class="form-group">
                        <label for="tipoHabitacion">Tipo de Habitación <span class="required">*</span></label>
                        <select id="tipoHabitacion" name="tipoHabitacion" required>
                            <option value="">Seleccione un tipo</option>
                            <option value="1">Individual</option>
                            <option value="2">Doble</option>
                            <option value="3">Suite</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="estado">Estado <span class="required">*</span></label>
                        <select id="estado" name="estado" required>
                            <option value="">Seleccione un estado</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupado">Ocupado</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="foto">Foto de la Habitación</label>
                        <div class="file-upload-wrapper">
                            <input type="file" id="foto" name="foto" class="file-upload-input" accept="image/*">
                            <div class="file-upload-button" id="file-upload-button">
                                📷 Seleccionar imagen
                            </div>
                        </div>
                        <div class="image-preview" id="image-preview">
                            <img id="preview-img" src="" alt="Vista previa">
                            <button type="button" class="remove-image" id="remove-image">Eliminar imagen</button>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="descripcion">Descripción</label>
                    <textarea id="descripcion" name="descripcion" rows="3" placeholder="Descripción detallada de la habitación, servicios incluidos, etc."></textarea>
                </div>

                <div class="form-group" id="mantenimiento-group" style="display: none;">
                    <label for="descripcionMantenimiento">Descripción del Mantenimiento</label>
                    <textarea id="descripcionMantenimiento" name="descripcionMantenimiento" rows="2" placeholder="Describa el problema o mantenimiento requerido"></textarea>
                </div>

                <div style="display: flex; gap: 15px; margin-top: 25px;">
                    <button type="submit" class="btn btn-primary">
                        Guardar Habitación
                    </button>
                    <button type="button" class="btn btn-secondary" id="reset-btn">
                        Limpiar Formulario
                    </button>
                </div>
            </form>

            <!-- Preview de la habitación -->
            <div class="habitacion-preview" id="habitacion-preview" style="display: none;">
                <div class="preview-title">
                    👁️ Vista previa de la habitación
                </div>
                <div class="preview-content" id="preview-content">
                    <!-- El contenido se generará dinámicamente -->
                </div>
            </div>
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
        const fileInput = document.getElementById('foto');
        const fileUploadButton = document.getElementById('file-upload-button');
        const imagePreview = document.getElementById('image-preview');
        const previewImg = document.getElementById('preview-img');
        const removeImageBtn = document.getElementById('remove-image');

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

        // Mostrar/ocultar campo de mantenimiento
        estadoSelect.addEventListener('change', function() {
            if (this.value === 'Mantenimiento') {
                mantenimientoGroup.style.display = 'block';
            } else {
                mantenimientoGroup.style.display = 'none';
                document.getElementById('descripcionMantenimiento').value = '';
            }
            updatePreview();
        });

        // Actualizar preview en tiempo real
        form.addEventListener('input', updatePreview);
        form.addEventListener('change', updatePreview);

        function updatePreview() {
            const formData = new FormData(form);
            const numero = formData.get('numero');
            const costo = formData.get('costo');
            const capacidad = formData.get('capacidad');
            const tipoHabitacion = formData.get('tipoHabitacion');
            const estado = formData.get('estado');
            const descripcion = formData.get('descripcion');

            if (numero || costo || capacidad || tipoHabitacion || estado) {
                habitacionPreview.style.display = 'block';
                
                let previewHTML = `
                    <div class="preview-item">
                        <div class="preview-label">Número:</div>
                        <div class="preview-value">${numero || 'Sin especificar'}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Tipo:</div>
                        <div class="preview-value">${tipoHabitacion ? tiposHabitacion[tipoHabitacion] : 'Sin especificar'}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Costo:</div>
                        <div class="preview-value">${costo ? '$' + parseFloat(costo).toLocaleString('es-CO') : 'Sin especificar'}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Capacidad:</div>
                        <div class="preview-value">${capacidad ? capacidad + ' persona' + (capacidad != 1 ? 's' : '') : 'Sin especificar'}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Estado:</div>
                        <div class="preview-value">${estado || 'Sin especificar'}</div>
                    </div>
                    <div class="preview-item">
                        <div class="preview-label">Descripción:</div>
                        <div class="preview-value">${descripcion || 'Sin descripción'}</div>
                    </div>
                `;

                // Agregar imagen si existe
                if (currentImageFile && previewImg.src) {
                    previewHTML += `
                        <div class="preview-item preview-image">
                            <div class="preview-label">Imagen:</div>
                            <img src="${previewImg.src}" alt="Vista previa de la habitación">
                        </div>
                    `;
                }

                previewContent.innerHTML = previewHTML;
            } else {
                habitacionPreview.style.display = 'none';
            }
        }

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

            // Simular guardado exitoso
            setTimeout(() => {
                // Agregar a la lista de existentes
                habitacionesExistentes.push(numero);
                
                // Mostrar éxito
                showSuccess();
                
                // Limpiar formulario y resetear imagen
                form.reset();
                resetImageUpload();
                habitacionPreview.style.display = 'none';
                mantenimientoGroup.style.display = 'none';
                
                // Scroll al mensaje de éxito
                successMessage.scrollIntoView({ behavior: 'smooth' });
            }, 500);
        });

        // Función para resetear la carga de imagen
        function resetImageUpload() {
            currentImageFile = null;
            imagePreview.style.display = 'none';
            fileUploadButton.classList.remove('has-file');
            fileUploadButton.innerHTML = '📷 Seleccionar imagen';
        }

        // Botón limpiar formulario
        document.getElementById('reset-btn').addEventListener('click', function() {
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
</body>
</html>