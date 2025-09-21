document.addEventListener('DOMContentLoaded', () => {

    // --- CONSTANTES Y VARIABLES ---
    const API_URL = '/lodgehub/app/controllers/huespedController.php';
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
    const editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
    const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));
    const verModal = new bootstrap.Modal(document.getElementById('verModal'));

    let currentPage = 1;
    let itemParaEliminar = null;
    let debounceTimer;

    // --- FUNCIONES ---

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        window.scrollTo(0, 0);
        setTimeout(() => { elem.style.display = 'none'; }, 4000);
    };

    const cargarHuespedes = async (pagina = 1, terminoBusqueda = '') => {
        if (!tablaHuespedes) return;
        tablaHuespedes.innerHTML = `<tr><td colspan="6" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>`;
        currentPage = pagina;

        try {
            let url = `${API_URL}?action=obtener&pagina=${pagina}`;
            if (terminoBusqueda) url += `&busqueda=${encodeURIComponent(terminoBusqueda)}`;

            const response = await fetch(url);
            if (!response.ok) throw new Error(`Error HTTP: ${response.status}`);
            const resultado = await response.json();

            if (resultado.success) {
                renderizarTabla(resultado.data.huespedes);
                renderizarPaginacion(resultado.data);
            } else {
                throw new Error(resultado.message || 'Error al cargar los datos.');
            }
        } catch (error) {
            tablaHuespedes.innerHTML = `<tr><td colspan="6" class="text-danger text-center">❌ Error: ${error.message}</td></tr>`;
        }
    };

    const renderizarTabla = (data) => {
        tablaHuespedes.innerHTML = '';
        if (data.length === 0) {
            tablaHuespedes.innerHTML = `<tr><td colspan="6" class="text-center">No se encontraron huéspedes.</td></tr>`;
            return;
        }

        data.forEach(item => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td class="huesped-info">
                    <h6>${item.nombres} ${item.apellidos}</h6>
                    <small>${item.correo}</small>
                </td>
                <td>
                    <small>${item.tipoDocumento}</small><br>
                    <strong>${item.numDocumento}</strong>
                </td>
                <td>${item.numTelefono}</td>
                <td>${formatearSexo(item.sexo)}</td>
                <td>${new Date(item.fechaCreacion).toLocaleDateString()}</td>
                <td class="acciones-tabla">
                    <div class="btn-group">
                        <button class="btn btn-sm btn-warning btn-editar" data-id="${item.numDocumento}" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger btn-eliminar" data-id="${item.numDocumento}" data-info="${item.nombres} ${item.apellidos}" title="Eliminar"><i class="fas fa-trash"></i></button>
                    </div>
                </td>
            `;
            tablaHuespedes.appendChild(tr);
        });
    };


    const renderizarPaginacion = ({ totalPaginas, pagina }) => {
        paginacionUl.innerHTML = '';
        if (totalPaginas <= 1) {
            paginacionContainer.style.visibility = 'hidden';
            return;
        }
        paginacionContainer.style.visibility = 'visible';

        for (let i = 1; i <= totalPaginas; i++) {
            paginacionUl.innerHTML += `<li class="page-item ${i === pagina ? 'active' : ''}"><a class="page-link" href="#" data-page="${i}">${i}</a></li>`;
        }
    };

    // --- MANEJO DE EVENTOS ---

    cargarHuespedes();

    buscarBtn.addEventListener('click', () => cargarHuespedes(1, buscarInput.value.trim()));
    buscarInput.addEventListener('keyup', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            if (e.key === 'Enter' || buscarInput.value.length === 0 || buscarInput.value.length > 2) {
                cargarHuespedes(1, buscarInput.value.trim());
            }
        }, 400);
    });

    refreshBtn.addEventListener('click', () => {
        buscarInput.value = '';
        cargarHuespedes();
    });

    paginacionUl.addEventListener('click', (e) => {
        e.preventDefault();
        if (e.target.dataset.page) {
            cargarHuespedes(parseInt(e.target.dataset.page, 10), buscarInput.value.trim());
        }
    });

    tablaHuespedes.addEventListener('click', async (e) => {
        const target = e.target.closest('button');
        if (!target) return;
        const numDocumento = target.dataset.id;

        if (target.classList.contains('btn-ver')) {
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorDocumento&numDocumento=${numDocumento}`);
                const resultado = await response.json();
                if (!resultado.success) throw new Error(resultado.message);

                renderizarDetalles(resultado.data);
                verModal.show();

            } catch (error) {
                mostrarMensaje(error.message, 'error');
            }
        }

        if (target.classList.contains('btn-editar')) {
            try {
                const response = await fetch(`${API_URL}?action=obtenerPorDocumento&numDocumento=${numDocumento}`);
                const resultado = await response.json();
                if (!resultado.success) throw new Error(resultado.message);
                
                const form = document.getElementById('form-editar-huesped');
                form.querySelector('#edit-numDocumentoOriginal').value = resultado.data.numDocumento;
                form.querySelector('#edit-tipoDocumento').value = resultado.data.tipoDocumento;
                form.querySelector('#edit-numDocumento').value = resultado.data.numDocumento;
                form.querySelector('#edit-nombres').value = resultado.data.nombres;
                form.querySelector('#edit-apellidos').value = resultado.data.apellidos;
                form.querySelector('#edit-correo').value = resultado.data.correo;
                form.querySelector('#edit-numTelefono').value = resultado.data.numTelefono;
                form.querySelector('#edit-sexo').value = resultado.data.sexo;
                editarModal.show();

            } catch (error) {
                mostrarMensaje(error.message, 'error');
            }
        }

        if (target.classList.contains('btn-eliminar')) {
            itemParaEliminar = numDocumento;
            document.getElementById('eliminar-id').textContent = numDocumento;
            document.getElementById('eliminar-info').textContent = target.dataset.info;
            eliminarModal.show();
        }
    });

    document.getElementById('guardar-edicion').addEventListener('click', async () => {
        const form = document.getElementById('form-editar-huesped');
        const formData = new FormData(form);

        try {
            const response = await fetch(`${API_URL}?action=actualizar`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!response.ok || !resultado.success) throw new Error(resultado.message);

            mostrarMensaje(resultado.message, 'success');
            editarModal.hide();
            cargarHuespedes(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        }
    });

    document.getElementById('confirmar-eliminacion').addEventListener('click', async () => {
        if (!itemParaEliminar) return;
        const formData = new FormData();
        formData.append('numDocumento', itemParaEliminar);

        try {
            const response = await fetch(`${API_URL}?action=eliminar`, { method: 'POST', body: formData });
            const resultado = await response.json();
            if (!response.ok || !resultado.success) throw new Error(resultado.message);

            mostrarMensaje(resultado.message, 'success');
            eliminarModal.hide();
            cargarHuespedes(currentPage, buscarInput.value.trim());
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            itemParaEliminar = null;
        }
    });

    // Verificación en tiempo real para el modal de edición
    const verificarExistenciaEdicion = async (campo, valor, feedbackElement, documentoActual) => {
        if (!valor) {
            feedbackElement.style.display = 'none';
            return;
        }
        try {
            const response = await fetch(`${API_URL}?action=verificar&campo=${campo}&valor=${encodeURIComponent(valor)}&documentoActual=${documentoActual}`);
            const resultado = await response.json();
            if (resultado.success && resultado.data.existe) {
                feedbackElement.textContent = `Este ${campo} ya está en uso.`;
                feedbackElement.className = 'correo-feedback invalid';
                feedbackElement.style.display = 'block';
            } else {
                feedbackElement.style.display = 'none';
            }
        } catch (error) {
            console.error(`Error al verificar ${campo}:`, error);
        }
    };

    document.getElementById('edit-numDocumento').addEventListener('keyup', (e) => {
        clearTimeout(debounceTimer);
        const docOriginal = document.getElementById('edit-numDocumentoOriginal').value;
        if (e.target.value !== docOriginal) {
            debounceTimer = setTimeout(() => {
                verificarExistenciaEdicion('numDocumento', e.target.value, document.getElementById('edit-documento-feedback'), docOriginal);
            }, 500);
        } else {
            document.getElementById('edit-documento-feedback').style.display = 'none';
        }
    });

    document.getElementById('edit-correo').addEventListener('keyup', (e) => {
        clearTimeout(debounceTimer);
        const docOriginal = document.getElementById('edit-numDocumentoOriginal').value;
        debounceTimer = setTimeout(() => {
            verificarExistenciaEdicion('correo', e.target.value, document.getElementById('edit-correo-feedback'), docOriginal);
        }, 500);
    });


    // --- FUNCIONES UTILITARIAS ---

    function formatearSexo(sexo) {
        const badges = {
            'Hombre': 'badge-hombre',
            'Mujer': 'badge-mujer',
            'Otro': 'badge-otro',
            'Prefiero no decirlo': 'badge-no-decir'
        };
        return `<span class="badge ${badges[sexo] || 'bg-secondary'}">${sexo}</span>`;
    }
});
