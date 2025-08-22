document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const habitacionesContainer = document.getElementById('habitaciones-container');
    const paginacionContainer = document.getElementById('paginacion-container');
    const buscarInput = document.getElementById('buscar-input');
    const buscarBtn = document.getElementById('buscar-btn');
    const refreshBtn = document.getElementById('refresh-btn');
    const nuevaHabitacionBtn = document.getElementById('nueva-habitacion-btn');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');

    // Variables de estado
    let currentPage = 1;
    const recordsPerPage = 12;
    let currentFilter = 'all';
    let currentSearch = '';

    // Modales
    const editarModalEl = document.getElementById('editarModal');
    const editarModal = new bootstrap.Modal(editarModalEl);
    const eliminarModalEl = document.getElementById('eliminarModal');
    const eliminarModal = new bootstrap.Modal(eliminarModalEl);
    const mantenimientoModalEl = document.getElementById('mantenimientoModal');
    const mantenimientoModal = new bootstrap.Modal(mantenimientoModalEl);

    let habitacionParaEliminar = null;
    let habitacionParaMantenimiento = null;

    // Funciones de utilidad
    function mostrarMensaje(mensaje, tipo = 'success') {
        if (tipo === 'success') {
            successText.textContent = mensaje;
            successMessage.style.display = 'block';
            if (errorMessage) errorMessage.style.display = 'none';
            setTimeout(() => {
                successMessage.style.display = 'none';
            }, 4000);
        } else {
            errorText.textContent = mensaje;
            errorMessage.style.display = 'block';
            if (successMessage) successMessage.style.display = 'none';
            setTimeout(() => {
                errorMessage.style.display = 'none';
            }, 5000);
        }
    }

    function formatearPrecio(precio) {
        if (!precio) return '$0';
        return `$${parseFloat(precio).toLocaleString('es-CO')}`;
    }

    function formatearEstado(estado) {
        const badges = {
            'Disponible': { class: 'disponible', icon: 'fa-check-circle' },
            'Reservada': { class: 'reservada', icon: 'fa-clock' },
            'Ocupada': { class: 'ocupada', icon: 'fa-user' },
            'Mantenimiento': { class: 'mantenimiento', icon: 'fa-wrench' }
        };
        
        const badge = badges[estado] || { class: 'mantenimiento', icon: 'fa-question' };
        return `<span class="estado-badge ${badge.class}"><i class="fas ${badge.icon}"></i> ${estado}</span>`;
    }

    function generarCardHabitacion(habitacion) {
        const estadoClass = habitacion.estado.toLowerCase().replace(' ', '');
        const fotoUrl = habitacion.foto || '';
        
        return `
            <div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="habitacion-card ${estadoClass}" data-numero="${habitacion.numero}">
                    <div class="habitacion-imagen">
                        ${fotoUrl ? 
                            `<img src="${fotoUrl}" alt="Habitación ${habitacion.numero}" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                             <div class="placeholder" style="display:none;"><i class="fas fa-bed"></i></div>` :
                            '<div class="placeholder"><i class="fas fa-bed"></i></div>'
                        }
                        ${formatearEstado(habitacion.estado)}
                    </div>
                    <div class="habitacion-info">
                        <div class="habitacion-numero">
                            <i class="fas fa-door-open"></i>
                            Habitación ${habitacion.numero}
                        </div>
                        <div class="habitacion-tipo">
                            <i class="fas fa-tag"></i>
                            ${habitacion.tipo_descripcion || 'Tipo no definido'}
                        </div>
                        <div class="habitacion-details">
                            <div class="habitacion-capacidad">
                                <i class="fas fa-users"></i>
                                ${habitacion.capacidad} ${habitacion.capacidad === 1 ? 'persona' : 'personas'}
                            </div>
                            <div class="habitacion-precio">${formatearPrecio(habitacion.costo)}/noche</div>
                        </div>
                        ${habitacion.descripcion ? 
                            `<div class="habitacion-descripcion">${habitacion.descripcion}</div>` : 
                            '<div class="habitacion-descripcion text-muted fst-italic">Sin descripción disponible</div>'
                        }
                        ${habitacion.estado === 'Mantenimiento' && habitacion.descripcionMantenimiento ?
                            `<div class="mantenimiento-info">
                                <small class="text-warning"><i class="fas fa-tools"></i> ${habitacion.descripcionMantenimiento}</small>
                            </div>` : ''
                        }
                        <div class="habitacion-acciones">
                            <button class="btn btn-primary btn-sm btn-editar" data-numero="${habitacion.numero}" title="Editar habitación">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            ${habitacion.estado === 'Disponible' || habitacion.estado === 'Reservada' ? 
                                `<button class="btn btn-warning btn-sm btn-mantenimiento" data-numero="${habitacion.numero}" title="Poner en mantenimiento">
                                    <i class="fas fa-wrench"></i> Mantenim.
                                </button>` : 
                                habitacion.estado === 'Mantenimiento' ?
                                `<button class="btn btn-success btn-sm btn-finalizar-mantenimiento" data-numero="${habitacion.numero}" title="Finalizar mantenimiento">
                                    <i class="fas fa-check"></i> Finalizar
                                </button>` : ''
                            }
                            <button class="btn btn-danger btn-sm btn-eliminar" data-numero="${habitacion.numero}" title="Eliminar habitación">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    // Función principal para cargar habitaciones
    async function cargarHabitaciones(pagina = 1, termino = '', filtro = 'all') {
        try {
            habitacionesContainer.innerHTML = '<div class="col-12 habitaciones-loading"><div class="loading-spinner"></div><br>Cargando habitaciones...</div>';
            
            currentPage = pagina;
            currentFilter = filtro;
            currentSearch = termino;
            
            let url = '';
            if (termino) {
                url = `../controllers/HabitacionesController.php?action=buscar&termino=${encodeURIComponent(termino)}`;
            } else {
                url = `../controllers/HabitacionesController.php?action=obtener&paginado=true&pagina=${pagina}&registros=${recordsPerPage}`;
                if (filtro && filtro !== 'all') {
                    url += `&filtro=${encodeURIComponent(filtro)}`;
                }
            }
            
            const response = await fetch(url);
            const result = await response.json();
            
            if (result.success) {
                if (termino) {
                    renderizarHabitaciones(result.data);
                    paginacionContainer.style.display = 'none';
                } else {
                    renderizarHabitaciones(result.data.habitaciones);
                    renderizarPaginacion(result.data);
                }
            } else {
                throw new Error(result.message || 'Error al cargar habitaciones');
            }
            
        } catch (error) {
            console.error('Error:', error);
            habitacionesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-danger text-center">
                        <i class="fas fa-exclamation-triangle"></i>
                        Error al cargar las habitaciones: ${error.message}
                    </div>
                </div>
            `;
        }
    }

    // Renderizar habitaciones en el grid
    function renderizarHabitaciones(habitaciones) {
        habitacionesContainer.innerHTML = '';
        
        if (habitaciones.length === 0) {
            habitacionesContainer.innerHTML = `
                <div class="col-12">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-info-circle"></i>
                        No se encontraron habitaciones.
                    </div>
                </div>
            `;
            return;
        }
        
        habitaciones.forEach(habitacion => {
            habitacionesContainer.innerHTML += generarCardHabitacion(habitacion);
        });
    }

    // Renderizar paginación
    function renderizarPaginacion(datosPaginacion) {
        const { total, pagina, totalPaginas } = datosPaginacion;
        const paginacionUl = document.getElementById('paginacion');
        
        if (totalPaginas <= 1) {
            paginacionContainer.style.display = 'none';
            return;
        }
        
        paginacionContainer.style.display = 'block';
        paginacionUl.innerHTML = '';
        
        // Botón anterior
        paginacionUl.innerHTML += `
            <li class="page-item ${pagina <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagina - 1}">
                    <i class="fas fa-chevron-left"></i> Anterior
                </a>
            </li>
        `;
        
        // Números de página
        let startPage = Math.max(1, pagina - 2);
        let endPage = Math.min(totalPaginas, startPage + 4);
        
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }
        
        for (let i = startPage; i <= endPage; i++) {
            paginacionUl.innerHTML += `
                <li class="page-item ${i === pagina ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>
            `;
        }
        
        // Botón siguiente
        paginacionUl.innerHTML += `
            <li class="page-item ${pagina >= totalPaginas ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagina + 1}">
                    Siguiente <i class="fas fa-chevron-right"></i>
                </a>
            </li>
        `;
    }

    // Cargar tipos de habitación para el modal de edición
    async function cargarTiposHabitacion() {
        try {
            const response = await fetch('../controllers/HabitacionesController.php?action=obtenerTipos');
            const result = await response.json();
            
            if (result.success) {
                const select = document.getElementById('edit-tipoHabitacion');
                select.innerHTML = '<option value="">Seleccionar tipo</option>';
                
                result.data.forEach(tipo => {
                    const option = document.createElement('option');
                    option.value = tipo.id;
                    option.textContent = tipo.descripcion;
                    select.appendChild(option);
                });
            }
        } catch (error) {
            console.error('Error al cargar tipos:', error);
        }
    }

    // Revisión de parámetros URL para mensajes
    function revisarEstadoURL() {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');
        
        if (status === 'created') {
            mostrarMensaje('Habitación creada exitosamente', 'success');
        }
        
        // Limpiar URL
        if (window.history.replaceState) {
            const urlLimpia = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({path: urlLimpia}, '', urlLimpia);
        }
    }

    // Event Listeners
    
    // Carga inicial
    revisarEstadoURL();
    cargarHabitaciones();
    cargarTiposHabitacion();
    
    // Asignar función global para recargar
    window.cargarListaHabitaciones = function() {
        cargarHabitaciones(currentPage, currentSearch, currentFilter);
    };

    // Búsqueda
    const ejecutarBusqueda = () => {
        const termino = buscarInput.value.trim();
        cargarHabitaciones(1, termino, 'all');
    };
    
    buscarBtn.addEventListener('click', ejecutarBusqueda);
    buscarInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            ejecutarBusqueda();
        }
    });

    // Filtros
    document.querySelectorAll('.filter-option').forEach(filterBtn => {
        filterBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const filtro = e.target.dataset.filter;
            buscarInput.value = '';
            cargarHabitaciones(1, '', filtro);
            
            // Actualizar texto del botón de filtro
            const filterToggle = document.querySelector('.filter-toggle');
            if (filtro === 'all') {
                filterToggle.innerHTML = '<i class="fas fa-filter"></i> Todos los estados';
            } else {
                filterToggle.innerHTML = `<i class="fas fa-filter"></i> ${filtro}`;
            }
        });
    });

    // Actualizar
    refreshBtn.addEventListener('click', () => {
        buscarInput.value = '';
        currentFilter = 'all';
        const filterToggle = document.querySelector('.filter-toggle');
        filterToggle.innerHTML = '<i class="fas fa-filter"></i> Todos los estados';
        cargarHabitaciones();
        mostrarMensaje('Lista actualizada', 'success');
    });

    // Paginación
    paginacionContainer.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            const pageNum = parseInt(e.target.dataset.page);
            if (!isNaN(pageNum)) {
                cargarHabitaciones(pageNum, currentSearch, currentFilter);
            }
        }
    });

    // Event delegation para acciones de habitaciones
    habitacionesContainer.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const numero = target.dataset.numero;

        if (target.classList.contains('btn-editar')) {
            // Editar habitación
            try {
                const habitacion = await obtenerHabitacionPorNumero(numero);
                
                // Llenar formulario de edición
                document.getElementById('edit-numero').value = habitacion.numero;
                document.getElementById('edit-costo').value = habitacion.costo;
                document.getElementById('edit-capacidad').value = habitacion.capacidad;
                document.getElementById('edit-tipoHabitacion').value = habitacion.tipoHabitacion;
                document.getElementById('edit-foto').value = habitacion.foto || '';
                document.getElementById('edit-descripcion').value = habitacion.descripcion || '';
                document.getElementById('edit-estado').value = habitacion.estado;
                
                editarModal.show();
            } catch (error) {
                mostrarMensaje('Error al cargar datos de la habitación', 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            // Eliminar habitación
            habitacionParaEliminar = numero;
            document.getElementById('eliminar-numero').textContent = numero;
            eliminarModal.show();
        }

        if (target.classList.contains('btn-mantenimiento')) {
            // Poner en mantenimiento
            habitacionParaMantenimiento = numero;
            document.getElementById('mantenimiento-numero').textContent = numero;
            document.getElementById('descripcion-mantenimiento').value = '';
            mantenimientoModal.show();
        }

        if (target.classList.contains('btn-finalizar-mantenimiento')) {
            // Finalizar mantenimiento
            if (confirm(`¿Desea finalizar el mantenimiento de la habitación ${numero}?`)) {
                try {
                    await finalizarMantenimiento(numero);
                    mostrarMensaje('Mantenimiento finalizado exitosamente', 'success');
                    cargarHabitaciones(currentPage, currentSearch, currentFilter);
                } catch (error) {
                    mostrarMensaje('Error al finalizar mantenimiento: ' + error.message, 'error');
                }
            }
        }
    });

    // Guardar cambios de edición
    document.getElementById('guardar-edicion').addEventListener('click', async () => {
        try {
            const numero = document.getElementById('edit-numero').value;
            const datos = {
                costo: document.getElementById('edit-costo').value,
                capacidad: document.getElementById('edit-capacidad').value,
                tipoHabitacion: document.getElementById('edit-tipoHabitacion').value,
                foto: document.getElementById('edit-foto').value,
                descripcion: document.getElementById('edit-descripcion').value,
                estado: document.getElementById('edit-estado').value
            };

            await actualizarHabitacion(numero, datos);
            editarModal.hide();
            mostrarMensaje('Habitación actualizada exitosamente', 'success');
            cargarHabitaciones(currentPage, currentSearch, currentFilter);
        } catch (error) {
            mostrarMensaje('Error al actualizar habitación: ' + error.message, 'error');
        }
    });

    // Confirmar eliminación
    document.getElementById('confirmar-eliminacion').addEventListener('click', async () => {
        if (!habitacionParaEliminar) return;

        try {
            await eliminarHabitacion(habitacionParaEliminar);
            eliminarModal.hide();
            mostrarMensaje('Habitación eliminada exitosamente', 'success');
            
            // Lógica para retroceder de página si queda vacía
            const habitacionesVisibles = habitacionesContainer.querySelectorAll('.habitacion-card').length;
            if (habitacionesVisibles === 1 && currentPage > 1) {
                currentPage--;
            }
            cargarHabitaciones(currentPage, currentSearch, currentFilter);
        } catch (error) {
            mostrarMensaje('Error al eliminar habitación: ' + error.message, 'error');
        }
    });

    // Confirmar mantenimiento
    document.getElementById('confirmar-mantenimiento').addEventListener('click', async () => {
        if (!habitacionParaMantenimiento) return;

        try {
            const descripcion = document.getElementById('descripcion-mantenimiento').value || 'Mantenimiento programado';
            await ponerEnMantenimiento(habitacionParaMantenimiento, descripcion);
            mantenimientoModal.hide();
            mostrarMensaje('Habitación puesta en mantenimiento', 'success');
            cargarHabitaciones(currentPage, currentSearch, currentFilter);
        } catch (error) {
            mostrarMensaje('Error al poner en mantenimiento: ' + error.message, 'error');
        }
    });

    // Limpiar modales al cerrar
    editarModalEl.addEventListener('hidden.bs.modal', () => {
        document.getElementById('form-editar').reset();
    });

    eliminarModalEl.addEventListener('hidden.bs.modal', () => {
        habitacionParaEliminar = null;
    });

    mantenimientoModalEl.addEventListener('hidden.bs.modal', () => {
        habitacionParaMantenimiento = null;
        document.getElementById('descripcion-mantenimiento').value = '';
    });
});