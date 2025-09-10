/**
 * JavaScript para formularios de habitaciones
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const formHabitacion = document.getElementById('form-habitacion');
    const selectHotel = document.getElementById('id_hotel');
    const selectTipo = document.getElementById('tipoHabitacion');
    const selectEstado = document.getElementById('estado');
    const grupoMantenimiento = document.getElementById('grupo-mantenimiento');
    const btnPreview = document.getElementById('btn-preview');
    const previewSection = document.getElementById('preview-section');
    const previewContent = document.getElementById('preview-content');
    
    // Elementos de imagen
    const imageUploadArea = document.getElementById('image-upload-area');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const removeImageBtn = document.getElementById('remove-image');
    const inputImagen = document.getElementById('imagen');
    const inputFoto = document.getElementById('foto');
    
    // Mensajes
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');
    
    // ============================================
    // EVENTOS
    // ============================================
    
    // Cambio de hotel - cargar tipos
    if (selectHotel) {
        selectHotel.addEventListener('change', cargarTiposHabitacion);
    }
    
    // Cambio de estado - mostrar/ocultar campos de mantenimiento
    if (selectEstado) {
        selectEstado.addEventListener('change', toggleCamposMantenimiento);
    }
    
    // Vista previa
    if (btnPreview) {
        btnPreview.addEventListener('click', mostrarVistaPrevia);
    }
    
    // Envío del formulario
    if (formHabitacion) {
        formHabitacion.addEventListener('submit', enviarFormulario);
    }
    
    // Manejo de imagen
    if (imageUploadArea) {
        setupImageUpload();
    }
    
    // Validación en tiempo real
    setupValidacionTiempoReal();
    
    // Inicialización
    if (selectEstado) {
        toggleCamposMantenimiento();
    }
    
    // ============================================
    // FUNCIONES PRINCIPALES
    // ============================================
    
    /**
     * Cargar tipos de habitación según el hotel seleccionado
     */
    function cargarTiposHabitacion() {
        const hotelId = selectHotel.value;
        
        if (!hotelId) {
            selectTipo.innerHTML = '<option value="">Selecciona primero un hotel</option>';
            selectTipo.disabled = true;
            return;
        }
        
        // Mostrar loading
        selectTipo.innerHTML = '<option value="">Cargando tipos...</option>';
        selectTipo.disabled = true;
        
        fetch(`?action=obtener-tipos&hotel=${hotelId}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    let opciones = '<option value="">Seleccionar tipo de habitación</option>';
                    data.data.forEach(tipo => {
                        opciones += `<option value="${tipo.id}">${escapeHtml(tipo.descripcion)}</option>`;
                    });
                    selectTipo.innerHTML = opciones;
                    selectTipo.disabled = false;
                } else {
                    selectTipo.innerHTML = '<option value="">Error al cargar tipos</option>';
                    mostrarError('Error al cargar los tipos de habitación');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                selectTipo.innerHTML = '<option value="">Error de conexión</option>';
                mostrarError('Error de conexión');
            });
    }
    
    /**
     * Mostrar/ocultar campos de mantenimiento
     */
    function toggleCamposMantenimiento() {
        const estado = selectEstado.value;
        if (grupoMantenimiento) {
            grupoMantenimiento.style.display = estado === 'Mantenimiento' ? 'block' : 'none';
            
            const textarea = document.getElementById('descripcionMantenimiento');
            if (textarea) {
                if (estado === 'Mantenimiento') {
                    textarea.setAttribute('required', 'required');
                } else {
                    textarea.removeAttribute('required');
                    textarea.value = '';
                }
            }
        }
    }
    
    /**
     * Mostrar vista previa del formulario
     */
    function mostrarVistaPrevia() {
        const formData = new FormData(formHabitacion);
        const datos = {};
        
        // Recopilar datos del formulario
        for (let [key, value] of formData.entries()) {
            datos[key] = value;
        }
        
        // Obtener nombres descriptivos
        const hotelNombre = selectHotel.options[selectHotel.selectedIndex]?.text || 'No seleccionado';
        const tipoNombre = selectTipo.options[selectTipo.selectedIndex]?.text || 'No seleccionado';
        
        // Generar preview HTML
        const previewHTML = `
            <div class="preview-item">
                <span class="preview-label">Hotel:</span>
                <span class="preview-value">${escapeHtml(hotelNombre)}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Número:</span>
                <span class="preview-value">${escapeHtml(datos.numero || 'No especificado')}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Tipo:</span>
                <span class="preview-value">${escapeHtml(tipoNombre)}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Capacidad:</span>
                <span class="preview-value">${datos.capacidad || '0'} personas</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Costo:</span>
                <span class="preview-value">${formatearNumero(datos.costo || 0)} por noche</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Estado:</span>
                <span class="preview-value">
                    <span class="badge bg-${getEstadoColor(datos.estado)}">${datos.estado}</span>
                </span>
            </div>
            ${datos.descripcion ? `
                <div class="preview-item">
                    <span class="preview-label">Descripción:</span>
                    <span class="preview-value">${escapeHtml(datos.descripcion)}</span>
                </div>
            ` : ''}
            ${datos.estado === 'Mantenimiento' && datos.descripcionMantenimiento ? `
                <div class="preview-item">
                    <span class="preview-label">Mantenimiento:</span>
                    <span class="preview-value">${escapeHtml(datos.descripcionMantenimiento)}</span>
                </div>
            ` : ''}
            ${datos.foto ? `
                <div class="preview-item">
                    <span class="preview-label">Imagen:</span>
                    <span class="preview-value">
                        <img src="${datos.foto}" alt="Vista previa" style="max-width: 200px; border-radius: 8px;">
                    </span>
                </div>
            ` : ''}
        `;
        
        previewContent.innerHTML = previewHTML;
        previewSection.style.display = 'block';
        
        // Scroll suave a la vista previa
        previewSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
    
    /**
     * Enviar formulario
     */
    function enviarFormulario(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!validarFormulario()) {
            mostrarError('Por favor corrige los errores en el formulario');
            return;
        }
        
        const formData = new FormData(formHabitacion);
        const btnGuardar = document.getElementById('btn-guardar');
        const isEditing = formHabitacion.dataset.id;
        
        // Mostrar estado de carga
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = isEditing ? 
            '<i class="fas fa-spinner fa-spin"></i> Actualizando...' : 
            '<i class="fas fa-spinner fa-spin"></i> Creando...';
        
        // URL de destino
        const url = isEditing ? 
            `?action=update&id=${formHabitacion.dataset.id}` : 
            '?action=store';
        
        fetch(url, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarExito(data.message);
                
                // Redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = '?action=index';
                }, 2000);
            } else {
                mostrarError(data.message);
                
                // Mostrar errores específicos de campos
                if (data.errors) {
                    mostrarErroresCampos(data.errors);
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión. Por favor intenta nuevamente.');
        })
        .finally(() => {
            // Restaurar botón
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = isEditing ? 
                '<i class="fas fa-save"></i> Actualizar Habitación' : 
                '<i class="fas fa-save"></i> Crear Habitación';
        });
    }
    
    /**
     * Configurar subida de imagen
     */
    function setupImageUpload() {
        // Click en el área
        imageUploadArea.addEventListener('click', () => {
            if (!imagePreview || imagePreview.style.display === 'none') {
                inputImagen.click();
            }
        });
        
        // Drag and drop
        imageUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            imageUploadArea.classList.add('dragover');
        });
        
        imageUploadArea.addEventListener('dragleave', () => {
            imageUploadArea.classList.remove('dragover');
        });
        
        imageUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            imageUploadArea.classList.remove('dragover');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                handleImageFile(files[0]);
            }
        });
        
        // Cambio en input file
        inputImagen.addEventListener('change', (e) => {
            if (e.target.files.length > 0) {
                handleImageFile(e.target.files[0]);
            }
        });
        
        // Remover imagen
        if (removeImageBtn) {
            removeImageBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                removerImagen();
            });
        }
    }
    
    /**
     * Manejar archivo de imagen
     */
    function handleImageFile(file) {
        // Validar tipo
        if (!file.type.startsWith('image/')) {
            mostrarError('Por favor selecciona un archivo de imagen válido');
            return;
        }
        
        // Validar tamaño (5MB)
        if (file.size > 5 * 1024 * 1024) {
            mostrarError('La imagen es demasiado grande. Máximo 5MB.');
            return;
        }
        
        // Mostrar vista previa
        const reader = new FileReader();
        reader.onload = (e) => {
            previewImg.src = e.target.result;
            uploadPlaceholder.style.display = 'none';
            imagePreview.style.display = 'block';
        };
        reader.readAsDataURL(file);
        
        // Subir imagen al servidor
        subirImagen(file);
    }
    
    /**
     * Subir imagen al servidor
     */
    function subirImagen(file) {
        const formData = new FormData();
        formData.append('imagen', file);
        
        fetch('?action=subir-imagen', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                inputFoto.value = data.url;
                mostrarExito('Imagen subida correctamente');
            } else {
                mostrarError(data.message);
                removerImagen();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error al subir la imagen');
            removerImagen();
        });
    }
    
    /**
     * Remover imagen
     */
    function removerImagen() {
        previewImg.src = '';
        inputFoto.value = '';
        inputImagen.value = '';
        uploadPlaceholder.style.display = 'block';
        imagePreview.style.display = 'none';
    }
    
    /**
     * Configurar validación en tiempo real
     */
    function setupValidacionTiempoReal() {
        const campos = formHabitacion.querySelectorAll('input, select, textarea');
        
        campos.forEach(campo => {
            campo.addEventListener('blur', () => validarCampo(campo));
            campo.addEventListener('input', () => {
                // Remover clases de error mientras el usuario escribe
                campo.classList.remove('is-invalid');
                const errorElement = document.getElementById(`error-${campo.name}`);
                if (errorElement) {
                    errorElement.remove();
                }
            });
        });
    }
    
    /**
     * Validar campo individual
     */
    function validarCampo(campo) {
        let esValido = true;
        let mensaje = '';
        
        // Limpiar errores previos
        campo.classList.remove('is-invalid', 'is-valid');
        const errorElement = document.getElementById(`error-${campo.name}`);
        if (errorElement) {
            errorElement.remove();
        }
        
        // Validaciones específicas
        switch (campo.name) {
            case 'numero':
                if (!campo.value.trim()) {
                    esValido = false;
                    mensaje = 'El número de habitación es requerido';
                }
                break;
                
            case 'costo':
                if (!campo.value || parseFloat(campo.value) <= 0) {
                    esValido = false;
                    mensaje = 'El costo debe ser mayor a 0';
                }
                break;
                
            case 'capacidad':
                if (!campo.value || parseInt(campo.value) <= 0) {
                    esValido = false;
                    mensaje = 'La capacidad debe ser mayor a 0';
                }
                break;
                
            case 'tipoHabitacion':
                if (!campo.value) {
                    esValido = false;
                    mensaje = 'Selecciona un tipo de habitación';
                }
                break;
                
            case 'id_hotel':
                if (!campo.value) {
                    esValido = false;
                    mensaje = 'Selecciona un hotel';
                }
                break;
                
            case 'descripcionMantenimiento':
                if (selectEstado.value === 'Mantenimiento' && !campo.value.trim()) {
                    esValido = false;
                    mensaje = 'Describe el motivo del mantenimiento';
                }
                break;
        }
        
        // Aplicar resultado
        if (esValido) {
            campo.classList.add('is-valid');
        } else {
            campo.classList.add('is-invalid');
            mostrarErrorCampo(campo, mensaje);
        }
        
        return esValido;
    }
    
    /**
     * Validar todo el formulario
     */
    function validarFormulario() {
        const campos = formHabitacion.querySelectorAll('input[required], select[required], textarea[required]');
        let formularioValido = true;
        
        campos.forEach(campo => {
            if (!validarCampo(campo)) {
                formularioValido = false;
            }
        });
        
        return formularioValido;
    }
    
    /**
     * Mostrar error en campo específico
     */
    function mostrarErrorCampo(campo, mensaje) {
        const errorDiv = document.createElement('div');
        errorDiv.id = `error-${campo.name}`;
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = mensaje;
        
        campo.parentNode.appendChild(errorDiv);
    }
    
    /**
     * Mostrar errores de campos desde el servidor
     */
    function mostrarErroresCampos(errores) {
        Object.keys(errores).forEach(campo => {
            const input = document.querySelector(`[name="${campo}"]`);
            if (input) {
                input.classList.add('is-invalid');
                mostrarErrorCampo(input, errores[campo]);
            }
        });
    }
    
    // ============================================
    // FUNCIONES AUXILIARES
    // ============================================
    
    /**
     * Mostrar mensaje de éxito
     */
    function mostrarExito(mensaje) {
        if (successMessage && successText) {
            successText.textContent = mensaje;
            successMessage.style.display = 'block';
            errorMessage.style.display = 'none';
            
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
            
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    /**
     * Mostrar mensaje de error
     */
    function mostrarError(mensaje) {
        if (errorMessage && errorText) {
            errorText.textContent = mensaje;
            errorMessage.style.display = 'block';
            successMessage.style.display = 'none';
            
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 7000);
            
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    /**
     * Escape HTML
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Formatear número
     */
    function formatearNumero(numero) {
        return new Intl.NumberFormat('es-CO').format(numero);
    }
    
    /**
     * Obtener color del estado
     */
    function getEstadoColor(estado) {
        const colores = {
            'Disponible': 'success',
            'Reservada': 'warning',
            'Ocupada': 'info',
            'Mantenimiento': 'danger'
        };
        return colores[estado] || 'secondary';
    }
});