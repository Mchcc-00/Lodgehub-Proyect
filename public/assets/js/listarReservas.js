document.addEventListener('DOMContentLoaded', () => {

    // --- CONSTANTES Y VARIABLES ---
    const API_URL = '/lodgehub/app/controllers/reservasController.php';
    const tablaReservas = document.getElementById('tabla-reservas');
    const paginacionContainer = document.getElementById('paginacion-container');
    const paginacionUl = document.getElementById('paginacion');
    const buscarInput = document.getElementById('buscar-input');
    const buscarBtn = document.getElementById('buscar-btn');
    const refreshBtn = document.getElementById('refresh-btn');
    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');

    // Modales
    const editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
    const verModal = new bootstrap.Modal(document.getElementById('verModal'));
    const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));

    let currentPage = 1;
    let currentFilters = { estado: 'all' };
    let itemParaEliminar = null;

    // --- FUNCIONES ---

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        setTimeout(() => { elem.style.display = 'none'; }, 4000);
    };

    const cargarReservas = async (pagina = 1, terminoBusqueda = '') => {
        if (!tablaReservas) return; // Salir si la tabla no existe (cuando no se ha seleccionado hotel)
        tablaReservas.innerHTML = `<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>`;
        currentPage = pagina;

        try {
            let url = `${API_URL}?action=obtener&pagina=${pagina}&estado=${currentFilters.estado}`;
            if (terminoBusqueda) url += `&busqueda=${encodeURIComponent(terminoBusqueda)}`;

            const response = await fetch(url);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const resultado = await response.json();

            if (resultado.success) {
                const data = resultado.data.reservas;
                renderizarTabla(data);
                renderizarPaginacion(resultado.data);
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            tablaReservas.innerHTML = `<tr><td colspan="9" class="text-danger text-center">❌ Error: ${error.message}</td></tr>`;
        }
    };

    const renderizarTabla = (data) => {
        tablaReservas.innerHTML = '';
        if (data.length === 0) {
            tablaReservas.innerHTML = `<tr><td colspan="9" class="text-center">No se encontraron reservas.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>#${item.id}</strong></td>
                <td><i class="fas fa-user"></i> ${item.nombreHuesped || 'N/A'}</td>
                <td><i class="fas fa-door-open"></i> ${item.numeroHabitacion || 'N/A'}</td>
                <td>${new Date(item.fechainicio + 'T00:00:00').toLocaleDateString()}</td>
                <td>${new Date(item.fechaFin + 'T00:00:00').toLocaleDateString()}</td>
                <td>${formatearEstado(item.estado)}</td>
                <td>$${parseFloat(item.pagoFinal || 0).toLocaleString('es-CO')}</td>
                <td>${new Date(item.fechaRegistro).toLocaleDateString()}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btn-ver" data-id="${item.id}" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-warning btn-editar" data-id="${item.id}" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${item.id}" data-info="Huésped: ${item.nombreHuesped}, Hab. ${item.numeroHabitacion}" title="Eliminar/Cancelar"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            tablaReservas.appendChild(tr);
        });
    };

    const renderizarPaginacion = ({ totalPaginas, pagina }) => {
        paginacionUl.innerHTML = '';
        if (totalPaginas <= 1) {
            paginacionContainer.style.display = 'none';
            return;
        }
        paginacionContainer.style.display = 'block';

        for (let i = 1; i <= totalPaginas; i++) {
            paginacionUl.innerHTML += `<li class="page-item ${i === pagina ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
    };

    const renderizarDetalles = (item) => {
        const container = document.getElementById('detalles-reserva');
        container.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <p><strong>ID Reserva:</strong> #${item.id}</p>
                    <p><strong>Huésped:</strong> ${item.nombreHuesped}</p>
                    <p><strong>Contacto Huésped:</strong> ${item.correoHuesped} / ${item.telefonoHuesped}</p>
                    <p><strong>Habitación:</strong> ${item.numeroHabitacion}</p>
                    <p><strong>Estado:</strong> ${formatearEstado(item.estado)}</p>
                </div>
                <div class="col-md-6">
                    <p><strong>Check-in:</strong> ${new Date(item.fechainicio + 'T00:00:00').toLocaleDateString()}</p>
                    <p><strong>Check-out:</strong> ${new Date(item.fechaFin + 'T00:00:00').toLocaleDateString()}</p>
                    <p><strong>Pago Final:</strong> $${parseFloat(item.pagoFinal || 0).toLocaleString('es-CO')}</p>
                    <p><strong>Registrado por:</strong> ${item.nombreUsuario} ${item.apellidoUsuario}</p>
                    <p><strong>Fecha Registro:</strong> ${new Date(item.fechaRegistro).toLocaleString()}</p>
                </div>
            </div>
            <hr>
            <p><strong>Información Adicional:</strong><br>${item.informacionAdicional || 'Sin información adicional.'}</p>
        `;
    };

    // --- MANEJO DE EVENTOS ---

    cargarReservas();

    buscarBtn.addEventListener('click', () => cargarReservas(1, buscarInput.value.trim()));
    buscarInput.addEventListener('keyup', (e) => e.key === 'Enter' && cargarReservas(1, buscarInput.value.trim()));
    refreshBtn.addEventListener('click', () => {
        buscarInput.value = '';
        currentFilters = { estado: 'all' };
        cargarReservas();
    });

    document.querySelectorAll('.filter-option').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const group = e.target.dataset.filterGroup;
            const value = e.target.dataset.filterValue;
            currentFilters[group] = value;
            cargarReservas(1);
        });
    });

    paginacionUl.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.dataset.page) {
            cargarReservas(parseInt(e.target.dataset.page, 10), buscarInput.value.trim());
        }
    });

    tablaReservas.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;
        const id = target.dataset.id;

        if (target.classList.contains('btn-ver') || target.classList.contains('btn-editar')) {
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
                const resultado = await response.json();
                if (!resultado.success) throw new Error(resultado.message);
                
                if (target.classList.contains('btn-ver')) {
                    renderizarDetalles(resultado.data);
                    verModal.show();
                } else { // Editar
                    const form = document.getElementById('form-editar-reserva');
                    form.querySelector('#edit-id').value = resultado.data.id;
                    form.querySelector('#edit-fechainicio').value = resultado.data.fechainicio;
                    form.querySelector('#edit-fechaFin').value = resultado.data.fechaFin;
                    form.querySelector('#edit-pagoFinal').value = resultado.data.pagoFinal;
                    form.querySelector('#edit-estado').value = resultado.data.estado;
                    form.querySelector('#edit-informacionAdicional').value = resultado.data.informacionAdicional || '';
                    editarModal.show();
                }
            } catch (error) {
                mostrarMensaje(error.message, 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            itemParaEliminar = id;
            document.getElementById('eliminar-id').textContent = id;
            document.getElementById('eliminar-info-reserva').textContent = target.dataset.info;
            eliminarModal.show();
        }
    });

    document.getElementById('guardar-edicion-reserva').addEventListener('click', async () => {
        const form = document.getElementById('form-editar-reserva');
        const formData = new FormData(form);
        const id = form.querySelector('#edit-id').value;
        formData.append('id', id);

        try {
            const response = await fetch(`${API_URL}?action=actualizar`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);

            mostrarMensaje('Reserva actualizada correctamente.', 'success');
            editarModal.hide();
            cargarReservas(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        }
    });

    document.getElementById('confirmar-eliminacion-reserva').addEventListener('click', async () => {
        if (!itemParaEliminar) return;
        const formData = new FormData();
        formData.append('id', itemParaEliminar);

        try {
            const response = await fetch(`${API_URL}?action=eliminar`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);

            mostrarMensaje('Reserva eliminada correctamente.', 'success');
            eliminarModal.hide();
            cargarReservas(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            itemParaEliminar = null;
        }
    });

    // --- FUNCIONES UTILITARIAS ---

    function formatearEstado(estado) {
        const badges = {
            'Activa': 'bg-success',
            'Pendiente': 'bg-warning text-dark',
            'Finalizada': 'bg-info text-dark',
            'Cancelada': 'bg-danger'
        };
        return `<span class="badge ${badges[estado] || 'bg-secondary'}">${estado}</span>`;
    }

});
