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

        this.init();
    }

    init() {
        this.cargarEventListeners();
        this.cargarReservas();
    }

    cargarEventListeners() {
        document.getElementById('buscar-btn')?.addEventListener('click', () => this.buscarReservas());
        document.getElementById('buscar-input')?.addEventListener('keyup', (e) => {
            if (e.key === 'Enter') this.buscarReservas();
        });
        document.getElementById('refresh-btn')?.addEventListener('click', () => this.cargarReservas());
        document.querySelectorAll('.filter-option').forEach(el => el.addEventListener('click', (e) => {
            e.preventDefault();
            this.aplicarFiltro(e.target.dataset.filter);
        }));
        document.getElementById('tabla-reservas')?.addEventListener('click', (e) => this.manejarAccionesTabla(e));
        document.getElementById('guardar-edicion')?.addEventListener('click', () => this.guardarEdicion());
        document.getElementById('confirmar-eliminacion')?.addEventListener('click', () => this.confirmarEliminacion());
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
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const data = await response.json();

            if (data.success) {
                this.reservas = data.data.reservas || [];
                this.renderizarTabla();
                this.renderizarPaginacion(data.data);
            } else {
                throw new Error(data.message || 'Error al cargar las reservas');
            }
        } catch (error) {
            console.error('Error en cargarReservas:', error);
            this.mostrarMensaje('error', error.message);
            this.renderizarTablaVacia(error.message);
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
                    ${this.escapeHtml(reserva.nombreHuesped)}<br>
                    <small class="text-muted">${this.escapeHtml(reserva.huespedDocumento)}</small>
                </td>
                <td>
                    Hab. ${this.escapeHtml(reserva.numeroHabitacion)}<br>
                    <small class="text-muted">${this.escapeHtml(reserva.nombreHotel)}</small>
                </td>
                <td>
                    ${this.formatearFecha(reserva.fechainicio)}<br>
                    ${this.formatearFecha(reserva.fechaFin)}
                </td>
                <td>${this.escapeHtml(reserva.totalPersonas)}</td>
                <td>$${new Intl.NumberFormat('es-CO').format(reserva.pagoFinal)}</td>
                <td><span class="badge ${this.getBadgeClass(reserva.estado)}">${this.escapeHtml(reserva.estado)}</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-outline-info action-btn" data-action="ver" data-id="${reserva.id}" title="Ver Detalles"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-outline-primary action-btn" data-action="editar" data-id="${reserva.id}" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-outline-danger action-btn" data-action="eliminar" data-id="${reserva.id}" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            </tr>
        `).join('');
    }

    renderizarTablaVacia(mensaje) {
        const tbody = document.getElementById('tabla-reservas');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="8" class="text-center p-4"><i class="fas fa-calendar-times fa-3x text-muted mb-3"></i><p>${mensaje}</p></td></tr>`;
        }
        document.getElementById('paginacion-container').style.display = 'none';
    }

    mostrarLoading() {
        const tbody = document.getElementById('tabla-reservas');
        if (tbody) {
            tbody.innerHTML = `<tr><td colspan="8" class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>`;
        }
    }

    renderizarPaginacion(data) {
        const { pagina, totalPaginas } = data;
        const paginacionUl = document.getElementById('paginacion');
        const paginacionContainer = document.getElementById('paginacion-container');

        if (!paginacionUl || !paginacionContainer || totalPaginas <= 1) {
            paginacionContainer.style.display = 'none';
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

        paginacionUl.appendChild(crearBoton('Anterior', pagina - 1, pagina <= 1));

        for (let i = 1; i <= totalPaginas; i++) {
            paginacionUl.appendChild(crearBoton(i, i, false, i === pagina));
        }

        paginacionUl.appendChild(crearBoton('Siguiente', pagina + 1, pagina >= totalPaginas));

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

    buscarReservas() {
        this.busquedaActiva = document.getElementById('buscar-input').value.trim();
        this.filtroActivo = 'all'; // Reset filter on new search
        this.cargarReservas(1);
    }

    aplicarFiltro(filtro) {
        this.filtroActivo = filtro;
        this.busquedaActiva = ''; // Reset search on filter change
        document.getElementById('buscar-input').value = '';
        this.cargarReservas(1);
    }

    async manejarAccionesTabla(e) {
        const boton = e.target.closest('.action-btn');
        if (!boton) return;

        const id = boton.dataset.id;
        const action = boton.dataset.action;

        try {
            const response = await fetch(`../controllers/reservasController.php?action=obtener&id=${id}`);
            if (!response.ok) throw new Error('No se pudo obtener la reserva.');
            const data = await response.json();
            if (!data.success) throw new Error(data.message);
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
            this.mostrarMensaje('error', error.message);
        }
    }

    abrirModalVer(reserva) {
        document.getElementById('verModalTitle').innerHTML = `<i class="fas fa-file-invoice-dollar"></i> Detalles de la Reserva #${reserva.id}`;
        const detallesBody = document.getElementById('detalles-reserva');
        detallesBody.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-hotel"></i> Hotel y Habitación</h6>
                    <p><strong>Hotel:</strong> ${this.escapeHtml(reserva.nombreHotel)}</p>
                    <p><strong>Habitación:</strong> Nº ${this.escapeHtml(reserva.numeroHabitacion)} (${this.escapeHtml(reserva.tipoHabitacion)})</p>
                    <p><strong>Estado:</strong> <span class="badge ${this.getBadgeClass(reserva.estado)}">${this.escapeHtml(reserva.estado)}</span></p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-calendar-alt"></i> Fechas y Duración</h6>
                    <p><strong>Check-in:</strong> ${this.formatearFecha(reserva.fechainicio)}</p>
                    <p><strong>Check-out:</strong> ${this.formatearFecha(reserva.fechaFin)}</p>
                    <p><strong>Noches:</strong> ${this.escapeHtml(reserva.diasEstadia)}</p>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-user-friends"></i> Huésped y Ocupantes</h6>
                    <p><strong>Huésped:</strong> ${this.escapeHtml(reserva.nombreHuesped)}</p>
                    <p><strong>Documento:</strong> ${this.escapeHtml(reserva.huespedDocumento)}</p>
                    <p><strong>Ocupantes:</strong> ${this.escapeHtml(reserva.totalPersonas)} (${reserva.cantidadAdultos}A, ${reserva.cantidadNinos}N, ${reserva.cantidadDiscapacitados}D)</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-cash-register"></i> Pago</h6>
                    <p><strong>Total Pagado:</strong> $${new Intl.NumberFormat('es-CO').format(reserva.pagoFinal)}</p>
                    <p><strong>Método:</strong> ${this.escapeHtml(reserva.metodoPago)}</p>
                </div>
            </div>
            <hr>
            <h6><i class="fas fa-info-circle"></i> Detalles Adicionales</h6>
            <p><strong>Motivo:</strong> ${this.escapeHtml(reserva.motivoReserva)}</p>
            <p><strong>Registrado por:</strong> ${this.escapeHtml(reserva.nombreUsuario)}</p>
            <p><strong>Notas:</strong> ${this.escapeHtml(reserva.informacionAdicional) || 'Ninguna'}</p>
        `;
        new bootstrap.Modal(document.getElementById('verModal')).show();
    }

    abrirModalEdicion(reserva) {
        document.getElementById('edit-id').value = reserva.id;
        document.getElementById('edit-fechainicio').value = reserva.fechainicio;
        document.getElementById('edit-fechaFin').value = reserva.fechaFin;
        document.getElementById('edit-pagoFinal').value = reserva.pagoFinal;
        document.getElementById('edit-estado').value = reserva.estado;
        document.getElementById('edit-informacionAdicional').value = reserva.informacionAdicional;
        new bootstrap.Modal(document.getElementById('editarModal')).show();
    }

    async guardarEdicion() {
        const id = document.getElementById('edit-id').value;
        const form = document.getElementById('form-editar');
        const formData = new FormData(form);
        const datos = Object.fromEntries(formData.entries());
        datos.id = id;
        datos.action = 'actualizar';

        try {
            const response = await fetch('../controllers/reservasController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });
            const data = await response.json();
            if (data.success) {
                this.mostrarMensaje('success', 'Reserva actualizada correctamente.');
                bootstrap.Modal.getInstance(document.getElementById('editarModal')).hide();
                this.cargarReservas(this.paginaActual);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.mostrarMensaje('error', error.message);
        }
    }

    abrirModalEliminar(reserva) {
        this.reservaParaEliminar = reserva.id;
        document.getElementById('eliminar-info').innerHTML = `Reserva para <strong>${this.escapeHtml(reserva.nombreHuesped)}</strong> en la habitación <strong>${this.escapeHtml(reserva.numeroHabitacion)}</strong>.`;
        document.getElementById('eliminar-id').textContent = reserva.id;
        new bootstrap.Modal(document.getElementById('eliminarModal')).show();
    }

    async confirmarEliminacion() {
        if (!this.reservaParaEliminar) return;

        try {
            const response = await fetch('../controllers/reservasController.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ action: 'eliminar', id: this.reservaParaEliminar })
            });
            const data = await response.json();
            if (data.success) {
                this.mostrarMensaje('success', 'Reserva eliminada correctamente.');
                bootstrap.Modal.getInstance(document.getElementById('eliminarModal')).hide();
                this.cargarReservas(this.paginaActual);
            } else {
                throw new Error(data.message);
            }
        } catch (error) {
            this.mostrarMensaje('error', error.message);
        } finally {
            this.reservaParaEliminar = null;
        }
    }

    // --- Funciones Utilitarias ---

    mostrarMensaje(tipo, mensaje) {
        const successDiv = document.getElementById('success-message');
        const errorDiv = document.getElementById('error-message');
        if (tipo === 'success') {
            document.getElementById('success-text').textContent = mensaje;
            successDiv.style.display = 'block';
            setTimeout(() => successDiv.style.display = 'none', 4000);
        } else {
            document.getElementById('error-text').textContent = mensaje;
            errorDiv.style.display = 'block';
            setTimeout(() => errorDiv.style.display = 'none', 6000);
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
        const [year, month, day] = fecha.split('-');
        return `${day}/${month}/${year}`;
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

document.addEventListener('DOMContentLoaded', () => new ReservasManager());
