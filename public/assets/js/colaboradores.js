/**
 * JavaScript para crear y gestionar colaboradores
 * Archivo: colaboradores.js
 */

class CrearColaboradorManager {
    constructor() {
        this.form = null;
        this.documentoValidado = false;
        this.correoValidado = false;
        this.passwordValida = false;
        
        this.init();
    }
    
    init() {
        this.form = document.getElementById('colaborador-form');
        if (!this.form) {
            console.error('No se encontró el formulario de colaborador');
            return;
        }
        
        this.configurarEventListeners();
        this.configurarValidaciones();
        this.configurarFechaMaxima();
    }
    
    configurarEventListeners() {
        // Evento de envío del formulario
        this.form.addEventListener('submit', (e) => {
            e.preventDefault();
            this.crearColaborador();
        });
        
        // Validación de documento
        const documentoInput = document.getElementById('numDocumento');
        if (documentoInput) {
            documentoInput.addEventListener('blur', () => this.validarDocumento());
            documentoInput.addEventListener('input', () => {
                this.documentoValidado = false;
                this.limpiarFeedback('documento-feedback');
            });
        }
        
        // Validación de correo
        const correoInput = document.getElementById('correo');
        if (correoInput) {
            correoInput.addEventListener('blur', () => this.validarCorreo());
            correoInput.addEventListener('input', () => {
                this.correoValidado = false;
                this.limpiarFeedback('correo-feedback');
            });
        }
        
        // Validación de contraseña
        const passwordInput = document.getElementById('password');
        const confirmarPasswordInput = document.getElementById('confirmarPassword');
        
        if (passwordInput) {
            passwordInput.addEventListener('input', () => this.validarFortalezaPassword());
        }
        
        if (confirmarPasswordInput) {
            confirmarPasswordInput.addEventListener('input', () => this.validarConfirmacionPassword());
        }
        
        // Toggle de visibilidad de contraseña
        const togglePasswordBtn = document.getElementById('toggle-password');
        if (togglePasswordBtn) {
            togglePasswordBtn.addEventListener('click', () => this.togglePasswordVisibility());
        }
        
        // Vista previa de foto
        const fotoInput = document.getElementById('foto');
        if (fotoInput) {
            fotoInput.addEventListener('change', () => this.previsualizarFoto());
        }
        
        // Botones de acción
        const resetBtn = document.getElementById('reset-btn');
        const previewBtn = document.getElementById('preview-btn');
        
        if (resetBtn) {
            resetBtn.addEventListener('click', () => this.limpiarFormulario());
        }
        
        if (previewBtn) {
            previewBtn.addEventListener('click', () => this.mostrarVistaPrevia());
        }
        
        // Auto-completar rol por defecto
        this.configurarRolPorDefecto();
    }
    
    configurarValidaciones() {
        // Validaciones en tiempo real para campos específicos
        const campos = {
            'nombres': { pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, message: 'Solo se permiten letras y espacios' },
            'apellidos': { pattern: /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/, message: 'Solo se permiten letras y espacios' },
            'numTelefono': { pattern: /^[0-9+\-\s()]{7,15}$/, message: 'Formato de teléfono inválido' }
        };
        
        Object.keys(campos).forEach(campo => {
            const input = document.getElementById(campo);
            if (input) {
                input.addEventListener('input', () => {
                    this.validarCampo(input, campos[campo]);
                });
            }
        });
    }
    
    configurarFechaMaxima() {
        // Establecer fecha máxima para ser mayor de 18 años
        const fechaInput = document.getElementById('fechaNacimiento');
        if (fechaInput) {
            const hoy = new Date();
            const fechaMaxima = new Date(hoy.getFullYear() - 18, hoy.getMonth(), hoy.getDate());
            fechaInput.max = fechaMaxima.toISOString().split('T')[0];
        }
    }
    
    configurarRolPorDefecto() {
        const rolSelect = document.getElementById('roles');
        if (rolSelect && !rolSelect.value) {
            rolSelect.value = 'Colaborador';
        }
    }
    
    async validarDocumento() {
        const documentoInput = document.getElementById('numDocumento');
        const feedbackDiv = document.getElementById('documento-feedback');
        
        if (!documentoInput || !feedbackDiv) return;
        
        const documento = documentoInput.value.trim();
        
        if (!documento) {
            this.mostrarFeedback(feedbackDiv, 'error', 'El documento es requerido');
            this.documentoValidado = false;
            return;
        }
        
        if (documento.length < 6 || documento.length > 15) {
            this.mostrarFeedback(feedbackDiv, 'error', 'El documento debe tener entre 6 y 15 caracteres');
            this.documentoValidado = false;
            return;
        }
        
        try {
            this.mostrarFeedback(feedbackDiv, 'info', 'Verificando documento...');
            
            const params = new URLSearchParams();
            params.append('action', 'checkDocumento');
            params.append('numDocumento', documento);
            
            const response = await fetch(`/lodgehub/app/controllers/misColaboradoresControllers.php?${params.toString()}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.data.exists) {
                    this.mostrarFeedback(feedbackDiv, 'error', 'Este documento ya está registrado');
                    this.documentoValidado = false;
                } else {
                    this.mostrarFeedback(feedbackDiv, 'success', 'Documento disponible');
                    this.documentoValidado = true;
                }
            } else {
                this.mostrarFeedback(feedbackDiv, 'error', data.message || 'Error al verificar documento');
                this.documentoValidado = false;
            }
        } catch (error) {
            console.error('Error al validar documento:', error);
            this.mostrarFeedback(feedbackDiv, 'error', 'Error de conexión');
            this.documentoValidado = false;
        }
    }
    
    async validarCorreo() {
        const correoInput = document.getElementById('correo');
        const feedbackDiv = document.getElementById('correo-feedback');
        
        if (!correoInput || !feedbackDiv) return;
        
        const correo = correoInput.value.trim();
        
        if (!correo) {
            this.mostrarFeedback(feedbackDiv, 'error', 'El correo es requerido');
            this.correoValidado = false;
            return;
        }
        
        if (!this.validarFormatoEmail(correo)) {
            this.mostrarFeedback(feedbackDiv, 'error', 'Formato de correo inválido');
            this.correoValidado = false;
            return;
        }
        
        try {
            this.mostrarFeedback(feedbackDiv, 'info', 'Verificando correo...');
            
            const params = new URLSearchParams();
            params.append('action', 'checkEmail');
            params.append('correo', correo);
            
            const response = await fetch(`/lodgehub/app/controllers/misColaboradoresControllers.php?${params.toString()}`, {
                method: 'GET',
                headers: { 'Accept': 'application/json' }
            });
            
            const data = await response.json();
            
            if (data.success) {
                if (data.data.exists) {
                    this.mostrarFeedback(feedbackDiv, 'error', 'Este correo ya está registrado');
                    this.correoValidado = false;
                } else {
                    this.mostrarFeedback(feedbackDiv, 'success', 'Correo disponible');
                    this.correoValidado = true;
                }
            } else {
                this.mostrarFeedback(feedbackDiv, 'error', data.message || 'Error al verificar correo');
                this.correoValidado = false;
            }
        } catch (error) {
            console.error('Error al validar correo:', error);
            this.mostrarFeedback(feedbackDiv, 'error', 'Error de conexión');
            this.correoValidado = false;
        }
    }
    
    validarFortalezaPassword() {
        const passwordInput = document.getElementById('password');
        const strengthDiv = document.getElementById('password-strength');
        
        if (!passwordInput || !strengthDiv) return;
        
        const password = passwordInput.value;
        const strength = this.calcularFortalezaPassword(password);
        
        let mensaje = '';
        let clase = '';
        
        switch (strength) {
            case 0:
                mensaje = 'Muy débil';
                clase = 'text-danger';
                this.passwordValida = false;
                break;
            case 1:
                mensaje = 'Débil';
                clase = 'text-warning';
                this.passwordValida = false;
                break;
            case 2:
                mensaje = 'Regular';
                clase = 'text-info';
                this.passwordValida = true;
                break;
            case 3:
                mensaje = 'Fuerte';
                clase = 'text-success';
                this.passwordValida = true;
                break;
            case 4:
                mensaje = 'Muy fuerte';
                clase = 'text-success fw-bold';
                this.passwordValida = true;
                break;
        }
        
        strengthDiv.innerHTML = `<small class="${clase}">Fortaleza: ${mensaje}</small>`;
    }
    
    calcularFortalezaPassword(password) {
        if (!password) return 0;
        
        let puntos = 0;
        
        // Longitud
        if (password.length >= 8) puntos++;
        if (password.length >= 12) puntos++;
        
        // Contiene minúsculas
        if (/[a-z]/.test(password)) puntos++;
        
        // Contiene mayúsculas
        if (/[A-Z]/.test(password)) puntos++;
        
        // Contiene números
        if (/\d/.test(password)) puntos++;
        
        // Contiene caracteres especiales
        if (/[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password)) puntos++;
        
        return Math.min(puntos - 1, 4);
    }
    
    validarConfirmacionPassword() {
        const passwordInput = document.getElementById('password');
        const confirmarPasswordInput = document.getElementById('confirmarPassword');
        const matchDiv = document.getElementById('password-match');
        
        if (!passwordInput || !confirmarPasswordInput || !matchDiv) return;
        
        const password = passwordInput.value;
        const confirmPassword = confirmarPasswordInput.value;
        
        if (!confirmPassword) {
            matchDiv.innerHTML = '';
            return;
        }
        
        if (password === confirmPassword) {
            matchDiv.innerHTML = '<small class="text-success">Las contraseñas coinciden</small>';
        } else {
            matchDiv.innerHTML = '<small class="text-danger">Las contraseñas no coinciden</small>';
        }
    }
    
    togglePasswordVisibility() {
        const passwordInput = document.getElementById('password');
        const toggleBtn = document.getElementById('toggle-password');
        const icon = toggleBtn.querySelector('i');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
    
    previsualizarFoto() {
        const fotoInput = document.getElementById('foto');
        const previewDiv = document.getElementById('foto-preview');
        const previewImg = document.getElementById('preview-img');
        
        if (!fotoInput || !previewDiv || !previewImg) return;
        
        const file = fotoInput.files[0];
        
        if (file) {
            // Validar tipo de archivo
            const tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!tiposPermitidos.includes(file.type)) {
                alert('Tipo de archivo no permitido. Use JPG, PNG o GIF.');
                fotoInput.value = '';
                previewDiv.style.display = 'none';
                return;
            }
            
            // Validar tamaño (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('El archivo es demasiado grande. Máximo 2MB.');
                fotoInput.value = '';
                previewDiv.style.display = 'none';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewDiv.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewDiv.style.display = 'none';
        }
    }
    
    validarCampo(input, validacion) {
        const valor = input.value.trim();
        
        if (valor && !validacion.pattern.test(valor)) {
            input.setCustomValidity(validacion.message);
        } else {
            input.setCustomValidity('');
        }
    }
    
    validarFormatoEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return regex.test(email);
    }
    
    async crearColaborador() {
        try {
            // Validar formulario
            if (!this.form.checkValidity()) {
                this.form.reportValidity();
                return;
            }
            
            // Validaciones adicionales
            if (!this.documentoValidado) {
                this.mostrarMensaje('error', 'Por favor, valide el número de documento');
                return;
            }
            
            if (!this.correoValidado) {
                this.mostrarMensaje('error', 'Por favor, valide el correo electrónico');
                return;
            }
            
            // Validar confirmación de contraseña
            const password = document.getElementById('password').value;
            const confirmarPassword = document.getElementById('confirmarPassword').value;
            
            if (password !== confirmarPassword) {
                this.mostrarMensaje('error', 'Las contraseñas no coinciden');
                return;
            }
            
            this.mostrarLoading(true);
            
            const formData = new FormData(this.form);
            formData.append('action', 'crear');
            
            const response = await fetch('/lodgehub/app/controllers/misColaboradoresControllers.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.mostrarMensaje('success', data.message + " Redirigiendo a la lista...");
                
                // Redireccionar después de 2 segundos
                setTimeout(() => {
                    this.limpiarFormulario();
                    window.location.href = 'listaMisColaboradores.php';
                }, 2500);
            } else {
                this.mostrarMensaje('error', data.message);
            }
        } catch (error) {
            console.error('Error al crear colaborador:', error);
            this.mostrarMensaje('error', 'Error de conexión. Intente nuevamente.');
        } finally {
            this.mostrarLoading(false);
        }
    }
    
    mostrarVistaPrevia() {
        const previewDiv = document.getElementById('colaborador-preview');
        const previewContent = document.getElementById('preview-content');
        
        if (!previewDiv || !previewContent) return;
        
        // Recopilar datos del formulario
        const datos = {
            numDocumento: document.getElementById('numDocumento').value,
            tipoDocumento: document.getElementById('tipoDocumento').value,
            nombres: document.getElementById('nombres').value,
            apellidos: document.getElementById('apellidos').value,
            correo: document.getElementById('correo').value,
            numTelefono: document.getElementById('numTelefono').value,
            sexo: document.getElementById('sexo').value,
            fechaNacimiento: document.getElementById('fechaNacimiento').value,
            roles: document.getElementById('roles').value,
            solicitarContraseña: document.getElementById('solicitarContraseña').checked
        };
        
        const html = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-user"></i> Información Personal</h6>
                    <p><strong>Documento:</strong> ${datos.numDocumento || 'No especificado'}</p>
                    <p><strong>Tipo:</strong> ${datos.tipoDocumento || 'No especificado'}</p>
                    <p><strong>Nombres:</strong> ${datos.nombres || 'No especificado'}</p>
                    <p><strong>Apellidos:</strong> ${datos.apellidos || 'No especificado'}</p>
                    <p><strong>Sexo:</strong> ${datos.sexo || 'No especificado'}</p>
                    <p><strong>Fecha de Nacimiento:</strong> ${this.formatearFecha(datos.fechaNacimiento) || 'No especificada'}</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-envelope"></i> Información de Contacto</h6>
                    <p><strong>Correo:</strong> ${datos.correo || 'No especificado'}</p>
                    <p><strong>Teléfono:</strong> ${datos.numTelefono || 'No especificado'}</p>
                    <p><strong>Rol:</strong> <span class="badge ${this.getBadgeClass(datos.roles)}">${datos.roles || 'No especificado'}</span></p>
                    <p><strong>Solicitar cambio de contraseña:</strong> ${datos.solicitarContraseña ? 'Sí' : 'No'}</p>
                </div>
            </div>
        `;
        
        previewContent.innerHTML = html;
        previewDiv.style.display = 'block';
        
        // Scroll suave hacia la vista previa
        previewDiv.scrollIntoView({ behavior: 'smooth' });
    }
    
    limpiarFormulario() {
        if (this.form) {
            this.form.reset();
            
            // Limpiar validaciones
            this.documentoValidado = false;
            this.correoValidado = false;
            this.passwordValida = false;
            
            // Limpiar feedback
            this.limpiarFeedback('documento-feedback');
            this.limpiarFeedback('correo-feedback');
            this.limpiarFeedback('password-strength');
            this.limpiarFeedback('password-match');
            
            // Ocultar vista previa de foto
            const previewDiv = document.getElementById('foto-preview');
            if (previewDiv) {
                previewDiv.style.display = 'none';
            }
            
            // Ocultar vista previa del colaborador
            const colaboradorPreview = document.getElementById('colaborador-preview');
            if (colaboradorPreview) {
                colaboradorPreview.style.display = 'none';
            }
            
            // Resetear rol por defecto
            this.configurarRolPorDefecto();
        }
    }
    
    mostrarMensaje(tipo, mensaje) {
        const successDiv = document.getElementById('success-message');
        const errorDiv = document.getElementById('error-message');
        const errorText = document.getElementById('error-text');
        
        // Ocultar ambos mensajes primero
        if (successDiv) successDiv.style.display = 'none';
        if (errorDiv) errorDiv.style.display = 'none';
        
        if (tipo === 'success' && successDiv) {
            successDiv.style.display = 'block';
            setTimeout(() => successDiv.style.display = 'none', 5000);
            
            // Scroll al mensaje
            successDiv.scrollIntoView({ behavior: 'smooth' });
        } else if (tipo === 'error' && errorDiv && errorText) {
            errorText.textContent = mensaje;
            errorDiv.style.display = 'block';
            setTimeout(() => errorDiv.style.display = 'none', 8000);
            
            // Scroll al mensaje
            errorDiv.scrollIntoView({ behavior: 'smooth' });
        }
    }
    
    mostrarLoading(mostrar) {
        const submitBtn = this.form.querySelector('button[type="submit"]');
        if (!submitBtn) return;
        
        if (mostrar) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando...';
        } else {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-user-plus"></i> Crear Colaborador';
        }
    }
    
    mostrarFeedback(elemento, tipo, mensaje) {
        if (!elemento) return;
        
        const clases = {
            'success': 'text-success',
            'error': 'text-danger',
            'info': 'text-info',
            'warning': 'text-warning'
        };
        
        const iconos = {
            'success': 'fa-check-circle',
            'error': 'fa-exclamation-circle',
            'info': 'fa-info-circle',
            'warning': 'fa-exclamation-triangle'
        };
        
        elemento.innerHTML = `
            <small class="${clases[tipo] || 'text-muted'}">
                <i class="fas ${iconos[tipo] || 'fa-info-circle'}"></i>
                ${mensaje}
            </small>
        `;
    }
    
    limpiarFeedback(elementoId) {
        const elemento = document.getElementById(elementoId);
        if (elemento) {
            elemento.innerHTML = '';
        }
    }
    
    // Funciones utilitarias
    formatearFecha(fecha) {
        if (!fecha) return '';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-CO');
    }
    
    getBadgeClass(rol) {
        const badges = {
            'Administrador': 'bg-danger',
            'Colaborador': 'bg-primary',
            'Usuario': 'bg-success'
        };
        return badges[rol] || 'bg-secondary';
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.crearColaboradorManager = new CrearColaboradorManager();
});