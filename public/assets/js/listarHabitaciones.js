document.addEventListener('DOMContentLoaded', () => {
    const API_URL = '../controllers/habitacionesController.php';
    const grid = document.getElementById('habitaciones-grid');
    const loadingIndicator = document.getElementById('loading');
    const paginationContainer = document.getElementById('paginacion');
    const buscarInput = document.getElementById('buscar-input');
    const filtroEstado = document.getElementById('filtro-estado');
    const filtroTipo = document.getElementById('filtro-tipo');

    // Modales
    const editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
    const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));
    const verModal = new bootstrap.Modal(document.getElementById('verModal'));

    let currentPage = 1;
    let habitacionParaEliminar = null;

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const successMessage = document.getElementById('success-message');
        const errorMessage = document.getElementById('error-message');
        const successText = document.getElementById('success-text');
        const errorText = document.getElementById('error-text');

        if (tipo === 'success') {
            successText.textContent = mensaje;
            successMessage.style.display = 'flex';
            setTimeout(() => { successMessage.style.display = 'none'; }, 4000);
        } else {
            errorText.textContent = mensaje;
            errorMessage.style.display = 'flex';
            setTimeout(() => { errorMessage.style.display = 'none'; }, 5000);
        }
    };

    const cargarHabitaciones = async (pagina = 1) => {
        currentPage = pagina;
        loadingIndicator.style.display = 'block';
        grid.innerHTML = '';
        paginationContainer.parentElement.style.display = 'none';

        const busqueda = buscarInput.value.trim();
        const estado = filtroEstado.value;
        const tipo = filtroTipo.value;

        try {
            const params = new URLSearchParams({
                action: 'obtener',
                pagina: currentPage,
                busqueda: busqueda,
                estado: estado,
                tipo: tipo
            });

            const response = await fetch(`${API_URL}?${params.toString()}`);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);

            const resultado = await response.json();
            if (resultado.success) {
                renderizarHabitaciones(resultado.data.habitaciones);
                renderizarPaginacion(resultado.data);
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            grid.innerHTML = `<div class="no-habitaciones"><i class="fas fa-exclamation-triangle fa-3x"></i><h3>Error al cargar</h3><p>${error.message}</p></div>`;
        } finally {
            loadingIndicator.style.display = 'none';
        }
    };

    const renderizarHabitaciones = (habitaciones) => {
        if (habitaciones.length === 0) {
            grid.innerHTML = '<div class="no-habitaciones"><i class="fas fa-door-closed fa-3x"></i><h3>No se encontraron habitaciones</h3><p>Intenta ajustar los filtros o crea una nueva habitación.</p></div>';
            return;
        }

        grid.innerHTML = habitaciones.map(hab => `
            <div class="habitacion-card" data-estado="${hab.estado}">
                <div class="habitacion-image">
                    ${hab.foto ? `<img src="${hab.foto}" alt="Habitación ${hab.numero}" onerror="this.src='../../public/assets/img/default_room.png';">` : '<div class="no-image"><i class="fas fa-image"></i><span>Sin foto</span></div>'}
                    <div class="habitacion-estado">
                        <span class="badge bg-${getEstadoColor(hab.estado)}">${hab.estado}</span>
                    </div>
                </div>
                <div class="habitacion-content">
                    <div class="habitacion-header">
                        <h5 class="habitacion-numero"><i class="fas fa-door-open"></i> Habitación ${hab.numero}</h5>
                    </div>
                    <div class="habitacion-info">
                        <div class="info-item">
                            <i class="fas fa-tag"></i>
                            <span>${hab.tipo_descripcion}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-users"></i>
                            <span>Capacidad: ${hab.capacidad} personas</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-dollar-sign"></i>
                            <span>${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(hab.costo)} / noche</span>
                        </div>
                    </div>
                </div>
                <div class="habitacion-actions">
                    <button class="btn btn-sm btn-info btn-ver" data-id="${hab.id}" title="Ver Detalles"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-sm btn-warning btn-editar" data-id="${hab.id}" title="Editar"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger btn-eliminar" data-id="${hab.id}" data-numero="${hab.numero}" title="Eliminar"><i class="fas fa-trash"></i></button>
                </div>
            </div>
        `).join('');
    };

    const renderizarDetallesHabitacion = (hab) => {
        const container = document.getElementById('detalles-habitacion');
        const fotoHtml = hab.foto 
            ? `<img src="${hab.foto}" alt="Habitación ${hab.numero}" class="img-fluid rounded mb-3" onerror="this.src='../../public/assets/img/default_room.png';">`
            : '<p class="text-muted text-center p-5 border rounded bg-light">No hay foto disponible.</p>';

        let detallesEstado = '';
        if (hab.estado === 'Ocupada' && hab.id_reserva) {
            detallesEstado = `
                <div class="alert alert-warning mt-3">
                    <h6 class="alert-heading"><i class="fas fa-user-clock"></i> Detalles de la Ocupación</h6>
                    <p class="mb-1"><strong>Huésped:</strong> ${hab.reserva_huesped || 'No especificado'}</p>
                    <p class="mb-0"><strong>Check-out:</strong> ${new Date(hab.reserva_fecha_fin + 'T00:00:00').toLocaleDateString()}</p>
                </div>`;
        } else if (hab.estado === 'Mantenimiento' && hab.id_mantenimiento) {
            detallesEstado = `
                <div class="alert alert-danger mt-3">
                    <h6 class="alert-heading"><i class="fas fa-tools"></i> Detalles del Mantenimiento</h6>
                    <p class="mb-0"><strong>Tipo:</strong> ${hab.mantenimiento_tipo} - ${hab.mantenimiento_descripcion || 'Sin descripción.'}</p>
                </div>`;
        }
        container.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    ${fotoHtml}
                </div>
                <div class="col-md-7">
                    <h4>Habitación N° ${hab.numero}</h4>
                    <p class="mb-2"><strong>Tipo:</strong> ${hab.tipo_descripcion}</p>
                    <p class="mb-2"><strong>Estado:</strong> <span class="badge bg-${getEstadoColor(hab.estado)}">${hab.estado}</span></p>
                    <hr>
                    <p class="mb-2"><i class="fas fa-users me-2 text-primary"></i><strong>Capacidad:</strong> ${hab.capacidad} personas</p>
                    <p class="mb-2"><i class="fas fa-dollar-sign me-2 text-success"></i><strong>Costo:</strong> ${new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(hab.costo)} / noche</p>
                    <hr>
                    <h5><i class="fas fa-info-circle me-2 text-info"></i>Descripción</h5>
                    <p>${hab.descripcion || '<em>Sin descripción.</em>'}</p>
                    ${detallesEstado}
                </div>
            </div>
        `;
    };

    const renderizarPaginacion = ({ pagina, totalPaginas }) => {
        if (totalPaginas <= 1) {
            paginationContainer.parentElement.style.display = 'none';
            return;
        }
        paginationContainer.parentElement.style.display = 'block';
        paginationContainer.innerHTML = '';

        // Botón "Anterior"
        paginationContainer.innerHTML += `<li class="page-item ${pagina <= 1 ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${pagina - 1}">Anterior</a></li>`;

        // Números de página
        for (let i = 1; i <= totalPaginas; i++) {
            paginationContainer.innerHTML += `<li class="page-item ${i === pagina ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }

        // Botón "Siguiente"
        paginationContainer.innerHTML += `<li class="page-item ${pagina >= totalPaginas ? 'disabled' : ''}"><a class="page-link" href="#" data-page="${pagina + 1}">Siguiente</a></li>`;
    };

    const getEstadoColor = (estado) => {
        const colores = {
            'Disponible': 'success',
            'Ocupada': 'warning',
            'Reservada': 'info',
            'Mantenimiento': 'danger'
        };
        return colores[estado] || 'secondary';
    };

    // Event Listeners
    buscarInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') cargarHabitaciones(1);
    });
    filtroEstado.addEventListener('change', () => cargarHabitaciones(1));
    filtroTipo.addEventListener('change', () => cargarHabitaciones(1));

    paginationContainer.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            const pageNum = parseInt(e.target.dataset.page, 10);
            if (!isNaN(pageNum)) cargarHabitaciones(pageNum);
        }
    });

    grid.addEventListener('click', (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const id = target.dataset.id;
        if (target.classList.contains('btn-ver')) {
            abrirModalVer(id);
        } else if (target.classList.contains('btn-editar')) {
            abrirModalEdicion(id);
        } else if (target.classList.contains('btn-eliminar')) {
            abrirModalEliminar(id, target.dataset.numero);
        }
    });

    const abrirModalEdicion = async (id) => {
        try {
            const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);

            const hab = resultado.data;
            document.getElementById('edit-id').value = hab.id;
            document.getElementById('edit-numero').value = hab.numero;
            document.getElementById('edit-tipoHabitacion').value = hab.tipoHabitacion;
            document.getElementById('edit-costo').value = hab.costo;
            document.getElementById('edit-capacidad').value = hab.capacidad;
            document.getElementById('edit-descripcion').value = hab.descripcion || '';
            document.getElementById('edit-estado').value = hab.estado;
            
            const previewImg = document.getElementById('edit-foto-preview');
            const previewContainer = document.getElementById('edit-foto-preview-container');
            if (hab.foto) {
                previewImg.src = hab.foto;
                previewContainer.style.display = 'block';
            } else {
                previewContainer.style.display = 'none';
            }

            editarModal.show();
        } catch (error) {
            mostrarMensaje(`Error al cargar datos para editar: ${error.message}`, 'error');
        }
    };

    const abrirModalVer = async (id) => {
        try {
            const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);
    
            renderizarDetallesHabitacion(resultado.data);
            verModal.show();
        } catch (error) {
            mostrarMensaje(`Error al cargar detalles: ${error.message}`, 'error');
        }
    };


    document.getElementById('btn-guardar-edicion').addEventListener('click', async () => {
        const form = document.getElementById('form-editar-habitacion');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        const formData = new FormData(form);
        const submitBtn = document.getElementById('btn-guardar-edicion');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        try {
            const response = await fetch(`${API_URL}?action=actualizar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                mostrarMensaje(resultado.message, 'success');
                editarModal.hide();
                cargarHabitaciones(currentPage);
            } else {
                throw new Error(resultado.message);
            }
        } catch (error) {
            mostrarMensaje(`Error al actualizar: ${error.message}`, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    const abrirModalEliminar = (id, numero) => {
        habitacionParaEliminar = id;
        document.getElementById('eliminar-numero-habitacion').textContent = numero;
        eliminarModal.show();
    };

    document.getElementById('btn-confirmar-eliminacion').addEventListener('click', async () => {
        if (!habitacionParaEliminar) return;

        const submitBtn = document.getElementById('btn-confirmar-eliminacion');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Eliminando...';

        try {
            const formData = new FormData();
            formData.append('id', habitacionParaEliminar);

            const response = await fetch(`${API_URL}?action=eliminar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                mostrarMensaje(resultado.message, 'success');
                eliminarModal.hide();
                cargarHabitaciones(currentPage);
            } else {
                throw new Error(resultado.message);
            }
        } catch (error) {
            mostrarMensaje(`Error al eliminar: ${error.message}`, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Eliminar';
            habitacionParaEliminar = null;
        }
    });

    // Limpiar formulario de edición al cerrar el modal
    document.getElementById('editarModal').addEventListener('hidden.bs.modal', () => {
        document.getElementById('form-editar-habitacion').reset();
        document.getElementById('edit-foto').value = '';
    });

    // Carga inicial
    if (document.getElementById('habitaciones-grid')) {
        cargarHabitaciones();
    }
});
