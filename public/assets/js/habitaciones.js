/**
 * JavaScript para la gestión de habitaciones
 */

document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const filtroHotel = document.getElementById('filtro-hotel');
    const filtroEstado = document.getElementById('filtro-estado');
    const filtroNumero = document.getElementById('filtro-numero');
    const btnBuscar = document.getElementById('btn-buscar');
    const habitacionesGrid = document.getElementById('habitaciones-grid');
    const loading = document.getElementById('loading');
    
    // Mensajes
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');
    
    // Modales
    const modalDetalles = new bootstrap.Modal(document.getElementById('modalDetalles'));
    const modalEliminar = new bootstrap.Modal(document.getElementById('modalEliminar'));
    
    // Variables globales
    let habitacionAEliminar = null;
    
    // ============================================
    // EVENTOS
    // ============================================
    
    // Búsqueda
    if (btnBuscar) {
        btnBuscar.addEventListener('click', buscarHabitaciones);
    }
    
    // Búsqueda en tiempo real
    if (filtroNumero) {
        filtroNumero.addEventListener('input', debounce(buscarHabitaciones, 300));
    }
    
    // Filtros
    if (filtroHotel) {
        filtroHotel.addEventListener('change', buscarHabitaciones);
    }
    
    if (filtroEstado) {
        filtroEstado.addEventListener('change', buscarHabitaciones);
    }
    
    // Confirmación de eliminación
    const btnConfirmarEliminar = document.getElementById('btn-confirmar-eliminar');
    if (btnConfirmarEliminar) {
        btnConfirmarEliminar.addEventListener('click', confirmarEliminacion);
    }
    
    // ============================================
    // FUNCIONES PRINCIPALES
    // ============================================
    
    /**
     * Buscar habitaciones con filtros
     */
    function buscarHabitaciones() {
        const filtros = {
            hotel: filtroHotel ? filtroHotel.value : '',
            estado: filtroEstado ? filtroEstado.value : '',
            numero: filtroNumero ? filtroNumero.value : ''
        };
        
        // Mostrar loading
        mostrarLoading(true);
        
        // Construir URL con parámetros
        const params = new URLSearchParams();
        Object.keys(filtros).forEach(key => {
            if (filtros[key]) {
                params.append(key, filtros[key]);
            }
        });
        
        const url = `?action=buscar&${params.toString()}`;
        
        fetch(url)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderizarHabitaciones(data.data);
                } else {
                    mostrarError('Error al buscar habitaciones');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error de conexión');
            })
            .finally(() => {
                mostrarLoading(false);
            });
    }
    
    /**
     * Renderizar habitaciones en el grid
     */
    function renderizarHabitaciones(habitaciones) {
        if (!habitacionesGrid) return;
        
        if (habitaciones.length === 0) {
            habitacionesGrid.innerHTML = `
                <div class="no-habitaciones">
                    <i class="fas fa-bed fa-5x text-muted"></i>
                    <h3 class="mt-3 text-muted">No hay habitaciones</h3>
                    <p class="text-muted">No se encontraron habitaciones con los filtros aplicados</p>
                    <a href="?action=crear" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Crear Habitación
                    </a>
                </div>
            `;
            return;
        }
        
        const habitacionesHTML = habitaciones.map(habitacion => `
            <div class="habitacion-card ${habitacion.estado.toLowerCase()}" data-id="${habitacion.id}">
                <div class="habitacion-image">
                    ${habitacion.foto ? 
                        `<img src="${escapeHtml(habitacion.foto)}" alt="Habitación ${escapeHtml(habitacion.numero)}">` :
                        `<div class="no-image">
                            <i class="fas fa-bed"></i>
                            <span>Sin imagen</span>
                        </div>`
                    }
                    <div class="habitacion-estado estado-${habitacion.estado.toLowerCase()}">
                        ${habitacion.estado}
                    </div>
                </div>
                
                <div class="habitacion-content">
                    <div class="habitacion-header">
                        <h3 class="habitacion-numero">
                            <i class="fas fa-door-open"></i>
                            Habitación ${escapeHtml(habitacion.numero)}
                        </h3>
                        <span class="habitacion-hotel">
                            ${escapeHtml(habitacion.hotel_nombre)}
                        </span>
                    </div>
                    
                    <div class="habitacion-info">
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span>${escapeHtml(habitacion.tipo_descripcion)}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-users"></i>
                            <span>${habitacion.capacidad} personas</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-dollar-sign"></i>
                            <span>$${formatearNumero(habitacion.costo)}/noche</span>
                        </div>
                    </div>
                    
                    ${habitacion.descripcion ? `
                        <div class="habitacion-descripcion">
                            <p>${escapeHtml(habitacion.descripcion.substring(0, 100))}${habitacion.descripcion.length > 100 ? '...' : ''}</p>
                        </div>
                    ` : ''}
                    
                    ${habitacion.estado === 'Mantenimiento' && habitacion.descripcionMantenimiento ? `
                        <div class="habitacion-mantenimiento">
                            <i class="fas fa-tools"></i>
                            <small>${escapeHtml(habitacion.descripcionMantenimiento)}</small>
                        </div>
                    ` : ''}
                </div>
                
                <div class="habitacion-actions">
                    <button type="button" class="btn btn-info btn-sm" onclick="verDetalles(${habitacion.id})">
                        <i class="fas fa-eye"></i>
                    </button>
                    <a href="?action=editar&id=${habitacion.id}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button type="button" class="btn btn-danger btn-sm" onclick="eliminarHabitacion(${habitacion.id})">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
        
        habitacionesGrid.innerHTML = habitacionesHTML;
        
        // Aplicar animación escalonada
        const tarjetas = habitacionesGrid.querySelectorAll('.habitacion-card');
        tarjetas.forEach((tarjeta, index) => {
            tarjeta.style.animationDelay = `${index * 0.1}s`;
        });
    }
    
    /**
     * Ver detalles de una habitación
     */
    window.verDetalles = function(id) {
        fetch(`?action=obtener&id=${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarDetalles(data.data);
                } else {
                    mostrarError('Error al cargar los detalles');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                mostrarError('Error de conexión');
            });
    };
    
    /**
     * Mostrar detalles en el modal
     */
    function mostrarDetalles(habitacion) {
        const detallesContent = document.getElementById('detalles-content');
        
        const estadoClass = habitacion.estado.toLowerCase();
        const estadoColor = {
            'disponible': 'success',
            'reservada': 'warning',
            'ocupada': 'info',
            'mantenimiento': 'danger'
        }[estadoClass] || 'secondary';
        
        detallesContent.innerHTML = `
            <div class="habitacion-detail">
                <div class="detail-image">
                    ${habitacion.foto ? 
                        `<img src="${escapeHtml(habitacion.foto)}" alt="Habitación ${escapeHtml(habitacion.numero)}">` :
                        `<div class="no-image text-center p-5 bg-light rounded">
                            <i class="fas fa-bed fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Sin imagen disponible</p>
                        </div>`
                    }
                </div>
                
                <div class="detail-info">
                    <div class="info-group">
                        <h6>Información General</h6>
                        <p><strong>Número:</strong> ${escapeHtml(habitacion.numero)}</p>
                        <p><strong>Hotel:</strong> ${escapeHtml(habitacion.hotel_nombre)}</p>
                        <p><strong>Tipo:</strong> ${escapeHtml(habitacion.tipo_descripcion)}</p>
                        <p><strong>Estado:</strong> 
                            <span class="badge bg-${estadoColor}">${habitacion.estado}</span>
                        </p>
                    </div>
                    
                    <div class="info-group">
                        <h6>Capacidad y Precio</h6>
                        <p><strong>Capacidad:</strong> ${habitacion.capacidad} personas</p>
                        <p><strong>Costo por noche:</strong> 
                            <span class="precio-destacado">$${formatearNumero(habitacion.costo)}</span>
                        </p>
                    </div>
                    
                    ${habitacion.descripcion ? `
                        <div class="info-group">
                            <h6>Descripción</h6>
                            <p>${escapeHtml(habitacion.descripcion)}</p>
                        </div>
                    ` : ''}
                    
                    ${habitacion.estado === 'Mantenimiento' && habitacion.descripcionMantenimiento ? `
                        <div class="info-group">
                            <h6>Información de Mantenimiento</h6>
                            <div class="alert alert-warning">
                                <i class="fas fa-tools me-2"></i>
                                ${escapeHtml(habitacion.descripcionMantenimiento)}
                            </div>
                        </div>
                    ` : ''}
                    
                    <div class="info-group">
                        <h6>Estado de Mantenimiento</h6>
                        <p><strong>Estado:</strong> 
                            <span class="badge ${habitacion.estadoMantenimiento === 'Activo' ? 'bg-success' : 'bg-secondary'}">
                                ${habitacion.estadoMantenimiento}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        `;
        
        modalDetalles.show();
    }
    
    /**
     * Preparar eliminación de habitación
     */
    window.eliminarHabitacion = function(id) {
        habitacionAEliminar = id;
        modalEliminar.show();
    };
    
    /**
     * Confirmar eliminación
     */
    function confirmarEliminacion() {
        if (!habitacionAEliminar) return;
        
        const formData = new FormData();
        const btnConfirmar = document.getElementById('btn-confirmar-eliminar');
        
        // Deshabilitar botón
        btnConfirmar.disabled = true;
        btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';
        
        fetch(`?action=eliminar&id=${habitacionAEliminar}`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                mostrarExito(data.message);
                modalEliminar.hide();
                
                // Remover la tarjeta del DOM con animación
                const tarjeta = document.querySelector(`[data-id="${habitacionAEliminar}"]`);
                if (tarjeta) {
                    tarjeta.style.animation = 'fadeOut 0.3s ease forwards';
                    setTimeout(() => {
                        tarjeta.remove();
                        // Verificar si no quedan habitaciones
                        if (habitacionesGrid.children.length === 0) {
                            buscarHabitaciones();
                        }
                    }, 300);
                }
            } else {
                mostrarError(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            mostrarError('Error de conexión');
        })
        .finally(() => {
            // Restaurar botón
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = '<i class="fas fa-trash"></i> Eliminar';
            habitacionAEliminar = null;
        });
    }
    
    // ============================================
    // FUNCIONES AUXILIARES
    // ============================================
    
    /**
     * Mostrar/ocultar loading
     */
    function mostrarLoading(mostrar) {
        if (loading) {
            loading.style.display = mostrar ? 'block' : 'none';
        }
        if (habitacionesGrid) {
            habitacionesGrid.classList.toggle('loading', mostrar);
        }
    }
    
    /**
     * Mostrar mensaje de éxito
     */
    function mostrarExito(mensaje) {
        if (successMessage && successText) {
            successText.textContent = mensaje;
            successMessage.style.display = 'block';
            errorMessage.style.display = 'none';
            
            // Auto ocultar después de 5 segundos
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 5000);
            
            // Scroll al mensaje
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
            
            // Auto ocultar después de 7 segundos
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 7000);
            
            // Scroll al mensaje
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
    
    /**
     * Escape HTML para prevenir XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    /**
     * Formatear número con separadores de miles
     */
    function formatearNumero(numero) {
        return new Intl.NumberFormat('es-CO').format(numero);
    }
    
    /**
     * Debounce para búsqueda en tiempo real
     */
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
    
    // ============================================
    // ANIMACIONES CSS ADICIONALES
    // ============================================
    
    // Añadir estilos de animación para fadeOut
    const style = document.createElement('style');
    style.textContent = `
        @keyframes fadeOut {
            from { opacity: 1; transform: scale(1); }
            to { opacity: 0; transform: scale(0.8); }
        }
        
        .habitacion-card.removing {
            animation: fadeOut 0.3s ease forwards;
        }
    `;
    document.head.appendChild(style);
});