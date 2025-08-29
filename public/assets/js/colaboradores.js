// colaboradores.js - Script para la página de crear colaboradores

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

    // Configurar fecha máxima (18 años atrás)
    const today = new Date();
    const maxDate = new Date(today.getFullYear() - 18, today.getMonth(), today.getDate());
    fechaNacimientoInput.max = maxDate.toISOString().split('T')[0];

    // Validación en tiempo real del documento
    numDocumentoInput.addEventListener('input', function() {
        validateDocumento(this.value);
    });

    numDocumentoInput.addEventListener('blur', function() {
        checkDocumentoExists(this.value);
    });

    // Validación en tiempo real del correo
    correoInput.addEventListener('input', function() {
        validateEmail(this.value);
    });

    correoInput.addEventListener('blur', function() {
        checkEmailExists(this.value);
    });

    // Toggle mostrar/ocultar contraseña
    togglePasswordBtn.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        
        const icon = this.querySelector('i');
        icon.classList.toggle('fa-eye');
        icon.classList.toggle('fa-eye-slash');
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
        if (confirm('¿Estás seguro de que deseas limpiar el formulario?')) {
            form.reset();
            hideMessages();
            clearValidationMessages();
            hidePreview();
        }
    });

    // Envío del formulario
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        if (validateForm()) {
            submitForm();
        }
    });

    // Funciones de validación
    function validateDocumento(documento) {
        const feedback = document.getElementById('documento-feedback');
        
        if (!documento) {
            feedback.innerHTML = '';
            return false;
        }

        if (documento.length < 7 || documento.length > 15) {
            showValidationMessage(feedback, 'El documento debe tener entre 7 y 15 caracteres', 'error');
            return false;
        }

        if (!/^\d+$/.test(documento)) {
            showValidationMessage(feedback, 'El documento solo debe contener números', 'error');
            return false;
        }

        showValidationMessage(feedback, 'Formato válido', 'success');
        return true;
    }

    function validateEmail(email) {
        const feedback = document.getElementById('correo-feedback');
        
        if (!email) {
            feedback.innerHTML = '';
            return false;
        }

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (!emailRegex.test(email)) {
            showValidationMessage(feedback, 'Formato de correo inválido', 'error');
            return false;
        }

        showValidationMessage(feedback, 'Formato válido', 'success');
        return true;
    }

    function validatePasswordStrength(password) {
        const strengthDiv = document.getElementById('password-strength');
        
        if (!password) {
            strengthDiv.innerHTML = '';
            return false;
        }

        let strength = 0;
        let feedback = [];

        // Longitud mínima
        if (password.length >= 6) strength++;
        else feedback.push('Mínimo 6 caracteres');

        // Contiene números
        if (/\d/.test(password)) strength++;
        else feedback.push('Al menos un número');

        // Contiene letras minúsculas
        if (/[a-z]/.test(password)) strength++;
        else feedback.push('Al menos una letra minúscula');

        // Contiene letras mayúsculas
        if (/[A-Z]/.test(password)) strength++;
        else feedback.push('Al menos una letra mayúscula');

        // Caracteres especiales
        if (/[!@#$%^&*(),.?":{}|<>]/.test(password)) strength++;

        let strengthText = '';
        let className = '';

        if (strength < 2) {
            strengthText = 'Débil';
            className = 'text-danger';
        } else if (strength < 4) {
            strengthText = 'Media';
            className = 'text-warning';
        } else {
            strengthText = 'Fuerte';
            className = 'text-success';
        }

        strengthDiv.innerHTML = `
            <small class="${className}">
                Fortaleza: ${strengthText}
                ${feedback.length > 0 ? ' - ' + feedback.join(', ') : ''}
            </small>
        `;

        return strength >= 2;
    }

    function checkPasswordMatch() {
        const matchDiv = document.getElementById('password-match');
        const password = passwordInput.value;
        const confirmPassword = confirmarPasswordInput.value;

        if (!confirmPassword) {
            matchDiv.innerHTML = '';
            return false;
        }

        if (password !== confirmPassword) {
            showValidationMessage(matchDiv, 'Las contraseñas no coinciden', 'error');
            return false;
        }

        showValidationMessage(matchDiv, 'Las contraseñas coinciden', 'success');
        return true;
    }

    function checkDocumentoExists(documento) {
        if (!validateDocumento(documento)) return;

        fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=checkDocumento&numDocumento=${encodeURIComponent(documento)}`
        })
        .then(response => response.json())
        .then(data => {
            const feedback = document.getElementById('documento-feedback');
            
            if (data.exists) {
                showValidationMessage(feedback, 'Este documento ya está registrado', 'error');
            } else {
                showValidationMessage(feedback, 'Documento disponible', 'success');
            }
        })
        .catch(error => {
            console.error('Error al verificar documento:', error);
        });
    }

    function checkEmailExists(email) {
        if (!validateEmail(email)) return;

        fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `action=checkEmail&correo=${encodeURIComponent(email)}`
        })
        .then(response => response.json())
        .then(data => {
            const feedback = document.getElementById('correo-feedback');
            
            if (data.exists) {
                showValidationMessage(feedback, 'Este correo ya está registrado', 'error');
            } else {
                showValidationMessage(feedback, 'Correo disponible', 'success');
            }
        })
        .catch(error => {
            console.error('Error al verificar correo:', error);
        });
    }

    function handleImagePreview(input) {
        const preview = document.getElementById('foto-preview');
        const previewImg = document.getElementById('preview-img');

        if (input.files && input.files[0]) {
            const file = input.files[0];
            
            // Validar tamaño (2MB máximo)
            if (file.size > 2 * 1024 * 1024) {
                alert('La imagen debe ser menor a 2MB');
                input.value = '';
                preview.style.display = 'none';
                return;
            }

            // Validar tipo de archivo
            const validTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                alert('Solo se permiten imágenes JPG, PNG o GIF');
                input.value = '';
                preview.style.display = 'none';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                preview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            preview.style.display = 'none';
        }
    }

    function showColaboradorPreview() {
        const preview = document.getElementById('colaborador-preview');
        const content = document.getElementById('preview-content');
        
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        // Validar que los campos requeridos estén llenos
        const requiredFields = ['numDocumento', 'tipoDocumento', 'nombres', 'apellidos', 'correo', 'numTelefono', 'sexo', 'fechaNacimiento', 'roles', 'password'];
        const missingFields = requiredFields.filter(field => !data[field]);

        if (missingFields.length > 0) {
            alert('Por favor completa todos los campos requeridos antes de ver la vista previa');
            return;
        }

        const fotoPreview = document.getElementById('preview-img').src || '../../public/assets/images/default-user.png';

        content.innerHTML = `
            <div class="row">
                <div class="col-md-3 text-center">
                    <img src="${fotoPreview}" alt="Foto de perfil" class="preview-photo">
                    <div class="mt-2">
                        <span class="badge bg-${getRoleBadgeColor(data.roles)}">${data.roles}</span>
                    </div>
                </div>
                <div class="col-md-9">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Documento:</strong> ${data.numDocumento}</p>
                            <p><strong>Tipo:</strong> ${data.tipoDocumento}</p>
                            <p><strong>Nombres:</strong> ${data.nombres}</p>
                            <p><strong>Apellidos:</strong> ${data.apellidos}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Correo:</strong> ${data.correo}</p>
                            <p><strong>Teléfono:</strong> ${data.numTelefono}</p>
                            <p><strong>Sexo:</strong> ${data.sexo}</p>
                            <p><strong>Fecha de Nacimiento:</strong> ${formatDate(data.fechaNacimiento)}</p>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p><strong>Configuraciones:</strong></p>
                        <ul>
                            <li>Solicitar cambio de contraseña: ${data.solicitarContraseña ? 'Sí' : 'No'}</li>
                        </ul>
                    </div>
                </div>
            </div>
        `;

        preview.style.display = 'block';
        preview.scrollIntoView({ behavior: 'smooth' });
    }

    function validateForm() {
        let isValid = true;
        const errors = [];

        // Validar documento
        if (!validateDocumento(numDocumentoInput.value)) {
            errors.push('Número de documento inválido');
            isValid = false;
        }

        // Validar correo
        if (!validateEmail(correoInput.value)) {
            errors.push('Correo electrónico inválido');
            isValid = false;
        }

        // Validar contraseñas
        if (!validatePasswordStrength(passwordInput.value)) {
            errors.push('La contraseña es muy débil');
            isValid = false;
        }

        if (!checkPasswordMatch()) {
            errors.push('Las contraseñas no coinciden');
            isValid = false;
        }

        // Validar edad (mayor de 18 años)
        const fechaNacimiento = new Date(fechaNacimientoInput.value);
        const edad = calcularEdad(fechaNacimiento);
        if (edad < 18) {
            errors.push('El colaborador debe ser mayor de 18 años');
            isValid = false;
        }

        if (!isValid) {
            showError(errors.join('<br>'));
        }

        return isValid;
    }

    function submitForm() {
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        
        // Mostrar loading
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
        
        hideMessages();

        const formData = new FormData(form);
        formData.append('action', 'crear');

        fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showSuccess('¡Colaborador creado exitosamente!');
                form.reset();
                clearValidationMessages();
                hidePreview();
                
                // Opcional: redirigir después de 2 segundos
                setTimeout(() => {
                    window.location.href = 'listaMisColaboradores.php';
                }, 2000);
            } else {
                showError(data.message || 'Error al crear el colaborador');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Error de conexión. Intenta nuevamente.');
        })
        .finally(() => {
            // Restaurar botón
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    }

    // Funciones auxiliares
    function showValidationMessage(element, message, type) {
        const className = type === 'success' ? 'text-success' : 'text-danger';
        const icon = type === 'success' ? 'fa-check' : 'fa-times';
        
        element.innerHTML = `<small class="${className}"><i class="fas ${icon}"></i> ${message}</small>`;
    }

    function clearValidationMessages() {
        const feedbacks = ['documento-feedback', 'correo-feedback', 'password-strength', 'password-match'];
        feedbacks.forEach(id => {
            const element = document.getElementById(id);
            if (element) element.innerHTML = '';
        });
    }

    function showSuccess(message) {
        const successDiv = document.getElementById('success-message');
        successDiv.style.display = 'block';
        successDiv.scrollIntoView({ behavior: 'smooth' });
        
        setTimeout(() => {
            successDiv.style.display = 'none';
        }, 5000);
    }

    function showError(message) {
        const errorDiv = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        
        errorText.innerHTML = message;
        errorDiv.style.display = 'block';
        errorDiv.scrollIntoView({ behavior: 'smooth' });
        
        setTimeout(() => {
            errorDiv.style.display = 'none';
        }, 8000);
    }

    function hideMessages() {
        document.getElementById('success-message').style.display = 'none';
        document.getElementById('error-message').style.display = 'none';
    }

    function hidePreview() {
        document.getElementById('colaborador-preview').style.display = 'none';
    }

    function calcularEdad(fechaNacimiento) {
        const today = new Date();
        const birthDate = new Date(fechaNacimiento);
        let age = today.getFullYear() - birthDate.getFullYear();
        const monthDiff = today.getMonth() - birthDate.getMonth();
        
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }
        
        return age;
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

    // Validación de solo letras para nombres y apellidos
    const nombresInput = document.getElementById('nombres');
    const apellidosInput = document.getElementById('apellidos');
    
    [nombresInput, apellidosInput].forEach(input => {
        input.addEventListener('input', function() {
            // Remover números y caracteres especiales, permitir solo letras y espacios
            this.value = this.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, '');
        });
    });

    // Validación de solo números para teléfono y documento
    const telefonoInput = document.getElementById('numTelefono');
    
    [numDocumentoInput, telefonoInput].forEach(input => {
        input.addEventListener('input', function() {
            this.value = this.value.replace(/[^\d]/g, '');
        });
    });

    // Auto-actualización de vista previa en tiempo real
    const formInputs = form.querySelectorAll('input, select');
    formInputs.forEach(input => {
        input.addEventListener('input', function() {
            if (document.getElementById('colaborador-preview').style.display === 'block') {
                showColaboradorPreview();
            }
        });
    });
});