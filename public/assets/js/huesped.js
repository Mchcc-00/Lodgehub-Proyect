document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de creación
    const form = document.getElementById('huesped-form');
    const resetBtn = document.getElementById('reset-btn');
    const previewBtn = document.getElementById('preview-btn');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const huespedPreview = document.getElementById('huesped-preview');
    
    // Campos del formulario
    const inputs = {
        tipoDocumento: document.getElementById('tipoDocumento'),
        numDocumento: document.getElementById('numDocumento'),
        nombres: document.getElementById('nombres'),
        apellidos: document.getElementById('apellidos'),
        sexo: document.getElementById('sexo'),
        numTelefono: document.getElementById('numTelefono'),
        correo: document.getElementById('correo')
    };

    // Solo ejecutar si estamos en la página de crear
    if (form) {
        // Validaciones en tiempo real
        setupRealTimeValidation();
        
        // Event listeners
        form.addEventListener('submit', handleSubmit);
        resetBtn.addEventListener('click', resetForm);
        previewBtn.addEventListener('click', togglePreview);
    }

    function setupRealTimeValidation() {
        // Validación de número de documento - CORREGIDO
        inputs.numDocumento.addEventListener('input', function() {
            const value = this.value.trim();
            if (value.length >= 5 && value.length <= 15 && /^[0-9A-Za-z]+$/.test(value)) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else if (value.length > 0) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });

        // Validación de nombres y apellidos - MEJORADO
        [inputs.nombres, inputs.apellidos].forEach(input => {
            input.addEventListener('input', function() {
                const value = this.value.trim();
                if (value.length >= 2 && value.length <= 50 && /^[a-zA-ZÀ-ÿñÑ\s]+$/.test(value)) {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else if (value.length > 0) {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                } else {
                    this.classList.remove('valid', 'invalid');
                }
            });
        });

        // Validación de teléfono - CORREGIDO
        inputs.numTelefono.addEventListener('input', function() {
            const value = this.value.trim();
            if (/^[0-9+\-\s()]{7,15}$/.test(value)) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else if (value.length > 0) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });

        // Validación de correo
        inputs.correo.addEventListener('input', function() {
            const value = this.value.trim();
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (emailRegex.test(value) && value.length <= 30) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else if (value.length > 0) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });
    }

    async function handleSubmit(e) {
        e.preventDefault();
        
        // Ocultar mensajes anteriores
        hideMessages();
        
        // Validar formulario
        if (!validateForm()) {
            return;
        }
        
        // Mostrar estado de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
        submitBtn.disabled = true;
        
        try {
            // Crear FormData directamente del formulario
            const formData = new FormData(form);
            
            // Enviar datos al servidor
            const formAction = form.action || '../controllers/huespedController.php';
            
            const response = await fetch(formAction, {
                method: 'POST',
                body: formData
            });
            
            // Verificar si la respuesta es JSON válida
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La respuesta del servidor no es JSON válida');
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Redireccionar inmediatamente a la lista con un parámetro de éxito.
                // El mensaje se mostrará en la página de la lista.
                window.location.href = 'listaHuesped.php?status=created';
            } else {
                showErrorMessage(result.message || 'Error al crear el huésped');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showErrorMessage('Error de conexión. Verifica que el servidor esté funcionando correctamente.');
        } finally {
            // Restaurar botón
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    function validateForm() {
        let isValid = true;
        const errors = [];

        // Validar campos requeridos
        Object.keys(inputs).forEach(key => {
            const input = inputs[key];
            const value = input.value.trim();
            
            if (!value) {
                input.classList.add('invalid');
                input.classList.remove('valid');
                isValid = false;
                errors.push(`El campo ${getFieldLabel(key)} es requerido`);
            }
        });

        // Validaciones específicas mejoradas
        if (inputs.numDocumento.value.length < 5 || inputs.numDocumento.value.length > 15) {
            errors.push('El número de documento debe tener entre 5 y 15 caracteres');
            isValid = false;
        }

        if (inputs.nombres.value.length < 2 || inputs.nombres.value.length > 50) {
            errors.push('Los nombres deben tener entre 2 y 50 caracteres');
            isValid = false;
        }

        if (inputs.apellidos.value.length < 2 || inputs.apellidos.value.length > 50) {
            errors.push('Los apellidos deben tener entre 2 y 50 caracteres');
            isValid = false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(inputs.correo.value) || inputs.correo.value.length > 30) {
            errors.push('El correo electrónico no es válido o excede 30 caracteres');
            isValid = false;
        }

        // NUEVA validación de teléfono
        if (!inputs.numTelefono.value.match(/^[0-9+\-\s()]{7,15}$/)) {
            errors.push('El formato del teléfono no es válido');
            isValid = false;
        }

        if (!isValid) {
            showErrorMessage(errors.join('<br>'));
        }

        return isValid;
    }

    function getFieldLabel(fieldName) {
        const labels = {
            tipoDocumento: 'Tipo de Documento',
            numDocumento: 'Número de Documento',
            nombres: 'Nombres',
            apellidos: 'Apellidos',
            sexo: 'Sexo',
            numTelefono: 'Número de Teléfono',
            correo: 'Correo Electrónico'
        };
        return labels[fieldName] || fieldName;
    }

    function resetForm() {
        if (form) {
            form.reset();
            
            // Limpiar clases de validación
            Object.values(inputs).forEach(input => {
                input.classList.remove('valid', 'invalid');
            });
            
            // Ocultar vista previa y mensajes
            if (huespedPreview) huespedPreview.style.display = 'none';
            hideMessages();
        }
    }

    function togglePreview() {
        if (huespedPreview.style.display === 'none' || !huespedPreview.style.display) {
            showPreview();
        } else {
            huespedPreview.style.display = 'none';
        }
    }

    function showPreview() {
        const previewContent = document.getElementById('preview-content');
        
        const previewData = {
            'Tipo de Documento': inputs.tipoDocumento.value,
            'Número de Documento': inputs.numDocumento.value,
            'Nombres': inputs.nombres.value,
            'Apellidos': inputs.apellidos.value,
            'Sexo': inputs.sexo.value,
            'Teléfono': inputs.numTelefono.value,
            'Correo Electrónico': inputs.correo.value
        };

        let html = '';
        Object.entries(previewData).forEach(([label, value]) => {
            if (value) {
                html += `
                    <div class="preview-item">
                        <span class="preview-label">${label}:</span>
                        <span class="preview-value">${value}</span>
                    </div>
                `;
            }
        });

        previewContent.innerHTML = html;
        huespedPreview.style.display = 'block';
    }

    function showSuccessMessage(mensaje = '¡Huésped creado exitosamente!') {
        if (successMessage) {
            const successDiv = successMessage.querySelector('div');
            if (successDiv) {
                successDiv.textContent = 'El huésped ha sido registrado en el sistema.';
            }
            
            successMessage.style.display = 'block';
            if (errorMessage) errorMessage.style.display = 'none';
            
            // Scroll al mensaje
            successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
            
            // Auto-ocultar después de 5 segundos
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
        }
    }

    function showErrorMessage(message) {
        if (errorMessage && errorText) {
            errorText.innerHTML = message;
            errorMessage.style.display = 'block';
            if (successMessage) successMessage.style.display = 'none';
            
            // Scroll al mensaje
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function hideMessages() {
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    }

    // Formateo automático para algunos campos - MEJORADO
    if (inputs.numTelefono) {
        inputs.numTelefono.addEventListener('input', function() {
            // Permitir solo números, espacios, paréntesis, guiones y el signo +
            this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
        });
    }

    if (inputs.numDocumento) {
        inputs.numDocumento.addEventListener('input', function() {
            // Permitir solo letras y números
            this.value = this.value.replace(/[^0-9A-Za-z]/g, '');
        });
    }

    // OPCIONAL: Capitalización automática más inteligente
    if (inputs.nombres) {
        inputs.nombres.addEventListener('blur', function() {
            this.value = this.value.replace(/\b\w+/g, function(word) {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            });
        });
    }

    if (inputs.apellidos) {
        inputs.apellidos.addEventListener('blur', function() {
            this.value = this.value.replace(/\b\w+/g, function(word) {
                return word.charAt(0).toUpperCase() + word.slice(1).toLowerCase();
            });
        });
    }
});

// ========================================
// FUNCIONES GLOBALES PARA CRUD COMPLETO
// ========================================

// Función para cargar todos los huéspedes
async function cargarHuespedes(pagina = 1, registrosPorPagina = 10) {
    try {
        const url = `../controllers/huespedController.php?action=obtener&paginado=true&pagina=${pagina}&registros=${registrosPorPagina}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al cargar huéspedes:', error);
        throw error;
    }
}

// Función para obtener un huésped por documento
async function obtenerHuespedPorDocumento(numDocumento) {
    try {
        const url = `../controllers/huespedController.php?action=obtenerPorDocumento&numDocumento=${encodeURIComponent(numDocumento)}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al obtener huésped:', error);
        throw error;
    }
}

// Función para actualizar un huésped
async function actualizarHuesped(numDocumento, datos) {
    try {
        const formData = new FormData();
        formData.append('numDocumento', numDocumento);
        
        // Agregar solo los campos que se van a actualizar
        Object.keys(datos).forEach(key => {
            if (datos[key] !== null && datos[key] !== undefined && datos[key] !== '') {
                formData.append(key, datos[key]);
            }
        });
        
        const response = await fetch('../controllers/huespedController.php?action=actualizar', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            return result;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al actualizar huésped:', error);
        throw error;
    }
}

// Función para eliminar un huésped
async function eliminarHuesped(numDocumento) {
    try {
        const formData = new FormData();
        formData.append('numDocumento', numDocumento);
        
        const response = await fetch('../controllers/huespedController.php?action=eliminar', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            return result;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al eliminar huésped:', error);
        throw error;
    }
}

// Función para buscar huéspedes
async function buscarHuespedes(termino) {
    try {
        const url = `../controllers/huespedController.php?action=buscar&termino=${encodeURIComponent(termino)}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al buscar huéspedes:', error);
        throw error;
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'success') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    notificacion.innerHTML = `
        ${tipo === 'success' ? '✅' : '❌'} ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.parentNode.removeChild(notificacion);
        }
    }, 5000);
}

// Función para validar datos comunes
function validarDatosHuesped(datos) {
    const errores = [];
    
    if (datos.nombres && (datos.nombres.length < 2 || datos.nombres.length > 50)) {
        errores.push('Los nombres deben tener entre 2 y 50 caracteres');
    }
    
    if (datos.apellidos && (datos.apellidos.length < 2 || datos.apellidos.length > 50)) {
        errores.push('Los apellidos deben tener entre 2 y 50 caracteres');
    }
    
    if (datos.numTelefono && !datos.numTelefono.match(/^[0-9+\-\s()]{7,15}$/)) {
        errores.push('El formato del teléfono no es válido');
    }
    
    if (datos.correo) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(datos.correo) || datos.correo.length > 30) {
            errores.push('El correo electrónico no es válido o excede 30 caracteres');
        }
    }
    
    return errores;
}

// Función para formatear datos para mostrar
function formatearHuesped(huesped) {
    return {
        ...huesped,
        nombreCompleto: `${huesped.nombres} ${huesped.apellidos}`,
        tipoDocumentoFormateado: huesped.tipoDocumento.replace(/([a-z])([A-Z])/g, '$1 $2')
    };
}