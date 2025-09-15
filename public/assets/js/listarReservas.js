/**
 * JavaScript para gestionar la lista de Reservas
 * Archivo: listarReservas.js
 */

class ReservasManager {
    constructor() {
        this.reservas = [];
        this.filtroActivo = 'all';
        this.busquedaActiva = '';
        this.paginaActual = 1;
        this.itemsPorPagina = 10;
        this.reservaParaEliminar = null;
        this.timeoutMessages = null;

        this.init();
    }

    init() {
        this.cargarEventListeners();
        this.cargarReservas();
    }

    cargarEventListeners() {
        // Eventos de búsqueda
        const buscarBtn = document.getElementById('buscar-btn');
        const buscarInput = document.getElementById('buscar-input');
        if (buscarBtn) buscarBtn.addEventListener('click', () => this.buscarReservas());
        if (buscarInput) {
            buscarInput.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') this.buscarReservas();
            });
        }

        // Eventos de control
        const refreshBtn = document.getElementById('refresh-btn');
        if (refreshBtn) refreshBtn.addEventListener('click', () => this.cargarReservas());

        // Filtros
        document.querySelectorAll('.filter-option').forEach(el => {
            el.addEventListener('click', (e) => {
                e.preventDefault();
                this.aplicarFiltro(e.target.dataset.filter);
            });
        });

        // Acciones de tabla
        const tablaReservas = document.getElementById('tabla-reservas');
        if (tablaReservas) {
            tablaReservas.addEventListener('click', (e) => this.manejarAccionesTabla(e));
        }

        // Modal de edición
        const guardarBtn = document.getElementById('guardar-edicion');
        if (guardarBtn) guardarBtn.addEventListener('click', () => this.guardarEdicion());

        // Modal de eliminación
        const confirmarBtn = document.getElementById('confirmar-eliminacion');
        if (confirmarBtn) confirmarBtn.addEventListener('click', () => this.confirmarEliminacion());
    }

    async cargarReservas(pagina = 1) {
        this.paginaActual = pagina;
        this.mostrarLoading();

        const params = new URLSearchParams({
            action: 'listar',
            pagina: this.paginaActual,
            registros: this.itemsPorPagina,
            filtro: this.filtroActivo,
            busqueda: this.busquedaActiva
        });

        try {
            const response = await fetch(`../controllers/reservasController.php?${params.toString()}`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status} - ${response.statusText}`);
            }

            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                const text = await response.text();
                throw new Error(`Respuesta no válida del servidor. Contenido: ${text.substring(0, 200)}...`);
            }

            const data = await response.json();

            if (data.success) {
                this.reservas = data.data.reservas || [];
                this.renderizarTabla();
                this.renderizarPaginacion(data.data);
            } else {
                throw new Error(data.message || 'Error desconocido al cargar las reservas');
            }
        } catch (error) {
            console.error('Error en cargarReservas:', error);
            this.mostrarMensaje('error', `Error al cargar reservas: ${error.message}`);
            this.renderizarTablaVacia(`Error al cargar reservas: ${error.message}`);
        }
    }

    renderizarTabla() {
        const tbody = document.getElementById('tabla-reservas');
        if (!tbody) return;

        if (this.reservas.length === 0) {
            this.renderizarTablaVacia('No se encontraron reservas con los filtros actuales.');
            return;
        }

        tbody.innerHTML = this.reservas.map(reserva => `
            <tr>
                <td><strong>#${this.escapeHtml(reserva.id)}</strong></td>
                <td>
                    ${this.escapeHtml(reserva.nombreHuesped || 'N/A')}<br>
                    <small class="text-muted">${this.escapeHtml(reserva.huespedDocumento || '')}</small>
                </td>
                <td>
                    Hab. ${this.escapeHtml(reserva.numeroHabitacion || 'N/A')}<br>
                    <small class="text-muted">${this.escapeHtml(reserva.nombreHotel || '')}</small>
                </td>
                <td>
                    <small><strong>Inicio:</strong></small><br>
                    ${this.formatearFecha(reserva.fechainicio)}<br>
                    <small><strong>Fin:</strong></small><br>
                    ${this.formatearFecha(reserva.fechaFin)}
                </td>
                <td>${this.escapeHtml(reserva.totalPersonas || 0)}</td>
                <td>${this.formatearNumero(reserva.pagoFinal)}</td>
                <td><span class="badge ${this.getBadgeClass(reserva.estado)}">${this.escapeHtml(reserva.estado)}</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-info action-btn" data-action="ver" data-id="${reserva.id}" title="Ver Detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-primary action-btn" data-action="editar" data-id="${reserva.id}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger action-btn" data-action="eliminar" data-id="${reserva.id}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderizarTablaVacia(mensaje) {
        const tbody = document.getElementById('tabla-reservas');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="text-center p-4">
                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                        <p class="mb-0">${mensaje}</p>
                    </td>
                </tr>
            `;
        }
        const paginacionContainer = document.getElementById('paginacion-container');
        if (paginacionContainer) {
            paginacionContainer.style.display = 'none';
        }
    }

    mostrarLoading() {
        const tbody = document.getElementById('tabla-reservas');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" class="loading text-center p-4">
                        <i class="fas fa-spinner fa-spin fa-2x mb-2"></i>
                        <p class="mb-0">Cargando reservas...</p>
                    </td>
                </tr>
            `;
        }
    }

    renderizarPaginacion(data) {
        const { pagina, totalPaginas } = data;
        const paginacionUl = document.getElementById('paginacion');
        const paginacionContainer = document.getElementById('paginacion-container');

        if (!paginacionUl || !paginacionContainer || totalPaginas <= 1) {
            if (paginacionContainer) paginacionContainer.style.display = 'none';
            return;
        }

        paginacionContainer.style.display = 'flex';
        paginacionUl.innerHTML = '';

        const crearBoton = (texto, numPagina, deshabilitado = false, activo = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${deshabilitado ? 'disabled' : ''} ${activo ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" data-page="${numPagina}">${texto}</a>`;
            return li;
        };

        // Botón Anterior
        paginacionUl.appendChild(crearBoton('Anterior', pagina - 1, pagina <= 1));

        // Números de página
        const mostrarPaginas = this.calcularPaginasAMostrar(pagina, totalPaginas);
        mostrarPaginas.forEach(numPagina => {
            if (numPagina === '...') {
                const li = document.createElement('li');
                li.className = 'page-item disabled';
                li.innerHTML = '<span class="page-link">...</span>';
                paginacionUl.appendChild(li);
            } else {
                paginacionUl.appendChild(crearBoton(numPagina, numPagina, false, numPagina === pagina));
            }
        });

        // Botón Siguiente
        paginacionUl.appendChild(crearBoton('Siguiente', pagina + 1, pagina >= totalPaginas));

        // Event listeners para paginación
        paginacionUl.querySelectorAll('.page-link').forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();
                const pageNum = parseInt(e.target.dataset.page);
                if (!isNaN(pageNum) && pageNum !== this.paginaActual) {
                    this.cargarReservas(pageNum);
                }
            });
        });
    }

    calcularPaginasAMostrar(paginaActual, totalPaginas) {
        const paginas = [];
        
        if (totalPaginas <= 7) {
            // Mostrar todas las páginas si son pocas
            for (let i = 1; i <= totalPaginas; i++) {
                paginas.push(i);
            }
        } else {
            // Lógica más compleja para muchas páginas
            paginas.push(1);
            
            if (paginaActual > 4) {
                paginas.push('...');
            }
            
            const inicio = Math.max(2, paginaActual - 1);
            const fin = Math.min(totalPaginas - 1, paginaActual + 1);
            
            for (let i = inicio; i <= fin; i++) {
                if (!paginas.includes(i)) {
                    paginas.push(i);
                }
            }
            
            if (paginaActual < totalPaginas - 3) {
                paginas.push('...');
            }
            
            if (!paginas.includes(totalPaginas)) {
                paginas.push(totalPaginas);
            }
        }
        
        return paginas;
    }

    buscarReservas() {
        const buscarInput = document.getElementById('buscar-input');
        if (buscarInput) {
            this.busquedaActiva = buscarInput.value.trim();
        }
        this.filtroActivo = 'all'; // Reset filter on new search
        this.cargarReservas(1);
    }

    aplicarFiltro(filtro) {
        this.filtroActivo = filtro;
        this.busquedaActiva = ''; // Reset search on filter change
        const buscarInput = document.getElementById('buscar-input');
        if (buscarInput) buscarInput.value = '';
        this.cargarReservas(1);
    }

    async manejarAccionesTabla(e) {
        const boton = e.target.closest('.action-btn');
        if (!boton) return;

        const id = boton.dataset.id;
        const action = boton.dataset.action;

        // Deshabilitar botón temporalmente
        boton.disabled = true;

        try {
            const response = await fetch(`../controllers/reservasController.php?action=obtener&id=${id}`);
            
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            if (!data.success) {
                throw new Error(data.message || 'Error al obtener la reserva');
            }

            const reserva = data.data;

            switch (action) {
                case 'ver':
                    this.abrirModalVer(reserva);
                    break;
                case 'editar':
                    this.abrirModalEdicion(reserva);
                    break;
                case 'eliminar':
                    this.abrirModalEliminar(reserva);
                    break;
            }
        } catch (error) {
            console.error('Error en manejarAccionesTabla:', error);
            this.mostrarMensaje('error', `Error: ${error.message}`);
        } finally {
            boton.disabled = false;
        }
    }

    abrirModalVer(reserva) {
        const modalTitle = document.getElementById('verModalTitle');
        const detallesBody = document.getElementById('detalles-reserva');
        
        if (modalTitle) {
            modalTitle.innerHTML = `<i class="fas fa-file-invoice-dollar"></i> Detalles de la Reserva #${reserva.id}`;
        }
        
        if (detallesBody) {
            detallesBody.innerHTML = `
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-hotel"></i> Hotel y Habitación</h6>
                        <p><strong>Hotel:</strong> ${this.escapeHtml(reserva.nombreHotel || 'N/A')}</p>
                        <p><strong>Habitación:</strong> Nº ${this.escapeHtml(reserva.numeroHabitacion || 'N/A')} (${this.escapeHtml(reserva.tipoHabitacion || 'N/A')})</p>
                        <p><strong>Estado:</strong> <span class="badge ${this.getBadgeClass(reserva.estado)}">${this.escapeHtml(reserva.estado)}</span></p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-calendar-alt"></i> Fechas y Duración</h6>
                        <p><strong>Check-in:</strong> ${this.formatearFecha(reserva.fechainicio)}</p>
                        <p><strong>Check-out:</strong> ${this.formatearFecha(reserva.fechaFin)}</p>
                        <p><strong>Noches:</strong> ${this.escapeHtml(reserva.diasEstadia || 0)}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user-friends"></i> Huésped y Ocupantes</h6>
                        <p><strong>Huésped:</strong> ${this.escapeHtml(reserva.nombreHuesped || 'N/A')}</p>
                        <p><strong>Documento:</strong> ${this.escapeHtml(reserva.huespedDocumento || 'N/A')}</p>
                        <p><strong>Ocupantes:</strong> ${this.escapeHtml(reserva.totalPersonas || 0)} (${reserva.cantidadAdultos || 0}A, ${reserva.cantidadNinos || 0}N, ${reserva.cantidadDiscapacitados || 0}D)</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-cash-register"></i> Pago</h6>
                        <p><strong>Total Pagado:</strong> ${this.formatearNumero(reserva.pagoFinal)}</p>
                        <p><strong>Método:</strong> ${this.escapeHtml(reserva.metodoPago || 'N/A')}</p>
                    </div>
                </div>
                <hr>
                <h6><i class="fas fa-info-circle"></i> Detalles Adicionales</h6>
                <p><strong>Motivo:</strong> ${this.escapeHtml(reserva.motivoReserva || 'N/A')}</p>
                <p><strong>Registrado por:</strong> ${this.escapeHtml(reserva.nombreUsuario || 'N/A')}</p>
                <p><strong>Fecha de Registro:</strong> ${this.formatearFechaHora(reserva.fechaRegistro)}</p>
                <p><strong>Notas:</strong> ${this.escapeHtml(reserva.informacionAdicional) || 'Ninguna'}</p>
            `;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('verModal'));
        modal.show();
    }

    abrirModalEdicion(reserva) {
        document.getElementById('edit-id').value = reserva.id;
        document.getElementById('edit-fechainicio').value = reserva.fechainicio;
        document.getElementById('edit-fechaFin').value = reserva.fechaFin;
        document.getElementById('edit-pagoFinal').value = reserva.pagoFinal;
        document.getElementById('edit-estado').value = reserva.estado;
        document.getElementById('edit-informacionAdicional').value = reserva.informacionAdicional || '';
        
        const modal = new bootstrap.Modal(document.getElementById('editarModal'));
        modal.show();
    }

    async guardarEdicion() {
        const btnGuardar = document.getElementById('guardar-edicion');
        const originalText = btnGuardar.innerHTML;
        
        try {
            btnGuardar.disabled = true;
            btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';
            
            const id = document.getElementById('edit-id').value;
            const form = document.getElementById('form-editar');
            const formData = new FormData(form);
            const datos = Object.fromEntries(formData.entries());
            datos.action = 'actualizar'; // <-- AÑADIR ESTA LÍNEA
            datos.id = id;

            const response = await fetch('../controllers/reservasController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                // El body ahora incluye la acción
                body: JSON.stringify(datos)
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.mostrarMensaje('success', 'Reserva actualizada correctamente.');
                bootstrap.Modal.getInstance(document.getElementById('editarModal')).hide();
                this.cargarReservas(this.paginaActual);
            } else {
                throw new Error(data.message || 'Error al actualizar la reserva');
            }
        } catch (error) {
            console.error('Error en guardarEdicion:', error);
            this.mostrarMensaje('error', `Error al guardar: ${error.message}`);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = originalText;
        }
    }

    abrirModalEliminar(reserva) {
        this.reservaParaEliminar = reserva.id;
        const eliminarInfo = document.getElementById('eliminar-info');
        const eliminaId = document.getElementById('eliminar-id');
        
        if (eliminarInfo) {
            eliminarInfo.innerHTML = `Reserva para <strong>${this.escapeHtml(reserva.nombreHuesped || 'N/A')}</strong> en la habitación <strong>${this.escapeHtml(reserva.numeroHabitacion || 'N/A')}</strong>.`;
        }
        
        if (eliminaId) {
            eliminaId.textContent = reserva.id;
        }
        
        const modal = new bootstrap.Modal(document.getElementById('eliminarModal'));
        modal.show();
    }

    async confirmarEliminacion() {
        if (!this.reservaParaEliminar) return;

        const btnConfirmar = document.getElementById('confirmar-eliminacion');
        const originalText = btnConfirmar.innerHTML;

        try {
            btnConfirmar.disabled = true;
            btnConfirmar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

            const response = await fetch('../controllers/reservasController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ 
                    action: 'eliminar', 
                    id: this.reservaParaEliminar 
                })
            });

            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const data = await response.json();
            
            if (data.success) {
                this.mostrarMensaje('success', 'Reserva eliminada correctamente.');
                bootstrap.Modal.getInstance(document.getElementById('eliminarModal')).hide();
                this.cargarReservas(this.paginaActual);
            } else {
                throw new Error(data.message || 'Error al eliminar la reserva');
            }
        } catch (error) {
            console.error('Error en confirmarEliminacion:', error);
            this.mostrarMensaje('error', `Error al eliminar: ${error.message}`);
        } finally {
            btnConfirmar.disabled = false;
            btnConfirmar.innerHTML = originalText;
            this.reservaParaEliminar = null;
        }
    }

    // --- Funciones Utilitarias ---

    mostrarMensaje(tipo, mensaje) {
        if (this.timeoutMessages) {
            clearTimeout(this.timeoutMessages);
        }

        const successDiv = document.getElementById('success-message');
        const errorDiv = document.getElementById('error-message');
        
        if (tipo === 'success' && successDiv) {
            const successText = document.getElementById('success-text');
            if (successText) successText.textContent = mensaje;
            successDiv.style.display = 'block';
            if (errorDiv) errorDiv.style.display = 'none';
            
            this.timeoutMessages = setTimeout(() => {
                successDiv.style.display = 'none';
            }, 4000);
        } else if (errorDiv) {
            const errorText = document.getElementById('error-text');
            if (errorText) errorText.textContent = mensaje;
            errorDiv.style.display = 'block';
            if (successDiv) successDiv.style.display = 'none';
            
            this.timeoutMessages = setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 6000);
        }
    }

    escapeHtml(text) {
        if (text === null || text === undefined) return '';
        return String(text).replace(/[&<>"']/g, match => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[match]));
    }

    formatearFecha(fecha) {
        if (!fecha) return 'N/A';
        try {
            const [year, month, day] = fecha.split('-');
            return `${day}/${month}/${year}`;
        } catch (error) {
            return 'Fecha inválida';
        }
    }

    formatearFechaHora(fechaHora) {
        if (!fechaHora) return 'N/A';
        try {
            const fecha = new Date(fechaHora);
            return fecha.toLocaleString('es-CO');
        } catch (error) {
            return 'Fecha inválida';
        }
    }

    formatearNumero(numero) {
        if (!numero && numero !== 0) return '0';
        return new Intl.NumberFormat('es-CO').format(numero);
    }

    getBadgeClass(estado) {
        const clases = {
            'Activa': 'bg-success',
            'Pendiente': 'bg-warning text-dark',
            'Finalizada': 'bg-secondary',
            'Cancelada': 'bg-danger'
        };
        return clases[estado] || 'bg-light text-dark';
    }
}

// Inicializar el manager cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', () => {
    try {
        new ReservasManager();
    } catch (error) {
        console.error('Error al inicializar ReservasManager:', error);
    }
});