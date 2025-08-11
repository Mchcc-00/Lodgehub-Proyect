let editingHotelId = null;
let isSubmitting = false;

document.addEventListener('DOMContentLoaded', function() {
    loadHotels();
    
    const form = document.getElementById('hotelForm');
    form.addEventListener('submit', handleFormSubmit);
    
    // Auto-refresh cada 30 segundos
    setInterval(loadHotels, 30000);
    
    // Validación en tiempo real
    setupRealTimeValidation();
    
    // Configurar contadores de caracteres
    setupCharCounters();
});

function setupCharCounters() {
    const textFields = [
        { id: 'nombre', max: 100 },
        { id: 'direccion', max: 200 },
        { id: 'descripcion', max: 1000 }
    ];

    textFields.forEach(field => {
        const input = document.getElementById(field.id);
        const counter = document.getElementById(field.id + '-counter');
        
        if (input && counter) {
            input.addEventListener('input', function() {
                updateCharCounter(input, counter, field.max);
            });
            
            // Inicializar contador
            updateCharCounter(input, counter, field.max);
        }
    });
}

function updateCharCounter(input, counter, maxLength) {
    const currentLength = input.value.length;
    counter.textContent = `${currentLength}/${maxLength}`;
    
    // Cambiar color según el porcentaje usado
    const percentage = (currentLength / maxLength) * 100;
    counter.classList.remove('warning', 'danger');
    
    if (percentage >= 90) {
        counter.classList.add('danger');
    } else if (percentage >= 75) {
        counter.classList.add('warning');
    }
}

function setupRealTimeValidation() {
    const inputs = document.querySelectorAll('input, textarea');
    inputs.forEach(input => {
        input.addEventListener('blur', function() {
            validateField(this);
        });
        
        input.addEventListener('input', function() {
            clearFieldError(this);
        });
    });
}

function validateField(field) {
    const value = field.value.trim();
    const name = field.name;
    let error = '';

    switch (name) {
        case 'nit':
            if (!value) {
                error = 'El NIT es requerido';
            } else if (!/^[0-9\-]+$/.test(value)) {
                error = 'El NIT debe contener solo números y guiones';
            } else if (value.length > 20) {
                error = 'El NIT no puede exceder 20 caracteres';
            }
            break;
            
        case 'nombre':
            if (!value) {
                error = 'El nombre es requerido';
            } else if (value.length > 100) {
                error = 'El nombre no puede exceder 100 caracteres';
            }
            break;
            
        case 'numDocumento':
            if (!value) {
                error = 'El número de documento es requerido';
            }
            break;
            
        case 'telefono':
            if (value && !/^[\+]?[0-9\-\s\(\)]+$/.test(value)) {
                error = 'El teléfono contiene caracteres no válidos';
            } else if (value.length > 20) {
                error = 'El teléfono no puede exceder 20 caracteres';
            }
            break;
            
        case 'correo':
            if (value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                error = 'El formato del correo no es válido';
            } else if (value.length > 100) {
                error = 'El correo no puede exceder 100 caracteres';
            }
            break;
            
        case 'direccion':
            if (value.length > 200) {
                error = 'La dirección no puede exceder 200 caracteres';
            }
            break;
            
        case 'descripcion':
            if (value.length > 1000) {
                error = 'La descripción no puede exceder 1000 caracteres';
            }
            break;
            
        case 'foto':
            if (value && !isValidUrl(value)) {
                error = 'La URL de la foto no es válida';
            }
            break;
    }

    if (error) {
        showFieldError(name, error);
        field.classList.add('error');
    } else {
        clearFieldError(field);
        field.classList.remove('error');
    }

    return !error;
}

function isValidUrl(string) {
    try {
        new URL(string);
        return true;
    } catch (_) {
        return false;
    }
}

async function handleFormSubmit(e) {
    e.preventDefault();
    
    if (isSubmitting) {
        return;
    }
    
    clearAllErrors();
    
    // Validar todos los campos
    const form = e.target;
    const inputs = form.querySelectorAll('input, textarea');
    let isValid = true;
    
    inputs.forEach(input => {
        if (!validateField(input)) {
            isValid = false;
        }
    });
    
    if (!isValid) {
        showMessage('Por favor, corrije los errores en el formulario', 'error');
        return;
    }
    
    isSubmitting = true;
    setSubmitButton(true);
    
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Limpiar campos vacíos para enviar como null
    Object.keys(data).forEach(key => {
        if (data[key] === '') {
            data[key] = null;
        }
    });
    
    const isEditing = editingHotelId !== null;
    const action = isEditing ? 'update' : 'create';
    const url = window.location.href.split('?')[0] + '?action=' + action;
    
    try {
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Error parsing JSON:', parseError);
            console.error('Response text:', responseText);
            throw new Error('Respuesta inválida del servidor');
        }
        
        if (result.success) {
            showMessage(result.message, 'success');
            resetForm();
            await loadHotels();
        } else if (result.errors) {
            showFormErrors(result.errors);
            showMessage('Por favor, corrije los errores indicados', 'error');
        } else {
            throw new Error(result.error || 'Error desconocido');
        }
        
    } catch (error) {
        console.error('Error:', error);
        showMessage('Error de conexión: ' + error.message, 'error');
    } finally {
        isSubmitting = false;
        setSubmitButton(false);
    }
}

async function loadHotels() {
    try {
        const url = window.location.href.split('?')[0] + '?action=read';
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const responseText = await response.text();
        
        let result;
        try {
            result = JSON.parse(responseText);
        } catch (parseError) {
            console.error('Error parsing JSON en loadHotels:', parseError);
            throw new Error('Respuesta inválida del servidor');
        }
        
        const hotelsList = document.getElementById('hotelsList');
        
        if (result.success && result.data && result.data.length > 0) {
            hotelsList.innerHTML = result.data.map(hotel => createHotelCard(hotel)).join('');
        } else if (result.success) {
            hotelsList.innerHTML = '<div class="no-hotels">No hay hoteles registrados</div>';
        } else {
            throw new Error(result.error || 'Error desconocido');
        }
        
    } catch (error) {
        console.error('Error en loadHotels:', error);
        document.getElementById('hotelsList').innerHTML = 
            '<div class="alert alert-error">Error al cargar hoteles: ' + error.message + '</div>';
    }
}

function createHotelCard(hotel) {
    const foto = hotel.foto ? `<img src="${escapeHtml(hotel.foto)}" alt="Foto del hotel" style="max-width: 100px; height: auto; border-radius: 5px; margin-bottom: 1rem;" onerror="this.style.display='none'">` : '';
    
    // Preparar la descripción con función de expandir/contraer
    let descripcionHtml = '';
    if (hotel.descripcion) {
        const descripcion = escapeHtml(hotel.descripcion);
        const isLong = descripcion.length > 150;
        const preview = isLong ? descripcion.substring(0, 150) + '...' : descripcion;
        
        descripcionHtml = `
            <div class="info-item" style="grid-column: 1 / -1;">
                <div class="info-label">Descripción</div>
                <div class="info-value">
                    <div class="description-preview" id="desc-${hotel.id}">
                        ${isLong ? preview : descripcion}
                    </div>
                    ${isLong ? `
                        <div class="description-toggle" onclick="toggleDescription(${hotel.id})">
                            <span id="toggle-text-${hotel.id}">Ver más</span>
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    return `
        <div class="hotel-card">
            <div class="hotel-header">
                <div>
                    <div class="hotel-name">${escapeHtml(hotel.nombre)}</div>
                    <small style="color: #666;">NIT: ${escapeHtml(hotel.nit)}</small>
                </div>
                <div>
                    <button class="btn btn-edit" onclick="editHotel(${hotel.id})" title="Editar hotel">
                        Editar
                    </button>
                    <button class="btn btn-danger" onclick="deleteHotel(${hotel.id})" title="Eliminar hotel">
                        Eliminar
                    </button>
                </div>
            </div>
            ${foto}
            <div class="hotel-info">
                <div class="info-item">
                    <div class="info-label">Administrador</div>
                    <div class="info-value">${escapeHtml(hotel.admin_nombre || 'No encontrado')}</div>
                    <small style="color: #666;">Doc: ${escapeHtml(hotel.numDocumento)}</small>
                </div>
                <div class="info-item">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value">${escapeHtml(hotel.telefono || 'No registrado')}</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Correo</div>
                    <div class="info-value">${hotel.correo ? `<a href="mailto:${escapeHtml(hotel.correo)}" style="color: #667eea;">${escapeHtml(hotel.correo)}</a>` : 'No registrado'}</div>
                </div>
                <div class="info-item" style="grid-column: 1 / -1;">
                    <div class="info-label">Dirección</div>
                    <div class="info-value">${escapeHtml(hotel.direccion || 'No registrada')}</div>
                </div>
                ${descripcionHtml}
            </div>
        </div>
    `;
}

function toggleDescription(hotelId) {
    const descElement = document.getElementById(`desc-${hotelId}`);
    const toggleElement = document.getElementById(`toggle-text-${hotelId}`);
    
    if (descElement.classList.contains('expanded')) {
        descElement.classList.remove('expanded');
        toggleElement.textContent = 'Ver más';
        // Restaurar texto truncado
        const fullText = descElement.getAttribute('data-full-text');
        const preview = fullText.substring(0, 150) + '...';
        descElement.innerHTML = preview;
    } else {
        descElement.classList.add('expanded');
        toggleElement.textContent = 'Ver menos';
        // Mostrar texto completo
        if (!descElement.getAttribute('data-full-text')) {
            // Obtener el texto completo del hotel
            loadHotelDescription(hotelId, descElement);
        } else {
            descElement.innerHTML = descElement.getAttribute('data-full-text');
        }
    }
}

async function loadHotelDescription(hotelId, descElement) {
    try {
        const url = window.location.href.split('?')[0] + '?action=read&id=' + hotelId;
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        const result = await response.json();
        if (result.success && result.data && result.data.descripcion) {
            const fullText = escapeHtml(result.data.descripcion);
            descElement.setAttribute('data-full-text', fullText);
            descElement.innerHTML = fullText;
        }
    } catch (error) {
        console.error('Error loading description:', error);
    }
}

async function editHotel(id) {
    try {
        const url = window.location.href.split('?')[0] + '?action=read&id=' + id;
        const response = await fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success && result.data) {
            fillForm(result.data);
            editingHotelId = id;
            document.getElementById('submitText').textContent = 'Actualizar Hotel';
            document.getElementById('cancelBtn').style.display = 'inline-block';
            document.querySelector('.form-container').scrollIntoView({ behavior: 'smooth' });
            showMessage('Editando hotel: ' + result.data.nombre, 'success');
        } else {
            throw new Error(result.error || 'Hotel no encontrado');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('Error al cargar datos del hotel: ' + error.message, 'error');
    }
}

async function deleteHotel(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este hotel?\n\nEsta acción no se puede deshacer.')) {
        return;
    }
    
    try {
        const url = window.location.href.split('?')[0] + '?action=delete';
        const response = await fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: id })
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const result = await response.json();
        
        if (result.success) {
            showMessage(result.message, 'success');
            await loadHotels();
        } else {
            throw new Error(result.error || 'Error desconocido');
        }
    } catch (error) {
        console.error('Error:', error);
        showMessage('Error al eliminar hotel: ' + error.message, 'error');
    }
}

function fillForm(hotel) {
    document.getElementById('hotelId').value = hotel.id || '';
    document.getElementById('nit').value = hotel.nit || '';
    document.getElementById('nombre').value = hotel.nombre || '';
    document.getElementById('direccion').value = hotel.direccion || '';
    document.getElementById('descripcion').value = hotel.descripcion || '';
    document.getElementById('numDocumento').value = hotel.numDocumento || '';
    document.getElementById('telefono').value = hotel.telefono || '';
    document.getElementById('correo').value = hotel.correo || '';
    document.getElementById('foto').value = hotel.foto || '';
    
    // Actualizar contadores de caracteres
    updateCharCounter(document.getElementById('nombre'), document.getElementById('nombre-counter'), 100);
    updateCharCounter(document.getElementById('direccion'), document.getElementById('direccion-counter'), 200);
    updateCharCounter(document.getElementById('descripcion'), document.getElementById('descripcion-counter'), 1000);
}

function cancelEdit() {
    if (editingHotelId && confirm('¿Estás seguro de que quieres cancelar la edición?')) {
        resetForm();
    } else if (!editingHotelId) {
        resetForm();
    }
}

function resetForm() {
    document.getElementById('hotelForm').reset();
    editingHotelId = null;
    document.getElementById('submitText').textContent = 'Guardar Hotel';
    document.getElementById('cancelBtn').style.display = 'none';
    clearAllErrors();
    clearMessages();
    
    // Remover clases de error
    document.querySelectorAll('input, textarea').forEach(input => {
        input.classList.remove('error');
    });
    
    // Reinicializar contadores
    setupCharCounters();
}

function setSubmitButton(loading) {
    const btn = document.getElementById('submitBtn');
    const text = document.getElementById('submitText');
    
    if (loading) {
        btn.disabled = true;
        text.textContent = 'Procesando...';
    } else {
        btn.disabled = false;
        text.textContent = editingHotelId ? 'Actualizar Hotel' : 'Guardar Hotel';
    }
}

function clearAllErrors() {
    document.querySelectorAll('.error').forEach(el => {
        el.textContent = '';
    });
}

function clearFieldError(field) {
    const errorEl = document.getElementById(field.name + '-error');
    if (errorEl) {
        errorEl.textContent = '';
    }
}

function showFieldError(fieldName, message) {
    const errorEl = document.getElementById(fieldName + '-error');
    if (errorEl) {
        errorEl.textContent = message;
    }
}

function showFormErrors(errors) {
    for (const [field, message] of Object.entries(errors)) {
        showFieldError(field, message);
        const input = document.getElementById(field);
        if (input) {
            input.classList.add('error');
        }
    }
}

function showMessage(message, type = 'info') {
    const messagesEl = document.getElementById('form-messages');
    const alertClass = type === 'error' ? 'alert-error' : 'alert-success';
    
    messagesEl.innerHTML = `<div class="alert ${alertClass}">${escapeHtml(message)}</div>`;
    
    // Auto-hide success messages
    if (type === 'success') {
        setTimeout(() => {
            clearMessages();
        }, 5000);
    }
}

function clearMessages() {
    document.getElementById('form-messages').innerHTML = '';
}

function escapeHtml(text) {
    if (!text && text !== 0) return '';
    const div = document.createElement('div');
    div.textContent = text.toString();
    return div.innerHTML;
}

// Manejo de errores globales
window.addEventListener('error', function(e) {
    console.error('Error global:', e.error);
});

window.addEventListener('unhandledrejection', function(e) {
    console.error('Promise rechazado:', e.reason);
    showMessage('Ocurrió un error inesperado: ' + e.reason, 'error');
});