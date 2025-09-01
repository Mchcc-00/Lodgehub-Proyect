// listarColaboradores.js - Script mejorado para la gestión de colaboradores

// Variables globales
let colaboradores = [];
let colaboradoresFiltrados = [];
let paginaActual = 1;
const itemsPorPagina = 10;
let filtroActual = 'all';
let busquedaActual = '';
let documentoSeleccionado = '';

// Referencias DOM
const elementos = {
    tabla: document.getElementById('tabla-colaboradores'),
    buscarInput: document.getElementById('buscar-input'),
    buscarBtn: document.getElementById('buscar-btn'),
    refreshBtn: document.getElementById('refresh-btn'),
    paginacionContainer: document.getElementById('paginacion-container'),
    paginacion: document.getElementById('paginacion'),
    successMessage: document.getElementById('success-message'),
    errorMessage: document.getElementById('error-message'),
    successText: document.getElementById('success-text'),
    errorText: document.getElementById('error-text')
};

// Instancias de modales
let modales = {};

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    initializeApp();
});

function initializeApp() {
    // Verificar elementos DOM críticos
    if (!verificarElementosDOM()) {
        console.error('Elementos DOM críticos no encontrados');
        return;
    }
    
    // Inicializar modales
    inicializarModales();
    
    // Configurar eventos
    configurarEventos();
    
    // Cargar datos iniciales
    cargarColaboradores();
    
    // Restaurar estado de búsqueda
    restaurarEstadoBusqueda();
    
    console.log('Aplicación de colaboradores inicializada');
}

function verificarElementosDOM() {
    const elementosRequeridos = ['tabla', 'buscarInput', 'refreshBtn'];
    return elementosRequeridos.every(el => elementos[el] !== null);
}

function inicializarModales() {
    const modalElements = [
        'editarModal',
        'verModal', 
        'eliminarModal',
        'cambiarPasswordModal'
    ];
    
    modalElements.forEach(modalId => {
        const modalElement = document.getElementById(modalId);
        if (modalElement) {
            modales[modalId] = new bootstrap.Modal(modalElement);
        }
    });
}

function configurarEventos() {
    // Búsqueda
    if (elementos.buscarBtn) {
        elementos.buscarBtn.addEventListener('click', buscarColaboradores);
    }
    
    if (elementos.buscarInput) {
        elementos.buscarInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarColaboradores();
            }
        });
        
        // Búsqueda en tiempo real con debounce
        elementos.buscarInput.addEventListener('input', debounce(function() {
            busquedaActual = this.value.trim().toLowerCase();
            aplicarFiltros();
            guardarEstadoBusqueda();
        }, 300));
    }

    // Refresh
    if (elementos.refreshBtn) {
        elementos.refreshBtn.addEventListener('click', function() {
            refreshData();
        });
    }

    // Filtros
    document.querySelectorAll('.filter-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            filtroActual = this.dataset.filter;
            aplicarFiltros();
            guardarEstadoBusqueda();
            
            // Actualizar UI del filtro activo
            document.querySelectorAll('.filter-option').forEach(opt => opt.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Eventos de modales
    setupModalEvents();
    
    // Atajos de teclado
    setupKeyboardShortcuts();
}

function setupModalEvents() {
    // Guardar edición
    const guardarEdicionBtn = document.getElementById('guardar-edicion');
    if (guardarEdicionBtn) {
        guardarEdicionBtn.addEventListener('click', guardarEdicion);
    }

    // Confirmar eliminación
    const confirmarEliminacionBtn = document.getElementById('confirmar-eliminacion');
    if (confirmarEliminacionBtn) {
        confirmarEliminacionBtn.addEventListener('click', confirmarEliminacion);
    }

    // Cambiar contraseña
    const guardarPasswordBtn = document.getElementById('guardar-password');
    if (guardarPasswordBtn) {
        guardarPasswordBtn.addEventListener('click', cambiarPassword);
    }

    // Validación de contraseñas en modal
    const confirmarPasswordModal = document.getElementById('confirmar-password');
    if (confirmarPasswordModal) {
        confirmarPasswordModal.addEventListener('input', validarPasswordsModal);
    }
}

function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl + F para enfocar búsqueda
        if (e.ctrlKey && e.key === 'f') {
            e.preventDefault();
            if (elementos.buscarInput) {
                elementos.buscarInput.focus();
                elementos.buscarInput.select();
            }
        }
        
        // F5 o Ctrl + R para actualizar
        if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
            e.preventDefault();
            refreshData();
        }
        
        // Escape para cerrar modales
        if (e.key === 'Escape') {
            Object.values(modales).forEach(modal => {
                if (modal._element && modal._element.classList.contains('show')) {
                    modal.hide();
                }
            });
        }
    });
}

// Funciones principales de datos
async function cargarColaboradores() {
    try {
        mostrarCargando(true);
        
        const url = '../controllers/colaboradorController.php?action=listar';
        const response = await fetch(url, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });
        
        if (!response.ok) {
            throw new Error(`Error HTTP: ${response.status}`);
        }
        
        const resultado = await response.json();
        
        if (resultado.success) {
            colaboradores = resultado.data || [];
            colaboradoresFiltrados = [...colaboradores];
            mostrarColaboradores();
            mostrarMensaje('success', `${colaboradores.length} colaboradores cargados`);
        } else {
            throw new Error(resultado.message || 'Error al cargar colaboradores');
        }
        
    } catch (error) {
        console.error('Error al cargar colaboradores:', error);
        mostrarMensaje('error', 'Error al cargar colaboradores: ' + error.message);
        mostrarColaboradoresVacio('Error al cargar datos');
    } finally {
        mostrarCargando(false);
    }
}

function refreshData() {
    // Limpiar filtros y búsqueda
    busquedaActual = '';
    filtroActual = 'all';
    paginaActual = 1;
    
    if (elementos.buscarInput) {
        elementos.buscarInput.value = '';
    }
    
    // Limpiar estado guardado
    sessionStorage.removeItem('colaboradores_estado');
    
    // Recargar datos
    cargarColaboradores();
}

function buscarColaboradores() {
    if (elementos.buscarInput) {
        busquedaActual = elementos.buscarInput.value.trim().toLowerCase();
        aplicarFiltros();
        guardarEstadoBusqueda();
    }
}

function aplicarFiltros() {
    colaboradoresFiltrados = colaboradores.filter(colaborador => {
        // Filtro de búsqueda
        const coincideBusqueda = !busquedaActual || 
            colaborador.numDocumento.toLowerCase().includes(busquedaActual) ||
            colaborador.nombres.toLowerCase().includes(busquedaActual) ||
            colaborador.apellidos.toLowerCase().includes(busquedaActual) ||
            colaborador.correo.toLowerCase().includes(busquedaActual) ||
            (colaborador.nombres + ' ' + colaborador.apellidos).toLowerCase().includes(busquedaActual);

        // Filtro por categoría
        const coincideFiltro = filtroActual === 'all' ||
            colaborador.roles === filtroActual ||
            colaborador.tipoDocumento === filtroActual ||
            colaborador.sexo === filtroActual;

        return coincideBusqueda && coincideFiltro;
    });

    paginaActual = 1;
    mostrarColaboradores();
}

// Funciones de visualización
function mostrarColaboradores() {
    if (!elementos.tabla) return;
    
    if (colaboradoresFiltrados.length === 0) {
        mostrarColaboradoresVacio();
        return;
    }

    const inicio = (paginaActual - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    const colaboradoresPagina = colaboradoresFiltrados.slice(inicio, fin);

    elementos.tabla.innerHTML = colaboradoresPagina.map(colaborador => 
        generarFilaColaborador(colaborador)
    ).join('');

    configurarPaginacion();
    
    // Mostrar información de resultados
    mostrarInfoResultados();
}

function generarFilaColaborador(colaborador) {
    const edad = calcularEdad(colaborador.fechaNacimiento);
    const fotoUrl = colaborador.foto ? 
        `../../public/${colaborador.foto}` : 
        '../../public/assets/images/default-user.png';
    
    return `
        <tr data-documento="${colaborador.numDocumento}" class="colaborador-row">
            <td data-label="Documento">
                <div class="colaborador-info">
                    <img src="${fotoUrl}" alt="Foto" class="colaborador-foto" onerror="this.src='../../public/assets/images/default-user.png'">
                    <div class="colaborador-datos">
                        <strong>${colaborador.numDocumento}</strong>
                        <small class="text-muted">${colaborador.tipoDocumento}</small>
                    </div>
                </div>
            </td>
            <td data-label="Tipo Doc.">${colaborador.tipoDocumento}</td>
            <td data-label="Nombres">${colaborador.nombres}</td>
            <td data-label="Apellidos">${colaborador.apellidos}</td>
            <td data-label="Correo">
                <div class="text-truncate-custom" title="${colaborador.correo}">
                    ${colaborador.correo}
                </div>
            </td>
            <td data-label="Teléfono">${colaborador.numTelefono}</td>
            <td data-label="Sexo">${colaborador.sexo}</td>
            <td data-label="Fecha Nac.">${formatearFecha(colaborador.fechaNacimiento)}<br>
                <small class="text-muted">${edad} años</small>
            </td>
            <td data-label="Rol">
                <span class="badge ${getRolBadgeClass(colaborador.roles)}">
                    ${colaborador.roles}
                </span>
            </td>
            <td data-label="Acciones">
                <div class="acciones-tabla">
                    <button class="btn btn-sm btn-info shadow-hover" 
                            onclick="verColaborador('${colaborador.numDocumento}')" 
                            title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning shadow-hover" 
                            onclick="editarColaborador('${colaborador.numDocumento}')" 
                            title="Editar colaborador">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary shadow-hover" 
                            onclick="cambiarPasswordColaborador('${colaborador.numDocumento}')" 
                            title="Cambiar contraseña">
                        <i class="fas fa-key"></i>
                    </button>
                    <button class="btn btn-sm btn-danger shadow-hover" 
                            onclick="eliminarColaborador('${colaborador.numDocumento}')" 
                            title="Eliminar colaborador">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `;
}

function mostrarColaboradoresVacio(mensaje = 'No se encontraron colaboradores') {
    elementos.tabla.innerHTML = `
        <tr>
            <td colspan="10" class="text-center py-5">
                <div class="no-data">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">${mensaje}</h5>
                    <p class="text-muted">
                        ${busquedaActual || filtroActual !== 'all' ? 
                            'Intenta ajustar los filtros de búsqueda' : 
                            'Comienza agregando colaboradores al sistema'}
                    </p>
                    ${!busquedaActual && filtroActual === 'all' ? 
                        '<a href="crearMisColaboradores.php" class="btn btn-primary mt-2"><i class="fas fa-plus"></i> Crear Primer Colaborador</a>' : 
                        '<button class="btn btn-outline-primary mt-2" onclick="limpiarFiltros()"><i class="fas fa-filter"></i> Limpiar Filtros</button>'}
                </div>
            </td>
        </tr>
    `;
    
    if (elementos.paginacionContainer) {
        elementos.paginacionContainer.style.display = 'none';
    }
}

function mostrarInfoResultados() {
    const total = colaboradoresFiltrados.length;
    const inicio = (paginaActual - 1) * itemsPorPagina + 1;
    const fin = Math.min(paginaActual * itemsPorPagina, total);
    
    // Mostrar información en el header o crear un elemento
    let infoElement = document.getElementById('resultados-info');
    if (!infoElement) {
        infoElement = document.createElement('div');
        infoElement.id = 'resultados-info';
        infoElement.className = 'alert alert-info';
        elementos.tabla.parentNode.insertBefore(infoElement, elementos.tabla);
    }
    
    if (total > 0) {
        infoElement.innerHTML = `
            <i class="fas fa-info-circle"></i> 
            Mostrando ${inicio}-${fin} de ${total} colaboradores
            ${busquedaActual ? ` | Búsqueda: "${busquedaActual}"` : ''}
            ${filtroActual !== 'all' ? ` | Filtro: ${filtroActual}` : ''}
        `;
        infoElement.style.display = 'block';
    } else {
        infoElement.style.display = 'none';
    }
}

function mostrarCargando(mostrar) {
    if (!elementos.tabla) return;
    
    if (mostrar) {
        elementos.tabla.innerHTML = `
            <tr>
                <td colspan="10" class="text-center py-5">
                    <div class="loading">
                        <i class="fas fa-spinner fa-spin fa-2x text-primary mb-3"></i>
                        <h5 class="text-muted">Cargando colaboradores...</h5>
                        <div class="progress" style="width: 200px; margin: 0 auto;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                 style="width: 100%"></div>
                        </div>
                    </div>
                </td>
            </tr>
        `;
    }
}

// Funciones de paginación
function configurarPaginacion() {
    if (!elementos.paginacionContainer || !elementos.paginacion) return;
    
    const totalPaginas = Math.ceil(colaboradoresFiltrados.length / itemsPorPagina);
    
    if (totalPaginas <= 1) {
        elementos.paginacionContainer.style.display = 'none';
        return;
    }

    elementos.paginacionContainer.style.display = 'block';
    
    let html = '';
    
    // Botón anterior
    html += `
        <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual - 1})" 
               ${paginaActual === 1 ? 'tabindex="-1"' : ''}>
                <i class="fas fa-chevron-left"></i> Anterior
            </a>
        </li>
    `;
    
    // Páginas numéricas
    const rango = generarRangoPaginacion(paginaActual, totalPaginas);
    rango.forEach(pagina => {
        if (pagina === '...') {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        } else {
            html += `
                <li class="page-item ${pagina === paginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${pagina})">${pagina}</a>
                </li>
            `;
        }
    });
    
    // Botón siguiente
    html += `
        <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual + 1})"
               ${paginaActual === totalPaginas ? 'tabindex="-1"' : ''}>
                Siguiente <i class="fas fa-chevron-right"></i>
            </a>
        </li>
    `;
    
    elementos.paginacion.innerHTML = html;
}

function generarRangoPaginacion(actual, total) {
    const rango = [];
    const delta = 2; // Páginas antes y después de la actual
    
    // Siempre mostrar primera página
    rango.push(1);
    
    // Calcular rango alrededor de la página actual
    const inicio = Math.max(2, actual - delta);
    const fin = Math.min(total - 1, actual + delta);
    
    // Agregar puntos suspensivos si hay gap
    if (inicio > 2) {
        rango.push('...');
    }
    
    // Agregar páginas del rango
    for (let i = inicio; i <= fin; i++) {
        if (i !== 1 && i !== total) {
            rango.push(i);
        }
    }
    
    // Agregar puntos suspensivos si hay gap
    if (fin < total - 1) {
        rango.push('...');
    }
    
    // Siempre mostrar última página (si es diferente de la primera)
    if (total > 1) {
        rango.push(total);
    }
    
    return rango;
}

function cambiarPagina(pagina) {
    const totalPaginas = Math.ceil(colaboradoresFiltrados.length / itemsPorPagina);
    
    if (pagina >= 1 && pagina <= totalPaginas && pagina !== paginaActual) {
        paginaActual = pagina;
        mostrarColaboradores();
        guardarEstadoBusqueda();
        
        // Scroll suave al inicio de la tabla
        elementos.tabla.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

// Funciones de modales
function verColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    const detalles = document.getElementById('detalles-colaborador');
    const edad = calcularEdad(colaborador.fechaNacimiento);
    const fotoUrl = colaborador.foto ? 
        `../../public/${colaborador.foto}` : 
        '../../public/assets/images/default-user.png';
    
    detalles.innerHTML = `
        <div class="detalle-colaborador">
            <div class="detalle-foto">
                <img src="${fotoUrl}" alt="Foto de perfil" 
                     onerror="this.src='../../public/assets/images/default-user.png'">
                <div class="mt-2">
                    <span class="badge ${getRolBadgeClass(colaborador.roles)} fs-6">
                        ${colaborador.roles}
                    </span>
                </div>
            </div>
            <div class="detalle-info">
                <div class="info-group">
                    <span class="info-label">Documento:</span>
                    <span class="info-value">${colaborador.numDocumento}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Tipo:</span>
                    <span class="info-value">${colaborador.tipoDocumento}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Nombre Completo:</span>
                    <span class="info-value">${colaborador.nombres} ${colaborador.apellidos}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Correo:</span>
                    <span class="info-value">
                        <a href="mailto:${colaborador.correo}">${colaborador.correo}</a>
                    </span>
                </div>
                <div class="info-group">
                    <span class="info-label">Teléfono:</span>
                    <span class="info-value">
                        <a href="tel:${colaborador.numTelefono}">${colaborador.numTelefono}</a>
                    </span>
                </div>
                <div class="info-group">
                    <span class="info-label">Sexo:</span>
                    <span class="info-value">${colaborador.sexo}</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Fecha de Nacimiento:</span>
                    <span class="info-value">${formatearFecha(colaborador.fechaNacimiento)} (${edad} años)</span>
                </div>
                <div class="info-group">
                    <span class="info-label">Solicitar Cambio:</span>
                    <span class="info-value">
                        <span class="badge ${colaborador.solicitarContraseña === '1' ? 'bg-warning' : 'bg-success'}">
                            ${colaborador.solicitarContraseña === '1' ? 'Sí' : 'No'}
                        </span>
                    </span>
                </div>
            </div>
        </div>
    `;
    
    if (modales.verModal) {
        modales.verModal.show();
    }
}

function editarColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    documentoSeleccionado = documento;
    
    // Llenar formulario de edición
    const campos = [
        'edit-documento-original',
        'edit-numDocumento',
        'edit-tipoDocumento',
        'edit-nombres',
        'edit-apellidos',
        'edit-correo',
        'edit-numTelefono',
        'edit-sexo',
        'edit-fechaNacimiento',
        'edit-roles',
        'edit-solicitarContraseña'
    ];
    
    campos.forEach(campo => {
        const elemento = document.getElementById(campo);
        if (elemento) {
            const propiedad = campo.replace('edit-', '').replace('-', '');
            
            if (campo === 'edit-documento-original') {
                elemento.value = colaborador.numDocumento;
            } else if (campo === 'edit-solicitarContraseña') {
                elemento.value = colaborador.solicitarContraseña || '0';
            } else {
                elemento.value = colaborador[propiedad] || '';
            }
        }
    });
    
    // Limpiar contraseña
    const passwordField = document.getElementById('edit-password');
    if (passwordField) {
        passwordField.value = '';
    }
    
    if (modales.editarModal) {
        modales.editarModal.show();
    }
}

async function guardarEdicion() {
    const form = document.getElementById('form-editar');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const datos = Object.fromEntries(formData);
    datos.documentoOriginal = document.getElementById('edit-documento-original').value;

    const guardarBtn = document.getElementById('guardar-edicion');
    const originalText = guardarBtn.innerHTML;
    
    try {
        setLoadingState(guardarBtn, true, 'Guardando...');

        const response = await fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ ...datos, action: 'actualizar' })
        });

        const resultado = await response.json();

        if (resultado.success) {
            // Actualizar datos locales
            const index = colaboradores.findIndex(c => c.numDocumento === datos.documentoOriginal);
            if (index !== -1) {
                colaboradores[index] = { ...colaboradores[index], ...datos };
                
                // Si cambió el documento, actualizar la clave
                if (datos.numDocumento !== datos.documentoOriginal) {
                    colaboradores[index].numDocumento = datos.numDocumento;
                }
            }
            
            aplicarFiltros();
            modales.editarModal.hide();
            mostrarMensaje('success', 'Colaborador actualizado correctamente');
            resaltarFila(datos.numDocumento);
        } else {
            mostrarMensaje('error', resultado.message || 'Error al actualizar colaborador');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('error', 'Error de conexión al actualizar colaborador');
    } finally {
        setLoadingState(guardarBtn, false, originalText);
    }
}

function eliminarColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    documentoSeleccionado = documento;
    
    // Configurar modal de confirmación
    const infoElement = document.getElementById('eliminar-info');
    const documentoElement = document.getElementById('eliminar-documento');
    
    if (infoElement && documentoElement) {
        infoElement.innerHTML = `${colaborador.nombres} ${colaborador.apellidos}`;
        documentoElement.textContent = documento;
    }
    
    if (modales.eliminarModal) {
        modales.eliminarModal.show();
    }
}

async function confirmarEliminacion() {
    const documento = documentoSeleccionado;
    
    if (!documento) {
        mostrarMensaje('error', 'No se ha seleccionado ningún colaborador');
        return;
    }
    
    const confirmarBtn = document.getElementById('confirmar-eliminacion');
    const originalText = confirmarBtn.innerHTML;
    
    try {
        setLoadingState(confirmarBtn, true, 'Eliminando...');

        const response = await fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ documento, action: 'eliminar' })
        });

        const resultado = await response.json();

        if (resultado.success) {
            // Remover de datos locales
            colaboradores = colaboradores.filter(c => c.numDocumento !== documento);
            aplicarFiltros();
            modales.eliminarModal.hide();
            mostrarMensaje('success', 'Colaborador eliminado correctamente');
        } else {
            mostrarMensaje('error', resultado.message || 'Error al eliminar colaborador');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('error', 'Error de conexión al eliminar colaborador');
    } finally {
        setLoadingState(confirmarBtn, false, originalText);
        documentoSeleccionado = '';
    }
}

function cambiarPasswordColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    documentoSeleccionado = documento;
    
    // Limpiar formulario
    const form = document.getElementById('form-cambiar-password');
    if (form) {
        form.reset();
    }
    
    const passwordDocumento = document.getElementById('password-documento');
    if (passwordDocumento) {
        passwordDocumento.value = documento;
    }
    
    if (modales.cambiarPasswordModal) {
        modales.cambiarPasswordModal.show();
    }
}

async function cambiarPassword() {
    const form = document.getElementById('form-cambiar-password');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const nuevaPassword = document.getElementById('nueva-password').value;
    const confirmarPassword = document.getElementById('confirmar-password').value;
    const documento = document.getElementById('password-documento').value;
    const solicitarCambio = document.getElementById('solicitar-cambio').checked;

    // Validaciones adicionales
    if (nuevaPassword !== confirmarPassword) {
        mostrarMensaje('error', 'Las contraseñas no coinciden');
        return;
    }

    if (nuevaPassword.length < 6) {
        mostrarMensaje('error', 'La contraseña debe tener al menos 6 caracteres');
        return;
    }

    const guardarBtn = document.getElementById('guardar-password');
    const originalText = guardarBtn.innerHTML;

    try {
        setLoadingState(guardarBtn, true, 'Cambiando...');

        const response = await fetch('../controllers/colaboradorController.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: 'cambiarPassword',
                documento,
                nuevaPassword,
                solicitarCambio
            })
        });

        const resultado = await response.json();

        if (resultado.success) {
            // Actualizar datos locales
            const index = colaboradores.findIndex(c => c.numDocumento === documento);
            if (index !== -1) {
                colaboradores[index].solicitarContraseña = solicitarCambio ? '1' : '0';
            }
            
            modales.cambiarPasswordModal.hide();
            mostrarMensaje('success', 'Contraseña actualizada correctamente');
            aplicarFiltros();
        } else {
            mostrarMensaje('error', resultado.message || 'Error al cambiar contraseña');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('error', 'Error de conexión al cambiar contraseña');
    } finally {
        setLoadingState(guardarBtn, false, originalText);
    }
}

function validarPasswordsModal() {
    const nuevaPassword = document.getElementById('nueva-password').value;
    const confirmarPassword = document.getElementById('confirmar-password').value;
    const confirmarInput = document.getElementById('confirmar-password');
    
    if (confirmarPassword && nuevaPassword !== confirmarPassword) {
        confirmarInput.setCustomValidity('Las contraseñas no coinciden');
        confirmarInput.classList.add('is-invalid');
        confirmarInput.classList.remove('is-valid');
    } else {
        confirmarInput.setCustomValidity('');
        if (confirmarPassword) {
            confirmarInput.classList.add('is-valid');
            confirmarInput.classList.remove('is-invalid');
        }
    }
}

// Funciones de utilidades
function formatearFecha(fecha) {
    if (!fecha) return '-';
    
    const opciones = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    };
    
    return new Date(fecha).toLocaleDateString('es-CO', opciones);
}

function calcularEdad(fechaNacimiento) {
    const hoy = new Date();
    const fechaNac = new Date(fechaNacimiento);
    let edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mesActual = hoy.getMonth();
    const mesNac = fechaNac.getMonth();
    
    if (mesActual < mesNac || (mesActual === mesNac && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }
    
    return edad;
}

function getRolBadgeClass(rol) {
    const clases = {
        'Administrador': 'bg-danger',
        'Colaborador': 'bg-primary',
        'Usuario': 'bg-secondary'
    };
    return clases[rol] || 'bg-secondary';
}

function mostrarMensaje(tipo, texto) {
    hideMessages();
    
    if (tipo === 'success' && elementos.successMessage && elementos.successText) {
        elementos.successText.textContent = texto;
        elementos.successMessage.style.display = 'block';
        elementos.successMessage.scrollIntoView({ behavior: 'smooth' });
        
        setTimeout(() => {
            elementos.successMessage.style.display = 'none';
        }, 5000);
    } else if (tipo === 'error' && elementos.errorMessage && elementos.errorText) {
        elementos.errorText.innerHTML = texto;
        elementos.errorMessage.style.display = 'block';
        elementos.errorMessage.scrollIntoView({ behavior: 'smooth' });
        
        setTimeout(() => {
            elementos.errorMessage.style.display = 'none';
        }, 7000);
    }
}

function hideMessages() {
    if (elementos.successMessage) elementos.successMessage.style.display = 'none';
    if (elementos.errorMessage) elementos.errorMessage.style.display = 'none';
}

function setLoadingState(button, isLoading, loadingText = 'Cargando...') {
    if (isLoading) {
        button.disabled = true;
        button.dataset.originalText = button.innerHTML;
        button.innerHTML = `<i class="fas fa-spinner fa-spin"></i> ${loadingText}`;
        button.classList.add('loading');
    } else {
        button.disabled = false;
        button.innerHTML = button.dataset.originalText || loadingText;
        button.classList.remove('loading');
    }
}

function resaltarFila(documento) {
    const fila = document.querySelector(`tr[data-documento="${documento}"]`);
    if (fila) {
        fila.classList.add('table-warning');
        setTimeout(() => {
            fila.classList.remove('table-warning');
            fila.classList.add('table-success');
            setTimeout(() => {
                fila.classList.remove('table-success');
            }, 2000);
        }, 500);
    }
}

// Funciones de persistencia
function guardarEstadoBusqueda() {
    const estado = {
        busqueda: busquedaActual,
        filtro: filtroActual,
        pagina: paginaActual,
        timestamp: Date.now()
    };
    sessionStorage.setItem('colaboradores_estado', JSON.stringify(estado));
}

function restaurarEstadoBusqueda() {
    try {
        const estadoGuardado = sessionStorage.getItem('colaboradores_estado');
        if (estadoGuardado) {
            const estado = JSON.parse(estadoGuardado);
            
            // Verificar que no sea muy antiguo (1 hora máximo)
            if (Date.now() - estado.timestamp < 3600000) {
                busquedaActual = estado.busqueda || '';
                filtroActual = estado.filtro || 'all';
                paginaActual = estado.pagina || 1;
                
                if (elementos.buscarInput) {
                    elementos.buscarInput.value = busquedaActual;
                }
                
                // Marcar filtro activo en UI
                document.querySelectorAll('.filter-option').forEach(opt => {
                    opt.classList.toggle('active', opt.dataset.filter === filtroActual);
                });
            }
        }
    } catch (error) {
        console.warn('Error al restaurar estado de búsqueda:', error);
    }
}

function limpiarFiltros() {
    busquedaActual = '';
    filtroActual = 'all';
    paginaActual = 1;
    
    if (elementos.buscarInput) {
        elementos.buscarInput.value = '';
    }
    
    document.querySelectorAll('.filter-option').forEach(opt => {
        opt.classList.remove('active');
    });
    
    aplicarFiltros();
    sessionStorage.removeItem('colaboradores_estado');
    mostrarMensaje('success', 'Filtros limpiados');
}

// Funciones de exportación
function exportarColaboradores(formato = 'csv') {
    if (colaboradoresFiltrados.length === 0) {
        mostrarMensaje('error', 'No hay datos para exportar');
        return;
    }

    const datos = colaboradoresFiltrados.map(colaborador => ({
        'Documento': colaborador.numDocumento,
        'Tipo de Documento': colaborador.tipoDocumento,
        'Nombres': colaborador.nombres,
        'Apellidos': colaborador.apellidos,
        'Correo': colaborador.correo,
        'Teléfono': colaborador.numTelefono,
        'Sexo': colaborador.sexo,
        'Fecha de Nacimiento': colaborador.fechaNacimiento,
        'Edad': calcularEdad(colaborador.fechaNacimiento),
        'Rol': colaborador.roles
    }));

    if (formato === 'csv') {
        exportarCSV(datos);
    } else if (formato === 'json') {
        exportarJSON(datos);
    }
}

function exportarCSV(datos) {
    const headers = Object.keys(datos[0]);
    let csv = headers.join(',') + '\n';
    
    datos.forEach(fila => {
        const valores = headers.map(header => {
            const valor = fila[header] || '';
            return `"${valor.toString().replace(/"/g, '""')}"`;
        });
        csv += valores.join(',') + '\n';
    });
    
    descargarArchivo(csv, `colaboradores_${obtenerFechaHoy()}.csv`, 'text/csv');
    mostrarMensaje('success', 'Datos exportados a CSV correctamente');
}

function exportarJSON(datos) {
    const json = JSON.stringify(datos, null, 2);
    descargarArchivo(json, `colaboradores_${obtenerFechaHoy()}.json`, 'application/json');
    mostrarMensaje('success', 'Datos exportados a JSON correctamente');
}

function descargarArchivo(contenido, nombreArchivo, tipoMime) {
    const blob = new Blob([contenido], { type: tipoMime + ';charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', nombreArchivo);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    URL.revokeObjectURL(url);
}

function obtenerFechaHoy() {
    return new Date().toISOString().split('T')[0];
}

// Función debounce
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Funciones globales para compatibilidad
window.verColaborador = verColaborador;
window.editarColaborador = editarColaborador;
window.eliminarColaborador = eliminarColaborador;
window.cambiarPasswordColaborador = cambiarPasswordColaborador;
window.cambiarPagina = cambiarPagina;
window.exportarColaboradores = exportarColaboradores;
window.limpiarFiltros = limpiarFiltros;

// Manejo de errores globales
window.addEventListener('error', function(e) {
    console.error('Error global:', e.error);
    mostrarMensaje('error', 'Ha ocurrido un error inesperado');
});

// Monitoreo de conectividad
window.addEventListener('online', function() {
    mostrarMensaje('success', 'Conexión restaurada');
});

window.addEventListener('offline', function() {
    mostrarMensaje('error', 'Sin conexión a internet');
});

// Auto-actualización cada 5 minutos (opcional)
setInterval(function() {
    if (document.visibilityState === 'visible') {
        cargarColaboradores();
    }
}, 5 * 60 * 1000);

console.log('Script de listado de colaboradores cargado correctamente');