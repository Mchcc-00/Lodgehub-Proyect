/**
 * Gestor del Modal de Editar Perfil
 */
class PerfilModal {
    constructor() {
        this.modal = null;
        this.form = null;
        this.alertContainer = null;
        this.submitButton = null;
        this.init();
    }

    init() {
        // Esperar a que el DOM esté listo
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.setupModal());
        } else {
            this.setupModal();
        }
    }

    setupModal() {
        // Obtener elementos del DOM
        this.modal = document.getElementById('editarPerfilModal');
        this.form = document.getElementById('formEditarPerfil');
        this.alertContainer = document.getElementById('modalAlert');
        this.submitButton = document.getElementById('btnGuardarPerfil');

        if (!this.modal || !this.form) {
            console.error('Modal o formulario no encontrado');
            return;
        }

        // Configurar eventos
        this.setupEvents();
    }

    setupEvents() {
        // Evento de envío del formulario
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));

        // Limpiar alertas cuando se abra el modal
        this.modal.addEventListener('show.bs.modal', () => {
            this.hideAlert();
            this.resetForm();
        });

        // Validación en tiempo real
        this.setupRealTimeValidation();
    }

    setupRealTimeValidation() {
        const inputs = this.form.querySelectorAll('input[required]');
        
        inputs.forEach(input => {
            input.addEventListener('blur', () => this.validateField(input));
            input.addEventListener('input', () => this.clearFieldError(input));
        });

        // Validación especial para email
        const emailInput = this.form.querySelector('#modalCorreo');
        if (emailInput) {
            emailInput.addEventListener('blur', () => this.validateEmail(emailInput));
        }

        // Validación especial para teléfono
        const phoneInput = this.form.querySelector('#modalTelefono');
        if (phoneInput) {
            phoneInput.addEventListener('blur', () => this.validatePhone(phoneInput));
        }
    }

    validateField(field) {
        if (field.hasAttribute('required') && !field.value.trim()) {
            this.setFieldError(field, 'Este campo es obligatorio');
            return false;
        }
        this.clearFieldError(field);
        return true;
    }

    validateEmail(field) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (field.value && !emailRegex.test(field.value)) {
            this.setFieldError(field, 'Formato de correo electrónico inválido');
            return false;
        }
        return this.validateField(field);
    }

    validatePhone(field) {
        const phoneRegex = /^[0-9+\-\s]+$/;
        
        if (field.value && !phoneRegex.test(field.value)) {
            this.setFieldError(field, 'El teléfono solo puede contener números, +, - y espacios');
            return false;
        }
        return this.validateField(field);
    }

    setFieldError(field, message) {
        field.classList.add('is-invalid');
        
        // Remover mensaje de error anterior
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) {
            existingError.remove();
        }

        // Agregar nuevo mensaje de error
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) {
            errorDiv.remove();
        }
    }

    async handleSubmit(e) {
        e.preventDefault();
        
        // Validar formulario
        if (!this.validateForm()) {
            return;
        }

        // Mostrar estado de carga
        this.setLoadingState(true);
        this.hideAlert();

        try {
            const formData = new FormData(this.form);
            
            const response = await fetch('procesar-editar-perfil.php', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.success) {
                this.showAlert('success', result.message);
                
                // Actualizar la página después de 1.5 segundos
                setTimeout(() => {
                    this.closeModal();
                    location.reload(); // Recargar para mostrar los cambios
                }, 1500);
                
            } else {
                this.showAlert('danger', result.message);
            }

        } catch (error) {
            console.error('Error:', error);
            this.showAlert('danger', 'Error de conexión. Intenta nuevamente.');
        } finally {
            this.setLoadingState(false);
        }
    }

    validateForm() {
        const requiredFields = this.form.querySelectorAll('input[required]');
        let isValid = true;

        requiredFields.forEach(field => {
            if (!this.validateField(field)) {
                isValid = false;
            }
        });

        // Validaciones adicionales
        const emailField = this.form.querySelector('#modalCorreo');
        if (emailField && !this.validateEmail(emailField)) {
            isValid = false;
        }

        const phoneField = this.form.querySelector('#modalTelefono');
        if (phoneField && !this.validatePhone(phoneField)) {
            isValid = false;
        }

        return isValid;
    }

    setLoadingState(loading) {
        if (!this.submitButton) return;

        if (loading) {
            this.submitButton.disabled = true;
            this.submitButton.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Guardando...';
        } else {
            this.submitButton.disabled = false;
            this.submitButton.innerHTML = '<i class="fas fa-save me-2"></i>Guardar Cambios';
        }
    }

    showAlert(type, message) {
        if (!this.alertContainer) return;

        this.alertContainer.className = `alert alert-${type}`;
        this.alertContainer.innerHTML = `
            <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
            ${message}
        `;
        this.alertContainer.classList.remove('d-none');

        // Scroll hacia la alerta
        this.alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    hideAlert() {
        if (this.alertContainer) {
            this.alertContainer.classList.add('d-none');
        }
    }

    resetForm() {
        // Limpiar errores de validación
        const invalidFields = this.form.querySelectorAll('.is-invalid');
        invalidFields.forEach(field => this.clearFieldError(field));
    }

    closeModal() {
        const modalInstance = bootstrap.Modal.getInstance(this.modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    }

    // Método público para abrir el modal
    open() {
        const modalInstance = new bootstrap.Modal(this.modal);
        modalInstance.show();
    }
}

// Inicializar cuando el DOM esté listo
const perfilModal = new PerfilModal();

// Función global para abrir el modal (para usar desde otros archivos)
function abrirModalEditarPerfil() {
    perfilModal.open();
}