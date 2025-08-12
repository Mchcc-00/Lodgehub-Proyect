document.addEventListener('DOMContentLoaded', () => {

    // --- CONSTANTES Y VARIABLES ---
    const API_URL = '../controllers/huespedController.php';
    const tablaHuespedes = document.getElementById('tabla-huespedes');
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
    const eliminarModalEl = document.getElementById('eliminarModal');
    const eliminarModal = new bootstrap.Modal(eliminarModalEl);

    // Formularios y botones de modales
    const formEditar = document.getElementById('form-editar');
    const guardarEdicionBtn = document.getElementById('guardar-edicion');
    const confirmarEliminacionBtn = document.getElementById('confirmar-eliminacion');

    let currentPage = 1;
    const recordsPerPage = 10;
    let huespedParaEliminar = null;

    // --- FUNCIONES ---

    /**
     * Muestra un mensaje de éxito o error.
     * @param {string} mensaje - El mensaje a mostrar.
     * @param {string} tipo - 'success' o 'error'.
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
     * Carga los huéspedes desde el backend.
     * @param {number} pagina - El número de página a cargar.
     * @param {string} terminoBusqueda - El término para filtrar la búsqueda.
     */
    const cargarHuespedes = async (pagina = 1, terminoBusqueda = '') => {
        tablaHuespedes.innerHTML = `<tr><td colspan="7" class="loading"><i class="fas fa-spinner fa-spin"></i> Cargando huéspedes...</td></tr>`;
        paginacionContainer.style.display = 'none';
        currentPage = pagina;

        try {
            let url = '';
            if (terminoBusqueda) {
                url = `${API_URL}?action=buscar&termino=${encodeURIComponent(terminoBusqueda)}`;
            } else {
                url = `${API_URL}?action=obtener&paginado=true&pagina=${pagina}&registros=${recordsPerPage}`;
            }

            const response = await fetch(url);
            if (!response.ok) {
                throw new Error(`Error HTTP: ${response.status}`);
            }

            const resultado = await response.json();

            if (resultado.success) {
                if (terminoBusqueda) {
                    renderizarTabla(resultado.data);
                    paginacionContainer.style.display = 'none'; // Ocultar paginación en búsqueda
                } else {
                    renderizarTabla(resultado.data.huespedes);
                    renderizarPaginacion(resultado.data);
                }
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            tablaHuespedes.innerHTML = `<tr><td colspan="7" class="text-danger text-center">❌ Error al cargar los huéspedes: ${error.message}</td></tr>`;
        }
    };

    /**
     * Renderiza los datos de los huéspedes en la tabla.
     * @param {Array} huespedes - Array de objetos de huéspedes.
     */
    const renderizarTabla = (huespedes) => {
        tablaHuespedes.innerHTML = '';
        if (huespedes.length === 0) {
            tablaHuespedes.innerHTML = `<tr><td colspan="7" class="text-center">No se encontraron huéspedes.</td></tr>`;
            return;
        }

        huespedes.forEach(huesped => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${huesped.tipoDocumento} ${huesped.numDocumento}</td>
                <td>${huesped.nombres}</td>
                <td>${huesped.apellidos}</td>
                <td>${huesped.sexo}</td>
                <td>${huesped.numTelefono}</td>
                <td>${huesped.correo}</td>
                <td>
                    <button class="btn btn-sm btn-warning btn-editar" data-id="${huesped.numDocumento}" title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger btn-eliminar" data-id="${huesped.numDocumento}" data-nombre="${huesped.nombres} ${huesped.apellidos}" title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </td>
            `;
            tablaHuespedes.appendChild(tr);
        });
    };

    /**
     * Renderiza los controles de paginación.
     * @param {object} datosPaginacion - Objeto con la información de paginación.
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

        // Números de página
        for (let i = 1; i <= totalPaginas; i++) {
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
     * Revisa los parámetros de la URL para mostrar mensajes de estado (ej. después de crear).
     */
    const revisarEstadoURL = () => {
        const urlParams = new URLSearchParams(window.location.search);
        const status = urlParams.get('status');

        if (status === 'created') {
            mostrarMensaje('Huésped registrado exitosamente.', 'success');
        }
        
        // Limpiar la URL para que el mensaje no reaparezca al recargar la página.
        if (window.history.replaceState) {
            const urlLimpia = window.location.protocol + "//" + window.location.host + window.location.pathname;
            window.history.replaceState({ path: urlLimpia }, '', urlLimpia);
        }
    };

    // --- MANEJO DE EVENTOS ---

    // Carga inicial y revisión de estado desde la URL
    revisarEstadoURL();
    cargarHuespedes();

    // Búsqueda
    const ejecutarBusqueda = () => {
        const termino = buscarInput.value.trim();
        if (termino.length > 1 || termino.length === 0) {
            cargarHuespedes(1, termino);
        }
    };
    buscarBtn.addEventListener('click', ejecutarBusqueda);
    buscarInput.addEventListener('keyup', (e) => {
        if (e.key === 'Enter') {
            ejecutarBusqueda();
        }
    });

    // Botón de actualizar
    refreshBtn.addEventListener('click', () => {
        buscarInput.value = '';
        cargarHuespedes();
        mostrarMensaje('Lista actualizada.', 'success');
    });

    // Paginación
    paginacionUl.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.tagName === 'A' && e.target.dataset.page) {
            const pageNum = parseInt(e.target.dataset.page, 10);
            if (!isNaN(pageNum)) {
                cargarHuespedes(pageNum, buscarInput.value.trim());
            }
        }
    });

    // Event delegation para botones de editar y eliminar
    tablaHuespedes.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;

        const numDocumento = target.dataset.id;

        if (target.classList.contains('btn-editar')) {
            // --- Editar Huésped ---
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorDocumento&numDocumento=${numDocumento}`);
                const resultado = await response.json();
                if (resultado.success) {
                    const huesped = resultado.data;
                    document.getElementById('edit-numDocumento').value = huesped.numDocumento;
                    document.getElementById('edit-nombres').value = huesped.nombres;
                    document.getElementById('edit-apellidos').value = huesped.apellidos;
                    document.getElementById('edit-sexo').value = huesped.sexo;
                    document.getElementById('edit-numTelefono').value = huesped.numTelefono;
                    document.getElementById('edit-correo').value = huesped.correo;
                    editarModal.show();
                } else {
                    mostrarMensaje(resultado.message, 'error');
                }
            } catch (error) {
                mostrarMensaje('Error al cargar datos para edición.', 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            // --- Eliminar Huésped ---
            const nombre = target.dataset.nombre;
            huespedParaEliminar = numDocumento;
            document.getElementById('eliminar-nombre').textContent = nombre;
            document.getElementById('eliminar-documento').textContent = numDocumento;
            eliminarModal.show();
        }
    });

    // Guardar cambios del modal de edición
    guardarEdicionBtn.addEventListener('click', async () => {
        const numDocumento = document.getElementById('edit-numDocumento').value;
        if (!numDocumento) {
            mostrarMensaje('Error: No se pudo identificar al huésped para la actualización.', 'error');
            return;
        }

        // El backend para huéspedes espera FormData, no JSON.
        const formData = new FormData(formEditar);
        // Añadimos el numDocumento manualmente ya que el input hidden no tiene 'name'.
        formData.append('numDocumento', numDocumento);

        try {
            const response = await fetch(`${API_URL}?action=actualizar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                editarModal.hide();
                mostrarMensaje('Huésped actualizado exitosamente.', 'success');
                cargarHuespedes(currentPage, buscarInput.value.trim());
            } else {
                mostrarMensaje(resultado.message || 'Ocurrió un error al actualizar.', 'error');
            }
        } catch (error) {
            console.error('Error al guardar los cambios:', error);
            mostrarMensaje('Error de comunicación al guardar los cambios.', 'error');
        }
    });

    // Confirmar eliminación
    confirmarEliminacionBtn.addEventListener('click', async () => {
        if (!huespedParaEliminar) return;

        // El backend para huéspedes espera FormData, no JSON.
        const formData = new FormData();
        formData.append('numDocumento', huespedParaEliminar);

        try {
            const response = await fetch(`${API_URL}?action=eliminar`, {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                eliminarModal.hide();
                mostrarMensaje('Huésped eliminado exitosamente.', 'success');
                // Lógica para retroceder de página si queda vacía
                const filasActuales = tablaHuespedes.getElementsByTagName('tr').length;
                if (filasActuales === 1 && currentPage > 1) {
                    currentPage--;
                }
                cargarHuespedes(currentPage, buscarInput.value.trim());
            } else {
                mostrarMensaje(resultado.message || 'Ocurrió un error desconocido.', 'error');
            }
        } catch (error) {
            console.error('Error en la solicitud de eliminación:', error);
            mostrarMensaje('Error de comunicación con el servidor.', 'error');
        } finally {
            huespedParaEliminar = null;
        }
    });

    // Limpiar datos del modal de eliminación cuando se cierra
    eliminarModalEl.addEventListener('hidden.bs.modal', () => {
        huespedParaEliminar = null;
        document.getElementById('eliminar-nombre').textContent = '';
        document.getElementById('eliminar-documento').textContent = '';
    });

    // Limpiar formulario de edición cuando se cierra
    editarModalEl.addEventListener('hidden.bs.modal', () => {
        formEditar.reset();
        document.getElementById('edit-numDocumento').value = '';
    });

});