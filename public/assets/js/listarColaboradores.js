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
        
        this.init();
    }
    
    init() {
        this.cargarEventListeners();
        this.cargarColaboradores();
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
        
        // Modal de cambiar contraseña
        const guardarPasswordBtn = document.getElementById('guardar-password');
        if (guardarPasswordBtn) {
            guardarPasswordBtn.addEventListener('click', () => this.cambiarPassword());
        }
        
        const confirmarPasswordInput = document.getElementById('confirmar-password');
        if (confirmarPasswordInput) {
            confirmarPasswordInput.addEventListener('input', () => this.validarConfirmacionPassword());
        }
        
        // Modal de eliminación
        const confirmarEliminacionBtn = document.getElementById('confirmar-eliminacion');
        if (confirmarEliminacionBtn) {
            confirmarEliminacionBtn.addEventListener('click', () => this.confirmarEliminacion());
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
            
            // Obtener el id_hotel del administrador desde el PHP (si está disponible)
            const hotelIdElement = document.getElementById('admin-hotel-id');
            if (hotelIdElement && hotelIdElement.value) {
                params.append('id_hotel', hotelIdElement.value);
            }

            if (this.filtroActivo !== 'all') {
                // Solo permitir filtros de Colaborador y Usuario
                if (['Colaborador', 'Usuario'].includes(this.filtroActivo)) {
                    params.append('rol', this.filtroActivo);
                } else {
                    // Para otros filtros como tipo documento o sexo
                    if (this.filtroActivo.includes('Cédula') || this.filtroActivo.includes('Tarjeta') || this.filtroActivo === 'Pasaporte' || this.filtroActivo.includes('Registro')) {
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
                    <td class="colaborador-actions">
                        <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-info action-btn" data-action="ver" data-documento="${colaborador.numDocumento}" title="Ver detalles">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-warning action-btn" data-action="editar" data-documento="${colaborador.numDocumento}" title="Editar">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-sm btn-danger action-btn" data-action="eliminar" data-documento="${colaborador.numDocumento}" data-nombre="${this.escapeHtml(colaborador.nombres)} ${this.escapeHtml(colaborador.apellidos)}" title="Eliminar">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });
        
        tbody.innerHTML = html;
        this.asignarEventosBotonesAccion();
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

    asignarEventosBotonesAccion() {
        const tabla = document.getElementById('tabla-colaboradores');
        if (!tabla) return;

        tabla.addEventListener('click', (e) => {
            const boton = e.target.closest('.action-btn');
            if (!boton) return;

            const action = boton.dataset.action;
            const documento = boton.dataset.documento;

            switch (action) {
                case 'ver':
                    this.verColaborador(documento);
                    break;
                case 'editar':
                    this.editarColaborador(documento);
                    break;
                case 'eliminar':
                    this.eliminarColaborador(documento, boton.dataset.nombre);
                    break;
            }
        });
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
        const modalElement = document.getElementById('editarModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

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
                this.cargarColaboradores();
            } else {
                this.mostrarMensaje('error', data.message);
            }
        } catch (error) {
            console.error('Error al guardar edición:', error);
            this.mostrarMensaje('error', 'Error de conexión al guardar.');
        } finally {
            // Asegurarse de que el modal siempre se oculte
            this.forceHideModal(modal);
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
        const modalElement = document.getElementById('eliminarModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalElement);

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
                this.cargarColaboradores();
                this.forceHideModal(modal); // Asegurarse de cerrar el modal
            } else {
                this.mostrarMensaje('error', data.message);
            }
        } catch (error) {
            console.error('Error al eliminar:', error);
            this.mostrarMensaje('error', 'Error de conexión al eliminar.');
        } finally {
            this.documentoParaEliminar = null;
            this.forceHideModal(modal);
        }
    }
    
    /**
     * Cierra un modal de forma robusta, asegurando la limpieza del backdrop.
     * Esto previene el problema de la "pantalla congelada".
     * @param {Object} modalInstance - La instancia del modal de Bootstrap.
     */
    forceHideModal(modalInstance) {
        if (modalInstance) {
            modalInstance.hide();
        }

        // Después de un breve retraso, limpia manualmente cualquier residuo que Bootstrap pueda dejar.
        setTimeout(() => {
            const backdrops = document.querySelectorAll('.modal-backdrop');
            backdrops.forEach(backdrop => backdrop.remove());
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        }, 500); // 500ms para dar tiempo a que la transición de Bootstrap termine.
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

document.addEventListener('DOMContentLoaded', () => new ColaboradoresManager());