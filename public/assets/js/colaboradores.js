// colaboradores.js - Script mejorado para la página de crear colaboradores

document.addEventListener('DOMContentLoaded', function() {
    // Referencias a elementos del DOM
    const form = document.getElementById('colaborador-form');
    const togglePasswordBtn = document.getElementById('toggle-password');
    const passwordInput = document.getElementById('password');
    const confirmarPasswordInput = document.getElementById('confirmarPassword');
    const fotoInput = document.getElementById('foto');
    const previewBtn = document.getElementById('preview-btn');
    const resetBtn = document.getElementById('reset-btn');
    const numDocumentoInput = document.getElementById('numDocumento');
    const correoInput = document.getElementById('correo');
    const fechaNacimientoInput = document.getElementById('fechaNacimiento');
    const tipoDocumentoSelect = document.getElementById('tipoDocumento');

    // Configuración inicial
    initializeForm();
    
    // Event Listeners
    setupEventListeners();
    
    function initializeForm() {
        // Configurar fecha máxima (18 años atrás)
        const today = new Date();
        const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
        fechaNacimientoInput.max = maxDate.toISOString().split('T')[0];
        
        // Limpiar mensajes al cargar
        hideMessages();
        clearValidationMessages();
    }
    
    function setupEventListeners() {
        // Validación en tiempo real del documento
        numDocumentoInput.addEventListener('input', debounce(function() {
            validateDocumento(this.value, tipoDocumentoSelect.value);
        }, 300));

        numDocumentoInput.addEventListener('blur', function() {
            if (this.value) {
                checkDocumentoExists(this.value);
            }
        });

        // Validación cuando cambia el tipo de documento
        tipoDocumentoSelect.addEventListener('change', function() {
            if (numDocumentoInput.value) {
                validateDocumento(numDocumentoInput.value, this.value);
            }
        });

        // Validación en tiempo real del correo
        correoInput.addEventListener('input', debounce(function() {
            validateEmail(this.value);
        }, 300));

        correoInput.addEventListener('blur', function() {
            if (this.value) {
                checkEmailExists(this.value);
            }
        });

        // Toggle mostrar/ocultar contraseña
        togglePasswordBtn.addEventListener('click', function() {
            togglePasswordVisibility();
        });

        // Validación de fortaleza de contraseña
        passwordInput.addEventListener('input', function() {
            validatePasswordStrength(this.value);
            checkPasswordMatch();
        });

        // Validación de coincidencia de contraseñas
        confirmarPasswordInput.addEventListener('input', function() {
            checkPasswordMatch();
        });

        // Preview de imagen
        fotoInput.addEventListener('change', function() {
            handleImagePreview(this);
        });

        // Vista previa del colaborador
        previewBtn.addEventListener('click', function() {
            showColaboradorPreview();
        });

        // Limpiar formulario
        resetBtn.addEventListener('click', function() {
            resetForm();
        });

        // Envío del formulario
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            handleFormSubmit();
        });

        // Validaciones de input
        setupInputValidations();
        
        // Auto-actualización de vista previa
        setupAutoPreview();
    }
    
    function setupInputValidations() {
        // Validación de solo letras para nombres y apellidos
        const nombresInput = document.getElementById('nombres');
        const apellidosInput = document.getElementById('apellidos');
        
        [nombresInput, apellidosInput].forEach(input => {
            input.addEventListener('input', function() {
                // Permitir solo letras, espacios y acentos
                this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
                // Capitalizar primera letra de cada palabra
                this.value = this.value.replace(/\b\w/g, l => l.toUpperCase());
            });
        });

        // Validación de solo números para teléfono y documento
        const telefonoInput = document.getElementById('numTelefono');
        
        telefonoInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d]/g, '');
            validateTelefono(this.value);
        });
        
        numDocumentoInput.addEventListener('input', function() {
            // La validación de formato depende del tipo de documento
            // Se manejará en validateDocumento()
        });
    }
    
    function setupAutoPreview() {
        const formInputs = form.querySelectorAll('input:not([type="file"]), select');
        formInputs.forEach(input => {
            input.addEventListener('input', debounce(function() {
                if (document.getElementById('colaborador-preview').style.display === 'block') {
                    showColaboradorPreview();
                }
            }, 500));
        });
    }

    // Funciones de validación
    function validateDocumento(documento, tipoDocumento) {
        const feedback = document.getElementById('documento-feedback');
        
        if (!documento) {
            feedback.innerHTML = '';
            setInputState(numDocumentoInput, 'neutral');
            return false;
        }

        // Validaciones específicas por tipo de documento
        let isValid = false;
        let message = '';

        switch (tipoDocumento) {
            case 'Cédula de Ciudadanía':
                isValid = /^\d{7,10}$/.test(documento);
                message = isValid ? 'Formato válido para Cédula de Ciudadanía' : 'Debe contener entre 7 y 10 dígitos';
                break;
                
            case 'Cedula de Extranjeria':
                isValid = /^[A-Z]{0,2}\d{6,10}$/.test(documento);
                message = isValid ? 'Formato válido para Cédula de Extranjería' : 'Formato: hasta 2 letras seguidas de 6-10 dígitos';
                break;
                
            case 'Pasaporte':
                isValid = /^[A-Z0-9]{6,12}$/.test(documento);
                message = isValid ? 'Formato válido para Pasaporte' : 'Debe contener entre 6 y 12 caracteres alfanuméricos';
                break;
                
            case 'Tarjeta de Identidad':
                isValid = /^\d{7,11}$/.test(documento);
                message = isValid ? 'Formato válido para Tarjeta de Identidad' : 'Debe contener entre 7 y 11 dígitos';
                break;
                
            case 'Registro Civil':
                isValid = /^\d{10,11}$/.test(documento);
                message = isValid ? 'Formato válido para Registro Civil' : 'Debe contener entre 10 y 11 dígitos';
                break;
                
            default:
                isValid = documento.length >= 6 && documento.length <= 15;
                message = isValid ? 'Formato básico válido' : 'Debe tener entre 6 y 15 caracteres';
        }

        showValidationMessage(feedback, message, isValid ? 'success' : 'error');
        setInputState(numDocumentoInput, isValid ? 'valid' : 'invalid');
        
        return isValid;
    }

    function validateEmail(email) {
        const feedback = document.getElementById('correo-feedback');
        
        if (!email) {
            feedback.innerHTML = '';
            setInputState(correoInput, 'neutral');
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        const isValid = emailRegex.test(email);
        
        showValidationMessage(
            feedback, 
            isValid ? 'Formato de correo válido' : 'Formato de correo inválido', 
            isValid ? 'success' : 'error'
        );
        
        setInputState(correoInput, isValid ? 'valid' : 'invalid');
        return isValid;
    }
    
    function validateTelefono(telefono) {
        if (!telefono) return false;
        
        // Validar formato colombiano (ejemplo)
        const telefonoRegex = /^[3][0-9]{9}$|^[+]?[0-9]{10,15}$/;
        return telefonoRegex.test(telefono);
    }

    function validatePasswordStrength(password) {
        const strengthDiv = document.getElementById('password-strength');
        
        if (!password) {
            strengthDiv.innerHTML = '';
            setInputState(passwordInput, 'neutral');
            return { isValid: false, strength: 0 };
        }

        let strength = 0;
        let requirements = [];

        // Criterios de fortaleza
        const checks = [
            { test: password.length >= 6, message: 'Al menos 6 caracteres' },
            { test: /[a-z]/.test(password), message: 'Una letra minúscula' },
            { test: /[A-Z]/.test(password), message: 'Una letra mayúscula' },
            { test: /\d/.test(password), message: 'Un número' },
            { test: /[!@#$%^&*(),.?":{}|<>]/.test(password), message: 'Un carácter especial' }
        ];

        checks.forEach(check => {
            if (check.test) {
                strength++;
            } else {
                requirements.push(check.message);
            }
        });

        let strengthText = '';
        let className = '';
        let progressWidth = 0;

        if (strength < 2) {
            strengthText = 'Muy débil';
            className = 'password-strength weak';
            progressWidth = 20;
        } else if (strength < 3) {
            strengthText = 'Débil';
            className = 'password-strength weak';
            progressWidth = 40;
        } else if (strength < 4) {
            strengthText = 'Media';
            className = 'password-strength medium';
            progressWidth = 60;
        } else if (strength < 5) {
            strengthText = 'Fuerte';
            className = 'password-strength strong';
            progressWidth = 80;
        } else {
            strengthText = 'Muy fuerte';
            className = 'password-strength strong';
            progressWidth = 100;
        }

        const progressBar = `
            <div class="password-progress-bar">
                <div class="password-progress-fill" style="width: ${progressWidth}%;"></div>
            </div>
        `;

        strengthDiv.innerHTML = `
            <div class="${className}">
                <div class="d-flex justify-content-between align-items-center">
                    <small>Fortaleza: ${strengthText}</small>
                    <small>${strength}/5</small>
                </div>
                ${progressBar}
                ${requirements.length > 0 ? `<small>Falta: ${requirements.join(', ')}</small>` : ''}
            </div>
        `;

        const isValid = strength >= 3;
        setInputState(passwordInput, isValid ? 'valid' : 'invalid');
        
        return { isValid, strength };
    }

    function checkPasswordMatch() {
        const matchDiv = document.getElementById('password-match');
        const password = passwordInput.value;
        const confirmPassword = confirmarPasswordInput.value;

        if (!confirmPassword) {
            matchDiv.innerHTML = '';
            setInputState(confirmarPasswordInput, 'neutral');
            return false;
        }

        const isMatch = password === confirmPassword;
        
        showValidationMessage(
            matchDiv,
            isMatch ? 'Las contraseñas coinciden' : 'Las contraseñas no coinciden',
            isMatch ? 'success' : 'error'
        );
        
        setInputState(confirmarPasswordInput, isMatch ? 'valid' : 'invalid');
        return isMatch;
    }

    function togglePasswordVisibility() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = togglePasswordBtn.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
    }

    // Manejo de imagen
    function handleImagePreview(input) {
        const preview = document.getElementById('foto-preview');
        const previewImg = document.getElementById('preview-img');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validar tamaño (2MB máximo)
            if (file.size > 2 * 1024 * 1024) {
                showError('La imagen debe ser menor a 2MB');
                input.value = '';
                preview.style.display = 'none';
                return;
            }

            // Validar tipo de archivo
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showError('Solo se permiten imágenes JPG, PNG o GIF');
                input.value = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
                
                // Añadir efectos visuales
                previewImg.style.opacity = '0';
                setTimeout(() => {
                    previewImg.style.transition = 'opacity 0.3s ease';
                    previewImg.style.opacity = '1';
                }, 100);
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    // Verificaciones de existencia en BD
    function checkDocumentoExists(documento) {
        if (!validateDocumento(documento, tipoDocumentoSelect.value)) return;

        const feedback = document.getElementById('documento-feedback');
        showValidationMessage(feedback, 'Verificando disponibilidad...', 'checking');

        fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `action=checkDocumento&numDocumento=${encodeURIComponent(documento)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showValidationMessage(
                    feedback,
                    data.data.exists ? 'Este documento ya está registrado' : 'Documento disponible',
                    data.data.exists ? 'error' : 'success'
                );
                setInputState(numDocumentoInput, data.data.exists ? 'invalid' : 'valid');
            } else {
                showValidationMessage(feedback, 'Error al verificar documento', 'error');
            }
        })
        .catch(error => {
            console.error('Error al verificar documento:', error);
            showValidationMessage(feedback, 'Error de conexión', 'error');
        });
    }

    function checkEmailExists(email) {
        if (!validateEmail(email)) return;

        const feedback = document.getElementById('correo-feedback');
        showValidationMessage(feedback, 'Verificando disponibilidad...', 'checking');

        fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: `action=checkEmail&correo=${encodeURIComponent(email)}`
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showValidationMessage(
                    feedback,
                    data.data.exists ? 'Este correo ya está registrado' : 'Correo disponible',
                    data.data.exists ? 'error' : 'success'
                );
                setInputState(correoInput, data.data.exists ? 'invalid' : 'valid');
            } else {
                showValidationMessage(feedback, 'Error al verificar correo', 'error');
            }
        })
        .catch(error => {
            console.error('Error al verificar correo:', error);
            showValidationMessage(feedback, 'Error de conexión', 'error');
        });
    }

    // Vista previa del colaborador
    function showColaboradorPreview() {
        const preview = document.getElementById('colaborador-preview');
        const content = document.getElementById('preview-content');
        
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (key !== 'foto') { // Excluir archivo
                data[key] = value;
            }
        }

        // Validar campos requeridos
        const requiredFields = [
            'numDocumento', 'tipoDocumento', 'nombres', 'apellidos', 
            'correo', 'numTelefono', 'sexo', 'fechaNacimiento', 'roles', 'password'
        ];
        
        const missingFields = requiredFields.filter(field => !data[field]);

        if (missingFields.length > 0) {
            showError('Complete todos los campos requeridos antes de ver la vista previa');
            return;
        }

        const fotoPreview = document.getElementById('preview-img').src || '../../public/assets/images/default-user.png';
        const edad = calcularEdad(data.fechaNacimiento);

        content.innerHTML = `
            <div class="preview-colaborador">
                <div class="preview-foto">
                    <img src="${fotoPreview}" alt="Foto de perfil" class="preview-photo">
                    <div class="preview-badge">
                        <span class="badge bg-${getRoleBadgeColor(data.roles)}">${data.roles}</span>
                    </div>
                </div>
                <div class="preview-datos">
                    <div class="preview-section">
                        <h6><i class="fas fa-id-card"></i> Identificación</h6>
                        <div class="preview-grid">
                            <div class="preview-item">
                                <span class="preview-label">Documento:</span>
                                <span class="preview-value">${data.numDocumento}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Tipo:</span>
                                <span class="preview-value">${data.tipoDocumento}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preview-section">
                        <h6><i class="fas fa-user"></i> Información Personal</h6>
                        <div class="preview-grid">
                            <div class="preview-item">
                                <span class="preview-label">Nombres:</span>
                                <span class="preview-value">${data.nombres}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Apellidos:</span>
                                <span class="preview-value">${data.apellidos}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Sexo:</span>
                                <span class="preview-value">${data.sexo}</span>
                            </div>
                            <div class='preview-item'>
                                <span class='preview-label'>Edad:</span>
                                <span class='preview-value'>${edad} años</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preview-section">
                        <h6><i class="fas fa-contact-book"></i> Contacto</h6>
                        <div class="preview-grid">
                            <div class="preview-item">
                                <span class="preview-label">Correo:</span>
                                <span class="preview-value">${data.correo}</span>
                            </div>
                            <div class="preview-item">
                                <span class="preview-label">Teléfono:</span>
                                <span class="preview-value">${data.numTelefono}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="preview-section">
                        <h6><i class="fas fa-cog"></i> Configuración</h6>
                        <div class="preview-grid">
                            <div class="preview-item">
                                <span class="preview-label">Solicitar cambio de contraseña:</span>
                                <span class="preview-value">
                                    <span class="badge ${data.solicitarContraseña ? 'bg-warning' : 'bg-success'}">
                                        ${data.solicitarContraseña ? 'Sí' : 'No'}
                                    </span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;

        preview.style.display = 'block';
        preview.scrollIntoView({ behavior: 'smooth' });
    }

    // Validación y envío del formulario
    function validateForm() {
        let isValid = true;
        const errors = [];

        // Validar documento
        if (!validateDocumento(numDocumentoInput.value, tipoDocumentoSelect.value)) {
            errors.push('Número de documento inválido');
            isValid = false;
        }

        // Validar correo
        if (!validateEmail(correoInput.value)) {
            errors.push('Correo electrónico inválido');
            isValid = false;
        }

        // Validar contraseñas
        const passwordValidation = validatePasswordStrength(passwordInput.value);
        if (!passwordValidation.isValid) {
            errors.push('La contraseña no cumple con los requisitos mínimos');
            isValid = false;
        }

        if (!checkPasswordMatch()) {
            errors.push('Las contraseñas no coinciden');
            isValid = false;
        }

        // Validar teléfono
        if (!validateTelefono(document.getElementById('numTelefono').value)) {
            errors.push('Número de teléfono inválido');
            isValid = false;
        }

        // Validar edad (mayor de 18 años)
        const fechaNacimiento = new Date(fechaNacimientoInput.value);
        const edad = calcularEdad(fechaNacimientoInput.value);
        if (edad < 18) {
            errors.push('El colaborador debe ser mayor de 18 años');
            isValid = false;
        }

        // Validar campos requeridos
        const camposRequeridos = form.querySelectorAll('[required]');
        camposRequeridos.forEach(campo => {
            if (!campo.value.trim()) {
                errors.push(`El campo ${campo.labels[0]?.textContent || campo.name} es requerido`);
                isValid = false;
            }
        });

        if (!isValid) {
            showError('Por favor corrija los siguientes errores:<br>• ' + errors.join('<br>• '));
        }

        return isValid;
    }

    function handleFormSubmit() {
        if (!validateForm()) {
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar estado de carga
        setLoadingState(submitBtn, true, 'Creando colaborador...');
        hideMessages();

        const formData = new FormData(form);
        formData.append('action', 'crear');

        fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                showSuccess('¡Colaborador creado exitosamente!');
                resetForm();
                
                // Opcional: redirigir después de mostrar el mensaje
                setTimeout(() => {
                    if (confirm('¿Deseas ir a la lista de colaboradores?')) {
                        window.location.href = 'listaMisColaboradores.php';
                    }
                }, 2000);
            } else {
                showError(data.message || 'Error al crear el colaborador');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error de conexión. Verifique su conexión e intente nuevamente.');
        })
        .finally(() => {
            setLoadingState(submitBtn, false, originalText);
        });
    }

    function resetForm() {
        if (confirm('¿Está seguro de que desea limpiar el formulario?')) {
            form.reset();
            hideMessages();
            clearValidationMessages();
            hidePreview();
            
            // Limpiar vista previa de imagen
            const preview = document.getElementById('foto-preview');
            preview.style.display = 'none';
            
            // Resetear estados de inputs
            const inputs = form.querySelectorAll('input, select');
            inputs.forEach(input => setInputState(input, 'neutral'));
        }
    }

    // Funciones auxiliares
    function showValidationMessage(element, message, type) {
        const iconMap = {
            success: 'fa-check-circle',
            error: 'fa-times-circle',
            checking: 'fa-spinner fa-spin'
        };
        
        const colorMap = {
            success: 'text-success',
            error: 'text-danger',
            checking: 'text-info'
        };
        
        const icon = iconMap[type] || 'fa-info-circle';
        const color = colorMap[type] || 'text-muted';
        
        element.innerHTML = `
            <small class="${color}">
                <i class="fas ${icon}"></i> ${message}
            </small>
        `;
    }

    function setInputState(input, state) {
        // Limpiar clases anteriores
        input.classList.remove('is-valid', 'is-invalid');
        
        switch (state) {
            case 'valid':
                input.classList.add('is-valid');
                break;
            case 'invalid':
                input.classList.add('is-invalid');
                break;
            case 'neutral':
            default:
                // Sin clases adicionales
                break;
        }
    }

    function clearValidationMessages() {
        const feedbackElements = [
            'documento-feedback', 
            'correo-feedback', 
            'password-strength', 
            'password-match'
        ];
        
        feedbackElements.forEach(id => {
            const element = document.getElementById(id);
            if (element) {
                element.innerHTML = '';
            }
        });
    }

    function setLoadingState(button, isLoading, loadingText = 'Cargando...') {
        if (isLoading) {
            button.disabled = true;
            button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
            button.classList.add('loading');
        } else {
            button.disabled = false;
            button.classList.remove('loading');
        }
    }

    function showSuccess(message) {
        const successDiv = document.getElementById('success-message');
        const messageDiv = successDiv.querySelector('div:last-child') || successDiv;
        
        if (messageDiv !== successDiv) {
            messageDiv.textContent = message;
        }
        
        successDiv.style.display = 'block';
        successDiv.scrollIntoView({ behavior: 'smooth' });
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            successDiv.style.display = 'none';
        }, 5000);
    }

    function showError(message) {
        const errorDiv = document.getElementById('error-message');
        const errorText = document.getElementById('error-text') || errorDiv.querySelector('div:last-child');
        
        if (errorText) {
            errorText.innerHTML = message;
        } else {
            errorDiv.innerHTML = `❌ <strong>Error</strong><br>${message}`;
        }
        
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth' });
        
        // Auto-ocultar después de 8 segundos
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 8000);
    }

    function hideMessages() {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    }

    function hidePreview() {
        const preview = document.getElementById('colaborador-preview');
        preview.style.display = 'none';
    }

    function calcularEdad(fechaNacimiento) {
        const hoy = new Date();
        const fechaNac = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mesActual = hoy.getMonth();
        const mesNac = fechaNac.getMonth();
        
        if (mesActual < mesNac || (mesActual === mesNac && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }
        
        return edad;
    }

    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    }

    function getRoleBadgeColor(role) {
        const colors = {
            'Administrador': 'danger',
            'Colaborador': 'primary',
            'Usuario': 'secondary'
        };
        return colors[role] || 'secondary';
    }

    // Función debounce para optimizar las validaciones en tiempo real
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Funciones de accesibilidad
    function setupAccessibility() {
        // Atajos de teclado
        document.addEventListener('keydown', function(e) {
            // Ctrl + Enter para enviar formulario
            if (e.ctrlKey && e.key === 'Enter') {
                e.preventDefault();
                if (validateForm()) {
                    handleFormSubmit();
                }
            }
            
            // Escape para limpiar mensajes
            if (e.key === 'Escape') {
                hideMessages();
            }
        });
        
        // Mejorar navegación con Tab
        const focusableElements = form.querySelectorAll(
            'input, select, button, [tabindex]:not([tabindex="-1"])'
        );
        
        focusableElements.forEach((el, index) => {
            el.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' && e.target.type !== 'submit') {
                    e.preventDefault();
                    const nextElement = focusableElements[index + 1];
                    if (nextElement) {
                        nextElement.focus();
                    }
                }
            });
        });
    }

    // Inicializar características de accesibilidad
    setupAccessibility();

    // Validación de conectividad
    function checkConnectivity() {
        if (!navigator.onLine) {
            showError('Sin conexión a internet. Verifique su conexión.');
            return false;
        }
        return true;
    }

    // Event listeners para conectividad
    window.addEventListener('online', function() {
        hideMessages();
        showSuccess('Conexión restaurada');
    });

    window.addEventListener('offline', function() {
        showError('Se perdió la conexión a internet');
    });

    // Función para exportar datos del formulario (útil para debugging)
    window.exportFormData = function() {
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        console.log('Datos del formulario:', data);
        return data;
    };

    // Auto-guardado en localStorage (opcional)
    function setupAutoSave() {
        const inputs = form.querySelectorAll('input:not([type="password"]):not([type="file"]), select');
        
        inputs.forEach(input => {
            // Cargar valor guardado
            const savedValue = localStorage.getItem(`colaborador_form_${input.name}`);
            if (savedValue && !input.value) {
                input.value = savedValue;
            }
            
            // Guardar en cada cambio
            input.addEventListener('input', debounce(function() {
                if (this.value) {
                    localStorage.setItem(`colaborador_form_${this.name}`, this.value);
                } else {
                    localStorage.removeItem(`colaborador_form_${this.name}`);
                }
            }, 1000));
        });
    }

    // Función para limpiar auto-guardado
    function clearAutoSave() {
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            localStorage.removeItem(`colaborador_form_${input.name}`);
        });
    }

    // Inicializar auto-guardado
    setupAutoSave();

    // Limpiar auto-guardado cuando el formulario se envía exitosamente
    form.addEventListener('submit', function() {
        // Se limpiará después de un envío exitoso
        setTimeout(clearAutoSave, 1000);
    });

    // Notificar si hay datos guardados
    window.addEventListener('load', function() {
        const hasSavedData = localStorage.getItem('colaborador_form_nombres');
        if (hasSavedData) {
            showSuccess('Se restauraron algunos datos guardados anteriormente');
        }
    });

    // Confirmación antes de salir si hay datos sin guardar
    window.addEventListener('beforeunload', function(e) {
        const formData = new FormData(form);
        let hasData = false;
        
        for (let [key, value] of formData.entries()) {
            if (value && key !== 'action') {
                hasData = true;
                break;
            }
        }
        
        if (hasData) {
            e.preventDefault();
            e.returnValue = '¿Está seguro de que desea salir? Los datos no guardados se perderán.';
            return e.returnValue;
        }
    });

    console.log('Script de colaboradores inicializado correctamente');
});