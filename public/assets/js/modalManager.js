/**
 * Gestor Global de Modales
 * Proporciona funcionalidades comunes para todos los modales del sistema
 */
class ModalManager {
    constructor() {
        this.activeModals = new Map();
        this.init();
    }

    init() {
        // Configurar eventos globales
        this.setupGlobalEvents();
    }

    setupGlobalEvents() {
        // Cerrar modal con ESC
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeTopModal();
            }
        });

        // Prevenir cierre accidental del modal al hacer clic en el contenido
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal') && e.target.classList.contains('show')) {
                // Solo cerrar si se hace clic en el backdrop, no en el contenido
                const modalContent = e.target.querySelector('.modal-content');
                if (modalContent && !modalContent.contains(e.target)) {
                    this.closeModal(e.target.id);
                }
            }
        });
    }

    /**
     * Registra un modal en el manager
     */
    registerModal(modalId, modalInstance) {
        this.activeModals.set(modalId, modalInstance);
    }

    /**
     * Abre un modal específico
     */
    openModal(modalId, options = {}) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) {
            console.error(`Modal con ID ${modalId} no encontrado`);
            return false;
        }

        try {
            const modal = new bootstrap.Modal(modalElement, {
                backdrop: options.backdrop !== false ? 'static' : false,
                keyboard: options.keyboard !== false,
                focus: options.focus !== false
            });
            
            modal.show();
            this.activeModals.set(modalId, modal);
            
            // Ejecutar callback si se proporciona
            if (options.onShow) {
                modalElement.addEventListener('shown.bs.modal', options.onShow, { once: true });
            }
            
            return true;
        } catch (error) {
            console.error(`Error al abrir modal ${modalId}:`, error);
            return false;
        }
    }

    /**
     * Cierra un modal específico
     */
    closeModal(modalId) {
        const modal = this.activeModals.get(modalId);
        if (modal) {
            modal.hide();
            this.activeModals.delete(modalId);
            return true;
        }
        return false;
    }

    /**
     * Cierra el modal que está arriba (último abierto)
     */
    closeTopModal() {
        const openModals = document.querySelectorAll('.modal.show');
        if (openModals.length > 0) {
            const topModal = openModals[openModals.length - 1];
            this.closeModal(topModal.id);
        }
    }

    /**
     * Cierra todos los modales abiertos
     */
    closeAllModals() {
        this.activeModals.forEach((modal, modalId) => {
            modal.hide();
        });
        this.activeModals.clear();
    }

    /**
     * Muestra una alerta dentro de un modal
     */
    showModalAlert(modalId, type, message, autoHide = false) {
        const modal = document.getElementById(modalId);
        if (!modal) return false;

        const alertContainer = modal.querySelector('.modal-alert, #modalAlert, .alert-container');
        if (!alertContainer) return false;

        alertContainer.className = `alert alert-${type}`;
        alertContainer.innerHTML = `
            <i class="fas fa-${this.getAlertIcon(type)} me-2"></i>
            ${message}
        `;
        alertContainer.classList.remove('d-none');

        // Auto-ocultar después de un tiempo
        if (autoHide) {
            setTimeout(() => {
                alertContainer.classList.add('d-none');
            }, autoHide);
        }

        // Scroll hacia la alerta
        alertContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        return true;
    }

    /**
     * Oculta la alerta de un modal
     */
    hideModalAlert(modalId) {
        const modal = document.getElementById(modalId);
        if (!modal) return false;

        const alertContainer = modal.querySelector('.modal-alert, #modalAlert, .alert-container');
        if (alertContainer) {
            alertContainer.classList.add('d-none');
            return true;
        }
        return false;
    }

    /**
     * Valida un formulario dentro de un modal
     */
    validateModalForm(modalId, options = {}) {
        const modal = document.getElementById(modalId);
        if (!modal) return false;

        const form = modal.querySelector('form');
        if (!form) return false;

        let isValid = true;
        const errors = [];

        // Validar campos requeridos
        const requiredFields = form.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            if (!field.value.trim()) {
                this.setFieldError(field, 'Este campo es obligatorio');
                errors.push(`${this.getFieldLabel(field)} es obligatorio`);
                isValid = false;
            } else {
                this.clearFieldError(field);
            }
        });

        // Validaciones personalizadas
        if (options.customValidations) {
            options.customValidations.forEach(validation => {
                const field = form.querySelector(validation.selector);
                if (field && !validation.validate(field.value)) {
                    this.setFieldError(field, validation.message);
                    errors.push(validation.message);
                    isValid = false;
                }
            });
        }

        // Mostrar errores si los hay
        if (!isValid && options.showErrors) {
            this.showModalAlert(modalId, 'danger', errors.join('<br>'));
        }

        return isValid;
    }

    /**
     * Establece el estado de carga en un modal
     */
    setModalLoadingState(modalId, loading, buttonSelector = '.btn-primary') {
        const modal = document.getElementById(modalId);
        if (!modal) return false;

        const button = modal.querySelector(buttonSelector);
        if (!button) return false;

        if (loading) {
            button.disabled = true;
            button.dataset.originalText = button.innerHTML;
            button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Cargando...';
        } else {
            button.disabled = false;
            button.innerHTML = button.dataset.originalText || button.innerHTML;
        }
        
        return true;
    }

    /**
     * Utilidades privadas
     */
    getAlertIcon(type) {
        const icons = {
            'success': 'check-circle',
            'danger': 'exclamation-triangle',
            'warning': 'exclamation-circle',
            'info': 'info-circle'
        };
        return icons[type] || 'info-circle';
    }

    setFieldError(field, message) {
        field.classList.add('is-invalid');
        
        // Remover mensaje anterior
        const existingError = field.parentNode.querySelector('.invalid-feedback');
        if (existingError) existingError.remove();

        // Agregar nuevo mensaje
        const errorDiv = document.createElement('div');
        errorDiv.className = 'invalid-feedback';
        errorDiv.textContent = message;
        field.parentNode.appendChild(errorDiv);
    }

    clearFieldError(field) {
        field.classList.remove('is-invalid');
        const errorDiv = field.parentNode.querySelector('.invalid-feedback');
        if (errorDiv) errorDiv.remove();
    }

    getFieldLabel(field) {
        const label = document.querySelector(`label[for="${field.id}"]`);
        return label ? label.textContent.replace('*', '').trim() : 'Campo';
    }

    /**
     * Realiza una petición AJAX para modales
     */
    async ajaxRequest(url, options = {}) {
        const defaultOptions = {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                ...options.headers
            }
        };

        try {
            const response = await fetch(url, { ...defaultOptions, ...options });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                return await response.json();
            } else {
                return await response.text();
            }
        } catch (error) {
            console.error('Error en petición AJAX:', error);
            throw error;
        }
    }

    /**
     * Carga contenido dinámico en un modal
     */
    async loadModalContent(modalId, url, targetSelector = '.modal-body') {
        const modal = document.getElementById(modalId);
        if (!modal) return false;

        const target = modal.querySelector(targetSelector);
        if (!target) return false;

        try {
            this.setModalLoadingState(modalId, true, '.btn');
            
            const content = await this.ajaxRequest(url, { method: 'GET' });
            target.innerHTML = content;
            
            return true;
        } catch (error) {
            this.showModalAlert(modalId, 'danger', 'Error al cargar el contenido');
            return false;
        } finally {
            this.setModalLoadingState(modalId, false, '.btn');
        }
    }
}

// Instancia global del manager
const modalManager = new ModalManager();

// Funciones globales para facilitar el uso
window.openModal = (modalId, options) => modalManager.openModal(modalId, options);
window.closeModal = (modalId) => modalManager.closeModal(modalId);
window.showModalAlert = (modalId, type, message, autoHide) => 
    modalManager.showModalAlert(modalId, type, message, autoHide);
window.hideModalAlert = (modalId) => modalManager.hideModalAlert(modalId);