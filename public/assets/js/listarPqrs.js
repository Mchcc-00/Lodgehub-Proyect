document.addEventListener('DOMContentLoaded', () => {

    // --- CONSTANTES Y VARIABLES ---
    const API_URL = '/lodgehub/app/controllers/pqrsController.php';
    const tablaPqrs = document.getElementById('tabla-pqrs');
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
    const editarModalEl = document.getElementById('editarModal');
    const editarModal = new bootstrap.Modal(editarModalEl);
    const verModalEl = document.getElementById('verModal');
    const verModal = new bootstrap.Modal(verModalEl);
    const eliminarModalEl = document.getElementById('eliminarModal');
    const eliminarModal = new bootstrap.Modal(eliminarModalEl);

    // Formularios y botones de modales
    const formEditar = document.getElementById('form-editar');
    const guardarEdicionBtn = document.getElementById('guardar-edicion');
    const confirmarEliminacionBtn = document.getElementById('confirmar-eliminacion');
    const estadoSelect = document.getElementById('edit-estado');
    const respuestaContainer = document.getElementById('respuesta-container');

    let currentPage = 1;
    const recordsPerPage = 10;
    let pqrsParaEliminar = null;
    let currentFilter = 'all';

    // --- FUNCIONES ---

    /**
     * Muestra un mensaje de éxito o error.
     */
    const mostrarMensaje = (mensaje, tipo = 'success') => {
        if (tipo === 'success') {
            successText.textContent = mensaje;
            successMessage.style.display = 'block';
            setTimeout(() => { successMessage.style.display = 'none'; }, 4000);
        } else {
            errorText.textContent = mensaje;
            errorMessage.style.display = 'block';
            setTimeout(() => { errorMessage.style.display = 'none'; }, 5000);
        }
    };

    /**
     * Carga las PQRS desde el backend.
     */
    const cargarPqrs = async (pagina = 1, terminoBusqueda = '', filtro = 'all') => {
        tablaPqrs.innerHTML = `<tr><td colspan="9" class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando PQRS...</td></tr>`;
        paginacionContainer.style.display = 'none';
        currentPage = pagina;
        currentFilter = filtro;

        const hotelId = document.getElementById('hotel-id-context')?.value;
        if (!hotelId) {
            tablaPqrs.innerHTML = `<tr><td colspan="9" class="text-center text-warning">⚠️ No se ha seleccionado un hotel.</td></tr>`;
            return;
        }

        try {
            let url = '';
            if (terminoBusqueda) {
                url = `${API_URL}?action=buscar&termino=${encodeURIComponent(terminoBusqueda)}&id_hotel=${hotelId}`;
            } else {
                url = `${API_URL}?action=obtener&paginado=true&pagina=${pagina}&registros=${recordsPerPage}&id_hotel=${hotelId}`;
                if (filtro && filtro !== 'all') {
                    url += `&filtro=${encodeURIComponent(filtro)}`;
                }
            }

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const resultado = await response.json();

            if (resultado.success) {
                if (terminoBusqueda) {
                    renderizarTabla(resultado.data);
                    paginacionContainer.style.display = 'none';
                } else {
                    renderizarTabla(resultado.data.pqrs);
                    renderizarPaginacion(resultado.data);
                }
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            tablaPqrs.innerHTML = `<tr><td colspan="9" class="text-danger text-center">❌ Error al cargar las PQRS: ${error.message}</td></tr>`;
        }
    };

    /**
     * Renderiza los datos de las PQRS en la tabla.
     */
    const renderizarTabla = (pqrs) => {
        tablaPqrs.innerHTML = '';
        if (pqrs.length === 0) {
            tablaPqrs.innerHTML = `<tr><td colspan="9" class="text-center">No se encontraron PQRS.</td></tr>`;
            return;
        }

        pqrs.forEach(pqrsItem => {
            const tr = document.createElement('tr');

            // Formatear fechas
            const fechaRegistro = formatearFecha(pqrsItem.fechaRegistro);
            const fechaLimite = formatearFecha(pqrsItem.fechaLimite);

            // Determinar clase de fila según estado y vencimiento
            let rowClass = '';
            if (pqrsItem.estado === 'Pendiente' && new Date(pqrsItem.fechaLimite) < new Date()) {
                rowClass = 'table-danger'; // Vencida
            }

            tr.className = rowClass;
            tr.innerHTML = `
                <td><strong>#${pqrsItem.id}</strong></td>
                <td>${fechaRegistro}</td>
                <td>${formatearTipo(pqrsItem.tipo)}</td>
                <td>
                    <div class="descripcion-cell" title="${pqrsItem.descripcion}">
                        ${truncarTexto(pqrsItem.descripcion, 50)}
                    </div>
                </td>
                <td>
                    <small class="text-muted">${pqrsItem.numDocumento}</small><br>
                    ${pqrsItem.usuario_nombres || ''} ${pqrsItem.usuario_apellidos || ''}
                </td>
                <td>${formatearPrioridad(pqrsItem.prioridad)}</td>
                <td>${formatearEstado(pqrsItem.estado)}</td>
                <td class="${new Date(pqrsItem.fechaLimite) < new Date() && pqrsItem.estado === 'Pendiente' ? 'text-danger fw-bold' : ''}">
                    ${fechaLimite}
                </td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btn-ver" data-id="${pqrsItem.id}" title="Ver detalles">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-warning btn-editar" data-id="${pqrsItem.id}" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${pqrsItem.id}" data-info="${pqrsItem.tipo} - ${pqrsItem.usuario_nombres || 'Usuario'} ${pqrsItem.usuario_apellidos || ''}" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            `;
            tablaPqrs.appendChild(tr);
        });
    };

    /**
     * Renderiza los controles de paginación.
     */
    const renderizarPaginacion = (datosPaginacion) => {
        const { total, pagina, totalPaginas } = datosPaginacion;
        paginacionUl.innerHTML = '';

        if (totalPaginas <= 1) {
            paginacionContainer.style.display = 'none';
            return;
        }

        paginacionContainer.style.display = 'block';

        // Botón "Anterior"
        paginacionUl.innerHTML += `
            <li class="page-item ${pagina <= 1 ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagina - 1}">Anterior</a>
            </li>`;

        // Números de página (mostrar máximo 5 páginas)
        let startPage = Math.max(1, pagina - 2);
        let endPage = Math.min(totalPaginas, startPage + 4);

        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        for (let i = startPage; i <= endPage; i++) {
            paginacionUl.innerHTML += `
                <li class="page-item ${i === pagina ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="${i}">${i}</a>
                </li>`;
        }

        // Botón "Siguiente"
        paginacionUl.innerHTML += `
            <li class="page-item ${pagina >= totalPaginas ? 'disabled' : ''}">
                <a class="page-link" href="#" data-page="${pagina + 1}">Siguiente</a>
            </li>`;
    };

    /**
     * Renderiza los detalles completos de una PQRS en el modal de visualización.
     */
    const renderizarDetallesPqrs = (pqrs) => {
        const detallesContainer = document.getElementById('detalles-pqrs');

        const fechaFinalizacion = pqrs.fechaFinalizacion ?
            `<div class="col-md-6">
                <strong>Fecha de Finalización:</strong><br>
                ${formatearFecha(pqrs.fechaFinalizacion)}
            </div>` : '';

        const respuesta = pqrs.respuesta ?
            `<div class="col-12 mt-3">
                <strong>Respuesta:</strong><br>
                <div class="border rounded p-3 bg-light">
                    ${pqrs.respuesta}
                </div>
            </div>` : '';

        detallesContainer.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <strong>ID:</strong> #${pqrs.id}<br>
                    <strong>Tipo:</strong> ${pqrs.tipo}<br>
                    <strong>Estado:</strong> ${formatearEstado(pqrs.estado)}<br>
                    <strong>Prioridad:</strong> ${formatearPrioridad(pqrs.prioridad)}
                </div>
                <div class="col-md-6">
                    <strong>Categoría:</strong> ${pqrs.categoria}<br>
                    <strong>Usuario:</strong> ${pqrs.usuario_nombres || ''} ${pqrs.usuario_apellidos || ''}<br>
                    <strong>Documento:</strong> ${pqrs.numDocumento}<br>
                    <strong>Email:</strong> ${pqrs.usuario_correo || 'No disponible'}
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-6">
                    <strong>Fecha de Registro:</strong><br>
                    ${formatearFecha(pqrs.fechaRegistro)}
                </div>
                <div class="col-md-6">
                    <strong>Fecha Límite:</strong><br>
                    <span class="${new Date(pqrs.fechaLimite) < new Date() && pqrs.estado === 'Pendiente' ? 'text-danger fw-bold' : ''}">
                        ${formatearFecha(pqrs.fechaLimite)}
                    </span>
                </div>
                ${fechaFinalizacion}
            </div>
            <div class="row mt-3">
                <div class="col-12">
                    <strong>Descripción:</strong><br>
                    <div class="border rounded p-3 bg-light">
                        ${pqrs.descripcion}
                    </div>
                </div>
                ${respuesta}
            </div>
        `;
    };

    /**
     * Revisa los parámetros de la URL para mostrar mensajes de estado.
     */
    const revisarEstadoURL = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'created') {
            mostrarMensaje('PQRS creada exitosamente.', 'success');
        }

        // Limpiar la URL
        if (window.history.replaceState) {
            const urlLimpia = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({ path: urlLimpia }, '', urlLimpia);
        }
    };

    // --- MANEJO DE EVENTOS ---

    // Carga inicial y revisión de estado desde la URL
    revisarEstadoURL();
    cargarPqrs();

    // Si no hay hotel seleccionado, no hacer nada más
    const hotelId = document.getElementById('hotel-id-context')?.value;
    if (!hotelId) {
        return;
    }

    // Búsqueda
    const ejecutarBusqueda = () => {
        const termino = buscarInput.value.trim();
        if (termino.length > 1 || termino.length === 0) {
            cargarPqrs(1, termino, 'all');
        }
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
            cargarPqrs(1, '', filtro);
        });
    });

    // Botón de actualizar
    refreshBtn.addEventListener('click', () => {
        buscarInput.value = '';
        currentFilter = 'all';
        cargarPqrs();
        mostrarMensaje('Lista actualizada.', 'success');
    });

    // Paginación
    paginacionUl.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            const pageNum = parseInt(e.target.dataset.page, 10);
            if (!isNaN(pageNum)) {
                cargarPqrs(pageNum, buscarInput.value.trim(), currentFilter);
            }
        }
    });

    // Event delegation para botones de ver, editar y eliminar
    tablaPqrs.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const id = target.dataset.id;

        if (target.classList.contains('btn-ver')) {
            // --- Ver PQRS ---
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
                const resultado = await response.json();
                if (resultado.success) {
                    renderizarDetallesPqrs(resultado.data);
                    verModal.show();
                } else {
                    mostrarMensaje(resultado.message, 'error');
                }
            } catch (error) {
                mostrarMensaje('Error al cargar detalles de la PQRS.', 'error');
            }
        }

        if (target.classList.contains('btn-editar')) {
            // --- Editar PQRS ---
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
                const resultado = await response.json();
                if (resultado.success) {
                    const pqrs = resultado.data;
                    document.getElementById('edit-id').value = pqrs.id;
                    document.getElementById('edit-tipo').value = pqrs.tipo;
                    document.getElementById('edit-prioridad').value = pqrs.prioridad;
                    document.getElementById('edit-categoria').value = pqrs.categoria;
                    document.getElementById('edit-estado').value = pqrs.estado;
                    document.getElementById('edit-descripcion').value = pqrs.descripcion;
                    document.getElementById('edit-respuesta').value = pqrs.respuesta || '';

                    // Mostrar/ocultar campo de respuesta
                    toggleRespuestaContainer();

                    editarModal.show();
                } else {
                    mostrarMensaje(resultado.message, 'error');
                }
            } catch (error) {
                mostrarMensaje('Error al cargar datos para edición.', 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            // --- Eliminar PQRS ---
            const info = target.dataset.info;
            pqrsParaEliminar = id;
            document.getElementById('eliminar-info').textContent = info;
            document.getElementById('eliminar-id').textContent = id;
            eliminarModal.show();
        }
    });

    // Control del campo respuesta según el estado
    const toggleRespuestaContainer = () => {
        const estado = estadoSelect.value;
        if (estado === 'Finalizado') {
            respuestaContainer.style.display = 'block';
            document.getElementById('edit-respuesta').required = true;
        } else {
            respuestaContainer.style.display = 'none';
            document.getElementById('edit-respuesta').required = false;
        }
    };

    estadoSelect.addEventListener('change', toggleRespuestaContainer);

    // Guardar cambios del modal de edición
    guardarEdicionBtn.addEventListener('click', async () => {
        const id = document.getElementById('edit-id').value;
        if (!id) {
            mostrarMensaje('Error: No se pudo identificar la PQRS para la actualización.', 'error');
            return;
        }

        const formData = new FormData(formEditar);
        formData.append('id', id);

        // Validar que si el estado es "Finalizado", haya una respuesta
        if (estadoSelect.value === 'Finalizado' && !document.getElementById('edit-respuesta').value.trim()) {
            mostrarMensaje('La respuesta es obligatoria cuando se finaliza una PQRS.', 'error');
            return;
        }

        try {
            const response = await fetch(`${API_URL}?action=actualizar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                editarModal.hide();
                mostrarMensaje('PQRS actualizada exitosamente.', 'success');
                cargarPqrs(currentPage, buscarInput.value.trim(), currentFilter);
            } else {
                mostrarMensaje(resultado.message || 'Ocurrió un error desconocido.', 'error');
            }
        } catch (error) {
            console.error('Error al guardar los cambios:', error);
            mostrarMensaje('Error de comunicación con el servidor.', 'error');
        }
    });

    // Limpiar datos del modal de eliminación cuando se cierra
    eliminarModalEl.addEventListener('hidden.bs.modal', () => {
        pqrsParaEliminar = null;
        document.getElementById('eliminar-info').textContent = '';
        document.getElementById('eliminar-id').textContent = '';
    });

    // Limpiar formulario de edición cuando se cierra
    editarModalEl.addEventListener('hidden.bs.modal', () => {
        formEditar.reset();
        document.getElementById('edit-id').value = '';
        respuestaContainer.style.display = 'none';
    });
    // Confirmar eliminación
    confirmarEliminacionBtn.addEventListener('click', async () => {
        if (!pqrsParaEliminar) return;

        const formData = new FormData();
        formData.append('id', pqrsParaEliminar);

        try {
            const response = await fetch(`${API_URL}?action=eliminar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                eliminarModal.hide();
                mostrarMensaje('PQRS eliminada exitosamente.', 'success');

                // Lógica para retroceder de página si queda vacía
                const filasActuales = tablaPqrs.getElementsByTagName('tr').length;
                if (filasActuales === 1 && currentPage > 1) {
                    currentPage--;
                }
                cargarPqrs(currentPage, buscarInput.value.trim(), currentFilter);
            } else {
                mostrarMensaje(resultado.message || 'Ocurrió un error al eliminar.', 'error');
            }
        } catch (error) {
            console.error('Error al eliminar la PQRS:', error);
            mostrarMensaje('Error de comunicación al eliminar la PQRS.', 'error');
        }
    });

});

// === FUNCIONES UTILITARIAS IMPORTADAS ===
// Estas funciones son las mismas que se definieron en pqrs.js

function formatearFecha(fecha) {
    if (!fecha) return '-';
    const date = new Date(fecha);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function formatearEstado(estado) {
    const badges = {
        'Pendiente': 'bg-warning text-dark',
        'Finalizado': 'bg-success'
    };

    return `<span class="badge ${badges[estado] || 'bg-secondary'}">${estado}</span>`;
}

function formatearPrioridad(prioridad) {
    const badges = {
        'Bajo': 'bg-info',
        'Alto': 'bg-danger'
    };

    return `<span class="badge ${badges[prioridad] || 'bg-secondary'}">${prioridad}</span>`;
}

function formatearTipo(tipo) {
    const colores = {
        'Peticiones': 'text-primary',
        'Quejas': 'text-warning',
        'Reclamos': 'text-danger',
        'Sugerencias': 'text-info',
        'Felicitaciones': 'text-success'
    };

    return `<span class="${colores[tipo] || ''} fw-bold">${tipo}</span>`;
}

// Función para truncar texto si excede una longitud máxima
function truncarTexto(texto, longitud = 100) {
    if (!texto) return '';
    if (texto.length <= longitud)
        return texto;

    return texto.substring(0, longitud) + '...';
}
