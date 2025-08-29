// Variables globales
let colaboradores = [];
let colaboradoresFiltrados = [];
let paginaActual = 1;
const itemsPorPagina = 10;
let filtroActual = 'all';
let busquedaActual = '';

// DOM Elements
const tabla = document.getElementById('tabla-colaboradores');
const buscarInput = document.getElementById('buscar-input');
const buscarBtn = document.getElementById('buscar-btn');
const refreshBtn = document.getElementById('refresh-btn');
const paginacionContainer = document.getElementById('paginacion-container');
const paginacion = document.getElementById('paginacion');

// Messages
const successMessage = document.getElementById('success-message');
const errorMessage = document.getElementById('error-message');
const successText = document.getElementById('success-text');
const errorText = document.getElementById('error-text');

// Modales
const editarModal = new bootstrap.Modal(document.getElementById('editarModal'));
const verModal = new bootstrap.Modal(document.getElementById('verModal'));
const eliminarModal = new bootstrap.Modal(document.getElementById('eliminarModal'));
const cambiarPasswordModal = new bootstrap.Modal(document.getElementById('cambiarPasswordModal'));

// Inicialización
document.addEventListener('DOMContentLoaded', function() {
    cargarColaboradores();
    configurarEventos();
});

// Configurar eventos
function configurarEventos() {
    // Búsqueda
    buscarBtn.addEventListener('click', buscarColaboradores);
    buscarInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            buscarColaboradores();
        }
    });

    // Refresh
    refreshBtn.addEventListener('click', function() {
        cargarColaboradores();
        busquedaActual = '';
        filtroActual = 'all';
        buscarInput.value = '';
        mostrarMensaje('success', 'Datos actualizados correctamente');
    });

    // Filtros
    document.querySelectorAll('.filter-option').forEach(option => {
        option.addEventListener('click', function(e) {
            e.preventDefault();
            filtroActual = this.dataset.filter;
            aplicarFiltros();
        });
    });

    // Editar colaborador
    document.getElementById('guardar-edicion').addEventListener('click', guardarEdicion);

    // Eliminar colaborador
    document.getElementById('confirmar-eliminacion').addEventListener('click', confirmarEliminacion);

    // Cambiar contraseña
    document.getElementById('guardar-password').addEventListener('click', cambiarPassword);

    // Validación de contraseñas
    document.getElementById('confirmar-password').addEventListener('input', validarPasswords);
}

// Cargar colaboradores desde el servidor
async function cargarColaboradores() {
    try {
        mostrarCargando(true);
        
        // Simulación de carga de datos (reemplazar con llamada real al backend)
        const response = await fetch('../../backend/controllers/colaboradoresController.php?action=listar');
        
        if (!response.ok) {
            throw new Error('Error al cargar colaboradores');
        }
        
        colaboradores = await response.json();
        
        // Datos de ejemplo para desarrollo
        if (!colaboradores || colaboradores.length === 0) {
            colaboradores = [
                {
                    numDocumento: '12345678',
                    tipoDocumento: 'Cédula de Ciudadanía',
                    nombres: 'Juan Carlos',
                    apellidos: 'González Pérez',
                    correo: 'juan.gonzalez@lodgehub.com',
                    numTelefono: '3001234567',
                    sexo: 'Hombre',
                    fechaNacimiento: '1985-03-15',
                    roles: 'Administrador',
                    fechaCreacion: '2024-01-15'
                },
                {
                    numDocumento: '87654321',
                    tipoDocumento: 'Cédula de Ciudadanía',
                    nombres: 'María Elena',
                    apellidos: 'Rodríguez Silva',
                    correo: 'maria.rodriguez@lodgehub.com',
                    numTelefono: '3007654321',
                    sexo: 'Mujer',
                    fechaNacimiento: '1990-07-22',
                    roles: 'Colaborador',
                    fechaCreacion: '2024-02-10'
                },
                {
                    numDocumento: 'CE1234567',
                    tipoDocumento: 'Cedula de Extranjeria',
                    nombres: 'Luis Alberto',
                    apellidos: 'Martínez López',
                    correo: 'luis.martinez@lodgehub.com',
                    numTelefono: '3009876543',
                    sexo: 'Hombre',
                    fechaNacimiento: '1988-11-08',
                    roles: 'Usuario',
                    fechaCreacion: '2024-03-05'
                }
            ];
        }
        
        colaboradoresFiltrados = [...colaboradores];
        mostrarColaboradores();
        
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('error', 'Error al cargar los colaboradores: ' + error.message);
        mostrarCargando(false);
    }
}

// Mostrar estado de carga
function mostrarCargando(mostrar) {
    if (mostrar) {
        tabla.innerHTML = `
            <tr>
                <td colspan="10" class="loading">
                    <i class="fas fa-spinner fa-spin"></i> Cargando colaboradores...
                </td>
            </tr>
        `;
    }
}

// Buscar colaboradores
function buscarColaboradores() {
    busquedaActual = buscarInput.value.trim().toLowerCase();
    aplicarFiltros();
}

// Aplicar filtros y búsqueda
function aplicarFiltros() {
    colaboradoresFiltrados = colaboradores.filter(colaborador => {
        // Filtro de búsqueda
        const coincideBusqueda = !busquedaActual || 
            colaborador.numDocumento.toLowerCase().includes(busquedaActual) ||
            colaborador.nombres.toLowerCase().includes(busquedaActual) ||
            colaborador.apellidos.toLowerCase().includes(busquedaActual) ||
            colaborador.correo.toLowerCase().includes(busquedaActual);

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

// Mostrar colaboradores en la tabla
function mostrarColaboradores() {
    if (colaboradoresFiltrados.length === 0) {
        tabla.innerHTML = `
            <tr>
                <td colspan="10" class="no-data">
                    <i class="fas fa-users"></i> No se encontraron colaboradores
                </td>
            </tr>
        `;
        paginacionContainer.style.display = 'none';
        return;
    }

    const inicio = (paginaActual - 1) * itemsPorPagina;
    const fin = inicio + itemsPorPagina;
    const colaboradoresPagina = colaboradoresFiltrados.slice(inicio, fin);

    tabla.innerHTML = colaboradoresPagina.map(colaborador => `
        <tr data-documento="${colaborador.numDocumento}">
            <td><strong>${colaborador.numDocumento}</strong></td>
            <td>${colaborador.tipoDocumento}</td>
            <td>${colaborador.nombres}</td>
            <td>${colaborador.apellidos}</td>
            <td>${colaborador.correo}</td>
            <td>${colaborador.numTelefono}</td>
            <td>${colaborador.sexo}</td>
            <td>${formatearFecha(colaborador.fechaNacimiento)}</td>
            <td>
                <span class="badge ${getRolBadgeClass(colaborador.roles)}">
                    ${colaborador.roles}
                </span>
            </td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-sm btn-info" onclick="verColaborador('${colaborador.numDocumento}')" 
                            title="Ver detalles">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="editarColaborador('${colaborador.numDocumento}')" 
                            title="Editar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-secondary" onclick="cambiarPasswordColaborador('${colaborador.numDocumento}')" 
                            title="Cambiar contraseña">
                        <i class="fas fa-key"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="eliminarColaborador('${colaborador.numDocumento}')" 
                            title="Eliminar">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');

    configurarPaginacion();
}

// Configurar paginación
function configurarPaginacion() {
    const totalPaginas = Math.ceil(colaboradoresFiltrados.length / itemsPorPagina);
    
    if (totalPaginas <= 1) {
        paginacionContainer.style.display = 'none';
        return;
    }

    paginacionContainer.style.display = 'block';
    
    let html = '';
    
    // Botón anterior
    html += `
        <li class="page-item ${paginaActual === 1 ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual - 1})">Anterior</a>
        </li>
    `;
    
    // Páginas
    for (let i = 1; i <= totalPaginas; i++) {
        if (i === 1 || i === totalPaginas || (i >= paginaActual - 2 && i <= paginaActual + 2)) {
            html += `
                <li class="page-item ${i === paginaActual ? 'active' : ''}">
                    <a class="page-link" href="#" onclick="cambiarPagina(${i})">${i}</a>
                </li>
            `;
        } else if (i === paginaActual - 3 || i === paginaActual + 3) {
            html += '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }
    
    // Botón siguiente
    html += `
        <li class="page-item ${paginaActual === totalPaginas ? 'disabled' : ''}">
            <a class="page-link" href="#" onclick="cambiarPagina(${paginaActual + 1})">Siguiente</a>
        </li>
    `;
    
    paginacion.innerHTML = html;
}

// Cambiar página
function cambiarPagina(pagina) {
    const totalPaginas = Math.ceil(colaboradoresFiltrados.length / itemsPorPagina);
    
    if (pagina >= 1 && pagina <= totalPaginas) {
        paginaActual = pagina;
        mostrarColaboradores();
    }
}

// Ver colaborador
function verColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    const detalles = document.getElementById('detalles-colaborador');
    detalles.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <h6><i class="fas fa-id-card"></i> Información Personal</h6>
                <p><strong>Documento:</strong> ${colaborador.numDocumento}</p>
                <p><strong>Tipo de Documento:</strong> ${colaborador.tipoDocumento}</p>
                <p><strong>Nombres:</strong> ${colaborador.nombres}</p>
                <p><strong>Apellidos:</strong> ${colaborador.apellidos}</p>
                <p><strong>Sexo:</strong> ${colaborador.sexo}</p>
                <p><strong>Fecha de Nacimiento:</strong> ${formatearFecha(colaborador.fechaNacimiento)}</p>
            </div>
            <div class="col-md-6">
                <h6><i class="fas fa-contact-card"></i> Información de Contacto</h6>
                <p><strong>Correo:</strong> ${colaborador.correo}</p>
                <p><strong>Teléfono:</strong> ${colaborador.numTelefono}</p>
                <br>
                <h6><i class="fas fa-user-tag"></i> Información del Sistema</h6>
                <p><strong>Rol:</strong> 
                    <span class="badge ${getRolBadgeClass(colaborador.roles)}">${colaborador.roles}</span>
                </p>
                <p><strong>Fecha de Registro:</strong> ${formatearFecha(colaborador.fechaCreacion)}</p>
            </div>
        </div>
    `;
    
    verModal.show();
}

// Editar colaborador
function editarColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    // Llenar formulario de edición
    document.getElementById('edit-documento-original').value = colaborador.numDocumento;
    document.getElementById('edit-numDocumento').value = colaborador.numDocumento;
    document.getElementById('edit-tipoDocumento').value = colaborador.tipoDocumento;
    document.getElementById('edit-nombres').value = colaborador.nombres;
    document.getElementById('edit-apellidos').value = colaborador.apellidos;
    document.getElementById('edit-correo').value = colaborador.correo;
    document.getElementById('edit-numTelefono').value = colaborador.numTelefono;
    document.getElementById('edit-sexo').value = colaborador.sexo;
    document.getElementById('edit-fechaNacimiento').value = colaborador.fechaNacimiento;
    document.getElementById('edit-roles').value = colaborador.roles;
    
    // Limpiar contraseña
    document.getElementById('edit-password').value = '';
    document.getElementById('edit-solicitarContraseña').value = '0';
    
    editarModal.show();
}

// Guardar edición
async function guardarEdicion() {
    const form = document.getElementById('form-editar');
    
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    const formData = new FormData(form);
    const datos = Object.fromEntries(formData);
    datos.documentoOriginal = document.getElementById('edit-documento-original').value;

    try {
        const response = await fetch('../../backend/controllers/colaboradoresController.php?action=editar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(datos)
        });

        const resultado = await response.json();

        if (resultado.success) {
            // Actualizar datos locales
            const index = colaboradores.findIndex(c => c.numDocumento === datos.documentoOriginal);
            if (index !== -1) {
                colaboradores[index] = { ...colaboradores[index], ...datos };
            }
            
            aplicarFiltros();
            editarModal.hide();
            mostrarMensaje('success', 'Colaborador actualizado correctamente');
        } else {
            mostrarMensaje('error', resultado.message || 'Error al actualizar colaborador');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('error', 'Error de conexión al actualizar colaborador');
    }
}

// Eliminar colaborador
function eliminarColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    // Configurar modal de confirmación
    document.getElementById('eliminar-info').textContent = 
        `${colaborador.nombres} ${colaborador.apellidos}`;
    document.getElementById('eliminar-documento').textContent = documento;
    
    // Guardar documento para eliminar
    document.getElementById('confirmar-eliminacion').dataset.documento = documento;
    
    eliminarModal.show();
}

// Confirmar eliminación
async function confirmarEliminacion() {
    const documento = document.getElementById('confirmar-eliminacion').dataset.documento;
    
    try {
        const response = await fetch('../../backend/controllers/colaboradoresController.php?action=eliminar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ documento })
        });

        const resultado = await response.json();

        if (resultado.success) {
            // Remover de datos locales
            colaboradores = colaboradores.filter(c => c.numDocumento !== documento);
            aplicarFiltros();
            eliminarModal.hide();
            mostrarMensaje('success', 'Colaborador eliminado correctamente');
        } else {
            mostrarMensaje('error', resultado.message || 'Error al eliminar colaborador');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('error', 'Error de conexión al eliminar colaborador');
    }
}

// Cambiar contraseña
function cambiarPasswordColaborador(documento) {
    const colaborador = colaboradores.find(c => c.numDocumento === documento);
    
    if (!colaborador) {
        mostrarMensaje('error', 'Colaborador no encontrado');
        return;
    }
    
    document.getElementById('password-documento').value = documento;
    document.getElementById('nueva-password').value = '';
    document.getElementById('confirmar-password').value = '';
    document.getElementById('solicitar-cambio').checked = false;
    
    cambiarPasswordModal.show();
}

// Guardar nueva contraseña
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

    if (nuevaPassword !== confirmarPassword) {
        mostrarMensaje('error', 'Las contraseñas no coinciden');
        return;
    }

    if (nuevaPassword.length < 6) {
        mostrarMensaje('error', 'La contraseña debe tener al menos 6 caracteres');
        return;
    }

    try {
        const response = await fetch('../../backend/controllers/colaboradoresController.php?action=cambiarPassword', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                documento,
                nuevaPassword,
                solicitarCambio
            })
        });

        const resultado = await response.json();

        if (resultado.success) {
            cambiarPasswordModal.hide();
            mostrarMensaje('success', 'Contraseña actualizada correctamente');
        } else {
            mostrarMensaje('error', resultado.message || 'Error al cambiar contraseña');
        }
    } catch (error) {
        console.error('Error:', error);
        mostrarMensaje('error', 'Error de conexión al cambiar contraseña');
    }
}

// Validar contraseñas
function validarPasswords() {
    const nuevaPassword = document.getElementById('nueva-password').value;
    const confirmarPassword = document.getElementById('confirmar-password').value;
    const confirmarInput = document.getElementById('confirmar-password');
    
    if (confirmarPassword && nuevaPassword !== confirmarPassword) {
        confirmarInput.setCustomValidity('Las contraseñas no coinciden');
    } else {
        confirmarInput.setCustomValidity('');
    }
}

// Funciones de utilidad
function formatearFecha(fecha) {
    if (!fecha) return '-';
    
    const opciones = {
        year: 'numeric',
        month: '2-digit',
        day: '2-digit'
    };
    
    return new Date(fecha).toLocaleDateString('es-CO', opciones);
}

function getRolBadgeClass(rol) {
    switch (rol) {
        case 'Administrador':
            return 'bg-danger';
        case 'Colaborador':
            return 'bg-warning text-dark';
        case 'Usuario':
            return 'bg-info';
        default:
            return 'bg-secondary';
    }
}

function mostrarMensaje(tipo, texto) {
    // Ocultar mensajes anteriores
    successMessage.style.display = 'none';
    errorMessage.style.display = 'none';
    
    if (tipo === 'success') {
        successText.textContent = texto;
        successMessage.style.display = 'block';
        
        // Auto-ocultar después de 5 segundos
        setTimeout(() => {
            successMessage.style.display = 'none';
        }, 5000);
    } else if (tipo === 'error') {
        errorText.textContent = texto;
        errorMessage.style.display = 'block';
        
        // Auto-ocultar después de 7 segundos
        setTimeout(() => {
            errorMessage.style.display = 'none';
        }, 7000);
    }
}

// Validaciones adicionales
function validarDocumento(documento, tipoDocumento) {
    // Validaciones básicas según tipo de documento
    switch (tipoDocumento) {
        case 'Cédula de Ciudadanía':
            return /^\d{7,10}$/.test(documento);
        case 'Cedula de Extranjeria':
            return /^[A-Z]{1,2}\d{6,8}$/.test(documento);
        case 'Pasaporte':
            return /^[A-Z0-9]{6,12}$/.test(documento);
        default:
            return documento.length >= 6;
    }
}

function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

function validarTelefono(telefono) {
    const regex = /^[3][0-9]{9}$|^[+]?[0-9]{10,15}$/;
    return regex.test(telefono);
}

// Exportar datos a CSV (función adicional)
function exportarCSV() {
    if (colaboradoresFiltrados.length === 0) {
        mostrarMensaje('error', 'No hay datos para exportar');
        return;
    }

    const headers = ['Documento', 'Tipo Doc.', 'Nombres', 'Apellidos', 'Correo', 'Teléfono', 'Sexo', 'Fecha Nac.', 'Rol'];
    
    let csv = headers.join(',') + '\n';
    
    colaboradoresFiltrados.forEach(colaborador => {
        const fila = [
            colaborador.numDocumento,
            colaborador.tipoDocumento,
            colaborador.nombres,
            colaborador.apellidos,
            colaborador.correo,
            colaborador.numTelefono,
            colaborador.sexo,
            colaborador.fechaNacimiento,
            colaborador.roles
        ];
        csv += fila.map(campo => `"${campo}"`).join(',') + '\n';
    });
    
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', `colaboradores_${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    
    mostrarMensaje('success', 'Datos exportados correctamente');
}

// Funciones de teclado para accesibilidad
document.addEventListener('keydown', function(e) {
    // Ctrl + F para enfocar búsqueda
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        buscarInput.focus();
    }
    
    // Escape para cerrar modales
    if (e.key === 'Escape') {
        const modalBackdrops = document.querySelectorAll('.modal.show');
        modalBackdrops.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) {
                modalInstance.hide();
            }
        });
    }
});

// Manejar errores globales
window.addEventListener('error', function(e) {
    console.error('Error global:', e.error);
    mostrarMensaje('error', 'Ha ocurrido un error inesperado');
});

// Prevenir envío de formularios con Enter accidental
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('keypress', function(e) {
        if (e.key === 'Enter' && e.target.type !== 'submit') {
            e.preventDefault();
        }
    });
});

// Auto-guardar búsqueda en sessionStorage para persistencia
function guardarEstadoBusqueda() {
    const estado = {
        busqueda: busquedaActual,
        filtro: filtroActual,
        pagina: paginaActual
    };
    sessionStorage.setItem('colaboradores_estado', JSON.stringify(estado));
}

function restaurarEstadoBusqueda() {
    const estado = sessionStorage.getItem('colaboradores_estado');
    if (estado) {
        const { busqueda, filtro, pagina } = JSON.parse(estado);
        busquedaActual = busqueda || '';
        filtroActual = filtro || 'all';
        paginaActual = pagina || 1;
        buscarInput.value = busquedaActual;
    }
}

// Mejorar UX con feedback visual
function resaltarFila(documento) {
    const fila = document.querySelector(`tr[data-documento="${documento}"]`);
    if (fila) {
        fila.classList.add('table-success');
        setTimeout(() => {
            fila.classList.remove('table-success');
        }, 2000);
    }
}

// Funciones para manejo de estados de carga
function deshabilitarBotones(deshabilitar = true) {
    const botones = document.querySelectorAll('button, .btn');
    botones.forEach(btn => {
        if (deshabilitar) {
            btn.disabled = true;
            btn.style.opacity = '0.6';
        } else {
            btn.disabled = false;
            btn.style.opacity = '1';
        }
    });
}

// Debounce para búsqueda en tiempo real
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

// Configurar búsqueda en tiempo real con debounce
const busquedaTiempoReal = debounce(function() {
    busquedaActual = buscarInput.value.trim().toLowerCase();
    aplicarFiltros();
}, 300);

// Añadir evento de búsqueda en tiempo real
buscarInput.addEventListener('input', busquedaTiempoReal);

// Función para validar edad mínima (ejemplo: 18 años)
function validarEdadMinima(fechaNacimiento) {
    const hoy = new Date();
    const fechaNac = new Date(fechaNacimiento);
    const edad = hoy.getFullYear() - fechaNac.getFullYear();
    const mesActual = hoy.getMonth();
    const mesNac = fechaNac.getMonth();
    
    if (mesActual < mesNac || (mesActual === mesNac && hoy.getDate() < fechaNac.getDate())) {
        edad--;
    }
    
    return edad >= 18;
}

// Tooltip para botones de acción
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips de Bootstrap si están disponibles
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[title]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
});

// Función para limpiar formularios
function limpiarFormulario(formId) {
    const form = document.getElementById(formId);
    if (form) {
        form.reset();
        form.classList.remove('was-validated');
        
        // Limpiar mensajes de validación personalizados
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.setCustomValidity('');
        });
    }
}