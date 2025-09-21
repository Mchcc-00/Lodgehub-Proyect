document.addEventListener('DOMContentLoaded', () => {

    // --- CONSTANTES Y VARIABLES ---
    const API_URL = '/lodgehub/app/controllers/tipoHabitacionController.php';
    const tablaTiposHabitacion = document.getElementById('tabla-tipos-habitacion');
    const paginacionContainer = document.getElementById('paginacion-container');
    const paginacionUl = document.getElementById('paginacion');
    const buscarInput = document.getElementById('buscar-input');
    const buscarBtn = document.getElementById('buscar-btn');
    const refreshBtn = document.getElementById('refresh-btn');
    const btnCrear = document.getElementById('btn-crear');
    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');

    // Modales
    const formModal = new bootstrap.Modal(document.getElementById('formModal'));
    const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));

    // Formulario del modal
    const form = document.getElementById('tipo-habitacion-form');
    const modalLabel = document.getElementById('modalLabel');
    const idTipoInput = document.getElementById('id_tipo');
    const descripcionInput = document.getElementById('descripcion');

    let currentPage = 1;
    let idParaEliminar = null;

    // --- FUNCIONES ---

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const alertElement = tipo === 'success' ? successMessage : errorMessage;
        const textElement = tipo === 'success' ? successText : errorText;
        
        textElement.textContent = mensaje;
        alertElement.style.display = 'block';
        
        // Ocultar el mensaje después de 4 segundos
        setTimeout(() => {
            const bsAlert = new bootstrap.Alert(alertElement);
            bsAlert.close();
        }, 4000);
    };

    const cargarTiposHabitacion = async (pagina = 1, busqueda = '') => {
        tablaTiposHabitacion.innerHTML = `<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>`;
        currentPage = pagina;

        try {
            const url = `${API_URL}?action=listar&pagina=${pagina}&busqueda=${encodeURIComponent(busqueda)}`;

            const response = await fetch(url);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const resultado = await response.json();

            if (resultado.success) {
                renderizarTabla(resultado.data.tipos);
                renderizarPaginacion(resultado.data);
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            tablaTiposHabitacion.innerHTML = `<tr><td colspan="4" class="text-danger text-center"><i class="fas fa-exclamation-triangle"></i> Error: ${error.message}</td></tr>`;
            paginacionContainer.style.display = 'none';
        }
    };

    const renderizarTabla = (data) => {
        tablaTiposHabitacion.innerHTML = '';
        if (data.length === 0) {
            tablaTiposHabitacion.innerHTML = `<tr><td colspan="4" class="text-center">No se encontraron registros.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.id}</td>
                <td>${item.descripcion}</td>
                <td><span class="badge bg-primary">${item.cantidad}</span></td>
                <td>
                    <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-warning btn-editar" data-id="${item.id}" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${item.id}" data-info="${item.descripcion}" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            tablaTiposHabitacion.appendChild(tr);
        });
    };

    const renderizarPaginacion = ({ totalPaginas, pagina: paginaActual }) => {
        paginacionUl.innerHTML = '';
        if (totalPaginas <= 1) {
            paginacionContainer.style.display = 'none';
            return;
        }
        paginacionContainer.style.display = 'block';
        
        for (let i = 1; i <= totalPaginas; i++) {
            const li = document.createElement('li');
            li.className = `page-item ${i === paginaActual ? 'active' : ''}`;
            li.innerHTML = `<a class="page-link" href="#" data-page="${i}">${i}</a>`;
            paginacionUl.appendChild(li);
        }
    };

    // --- MANEJO DE EVENTOS ---

    cargarTiposHabitacion();

    buscarBtn.addEventListener('click', () => cargarTiposHabitacion(1, buscarInput.value.trim()));
    buscarInput.addEventListener('keyup', (e) => e.key === 'Enter' && cargarTiposHabitacion(1, buscarInput.value.trim()));
    refreshBtn.addEventListener('click', () => {
        buscarInput.value = '';
        cargarTiposHabitacion();
    });

    btnCrear.addEventListener('click', () => {
        form.reset();
        idTipoInput.value = '';
        modalLabel.innerHTML = '<i class="fas fa-plus"></i> Crear Nuevo Tipo de Habitación';
        descripcionInput.classList.remove('is-invalid');
        formModal.show(); // <-- AÑADE ESTA LÍNEA
    });

    paginacionUl.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.dataset.page) {
            cargarTiposHabitacion(parseInt(e.target.dataset.page, 10), buscarInput.value.trim());
        }
    });

    tablaTiposHabitacion.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;
        const id = target.dataset.id;

        if (target.classList.contains('btn-editar')) {
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorId&id=${id}`);
                const resultado = await response.json();
                if (!resultado.success) throw new Error(resultado.message);
                
                form.reset();
                idTipoInput.value = resultado.data.id;
                descripcionInput.value = resultado.data.descripcion;
                modalLabel.innerHTML = '<i class="fas fa-edit"></i> Editar Tipo de Habitación';
                descripcionInput.classList.remove('is-invalid');
                formModal.show();

            } catch (error) {
                mostrarMensaje(error.message, 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            idParaEliminar = id;
            document.getElementById('eliminar-id').value = id;
            document.getElementById('eliminar-info').textContent = target.dataset.info;
            eliminarModal.show();
        }
    });

    document.getElementById('guardar-btn').addEventListener('click', async () => {
        const formData = new FormData(form);
        const id = idTipoInput.value;
        const action = id ? 'actualizar' : 'crear';

        try {
            const response = await fetch(`${API_URL}?action=${action}`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);

            mostrarMensaje(resultado.message, 'success');
            formModal.hide();
            cargarTiposHabitacion(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        }
    });

    document.getElementById('confirmar-eliminacion-btn').addEventListener('click', async () => {
        if (!idParaEliminar) return;
        const formData = new FormData();
        formData.append('id', idParaEliminar);

        try {
            const response = await fetch(`${API_URL}?action=eliminar`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!resultado.success) throw new Error(resultado.message);

            mostrarMensaje(resultado.message, 'success');
            eliminarModal.hide();
            cargarTiposHabitacion(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            idParaEliminar = null;
        }
    });
});
