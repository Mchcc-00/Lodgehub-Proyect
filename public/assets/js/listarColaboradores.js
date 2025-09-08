/**
 * JavaScript para gestionar la lista de colaboradores - ACTUALIZADO
 * Solo muestra Colaboradores y Usuarios, incluye estadísticas
 * Archivo: listarColaboradores.js
 */

class ColaboradoresManager {
    constructor() {
        this.colaboradores = [];
        this.filtroActivo = 'all';
        this.busquedaActiva = '';
        this.paginaActual = 1;
        this.itemsPorPagina = 10;
        this.estadisticas = {};
        
        this.init();
    }
    
    init() {
        this.cargarEventListeners();
        this.cargarColaboradores();
        this.cargarEstadisticas();
    }
    
    cargarEventListeners() {
        // Eventos de búsqueda
        const buscarInput = document.getElementById('buscar-input');
        const buscarBtn = document.getElementById('buscar-btn');
        
        if (buscarInput) {
            buscarInput.addEventListener('keyup', (e) => {
                if (e.key === 'Enter') {
                    this.buscarColaboradores();
                }
            });
        }
        
        if (buscarBtn) {
            buscarBtn.addEventListener('click', () => this.buscarColaboradores());
        }
        
        // Eventos de filtros
        const filtros = document.querySelectorAll('.filter-option');
        filtros.forEach(filtro => {
            filtro.addEventListener('click', (e) => {
                e.preventDefault();
                this.aplicarFiltro(e.target.getAttribute('data-filter'));
            });
        });
        
        // Evento de actualizar
        const refreshBtn = document.getElementById('refresh-btn');
        if (refreshBtn) {
            refreshBtn.addEventListener('click', () => {
                this.cargarColaboradores();
                this.cargarEstadisticas();
            });
        }
        
        // Eventos de modales
        this.configurarEventosModales();
    }
    
    configurarEventosModales() {
        // Modal de edición
        const guardarEdicionBtn = document.getElementById('guardar-edicion');
        if (guardarEdicionBtn) {
            guardarEdicionBtn.addEventListener('click', () => this.guardarEdicion());
        }
        
        // Modal de eliminación
        const confirmarEliminacionBtn = document.getElementById('confirmar-eliminacion');
        if (confirmarEliminacionBtn) {
            confirmarEliminacionBtn.addEventListener('click', () => this.confirmarEliminacion());
        }
        
        // Modal de cambiar contraseña
        const guardarPasswordBtn = document.getElementById('guardar-password');
        if (guardarPasswordBtn) {
            guardarPasswordBtn.addEventListener('click', () => this.cambiarPassword());
        }
        
        // Validar confirmación de contraseña
        const confirmarPasswordInput = document.getElementById('confirmar-password');
        if (confirmarPasswordInput) {
            confirmarPasswordInput.addEventListener('input', () => this.validarConfirmacionPassword());
        }
    }
    
    async cargarColaboradores() {
        try {
            this.mostrarLoading();
            
            const params = new URLSearchParams({
                action: 'listar'
            });
            
            if (this.busquedaActiva) {
                params.append('busqueda', this.busquedaActiva);
            }
            
            if (this.filtroActivo !== 'all') {
                // Solo permitir filtros de Colaborador y Usuario
                if (['Colaborador', 'Usuario'].includes(this.filtroActivo)) {
                    params.append('rol', this.filtroActivo);
                } else {
                    // Para otros filtros como tipo documento o sexo
                    if (this.filtroActivo.includes('Cédula') || this.filtroActivo === 'Pasaporte') {
                        params.append('tipoDocumento', this.filtroActivo);
                    } else if (['Hombre', 'Mujer', 'Otro'].includes(this.filtroActivo)) {
                        params.append('sexo', this.filtroActivo);
                    }
                }
            }
            
            const url = `../controllers/misColaboradoresControllers.php?${params.toString()}`;
            
            console.log('Cargando desde URL:', url);
            
            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            console.log('Respuesta del servidor:', data);
            
            if (data.success) {
                this.colaboradores = data.data || [];
                this.renderizarTabla();
                this.mostrarMensaje('success', `${this.colaboradores.length} colaboradores cargados`);
            } else {
                throw new Error(data.message || 'Error al cargar colaboradores');
            }
            
        } catch (error) {
            console.error('Error al cargar colaboradores:', error);
            this.mostrarMensaje('error', 'Error al cargar colaboradores: ' + error.message);
            this.renderizarTablaVacia('Error al cargar los datos');
        }
    }
    
    async cargarEstadisticas() {
        try {
            const response = await fetch('../controllers/misColaboradoresControllers.php?action=estadisticas', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            const data = await response.json();
            
            if (data.success) {
                this.estadisticas = data.data;
                this.actualizarEstadisticas();
            } else {
                console.error('Error al cargar estadísticas:', data.message);
            }
            
        } catch (error) {
            console.error('Error al cargar estadísticas:', error);
        }
    }
    
    actualizarEstadisticas() {
        const totalElement = document.getElementById('total-colaboradores');
        const colaboradoresElement = document.getElementById('total-colaboradores-rol');
        const usuariosElement = document.getElementById('total-usuarios');
        const pendientesElement = document.getElementById('pendientes-password');
        
        if (totalElement && this.estadisticas.total !== undefined) {
            totalElement.textContent = this.estadisticas.total || 0;
        }
        
        if (colaboradoresElement && this.estadisticas.colaboradores !== undefined) {
            colaboradoresElement.textContent = this.estadisticas.colaboradores || 0;
        }
        
        if (usuariosElement && this.estadisticas.usuarios !== undefined) {
            usuariosElement.textContent = this.estadisticas.usuarios || 0;
        }
        
        if (pendientesElement && this.estadisticas.pendientes_password !== undefined) {
            pendientesElement.textContent = this.estadisticas.pendientes_password || 0;
        }
    }
    
    renderizarTabla() {
        const tbody = document.getElementById('tabla-colaboradores');
        if (!tbody) {
            console.error('No se encontró el elemento tabla-colaboradores');
            return;
        }
        
        if (this.colaboradores.length === 0) {
            this.renderizarTablaVacia('No se encontraron colaboradores');
            return;
        }
        
        let html = '';
        
        this.colaboradores.forEach(colaborador => {
            // Solo mostrar si es Colaborador o Usuario (filtro adicional por seguridad)
            if (!['Colaborador', 'Usuario'].includes(colaborador.roles)) {
                return;
            }
            
            html += `
                <tr>
                    <td>${this.escapeHtml(colaborador.numDocumento || '')}</td>
                    <td><span class="badge bg-info">${this.escapeHtml(colaborador.tipoDocumento || '')}</span></td>
                    <td>${this.escapeHtml(colaborador.nombres || '')}</td>
                    <td>${this.escapeHtml(colaborador.apellidos || '')}</td>
                    <td>${this.escapeHtml(colaborador.correo || '')}</td>
                    <td>${this.escapeHtml(colaborador.numTelefono || '')}</td>
                    <td>${this.escapeHtml(colaborador.sexo || '')}</td>
                    <td>${this.formatearFecha(colaborador.fechaNacimiento)}</td>
                    <td>
                        <span class="badge ${this.getBadgeClass(colaborador.roles)}">
                            ${this.escapeHtml(colaborador.roles || '')}
                        </span>
                        ${colaborador.solicitarContraseña === '1' ? '<i class="fas fa-exclamation-triangle text-warning ms-1" title="Cambio de contraseña pendiente"></i>' : ''}
                    </td>
                    <td>
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-outline-info" onclick="colaboradoresManager.verColaborador('${colaborador.numDocumento}')" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="colaboradoresManager.editarColaborador('${colaborador.numDocumento}')" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-warning" onclick="colaboradoresManager.abrirCambiarPassword('${colaborador.numDocumento}')" title="Cambiar contraseña">
                                <i class="fas fa-key"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="colaboradoresManager.eliminarColaborador('${colaborador.numDocumento}', '${this.escapeHtml(colaborador.nombres)} ${this.escapeHtml(colaborador.apellidos)}')" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
    }
    
    renderizarTablaVacia(mensaje = 'No hay colaboradores para mostrar') {
        const tbody = document.getElementById('tabla-colaboradores');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center p-4">
                        <i class="fas fa-users fa-3x text-muted mb-3"></i>
                        <p class="text-muted">${mensaje}</p>
                    </td>
                </tr>
            `;
        }
    }
    
    mostrarLoading() {
        const tbody = document.getElementById('tabla-colaboradores');
        if (tbody) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="10" class="text-center loading">
                        <i class="fas fa-spinner fa-spin"></i> Cargando colaboradores...
                    </td>
                </tr>
            `;
        }
    }
    
    buscarColaboradores() {
        const input = document.getElementById('buscar-input');
        this.busquedaActiva = input ? input.value.trim() : '';
        this.cargarColaboradores();
    }
    
    aplicarFiltro(filtro) {
        this.filtroActivo = filtro;
        this.cargarColaboradores();
    }
    
    async verColaborador(documento) {
        try {
            const response = await fetch(`../controllers/misColaboradoresControllers.php?action=obtener&documento=${encodeURIComponent(documento)}`);
            const data = await response.json();
            
            if (data.success && data.data) {
                // Verificar que sea Colaborador o Usuario antes de mostrar
                if (!['Colaborador', 'Usuario'].includes(data.data.roles)) {
                    this.mostrarMensaje('error', 'No tiene permisos para ver este usuario');
                    return;
                }
                this.mostrarDetallesColaborador(data.data);
            } else {
                this.mostrarMensaje('error', 'Error al obtener detalles del colaborador');
            }
        } catch (error) {
            console.error('Error al obtener colaborador:', error);
            this.mostrarMensaje('error', 'Error de conexión');
        }
    }
    
    mostrarDetallesColaborador(colaborador) {
        const detallesContainer = document.getElementById('detalles-colaborador');
        if (!detallesContainer) return;
        
        const html = `
            <div class="row">
                <div class="col-md-6">
                    <h6><i class="fas fa-user"></i> Información Personal</h6>
                    <p><strong>Documento:</strong> ${this.escapeHtml(colaborador.numDocumento)}</p>
                    <p><strong>Tipo:</strong> ${this.escapeHtml(colaborador.tipoDocumento)}</p>
                    <p><strong>Nombres:</strong> ${this.escapeHtml(colaborador.nombres)}</p>
                    <p><strong>Apellidos:</strong> ${this.escapeHtml(colaborador.apellidos)}</p>
                    <p><strong>Sexo:</strong> ${this.escapeHtml(colaborador.sexo)}</p>
                    <p><strong>Fecha de Nacimiento:</strong> ${this.formatearFecha(colaborador.fechaNacimiento)}</p>
                    <p><strong>Edad:</strong> ${this.calcularEdad(colaborador.fechaNacimiento)} años</p>
                </div>
                <div class="col-md-6">
                    <h6><i class="fas fa-envelope"></i> Información de Contacto y Sistema</h6>
                    <p><strong>Correo:</strong> ${this.escapeHtml(colaborador.correo)}</p>
                    <p><strong>Teléfono:</strong> ${this.escapeHtml(colaborador.numTelefono)}</p>
                    <p><strong>Rol:</strong> <span class="badge ${this.getBadgeClass(colaborador.roles)}">${this.escapeHtml(colaborador.roles)}</span></p>
                    <p><strong>Solicitar cambio de contraseña:</strong> 
                        <span class="badge ${colaborador.solicitarContraseña === '1' ? 'bg-warning' : 'bg-success'}">
                            ${colaborador.solicitarContraseña === '1' ? 'Pendiente' : 'No requerido'}
                        </span>
                    </p>
                    ${colaborador.foto ? `<p><strong>Foto:</strong> <i class="fas fa-check text-success"></i> Disponible</p>` : ''}
                </div>
            </div>
        `;
        
        detallesContainer.innerHTML = html;
        
        const modal = new bootstrap.Modal(document.getElementById('verModal'));
        modal.show();
    }
    
    async editarColaborador(documento) {
        try {
            const response = await fetch(`../controllers/misColaboradoresControllers.php?action=obtener&documento=${encodeURIComponent(documento)}`);
            const data = await response.json();
            
            if (data.success && data.data) {
                // Verificar que sea Colaborador o Usuario antes de editar
                if (!['Colaborador', 'Usuario'].includes(data.data.roles)) {
                    this.mostrarMensaje('error', 'No tiene permisos para editar este usuario');
                    return;
                }
                this.llenarFormularioEdicion(data.data);
                const modal = new bootstrap.Modal(document.getElementById('editarModal'));
                modal.show();
            } else {
                this.mostrarMensaje('error', 'Error al cargar datos del colaborador');
            }
        } catch (error) {
            console.error('Error al cargar colaborador para edición:', error);
            this.mostrarMensaje('error', 'Error de conexión');
        }
    }
    
    llenarFormularioEdicion(colaborador) {
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
        document.getElementById('edit-solicitarContraseña').value = colaborador.solicitarContraseña;
        document.getElementById('edit-password').value = '';
    }
    
    async guardarEdicion() {
        try {
            const form = document.getElementById('form-editar');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const formData = new FormData(form);
            const datos = Object.fromEntries(formData);
            datos.documentoOriginal = document.getElementById('edit-documento-original').value;
            
            // Verificar que no se intente cambiar a Administrador
            if (datos.roles === 'Administrador') {
                this.mostrarMensaje('error', 'No tiene permisos para asignar el rol de Administrador');
                return;
            }
            
            const response = await fetch('../controllers/misColaboradoresControllers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'actualizar',
                    ...datos
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.mostrarMensaje('success', data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('editarModal'));
                modal.hide();
                this.cargarColaboradores();
                this.cargarEstadisticas();
            } else {
                this.mostrarMensaje('error', data.message);
            }
        } catch (error) {
            console.error('Error al guardar edición:', error);
            this.mostrarMensaje('error', 'Error de conexión');
        }
    }
    
    eliminarColaborador(documento, nombre) {
        document.getElementById('eliminar-info').textContent = nombre;
        document.getElementById('eliminar-documento').textContent = documento;
        
        const modal = new bootstrap.Modal(document.getElementById('eliminarModal'));
        modal.show();
        
        // Guardar el documento para la eliminación
        this.documentoParaEliminar = documento;
    }
    
    async confirmarEliminacion() {
        try {
            if (!this.documentoParaEliminar) {
                this.mostrarMensaje('error', 'Error: No hay documento seleccionado');
                return;
            }
            
            const response = await fetch('../controllers/misColaboradoresControllers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'eliminar',
                    documento: this.documentoParaEliminar
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.mostrarMensaje('success', data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('eliminarModal'));
                modal.hide();
                this.cargarColaboradores();
                this.cargarEstadisticas();
            } else {
                this.mostrarMensaje('error', data.message);
            }
            
            this.documentoParaEliminar = null;
        } catch (error) {
            console.error('Error al eliminar:', error);
            this.mostrarMensaje('error', 'Error de conexión');
        }
    }
    
    abrirCambiarPassword(documento) {
        document.getElementById('password-documento').value = documento;
        document.getElementById('nueva-password').value = '';
        document.getElementById('confirmar-password').value = '';
        document.getElementById('solicitar-cambio').checked = false;
        
        const modal = new bootstrap.Modal(document.getElementById('cambiarPasswordModal'));
        modal.show();
    }
    
    validarConfirmacionPassword() {
        const nuevaPassword = document.getElementById('nueva-password').value;
        const confirmarPassword = document.getElementById('confirmar-password').value;
        const confirmarInput = document.getElementById('confirmar-password');
        
        if (confirmarPassword && nuevaPassword !== confirmarPassword) {
            confirmarInput.setCustomValidity('Las contraseñas no coinciden');
        } else {
            confirmarInput.setCustomValidity('');
        }
    }
    
    async cambiarPassword() {
        try {
            const form = document.getElementById('form-cambiar-password');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }
            
            const documento = document.getElementById('password-documento').value;
            const nuevaPassword = document.getElementById('nueva-password').value;
            const solicitarCambio = document.getElementById('solicitar-cambio').checked;
            
            const response = await fetch('../controllers/misColaboradoresControllers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'cambiarPassword',
                    documento: documento,
                    nuevaPassword: nuevaPassword,
                    solicitarCambio: solicitarCambio
                })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.mostrarMensaje('success', data.message);
                const modal = bootstrap.Modal.getInstance(document.getElementById('cambiarPasswordModal'));
                modal.hide();
                this.cargarEstadisticas(); // Actualizar estadísticas
            } else {
                this.mostrarMensaje('error', data.message);
            }
        } catch (error) {
            console.error('Error al cambiar contraseña:', error);
            this.mostrarMensaje('error', 'Error de conexión');
        }
    }
    
    mostrarMensaje(tipo, mensaje) {
        const successDiv = document.getElementById('success-message');
        const errorDiv = document.getElementById('error-message');
        
        // Ocultar ambos mensajes primero
        if (successDiv) successDiv.style.display = 'none';
        if (errorDiv) errorDiv.style.display = 'none';
        
        if (tipo === 'success' && successDiv) {
            document.getElementById('success-text').textContent = mensaje;
            successDiv.style.display = 'block';
            setTimeout(() => successDiv.style.display = 'none', 5000);
        } else if (tipo === 'error' && errorDiv) {
            document.getElementById('error-text').textContent = mensaje;
            errorDiv.style.display = 'block';
            setTimeout(() => errorDiv.style.display = 'none', 8000);
        }
    }
    
    // Funciones utilitarias
    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
    
    formatearFecha(fecha) {
        if (!fecha) return '';
        const date = new Date(fecha);
        return date.toLocaleDateString('es-CO');
    }
    
    calcularEdad(fechaNacimiento) {
        if (!fechaNacimiento) return 'N/A';
        const hoy = new Date();
        const fechaNac = new Date(fechaNacimiento);
        let edad = hoy.getFullYear() - fechaNac.getFullYear();
        const mes = hoy.getMonth() - fechaNac.getMonth();
        
        if (mes < 0 || (mes === 0 && hoy.getDate() < fechaNac.getDate())) {
            edad--;
        }
        
        return edad;
    }
    
    getBadgeClass(rol) {
        const badges = {
            'Colaborador': 'bg-primary',
            'Usuario': 'bg-success'
        };
        return badges[rol] || 'bg-secondary';
    }
}

// Inicializar cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    window.colaboradoresManager = new ColaboradoresManager();
});

// Funciones globales para compatibilidad con onclick
window.verColaborador = function(documento) {
    if (window.colaboradoresManager) {
        window.colaboradoresManager.verColaborador(documento);
    }
};

window.editarColaborador = function(documento) {
    if (window.colaboradoresManager) {
        window.colaboradoresManager.editarColaborador(documento);
    }
};

window.eliminarColaborador = function(documento, nombre) {
    if (window.colaboradoresManager) {
        window.colaboradoresManager.eliminarColaborador(documento, nombre);
    }
};

window.abrirCambiarPassword = function(documento) {
    if (window.colaboradoresManager) {
        window.colaboradoresManager.abrirCambiarPassword(documento);
    }
};