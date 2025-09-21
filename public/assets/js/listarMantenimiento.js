document.addEventListener('DOMContentLoaded', () => {

    // --- CONSTANTES Y VARIABLES ---
    const API_URL = '/app/controllers/mantenimientoController.php';
    const tablaMantenimientos = document.getElementById('tabla-mantenimientos');
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
    let currentFilters = { estado: 'all', prioridad: 'all', tipo: 'all' };
    let itemParaEliminar = null;

    // --- FUNCIONES ---

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        setTimeout(() => { elem.style.display = 'none'; }, 4000);
    };

    const cargarMantenimientos = async (pagina = 1, terminoBusqueda = '') => {
        tablaMantenimientos.innerHTML = `<tr><td colspan="9" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>`;
        currentPage = pagina;

        try {
            let url = '';
            if (terminoBusqueda) {
                url = `${API_URL}?action=buscar&termino=${encodeURIComponent(terminoBusqueda)}`;
            } else {
                url = `${API_URL}?action=obtener&pagina=${pagina}&estado=${currentFilters.estado}&prioridad=${currentFilters.prioridad}&tipo=${currentFilters.tipo}`;
            }

            const response = await fetch(url);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const resultado = await response.json();

            if (resultado.success) {
                const data = terminoBusqueda ? resultado.data : resultado.data.mantenimientos;
                renderizarTabla(data);
                if (!terminoBusqueda) {
                    renderizarPaginacion(resultado.data);
                } else {
                    paginacionContainer.style.display = 'none';
                }
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            tablaMantenimientos.innerHTML = `<tr><td colspan="9" class="text-danger text-center">❌ Error: ${error.message}</td></tr>`;
        }
    };

    const renderizarTabla = (data) => {
        tablaMantenimientos.innerHTML = '';
        if (data.length === 0) {
            tablaMantenimientos.innerHTML = `<tr><td colspan="9" class="text-center">No se encontraron registros.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><strong>#${item.id}</strong></td>
                <td><i class="fas fa-door-open"></i> ${item.numeroHabitacion || 'N/A'}</td>
                <td>${item.tipo}</td>
                <td title="${item.problemaDescripcion}">${item.problemaDescripcion.substring(0, 30)}...</td>
                <td>${formatearPrioridad(item.prioridad)}</td>
                <td>${formatearEstado(item.estado)}</td>
                <td>${item.nombreResponsable || 'No asignado'}</td>
                <td>${new Date(item.fechaRegistro).toLocaleDateString()}</td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-info btn-ver" data-id="${item.id}" title="Ver"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-sm btn-warning btn-editar" data-id="${item.id}" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${item.id}" data-info="Hab. ${item.numeroHabitacion}: ${item.problemaDescripcion}" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            tablaMantenimientos.appendChild(tr);
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
        const container = document.getElementById('detalles-mantenimiento');
        container.innerHTML = `
            <p><strong>ID:</strong> #${item.id}</p>
            <p><strong>Habitación:</strong> ${item.numeroHabitacion}</p>
            <p><strong>Tipo:</strong> ${item.tipo}</p>
            <p><strong>Prioridad:</strong> ${formatearPrioridad(item.prioridad)}</p>
            <p><strong>Estado:</strong> ${formatearEstado(item.estado)}</p>
            <p><strong>Descripción del Problema:</strong><br>${item.problemaDescripcion}</p>
            <p><strong>Observaciones/Solución:</strong><br>${item.observaciones || 'Sin observaciones.'}</p>
            <p><strong>Responsable:</strong> ${item.nombreResponsable} (${item.correoResponsable})</p>
            <p><strong>Fecha Registro:</strong> ${new Date(item.fechaRegistro).toLocaleString()}</p>
            <p><strong>Última Actualización:</strong> ${new Date(item.ultimaActualizacion).toLocaleString()}</p>
        `;
    };

    // --- MANEJO DE EVENTOS ---

    cargarMantenimientos();

    buscarBtn.addEventListener('click', () => cargarMantenimientos(1, buscarInput.value.trim()));
    buscarInput.addEventListener('keyup', (e) => e.key === 'Enter' && cargarMantenimientos(1, buscarInput.value.trim()));
    refreshBtn.addEventListener('click', () => {
        buscarInput.value = '';
        currentFilters = { estado: 'all', prioridad: 'all', tipo: 'all' };
        cargarMantenimientos();
    });

    document.querySelectorAll('.filter-option').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const group = e.target.dataset.filterGroup;
            const value = e.target.dataset.filterValue;
            currentFilters[group] = value;
            cargarMantenimientos(1);
        });
    });

    paginacionUl.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.dataset.page) {
            cargarMantenimientos(parseInt(e.target.dataset.page, 10), buscarInput.value.trim());
        }
    });

    tablaMantenimientos.addEventListener('click', async (e) => {
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
                    const form = document.getElementById('form-editar');
                    form.querySelector('#edit-id').value = resultado.data.id;
                    form.querySelector('#edit-tipo').value = resultado.data.tipo;
                    form.querySelector('#edit-prioridad').value = resultado.data.prioridad;
                    form.querySelector('#edit-descripcion').value = resultado.data.problemaDescripcion;
                    form.querySelector('#edit-estado').value = resultado.data.estado;
                    form.querySelector('#edit-observaciones').value = resultado.data.observaciones || '';
                    editarModal.show();
                }
            } catch (error) {
                mostrarMensaje(error.message, 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            itemParaEliminar = id;
            document.getElementById('eliminar-id').textContent = id;
            document.getElementById('eliminar-info').textContent = target.dataset.info;
            eliminarModal.show();
        }
    });

    document.getElementById('guardar-edicion').addEventListener('click', async () => {
        const form = document.getElementById('form-editar');
        const formData = new FormData(form);
        // **INICIO: AJUSTE CRÍTICO**
        // Añadir el ID al FormData, ya que el input hidden no tiene 'name'
        const id = form.querySelector('#edit-id').value;
        formData.append('id', id);

        try {
            const response = await fetch(`${API_URL}?action=actualizar`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);

            mostrarMensaje(resultado.message, 'success');
            editarModal.hide();
            cargarMantenimientos(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        }
    });

    document.getElementById('confirmar-eliminacion').addEventListener('click', async () => {
        if (!itemParaEliminar) return;
        const formData = new FormData();
        formData.append('id', itemParaEliminar);

        try {
            const response = await fetch(`${API_URL}?action=eliminar`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);

            mostrarMensaje(resultado.message, 'success');
            eliminarModal.hide();
            cargarMantenimientos(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            itemParaEliminar = null;
        }
    });

    // --- FUNCIONES UTILITARIAS ---

    function formatearEstado(estado) {
        const badges = {
            'Pendiente': 'bg-warning text-dark',
            'Finalizado': 'bg-success'
        };
        return `<span class="badge ${badges[estado] || 'bg-secondary'}">${estado}</span>`;
    }

    function formatearPrioridad(prioridad) {
        const badges = {
            'Bajo': 'bg-info text-dark',
            'Alto': 'bg-danger'
        };
        return `<span class="badge ${badges[prioridad] || 'bg-secondary'}">${prioridad}</span>`;
    }
});
