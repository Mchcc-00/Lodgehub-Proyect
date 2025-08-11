document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario
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

    // Validaciones en tiempo real
    setupRealTimeValidation();
    
    // Event listeners
    form.addEventListener('submit', handleSubmit);
    resetBtn.addEventListener('click', resetForm);
    previewBtn.addEventListener('click', togglePreview);

    function setupRealTimeValidation() {
        // Validación de número de documento
        inputs.numDocumento.addEventListener('input', function() {
            const value = this.value.trim();
            if (value.length >= 5 && /^[0-9A-Za-z]+$/.test(value)) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else if (value.length > 0) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });

        // Validación de nombres y apellidos
        [inputs.nombres, inputs.apellidos].forEach(input => {
            input.addEventListener('input', function() {
                const value = this.value.trim();
                if (value.length >= 2 && /^[a-zA-ZÀ-ÿ\u00f1\u00d1\s]+$/.test(value)) {
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

        // Validación de teléfono
        inputs.numTelefono.addEventListener('input', function() {
            const value = this.value.trim();
            if (/^[0-9+\-\s()]+$/.test(value) && value.length >= 7) {
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
            if (emailRegex.test(value)) {
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
        submitBtn.innerHTML = 'Guardando...';
        submitBtn.disabled = true;
        
        try {
            // Crear FormData directamente del formulario
            const formData = new FormData(form);
            
            // Enviar datos al servidor (usando ruta absoluta como fallback)
            const formAction = form.action || '/lodgehub/app/controllers/huespedController.php';
            
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
                showSuccessMessage('¡Huésped creado exitosamente!');
                resetForm();
                
                // Opcional: Redireccionar después de 2 segundos
                setTimeout(() => {
                    // window.location.href = 'listar_huespedes.php';
                }, 2000);
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

        // Validaciones específicas
        if (inputs.numDocumento.value.length < 5) {
            errors.push('El número de documento debe tener al menos 5 caracteres');
            isValid = false;
        }

        if (inputs.nombres.value.length < 2) {
            errors.push('Los nombres deben tener al menos 2 caracteres');
            isValid = false;
        }

        if (inputs.apellidos.value.length < 2) {
            errors.push('Los apellidos deben tener al menos 2 caracteres');
            isValid = false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(inputs.correo.value)) {
            errors.push('El formato del correo electrónico no es válido');
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
        form.reset();
        
        // Limpiar clases de validación
        Object.values(inputs).forEach(input => {
            input.classList.remove('valid', 'invalid');
        });
        
        // Ocultar vista previa y mensajes
        huespedPreview.style.display = 'none';
        hideMessages();
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
        const successDiv = successMessage.querySelector('div');
        if (successDiv) {
            successDiv.textContent = 'El huésped ha sido registrado en el sistema.';
        }
        
        successMessage.style.display = 'block';
        errorMessage.style.display = 'none';
        
        // Scroll al mensaje
        successMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    }

    function showErrorMessage(message) {
        errorText.innerHTML = message;
        errorMessage.style.display = 'block';
        successMessage.style.display = 'none';
    }

    function hideMessages() {
        successMessage.style.display = 'none';
        errorMessage.style.display = 'none';
    }

    // Formateo automático para algunos campos
    inputs.numTelefono.addEventListener('input', function() {
        // Permitir solo números, espacios, paréntesis, guiones y el signo +
        this.value = this.value.replace(/[^0-9+\-\s()]/g, '');
    });

    inputs.numDocumento.addEventListener('input', function() {
        // Permitir solo letras y números
        this.value = this.value.replace(/[^0-9A-Za-z]/g, '');
    });

    inputs.nombres.addEventListener('input', function() {
        // Capitalizar primera letra de cada palabra
        this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
    });

    inputs.apellidos.addEventListener('input', function() {
        // Capitalizar primera letra de cada palabra
        this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
    });
});