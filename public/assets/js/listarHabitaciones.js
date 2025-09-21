document.addEventListener('DOMContentLoaded', () => {
    // --- CONSTANTES Y VARIABLES ---
    const API_URL = '/app/controllers/habitacionesController.php';
    const habitacionesGrid = document.getElementById('habitaciones-grid');
    const paginacionContainer = document.getElementById('paginacion-container');
    const paginacionUl = document.getElementById('paginacion');
    const buscarInput = document.getElementById('buscar-input');
    const filtroEstado = document.getElementById('filtro-estado');
    const filtroTipo = document.getElementById('filtro-tipo');
    const loadingDiv = document.getElementById('loading');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');

    // Modales
    const editarModalEl = document.getElementById('editarModal');
    const editarModal = new bootstrap.Modal(editarModalEl);
    const verModalEl = document.getElementById('verModal');
    const verModal = new bootstrap.Modal(verModalEl);
    const eliminarModalEl = document.getElementById('eliminarModal');
    const eliminarModal = new bootstrap.Modal(eliminarModalEl);

    // Formularios y botones de modales
    const formEditar = document.getElementById('form-editar-habitacion');
    const btnGuardarEdicion = document.getElementById('btn-guardar-edicion');
    const btnConfirmarEliminacion = document.getElementById('btn-confirmar-eliminacion');

    let currentPage = 1;
    const recordsPerPage = 8;
    let habitacionParaEliminar = null;
    let debounceTimer;

    // --- FUNCIONES ---

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        setTimeout(() => { elem.style.display = 'none'; }, 4000);
    };

    const cargarHabitaciones = async (pagina = 1) => {
        currentPage = pagina;
        loadingDiv.style.display = 'block';
        habitacionesGrid.innerHTML = '';
        paginacionContainer.style.display = 'none';

        const busqueda = buscarInput.value.trim();
        const estado = filtroEstado.value;
        const tipo = filtroTipo.value;

        try {
            let url = `${API_URL}?action=obtener&pagina=${pagina}&registros=${recordsPerPage}&estado=${estado}&tipo=${tipo}`;
            if (busqueda) {
                url += `&busqueda=${encodeURIComponent(busqueda)}`;
            }

            const response = await fetch(url);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            
            const resultado = await response.json();

            if (resultado.success) {
                renderizarGrid(resultado.data.habitaciones);
                renderizarPaginacion(resultado.data);
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            habitacionesGrid.innerHTML = `<div class="col-12 alert alert-danger">❌ Error al cargar las habitaciones: ${error.message}</div>`;
        } finally {
            loadingDiv.style.display = 'none';
        }
    };

    const renderizarGrid = (habitaciones) => {
        habitacionesGrid.innerHTML = '';
        if (habitaciones.length === 0) {
            habitacionesGrid.innerHTML = `<div class="col-12 alert alert-info text-center">No se encontraron habitaciones con los filtros seleccionados.</div>`;
            return;
        }

        const estadoClases = {
            'Disponible': 'border-success',
            'Ocupada': 'border-danger',
            'Reservada': 'border-warning',
            'Mantenimiento': 'border-info'
        };
        const estadoBadges = {
            'Disponible': 'bg-success',
            'Ocupada': 'bg-danger',
            'Reservada': 'bg-warning text-dark',
            'Mantenimiento': 'bg-info text-dark'
        };

        habitaciones.forEach(hab => {
            const card = document.createElement('div');
            card.className = 'col';
            const foto = hab.foto ? hab.foto : '../../public/assets/img/room-placeholder.png';
            const costoFormateado = parseFloat(hab.costo).toLocaleString('es-CO', { style: 'currency', currency: 'COP', minimumFractionDigits: 0 });

            card.innerHTML = `
                <div class="habitacion-card ${estadoClases[hab.estado] || 'border-secondary'}">
                    <img src="${foto}" class="card-img-top" alt="Habitación ${hab.numero}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">Habitación N° ${hab.numero}</h5>
                            <span class="badge ${estadoBadges[hab.estado] || 'bg-secondary'}">${hab.estado}</span>
                        </div>
                        <p class="card-text text-muted">${hab.tipo_descripcion}</p>
                        <ul class="list-unstyled">
                            <li><i class="fas fa-users"></i> Capacidad: ${hab.capacidad} personas</li>
                            <li><i class="fas fa-dollar-sign"></i> Costo: ${costoFormateado} / noche</li>
                        </ul>
                    </div>
                    <div class="card-footer">
                        <div class="btn-group w-100" role="group">
                            <button class="btn btn-sm btn-outline-info btn-ver" data-id="${hab.id}" title="Ver Detalles"><i class="fas fa-eye"></i></button>
                            <button class="btn btn-sm btn-outline-warning btn-editar" data-id="${hab.id}" title="Editar"><i class="fas fa-edit"></i></button>
                            <button class="btn btn-sm btn-outline-danger btn-eliminar" data-id="${hab.id}" data-numero="${hab.numero}" title="Eliminar"><i class="fas fa-trash"></i></button>
                        </div>
                    </div>
                </div>
            `;
            habitacionesGrid.appendChild(card);
        });
    };

    const renderizarPaginacion = (datosPaginacion) => {
        const { pagina, totalPaginas } = datosPaginacion;
        paginacionUl.innerHTML = '';

        if (totalPaginas <= 1) {
            paginacionContainer.style.display = 'none';
            return;
        }

        paginacionContainer.style.display = 'block';

        const crearBoton = (texto, pageNum, disabled = false, active = false) => {
            const li = document.createElement('li');
            li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
            const a = document.createElement('a');
            a.className = 'page-link';
            a.href = '#';
            a.dataset.page = pageNum;
            a.textContent = texto;
            li.appendChild(a);
            return li;
        };

        paginacionUl.appendChild(crearBoton('Anterior', pagina - 1, pagina <= 1));

        for (let i = 1; i <= totalPaginas; i++) {
            paginacionUl.appendChild(crearBoton(i, i, false, i === pagina));
        }

        paginacionUl.appendChild(crearBoton('Siguiente', pagina + 1, pagina >= totalPaginas));
    };

    const renderizarDetallesHabitacion = (hab) => {
        const detallesContainer = document.getElementById('detalles-habitacion');
        const foto = hab.foto ? hab.foto : '../../public/assets/img/room-placeholder.png';
        const costoFormateado = parseFloat(hab.costo).toLocaleString('es-CO', { style: 'currency', currency: 'COP' });

        detallesContainer.innerHTML = `
            <div class="row">
                <div class="col-md-5">
                    <img src="${foto}" class="img-fluid rounded" alt="Habitación ${hab.numero}">
                </div>
                <div class="col-md-7">
                    <h4>Habitación N° ${hab.numero}</h4>
                    <p><strong>Tipo:</strong> ${hab.tipo_descripcion}</p>
                    <p><strong>Estado:</strong> ${hab.estado}</p>
                    <p><strong>Capacidad:</strong> ${hab.capacidad} personas</p>
                    <p><strong>Costo por Noche:</strong> ${costoFormateado}</p>
                    <hr>
                    <p><strong>Descripción:</strong></p>
                    <p>${hab.descripcion || 'No hay descripción disponible.'}</p>
                </div>
            </div>
        `;
    };

    // --- MANEJO DE EVENTOS ---
    const aplicarFiltros = () => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            cargarHabitaciones(1);
        }, 300);
    };

    buscarInput.addEventListener('keyup', aplicarFiltros);
    filtroEstado.addEventListener('change', aplicarFiltros);
    filtroTipo.addEventListener('change', aplicarFiltros);

    paginacionUl.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            const pageNum = parseInt(e.target.dataset.page, 10);
            if (!isNaN(pageNum)) {
                cargarHabitaciones(pageNum);
            }
        }
    });

    habitacionesGrid.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const id = target.dataset.id;

        if (target.classList.contains('btn-ver')) {
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
                const resultado = await response.json();
                if (resultado.success) {
                    renderizarDetallesHabitacion(resultado.data);
                    verModal.show();
                } else {
                    mostrarMensaje(resultado.message, 'error');
                }
            } catch (error) {
                mostrarMensaje('Error al cargar detalles de la habitación.', 'error');
            }
        }

        if (target.classList.contains('btn-editar')) {
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
                const resultado = await response.json();
                if (resultado.success) {
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
                } else {
                    mostrarMensaje(resultado.message, 'error');
                }
            } catch (error) {
                mostrarMensaje('Error al cargar datos para edición.', 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            const numero = target.dataset.numero;
            habitacionParaEliminar = id;
            document.getElementById('eliminar-numero-habitacion').textContent = numero;
            eliminarModal.show();
        }
    });

    btnGuardarEdicion.addEventListener('click', async () => {
        if (!formEditar.checkValidity()) {
            formEditar.classList.add('was-validated');
            return;
        }

        const formData = new FormData(formEditar);
        btnGuardarEdicion.disabled = true;
        btnGuardarEdicion.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        try {
            const response = await fetch(`${API_URL}?action=actualizar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                editarModal.hide();
                mostrarMensaje('Habitación actualizada exitosamente.', 'success');
                cargarHabitaciones(currentPage);
            } else {
                mostrarMensaje(resultado.message, 'error');
            }
        } catch (error) {
            mostrarMensaje('Error de comunicación con el servidor.', 'error');
        } finally {
            btnGuardarEdicion.disabled = false;
            btnGuardarEdicion.innerHTML = '<i class="fas fa-save"></i> Guardar Cambios';
        }
    });

    btnConfirmarEliminacion.addEventListener('click', async () => {
        if (!habitacionParaEliminar) return;

        const formData = new FormData();
        formData.append('id', habitacionParaEliminar);

        try {
            const response = await fetch(`${API_URL}?action=eliminar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                eliminarModal.hide();
                mostrarMensaje('Habitación eliminada exitosamente.', 'success');
                cargarHabitaciones(currentPage);
            } else {
                mostrarMensaje(resultado.message, 'error');
            }
        } catch (error) {
            mostrarMensaje('Error de comunicación al eliminar.', 'error');
        }
    });

    editarModalEl.addEventListener('hidden.bs.modal', () => {
        formEditar.reset();
        formEditar.classList.remove('was-validated');
        document.getElementById('edit-foto').value = '';
    });

    // Carga inicial
    cargarHabitaciones();
});
