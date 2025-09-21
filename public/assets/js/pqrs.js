document.addEventListener('DOMContentLoaded', function() {
    // Elementos del formulario de creación
    const form = document.getElementById('pqrs-form');
    const resetBtn = document.getElementById('reset-btn');
    const previewBtn = document.getElementById('preview-btn');
    const validarUsuarioBtn = document.getElementById('validar-usuario-btn');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const pqrsPreview = document.getElementById('pqrs-preview');
    const contadorChars = document.getElementById('contador-chars');
    const usuarioInfo = document.getElementById('usuario-info');
    const usuarioNombre = document.getElementById('usuario-nombre');
    
    // Campos del formulario
    const inputs = {
        tipo: document.getElementById('tipo'),
        prioridad: document.getElementById('prioridad'),
        categoria: document.getElementById('categoria'),
        numDocumento: document.getElementById('numDocumento'),
        descripcion: document.getElementById('descripcion')
    };

    let usuarioValidado = false;

    // Solo ejecutar si estamos en la página de crear
    if (form) {
        // Validaciones en tiempo real
        setupRealTimeValidation();
        
        // Event listeners
        form.addEventListener('submit', handleSubmit);
        resetBtn.addEventListener('click', resetForm);
        previewBtn.addEventListener('click', togglePreview);
        validarUsuarioBtn.addEventListener('click', validarUsuario);
    }

    function setupRealTimeValidation() {
        // Contador de caracteres para descripción
        inputs.descripcion.addEventListener('input', function() {
            const count = this.value.length;
            contadorChars.textContent = `${count}/1000`;
            
            if (count >= 950) {
                contadorChars.style.color = '#dc3545';
            } else if (count >= 800) {
                contadorChars.style.color = '#ffc107';
            } else {
                contadorChars.style.color = '#6c757d';
            }

            // Validación de descripción
            if (count >= 10 && count <= 1000) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else if (count > 0) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });

        // Validación de número de documento
        inputs.numDocumento.addEventListener('input', function() {
            const value = this.value.trim();
            usuarioValidado = false;
            usuarioInfo.style.display = 'none';
            
            if (value.length >= 5 && value.length <= 15 && /^[0-9A-Za-z]+$/.test(value)) {
                this.classList.add('valid');
                this.classList.remove('invalid');
            } else if (value.length > 0) {
                this.classList.add('invalid');
                this.classList.remove('valid');
            } else {
                this.classList.remove('valid', 'invalid');
            }
        });

        // Validación de campos select
        [inputs.tipo, inputs.prioridad, inputs.categoria].forEach(select => {
            select.addEventListener('change', function() {
                if (this.value) {
                    this.classList.add('valid');
                    this.classList.remove('invalid');
                } else {
                    this.classList.add('invalid');
                    this.classList.remove('valid');
                }
            });
        });
    }

    async function validarUsuario() {
        const numDocumento = inputs.numDocumento.value.trim();
        
        if (!numDocumento) {
            showErrorMessage('Por favor ingrese un número de documento');
            return;
        }

        if (numDocumento.length < 5 || numDocumento.length > 15) {
            showErrorMessage('El número de documento debe tener entre 5 y 15 caracteres');
            return;
        }

        // Mostrar estado de carga
        const originalText = validarUsuarioBtn.innerHTML;
        validarUsuarioBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Validando...';
        validarUsuarioBtn.disabled = true;

        try {
            const response = await fetch(`/lodgehub/app/controllers/pqrsController.php?action=validarUsuario&numDocumento=${encodeURIComponent(numDocumento)}`);
            const result = await response.json();

            if (result.success) {
                const usuario = result.data;
                usuarioNombre.textContent = `${usuario.nombres} ${usuario.apellidos} - ${usuario.correo}`;
                usuarioInfo.style.display = 'block';
                usuarioValidado = true;
                inputs.numDocumento.classList.add('valid');
                inputs.numDocumento.classList.remove('invalid');
            } else {
                showErrorMessage('Usuario no encontrado con el documento proporcionado');
                usuarioInfo.style.display = 'none';
                usuarioValidado = false;
                inputs.numDocumento.classList.add('invalid');
                inputs.numDocumento.classList.remove('valid');
            }
        } catch (error) {
            console.error('Error:', error);
            showErrorMessage('Error al validar usuario. Verifique la conexión.');
            usuarioValidado = false;
        } finally {
            // Restaurar botón
            validarUsuarioBtn.innerHTML = originalText;
            validarUsuarioBtn.disabled = false;
        }
    }

    async function handleSubmit(e) {
        e.preventDefault();
        
        // Ocultar mensajes anteriores
        hideMessages();
        
        // Validar formulario
        if (!validateForm()) {
            return;
        }
        
        // Mostrar estado de carga
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.classList.add('loading');
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Creando PQRS...';
        submitBtn.disabled = true;
        
        try {
            // Crear FormData directamente del formulario
            const formData = new FormData(form);
            
            // Enviar datos al servidor
            const formAction = form.action || '/lodgehub/app/controllers/pqrsController.php';
            
            const response = await fetch(formAction, {
                method: 'POST',
                body: formData
            });
            
            // Verificar si la respuesta es JSON válida
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('La respuesta del servidor no es JSON válida');
            }
            
            const result = await response.json();
            
            if (result.success) {
                // Redireccionar inmediatamente a la lista con un parámetro de éxito
                window.location.href = 'listaPqrs.php?status=created';
            } else {
                showErrorMessage(result.message || 'Error al crear la PQRS');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showErrorMessage('Error de conexión. Verifica que el servidor esté funcionando correctamente.');
        } finally {
            // Restaurar botón
            submitBtn.classList.remove('loading');
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        }
    }

    function validateForm() {
        let isValid = true;
        const errors = [];

        // Validar campos requeridos
        Object.keys(inputs).forEach(key => {
            const input = inputs[key];
            const value = input.value.trim();
            
            if (!value) {
                input.classList.add('invalid');
                input.classList.remove('valid');
                isValid = false;
                errors.push(`El campo ${getFieldLabel(key)} es requerido`);
            }
        });

        // Validar que el usuario esté validado
        if (!usuarioValidado) {
            errors.push('Debe validar el usuario antes de crear la PQRS');
            isValid = false;
        }

        // Validaciones específicas
        if (inputs.descripcion.value.length < 10 || inputs.descripcion.value.length > 1000) {
            errors.push('La descripción debe tener entre 10 y 1000 caracteres');
            isValid = false;
        }

        if (inputs.numDocumento.value.length < 5 || inputs.numDocumento.value.length > 15) {
            errors.push('El número de documento debe tener entre 5 y 15 caracteres');
            isValid = false;
        }

        if (!isValid) {
            showErrorMessage(errors.join('<br>'));
        }

        return isValid;
    }

    function getFieldLabel(fieldName) {
        const labels = {
            tipo: 'Tipo de PQRS',
            prioridad: 'Prioridad',
            categoria: 'Categoría',
            numDocumento: 'Número de Documento',
            descripcion: 'Descripción'
        };
        return labels[fieldName] || fieldName;
    }

    function resetForm() {
        if (form) {
            form.reset();
            
            // Limpiar clases de validación
            Object.values(inputs).forEach(input => {
                input.classList.remove('valid', 'invalid');
            });
            
            // Ocultar vista previa y mensajes
            if (pqrsPreview) pqrsPreview.style.display = 'none';
            if (usuarioInfo) usuarioInfo.style.display = 'none';
            usuarioValidado = false;
            contadorChars.textContent = '0/1000';
            contadorChars.style.color = '#6c757d';
            hideMessages();
        }
    }

    function togglePreview() {
        if (pqrsPreview.style.display === 'none' || !pqrsPreview.style.display) {
            showPreview();
        } else {
            pqrsPreview.style.display = 'none';
        }
    }

    function showPreview() {
        const previewContent = document.getElementById('preview-content');
        
        const previewData = {
            'Tipo': inputs.tipo.options[inputs.tipo.selectedIndex]?.text || inputs.tipo.value,
            'Prioridad': inputs.prioridad.options[inputs.prioridad.selectedIndex]?.text || inputs.prioridad.value,
            'Categoría': inputs.categoria.options[inputs.categoria.selectedIndex]?.text || inputs.categoria.value,
            'Usuario': usuarioValidado ? usuarioNombre.textContent : inputs.numDocumento.value,
            'Descripción': inputs.descripcion.value
        };

        let html = '';
        Object.entries(previewData).forEach(([label, value]) => {
            if (value) {
                html += `
                    <div class="preview-item">
                        <span class="preview-label">${label}:</span>
                        <span class="preview-value">${value}</span>
                    </div>
                `;
            }
        });

        previewContent.innerHTML = html;
        pqrsPreview.style.display = 'block';
    }

    function showErrorMessage(message) {
        if (errorMessage && errorText) {
            errorText.innerHTML = message;
            errorMessage.style.display = 'block';
            if (successMessage) successMessage.style.display = 'none';
            
            // Scroll al mensaje
            errorMessage.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }

    function hideMessages() {
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    }

    // Formateo automático para algunos campos
    if (inputs.numDocumento) {
        inputs.numDocumento.addEventListener('input', function() {
            // Permitir solo letras y números
            this.value = this.value.replace(/[^0-9A-Za-z]/g, '');
        });
    }
});

// ========================================
// FUNCIONES GLOBALES PARA CRUD COMPLETO
// ========================================

// Función para cargar todas las PQRS
async function cargarPqrs(pagina = 1, registrosPorPagina = 10, filtro = null) {
    try {
        let url = `/lodgehub/app/controllers/pqrsController.php?action=obtener&paginado=true&pagina=${pagina}&registros=${registrosPorPagina}`;
        if (filtro) {
            url += `&filtro=${encodeURIComponent(filtro)}`;
        }
        
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al cargar PQRS:', error);
        throw error;
    }
}

// Función para obtener una PQRS por ID
async function obtenerPqrsPorId(id) {
    try {
        const url = `/lodgehub/app/controllers/pqrsController.php?action=obtenerPorId&id=${encodeURIComponent(id)}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al obtener PQRS:', error);
        throw error;
    }
}

// Función para actualizar una PQRS
async function actualizarPqrs(id, datos) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        // Agregar solo los campos que se van a actualizar
        Object.keys(datos).forEach(key => {
            if (datos[key] !== null && datos[key] !== undefined && datos[key] !== '') {
                formData.append(key, datos[key]);
            }
        });
        
        const response = await fetch('/lodgehub/app/controllers/pqrsController.php?action=actualizar', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            return result;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al actualizar PQRS:', error);
        throw error;
    }
}

// Función para eliminar una PQRS
async function eliminarPqrs(id) {
    try {
        const formData = new FormData();
        formData.append('id', id);
        
        const response = await fetch('/lodgehub/app/controllers/pqrsController.php?action=eliminar', {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            return result;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al eliminar PQRS:', error);
        throw error;
    }
}

// Función para buscar PQRS
async function buscarPqrs(termino) {
    try {
        const url = `/lodgehub/app/controllers/pqrsController.php?action=buscar&termino=${encodeURIComponent(termino)}`;
        const response = await fetch(url);
        const result = await response.json();
        
        if (result.success) {
            return result.data;
        } else {
            throw new Error(result.message);
        }
    } catch (error) {
        console.error('Error al buscar PQRS:', error);
        throw error;
    }
}

// Función para mostrar notificaciones
function mostrarNotificacion(mensaje, tipo = 'success') {
    // Crear elemento de notificación
    const notificacion = document.createElement('div');
    notificacion.className = `alert alert-${tipo === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
    notificacion.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    
    notificacion.innerHTML = `
        ${tipo === 'success' ? '✅' : '❌'} ${mensaje}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notificacion);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        if (notificacion.parentNode) {
            notificacion.parentNode.removeChild(notificacion);
        }
    }, 5000);
}

// Función para formatear fecha
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

// Función para formatear estado con badge
function formatearEstado(estado) {
    const badges = {
        'Pendiente': 'bg-warning text-dark',
        'Finalizado': 'bg-success'
    };
    
    return `<span class="badge ${badges[estado] || 'bg-secondary'}">${estado}</span>`;
}

// Función para formatear prioridad con badge
function formatearPrioridad(prioridad) {
    const badges = {
        'Bajo': 'bg-info',
        'Alto': 'bg-danger'
    };
    
    return `<span class="badge ${badges[prioridad] || 'bg-secondary'}">${prioridad}</span>`;
}

// Función para formatear tipo con color
function formatearTipo(tipo) {
    const colores = {
        'Peticiones': 'text-primary',
        'Quejas': 'text-warning',
        'Reclamos': 'text-danger', 
        'Sugerencias': 'text-info',
        'Felicitaciones': 'text-success'
    };
    
    return `<span class="${colores[tipo] || ''}">${tipo}</span>`;
}

// Función para truncar texto
function truncarTexto(texto, longitud = 100) {
    if (!texto) return '';
    if (texto.length <= longitud) return texto;
    return texto.substring(0, longitud) + '...';
}