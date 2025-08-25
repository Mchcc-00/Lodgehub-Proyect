document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de creación
    const form = document.getElementById('habitaciones-form');
    const resetBtn = document.getElementById('reset-btn');
    const previewBtn = document.getElementById('preview-btn');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const habitacionPreview = document.getElementById('habitacion-preview');
    
    // Campos del formulario
    const inputs = {
        numero: document.getElementById('numero'),
        costo: document.getElementById('costo'),
        capacidad: document.getElementById('capacidad'),
        tipoHabitacion: document.getElementById('tipoHabitacion'),
        foto: document.getElementById('foto'),
        descripcion: document.getElementById('descripcion')
    };

    // Solo ejecutar si estamos en la página de crear
    if (form) {
        // Cargar tipos de habitación al inicio
        cargarTiposHabitacion();
        
        // Validaciones en tiempo real
        setupRealTimeValidation();
        
        // Event listeners
        form.addEventListener('submit', handleSubmit);
        resetBtn?.addEventListener('click', resetForm);
        previewBtn?.addEventListener('click', togglePreview);
    }

    async function cargarTiposHabitacion() {
        try {
            const response = await fetch('../controllers/HabitacionesController.php?action=obtenerTipos');
            const result = await response.json();
            
            if (result.success && inputs.tipoHabitacion) {
                const select = inputs.tipoHabitacion;
                select.innerHTML = '<option value="">Seleccionar tipo</option>';
                
                result.data.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.textContent = tipo.descripcion;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar tipos de habitación:', error);
        }
    }

    function setupRealTimeValidation() {
        // Validación de número de habitación
        if (inputs.numero) {
            inputs.numero.addEventListener('input', function() {
                const value = this.value.trim();
                
                if (value.length >= 1 && value.length <= 5) {
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

        // Validación de costo
        if (inputs.costo) {
            inputs.costo.addEventListener('input', function() {
                const value = parseFloat(this.value);
                
                if (value > 0) {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else if (this.value !== '') {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                } else {
                    this.classList.remove('valid', 'invalid');
                }
            });
        }

        // Validación de capacidad
        if (inputs.capacidad) {
            inputs.capacidad.addEventListener('input', function() {
                const value = parseInt(this.value);
                
                if (value >= 1 && value <= 20) {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else if (this.value !== '') {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                } else {
                    this.classList.remove('valid', 'invalid');
                }
            });
        }

        // Validación de campos select
        [inputs.tipoHabitacion].forEach(select => {
            if (select) {
                select.addEventListener('change', function() {
                    if (this.value) {
                        this.classList.add('valid');
                        this.classList.remove('invalid');
                    } else {
                        this.classList.add('invalid');
                        this.classList.remove('valid');
                    }
                });
            }
        });

        // Validación de descripción
        if (inputs.descripcion) {
            inputs.descripcion.addEventListener('input', function() {
                const count = this.value.length;
                
                if (count <= 500) {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                }
            });
        }
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando habitación...';
        submitBtn.disabled = true;
        
        try {
            // Crear FormData directamente del formulario
            const formData = new FormData(form);
            
            // Enviar datos al servidor
            const formAction = form.action || '../controllers/HabitacionesController.php';
            
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
                // Redireccionar inmediatamente a la lista con un parámetro de éxito
                window.location.href = 'listaHabitaciones.php?status=created';
            } else {
                showErrorMessage(result.message || 'Error al crear la habitación');
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
        const camposRequeridos = ['numero', 'costo', 'capacidad', 'tipoHabitacion'];
        camposRequeridos.forEach(campo => {
            const input = inputs[campo];
            if (input && !input.value.trim()) {
                input.classList.add('invalid');
                input.classList.remove('valid');
                isValid = false;
                errors.push(`El campo ${getFieldLabel(campo)} es requerido`);
            }
        });

        // Validaciones específicas
        if (inputs.numero && inputs.numero.value.trim() && (inputs.numero.value.length < 1 || inputs.numero.value.length > 5)) {
            errors.push('El número de habitación debe tener entre 1 y 5 caracteres');
            isValid = false;
        }

        if (inputs.costo && inputs.costo.value && parseFloat(inputs.costo.value) <= 0) {
            errors.push('El costo debe ser mayor a 0');
            isValid = false;
        }

        if (inputs.capacidad && inputs.capacidad.value) {
            const capacidad = parseInt(inputs.capacidad.value);
            if (capacidad <= 0 || capacidad > 20) {
                errors.push('La capacidad debe ser entre 1 y 20 personas');
                isValid = false;
            }
        }

        if (inputs.descripcion && inputs.descripcion.value.length > 500) {
            errors.push('La descripción no puede tener más de 500 caracteres');
            isValid = false;
        }

        if (!isValid) {
            showErrorMessage(errors.join('<br>'));
        }

        return isValid;
    }

    function getFieldLabel(fieldName) {
        const labels = {
            numero: 'Número de Habitación',
            costo: 'Costo por Noche',
            capacidad: 'Capacidad',
            tipoHabitacion: 'Tipo de Habitación',
            foto: 'Foto',
            descripcion: 'Descripción'
        };
        return labels[fieldName] || fieldName;
    }

    function resetForm() {
        if (form) {
            form.reset();
            
            // Limpiar clases de validación
            Object.values(inputs).forEach(input => {
                if (input) {
                    input.classList.remove('valid', 'invalid');
                }
            });
            
            // Ocultar vista previa y mensajes
            if (habitacionPreview) habitacionPreview.style.display = 'none';
            hideMessages();
        }
    }

    function togglePreview() {
        if (habitacionPreview) {
            if (habitacionPreview.style.display === 'none' || !habitacionPreview.style.display) {
                showPreview();
            } else {
                habitacionPreview.style.display = 'none';
            }
        }
    }

    function showPreview() {
        if (!habitacionPreview) return;
        
        const previewContent = document.getElementById('preview-content');
        if (!previewContent) return;
        
        const tipoTexto = inputs.tipoHabitacion?.options[inputs.tipoHabitacion.selectedIndex]?.text || inputs.tipoHabitacion?.value || '';
        
        const previewData = {
            'Número': inputs.numero?.value || '',
            'Tipo': tipoTexto,
            'Capacidad': inputs.capacidad?.value ? `${inputs.capacidad.value} personas` : '',
            'Costo': inputs.costo?.value ? `${parseFloat(inputs.costo.value).toLocaleString()}/noche` : '',
            'Foto': inputs.foto?.value || 'Sin foto',
            'Descripción': inputs.descripcion?.value || 'Sin descripción'
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
        habitacionPreview.style.display = 'block';
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

    // Formateo automático para algunos campos
    if (inputs.numero) {
        inputs.numero.addEventListener('input', function() {
            // Permitir solo letras y números
            this.value = this.value.replace(/[^0-9A-Za-z]/g, '').toUpperCase();
        });
    }

    if (inputs.costo) {
        inputs.costo.addEventListener('input', function() {
            // Permitir solo números y punto decimal
            this.value = this.value.replace(/[^0-9.]/g, '');
            
            // Evitar múltiples puntos decimales
            const parts = this.value.split('.');
            if (parts.length > 2) {
                this.value = parts[0] + '.' + parts.slice(1).join('');
            }
        });
    }

    if (inputs.capacidad) {
        inputs.capacidad.addEventListener('input', function() {
            // Permitir solo números
            this.value = this.value.replace(/[^0-9]/g, '');
        });
    }
});

// ========================================
// FUNCIONES GLOBALES PARA CRUD COMPLETO
// ========================================

// Función para cargar todas las habitaciones
async function cargarHabitaciones(pagina = 1, registrosPorPagina = 12, filtro = null) {
    try {
        let url = `../controllers/HabitacionesController.php?action=obtener&paginado=true&pagina=${pagina}&registros=${registrosPorPagina}`;
        if (filtro) {
            url += `&filtro=${encodeURIComponent(filtro)}`;
        }
        
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al cargar habitaciones:', error);
        throw error;
    }
}

// Función para obtener una habitación por número
async function obtenerHabitacionPorNumero(numero) {
    try {
        const url = `../controllers/HabitacionesController.php?action=obtenerPorNumero&numero=${encodeURIComponent(numero)}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al obtener habitación:', error);
        throw error;
    }
}

// Función para actualizar una habitación
async function actualizarHabitacion(numero, datos) {
    try {
        const formData = new FormData();
        formData.append('numero', numero);
        
        // Agregar solo los campos que se van a actualizar
        Object.keys(datos).forEach(key => {
            if (datos[key] !== null && datos[key] !== undefined && datos[key] !== '') {
                formData.append(key, datos[key]);
            }
        });
        
        const response = await fetch('../controllers/HabitacionesController.php?action=actualizar', {
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
        console.error('Error al actualizar habitación:', error);
        throw error;
    }
}

// Función para eliminar una habitación
async function eliminarHabitacion(numero) {
    try {
        const formData = new FormData();
        formData.append('numero', numero);
        
        const response = await fetch('../controllers/HabitacionesController.php?action=eliminar', {
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
        console.error('Error al eliminar habitación:', error);
        throw error;
    }
}

// Función para buscar habitaciones
async function buscarHabitaciones(termino) {
    try {
        const url = `../controllers/HabitacionesController.php?action=buscar&termino=${encodeURIComponent(termino)}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al buscar habitaciones:', error);
        throw error;
    }
}

// Función para poner habitación en mantenimiento
async function ponerEnMantenimiento(numero, descripcion = 'Mantenimiento programado') {
    try {
        const formData = new FormData();
        formData.append('numero', numero);
        formData.append('descripcionMantenimiento', descripcion);
        
        const response = await fetch('../controllers/HabitacionesController.php?action=ponerMantenimiento', {
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
        console.error('Error al poner en mantenimiento:', error);
        throw error;
    }
}

// Función para finalizar mantenimiento
async function finalizarMantenimiento(numero) {
    try {
        const formData = new FormData();
        formData.append('numero', numero);
        
        const response = await fetch('../controllers/HabitacionesController.php?action=finalizarMantenimiento', {
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
        console.error('Error al finalizar mantenimiento:', error);
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

// Función para formatear precio
function formatearPrecio(precio) {
    if (!precio) return '$0';
    return `${parseFloat(precio).toLocaleString('es-CO')}`;
}

// Función para formatear estado con badge
function formatearEstado(estado) {
    const badges = {
        'Disponible': 'bg-success',
        'Reservada': 'bg-warning text-dark',
        'Ocupada': 'bg-danger',
        'Mantenimiento': 'bg-secondary'
    };
    
    return `<span class="badge ${badges[estado] || 'bg-secondary'}">${estado}</span>`;
}

// Función para generar card de habitación
function generarCardHabitacion(habitacion) {
    const estadoClass = habitacion.estado.toLowerCase().replace(' ', '');
    const fotoUrl = habitacion.foto || '';
    
    return `
        <div class="habitacion-card ${estadoClass}" data-numero="${habitacion.numero}">
            <div class="habitacion-imagen">
                ${fotoUrl ? 
                    `<img src="${fotoUrl}" alt="Habitación ${habitacion.numero}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                     <div class="placeholder" style="display:none;"><i class="fas fa-bed"></i></div>` :
                    '<div class="placeholder"><i class="fas fa-bed"></i></div>'
                }
                <div class="estado-badge ${estadoClass}">${habitacion.estado}</div>
            </div>
            <div class="habitacion-info">
                <div class="habitacion-numero">
                    <i class="fas fa-door-open"></i>
                    Habitación ${habitacion.numero}
                </div>
                <div class="habitacion-tipo">${habitacion.tipo_descripcion || 'Tipo no definido'}</div>
                <div class="habitacion-details">
                    <div class="habitacion-capacidad">
                        <i class="fas fa-users"></i>
                        ${habitacion.capacidad} pers.
                    </div>
                    <div class="habitacion-precio">${formatearPrecio(habitacion.costo)}/noche</div>
                </div>
                ${habitacion.descripcion ? 
                    `<div class="habitacion-descripcion">${habitacion.descripcion}</div>` : 
                    '<div class="habitacion-descripcion">Sin descripción disponible</div>'
                }
                <div class="habitacion-acciones">
                    <button class="btn btn-primary btn-sm" onclick="editarHabitacion('${habitacion.numero}')">
                        <i class="fas fa-edit"></i> Editar
                    </button>
                    ${habitacion.estado === 'Disponible' ? 
                        `<button class="btn btn-warning btn-sm" onclick="cambiarEstado('${habitacion.numero}', 'mantenimiento')">
                            <i class="fas fa-wrench"></i> Manten.
                        </button>` : 
                        habitacion.estado === 'Mantenimiento' ?
                        `<button class="btn btn-success btn-sm" onclick="finalizarMantenimientoUI('${habitacion.numero}')">
                            <i class="fas fa-check"></i> Finalizar
                        </button>` :
                        `<button class="btn btn-info btn-sm" onclick="cambiarEstado('${habitacion.numero}', '${habitacion.estado}')">
                            <i class="fas fa-exchange-alt"></i> Estado
                        </button>`
                    }
                    <button class="btn btn-danger btn-sm" onclick="eliminarHabitacionUI('${habitacion.numero}')">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Funciones de interfaz para las acciones
async function editarHabitacion(numero) {
    try {
        const habitacion = await obtenerHabitacionPorNumero(numero);
        // Aquí abrirías un modal o redireccionar a página de edición
        console.log('Editar habitación:', habitacion);
        mostrarNotificacion('Funcionalidad de edición pendiente', 'info');
    } catch (error) {
        mostrarNotificacion('Error al cargar datos de la habitación', 'error');
    }
}

async function eliminarHabitacionUI(numero) {
    if (confirm(`¿Está seguro de que desea eliminar la habitación ${numero}?`)) {
        try {
            await eliminarHabitacion(numero);
            mostrarNotificacion('Habitación eliminada exitosamente');
            // Recargar lista
            if (window.cargarListaHabitaciones) {
                window.cargarListaHabitaciones();
            }
        } catch (error) {
            mostrarNotificacion('Error al eliminar la habitación: ' + error.message, 'error');
        }
    }
}

async function finalizarMantenimientoUI(numero) {
    if (confirm(`¿Desea finalizar el mantenimiento de la habitación ${numero}?`)) {
        try {
            await finalizarMantenimiento(numero);
            mostrarNotificacion('Mantenimiento finalizado exitosamente');
            // Recargar lista
            if (window.cargarListaHabitaciones) {
                window.cargarListaHabitaciones();
            }
        } catch (error) {
            mostrarNotificacion('Error al finalizar mantenimiento: ' + error.message, 'error');
        }
    }
}

async function cambiarEstado(numero, estadoActual) {
    // Aquí implementarías un modal o prompt para cambiar estado
    mostrarNotificacion('Funcionalidad de cambio de estado pendiente', 'info');
}