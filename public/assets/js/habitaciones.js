// ===== GESTIÓN DE HABITACIONES - JAVASCRIPT =====

document.addEventListener('DOMContentLoaded', function() {
    initializeRoomsModule();
});

function initializeRoomsModule() {
    // Inicializar componentes según la página actual
    const currentPath = window.location.pathname;
    
    if (currentPath.includes('index') || currentPath.includes('room')) {
        const urlParams = new URLSearchParams(window.location.search);
        const action = urlParams.get('action') || 'index';
        
        switch (action) {
            case 'index':
                initializeRoomsList();
                break;
            case 'create':
            case 'edit':
                initializeRoomForm();
                break;
        }
    }
    
    // Funcionalidades globales
    initializeSidebar();
    initializeAlerts();
}

// ===== LISTA DE HABITACIONES =====
function initializeRoomsList() {
    initializeFilters();
    initializeSearch();
    initializeDeleteModal();
    initializeRoomCards();
}

function initializeFilters() {
    const statusFilter = document.getElementById('statusFilter');
    if (!statusFilter) return;
    
    statusFilter.addEventListener('change', function() {
        const selectedStatus = this.value;
        filterRoomsByStatus(selectedStatus);
        
        // Actualizar URL si es necesario
        if (selectedStatus) {
            updateURLParameter('estado', selectedStatus);
        } else {
            removeURLParameter('estado');
        }
    });
}

function initializeSearch() {
    const searchInput = document.getElementById('searchInput');
    if (!searchInput) return;
    
    let searchTimeout;
    
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const searchTerm = this.value.trim().toLowerCase();
        
        // Debounce para mejorar rendimiento
        searchTimeout = setTimeout(() => {
            searchRooms(searchTerm);
        }, 300);
    });
    
    // Limpiar búsqueda con Escape
    searchInput.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            this.value = '';
            searchRooms('');
        }
    });
}

function initializeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    const deleteButtons = document.querySelectorAll('.delete-room');
    const closeModal = document.getElementById('closeModal');
    const cancelDelete = document.getElementById('cancelDelete');
    
    if (!modal) return;
    
    // Abrir modal
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const roomNumber = this.getAttribute('data-numero');
            document.getElementById('roomNumberToDelete').textContent = roomNumber;
            document.getElementById('numeroToDelete').value = roomNumber;
            showModal(modal);
        });
    });
    
    // Cerrar modal
    [closeModal, cancelDelete].forEach(button => {
        if (button) {
            button.addEventListener('click', function() {
                hideModal(modal);
            });
        }
    });
    
    // Cerrar modal al hacer clic fuera
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            hideModal(modal);
        }
    });
    
    // Cerrar modal con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && modal.classList.contains('show')) {
            hideModal(modal);
        }
    });
}

function initializeRoomCards() {
    const roomCards = document.querySelectorAll('.room-card');
    
    // Animación de entrada para las tarjetas
    if (roomCards.length > 0) {
        roomCards.forEach((card, index) => {
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }
    
    // Lazy loading para imágenes
    const images = document.querySelectorAll('.room-card img[loading="lazy"]');
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.getAttribute('src');
                    img.classList.remove('lazy');
                    observer.unobserve(img);
                }
            });
        });
        
        images.forEach(img => imageObserver.observe(img));
    }
}

// ===== FORMULARIO DE HABITACIONES =====
function initializeRoomForm() {
    initializeFileUpload();
    initializeFormValidation();
    initializeMaintenanceToggle();
    initializeFormInteractions();
}

function initializeFileUpload() {
    const fileInput = document.getElementById('foto');
    const fileLabel = document.querySelector('.file-label');
    const fileText = document.getElementById('fileText');
    const imagePreview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');
    const removeImage = document.getElementById('removeImage');
    
    if (!fileInput) return;
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        
        if (file) {
            // Validar tipo de archivo
            const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
            if (!validTypes.includes(file.type)) {
                showAlert('Solo se permiten archivos JPG, JPEG, PNG y GIF', 'error');
                this.value = '';
                return;
            }
            
            // Validar tamaño (5MB)
            const maxSize = 5 * 1024 * 1024;
            if (file.size > maxSize) {
                showAlert('El archivo no debe superar los 5MB', 'error');
                this.value = '';
                return;
            }
            
            // Mostrar preview
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                imagePreview.style.display = 'block';
                fileText.textContent = file.name;
                
                // Ocultar imagen actual si existe
                const currentImage = document.querySelector('.current-image');
                if (currentImage) {
                    currentImage.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    });
    
    // Remover imagen
    if (removeImage) {
        removeImage.addEventListener('click', function() {
            fileInput.value = '';
            imagePreview.style.display = 'none';
            fileText.textContent = 'Seleccionar imagen';
            
            // Mostrar imagen actual nuevamente si existe
            const currentImage = document.querySelector('.current-image');
            if (currentImage) {
                currentImage.style.display = 'block';
            }
        });
    }
    
    // Drag and drop
    let dragCounter = 0;
    
    fileLabel.addEventListener('dragenter', function(e) {
        e.preventDefault();
        dragCounter++;
        this.classList.add('drag-over');
    });
    
    fileLabel.addEventListener('dragleave', function(e) {
        e.preventDefault();
        dragCounter--;
        if (dragCounter <= 0) {
            this.classList.remove('drag-over');
        }
    });
    
    fileLabel.addEventListener('dragover', function(e) {
        e.preventDefault();
    });
    
    fileLabel.addEventListener('drop', function(e) {
        e.preventDefault();
        dragCounter = 0;
        this.classList.remove('drag-over');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            fileInput.files = files;
            fileInput.dispatchEvent(new Event('change'));
        }
    });
}

function initializeFormValidation() {
    const form = document.getElementById('roomForm');
    if (!form) return;
    
    // Validación en tiempo real
    const inputs = form.querySelectorAll('input[required], select[required]');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
    
    // Validación antes del envío
    form.addEventListener('submit', function(e) {
        let isValid = true;
        
        inputs.forEach(input => {
            if (!validateField(input)) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            scrollToFirstError();
        }
    });
}

function initializeMaintenanceToggle() {
    const estadoSelect = document.getElementById('estado');
    const maintenanceDescription = document.querySelector('.maintenance-description');
    
    if (!estadoSelect || !maintenanceDescription) return;
    
    function toggleMaintenanceDescription() {
        if (estadoSelect.value === 'Mantenimiento') {
            maintenanceDescription.style.display = 'block';
            maintenanceDescription.classList.add('show');
        } else {
            maintenanceDescription.classList.add('hide');
            setTimeout(() => {
                maintenanceDescription.style.display = 'none';
                maintenanceDescription.classList.remove('hide', 'show');
            }, 300);
        }
    }
    
    estadoSelect.addEventListener('change', toggleMaintenanceDescription);
    
    // Ejecutar al cargar la página
    toggleMaintenanceDescription();
}

function initializeFormInteractions() {
    // Auto-format para campos numéricos
    const costoInput = document.getElementById('costo');
    const capacidadInput = document.getElementById('capacidad');
    
    if (costoInput) {
        costoInput.addEventListener('input', function() {
            // Permitir solo números y punto decimal
            this.value = this.value.replace(/[^0-9.]/g, '');
            
            // Asegurar solo un punto decimal
            const parts = this.value.split('.');
            if (parts.length > 2) {
                this.value = parts[0] + '.' + parts.slice(1).join('');
            }
        });
    }
    
    if (capacidadInput) {
        capacidadInput.addEventListener('input', function() {
            // Permitir solo números enteros
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
    
    // Contador de caracteres para textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        const maxLength = textarea.getAttribute('maxlength');
        if (maxLength) {
            createCharacterCounter(textarea, maxLength);
        }
    });
}

// ===== FUNCIONES DE FILTRADO Y BÚSQUEDA =====
function filterRoomsByStatus(status) {
    const roomCards = document.querySelectorAll('.room-card');
    const emptyState = document.querySelector('.empty-state');
    let visibleCount = 0;
    
    roomCards.forEach(card => {
        const cardStatus = card.getAttribute('data-estado');
        const shouldShow = !status || cardStatus === status;
        
        if (shouldShow) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });
    
    // Mostrar/ocultar estado vacío
    if (emptyState) {
        emptyState.style.display = visibleCount === 0 ? 'block' : 'none';
    }
    
    updateResultsCount(visibleCount);
}

function searchRooms(searchTerm) {
    const roomCards = document.querySelectorAll('.room-card');
    let visibleCount = 0;
    
    roomCards.forEach(card => {
        const numero = card.getAttribute('data-numero').toLowerCase();
        const tipo = card.getAttribute('data-tipo').toLowerCase();
        const descripcion = card.querySelector('.room-description')?.textContent.toLowerCase() || '';
        
        const matches = numero.includes(searchTerm) || 
                       tipo.includes(searchTerm) || 
                       descripcion.includes(searchTerm);
        
        if (matches) {
            card.classList.remove('hidden');
            visibleCount++;
        } else {
            card.classList.add('hidden');
        }
    });
    
    updateResultsCount(visibleCount);
    highlightSearchTerm(searchTerm);
}

function highlightSearchTerm(term) {
    if (!term) return;
    
    const roomCards = document.querySelectorAll('.room-card:not(.hidden)');
    roomCards.forEach(card => {
        const elements = card.querySelectorAll('.room-number, .room-type, .room-description');
        elements.forEach(element => {
            const text = element.textContent;
            const highlightedText = text.replace(
                new RegExp(`(${term})`, 'gi'),
                '<mark>$1</mark>'
            );
            element.innerHTML = highlightedText;
        });
    });
}

// ===== FUNCIONES DE MODAL =====
function showModal(modal) {
    modal.classList.add('show');
    document.body.style.overflow = 'hidden';
    
    // Focus en el primer elemento focusable
    const focusableElement = modal.querySelector('button, input, select, textarea');
    if (focusableElement) {
        setTimeout(() => focusableElement.focus(), 100);
    }
}

function hideModal(modal) {
    modal.classList.remove('show');
    document.body.style.overflow = '';
}

// ===== FUNCIONES DE VALIDACIÓN =====
function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name;
    let isValid = true;
    let errorMessage = '';
    
    // Validaciones específicas por campo
    switch (fieldName) {
        case 'numero':
            if (!value) {
                errorMessage = 'El número de habitación es obligatorio';
                isValid = false;
            } else if (value.length > 10) {
                errorMessage = 'El número no debe superar los 10 caracteres';
                isValid = false;
            }
            break;
            
        case 'costo':
            if (!value) {
                errorMessage = 'El costo es obligatorio';
                isValid = false;
            } else if (parseFloat(value) <= 0) {
                errorMessage = 'El costo debe ser mayor a 0';
                isValid = false;
            } else if (parseFloat(value) > 999999.99) {
                errorMessage = 'El costo es demasiado alto';
                isValid = false;
            }
            break;
            
        case 'capacidad':
            if (!value) {
                errorMessage = 'La capacidad es obligatoria';
                isValid = false;
            } else if (parseInt(value) <= 0) {
                errorMessage = 'La capacidad debe ser mayor a 0';
                isValid = false;
            } else if (parseInt(value) > 20) {
                errorMessage = 'La capacidad máxima es 20 personas';
                isValid = false;
            }
            break;
            
        case 'tipoHabitacion':
            if (!value) {
                errorMessage = 'Debe seleccionar un tipo de habitación';
                isValid = false;
            }
            break;
    }
    
    if (!isValid) {
        showFieldError(field, errorMessage);
    } else {
        clearFieldError(field);
    }
    
    return isValid;
}

function showFieldError(field, message) {
    clearFieldError(field);
    
    field.classList.add('error');
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.textContent = message;
    
    field.parentNode.appendChild(errorDiv);
}

function clearFieldError(field) {
    field.classList.remove('error');
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

function scrollToFirstError() {
    const firstError = document.querySelector('.field-error');
    if (firstError) {
        firstError.scrollIntoView({ 
            behavior: 'smooth', 
            block: 'center' 
        });
    }
}

// ===== FUNCIONES AUXILIARES =====
function showAlert(message, type = 'info') {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        ${message}
    `;
    
    const content = document.querySelector('.content');
    const firstChild = content.querySelector('.page-header').nextElementSibling;
    content.insertBefore(alertDiv, firstChild);
    
    // Auto-ocultar después de 5 segundos
    setTimeout(() => {
        alertDiv.remove();
    }, 5000);
}

function updateResultsCount(count) {
    let counter = document.querySelector('.results-counter');
    
    if (!counter) {
        counter = document.createElement('div');
        counter.className = 'results-counter';
        document.querySelector('.filters-container').appendChild(counter);
    }
    
    const total = document.querySelectorAll('.room-card').length;
    counter.textContent = `Mostrando ${count} de ${total} habitaciones`;
}

function createCharacterCounter(textarea, maxLength) {
    const counter = document.createElement('div');
    counter.className = 'character-counter';
    textarea.parentNode.appendChild(counter);
    
    function updateCounter() {
        const remaining = maxLength - textarea.value.length;
        counter.textContent = `${remaining} caracteres restantes`;
        counter.classList.toggle('warning', remaining < 50);
    }
    
    textarea.addEventListener('input', updateCounter);
    updateCounter();
}

function updateURLParameter(param, value) {
    const url = new URL(window.location);
    url.searchParams.set(param, value);
    window.history.pushState({}, '', url);
}

function removeURLParameter(param) {
    const url = new URL(window.location);
    url.searchParams.delete(param);
    window.history.pushState({}, '', url);
}

// ===== FUNCIONES GLOBALES =====
function initializeSidebar() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    
    if (!sidebarToggle || !sidebar) return;
    
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('open');
        if (sidebarOverlay) {
            sidebarOverlay.classList.toggle('active');
        }
    });
    
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', function() {
            sidebar.classList.remove('open');
            this.classList.remove('active');
        });
    }
    
    // Cerrar sidebar con Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('open')) {
            sidebar.classList.remove('open');
            if (sidebarOverlay) {
                sidebarOverlay.classList.remove('active');
            }
        }
    });
}

function initializeAlerts() {
    const alerts = document.querySelectorAll('.alert');
    
    alerts.forEach(alert => {
        // Auto-ocultar alertas de éxito después de 5 segundos
        if (alert.classList.contains('alert-success')) {
            setTimeout(() => {
                alert.style.opacity = '0';
                setTimeout(() => alert.remove(), 300);
            }, 5000);
        }
        
        // Hacer alertas clicables para cerrar
        alert.style.cursor = 'pointer';
        alert.addEventListener('click', function() {
            this.style.opacity = '0';
            setTimeout(() => this.remove(), 300);
        });
    });
}

// ===== ESTILOS CSS ADICIONALES PARA JAVASCRIPT =====
const additionalStyles = `
.field-error {
    color: var(--error-color);
    font-size: 0.75rem;
    margin-top: 0.25rem;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.field-error::before {
    content: "⚠";
}

input.error,
select.error,
textarea.error {
    border-color: var(--error-color);
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.1);
}

.file-label.drag-over {
    border-color: var(--primary-color);
    background: rgba(59, 130, 246, 0.1);
    transform: scale(1.02);
}

.character-counter {
    text-align: right;
    font-size: 0.75rem;
    color: var(--text-light);
    margin-top: 0.25rem;
}

.character-counter.warning {
    color: var(--warning-color);
    font-weight: 500;
}

.results-counter {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
    text-align: center;
}

mark {
    background: #fef08a;
    color: #92400e;
    padding: 0.1em 0.2em;
    border-radius: 2px;
}

.room-card {
    transition: all 0.3s ease;
}

.room-card.hidden {
    opacity: 0;
    transform: scale(0.95);
    pointer-events: none;
}

@media (prefers-reduced-motion: reduce) {
    .room-card,
    .modal-content,
    .alert {
        animation: none;
        transition: none;
    }
}
`;

// Agregar estilos al DOM
if (!document.querySelector('#rooms-js-styles')) {
    const styleSheet = document.createElement('style');
    styleSheet.id = 'rooms-js-styles';
    styleSheet.textContent = additionalStyles;
    document.head.appendChild(styleSheet);
}