document.addEventListener('DOMContentLoaded', function() {
    // Elementos del DOM
    const form = document.getElementById('form-crear-mantenimiento');
    if (!form) return; // Si no hay formulario, no hacer nada

    const selectHabitacion = document.getElementById('id_habitacion');
    const selectResponsable = document.getElementById('numDocumento');
    const selectFrecuencia = document.getElementById('frecuencia');
    const grupoCantFrecuencia = document.getElementById('grupo-cantFrecuencia');
    const resetBtn = document.getElementById('reset-btn');
    const previewBtn = document.getElementById('preview-btn');
    const mantenimientoPreview = document.getElementById('mantenimiento-preview');
    const previewContent = document.getElementById('preview-content');
    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');

    // Cargar datos iniciales (habitaciones y responsables)
    cargarHabitaciones();
    cargarResponsables();

    // --- EVENT LISTENERS ---

    // Mostrar/ocultar campo de frecuencia
    selectFrecuencia.addEventListener('change', function() {
        grupoCantFrecuencia.style.display = this.value === 'Sí' ? 'block' : 'none';
    });

    // Enviar formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        if (!this.checkValidity()) {
            e.stopPropagation();
            this.classList.add('was-validated');
            return;
        }

        const submitBtn = this.querySelector('button[type="submit"]');
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Guardando...`;

        try {
            const formData = new FormData(this);
            const response = await fetch('/app/controllers/mantenimientoController.php?action=crear', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                mostrarMensaje(successMessage, successText, result.message);
                form.reset();
                form.classList.remove('was-validated');
                // Recargar las habitaciones para que la recién asignada ya no aparezca
                cargarHabitaciones();
            } else {
                mostrarMensaje(errorMessage, errorText, result.message || 'Error desconocido.');
            }

        } catch (error) {
            mostrarMensaje(errorMessage, errorText, 'Error de conexión con el servidor.');
            console.error('Error:', error);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    });

    // Botón de limpiar
    resetBtn.addEventListener('click', function() {
        form.reset();
        form.classList.remove('was-validated');
        mantenimientoPreview.style.display = 'none';
        grupoCantFrecuencia.style.display = 'none';
        hideMessages();
    });

    // Botón de vista previa
    previewBtn.addEventListener('click', function() {
        if (mantenimientoPreview.style.display === 'none') {
            mostrarVistaPrevia();
        } else {
            mantenimientoPreview.style.display = 'none';
        }
    });

    // --- FUNCIONES AUXILIARES ---

    async function cargarHabitaciones() {
        try {
            const response = await fetch('/app/controllers/mantenimientoController.php?action=obtenerHabitaciones');
            const result = await response.json();

            selectHabitacion.innerHTML = '<option value="">Selecciona una habitación</option>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(hab => {
                    const option = document.createElement('option');
                    option.value = hab.id;
                    option.textContent = `N° ${hab.numero} - ${hab.tipo_descripcion}`;
                    selectHabitacion.appendChild(option);
                });
            } else {
                selectHabitacion.innerHTML = '<option value="" disabled>No hay habitaciones disponibles para mantenimiento</option>';
            }
        } catch (error) {
            console.error('Error al cargar habitaciones:', error);
            selectHabitacion.innerHTML = '<option value="" disabled>Error al cargar habitaciones</option>';
        }
    }

    async function cargarResponsables() {
        try {
            const response = await fetch('../controllers/mantenimientoController.php?action=obtenerColaboradores');
            const result = await response.json();

            selectResponsable.innerHTML = '<option value="">Selecciona un responsable</option>';

            if (result.success && result.data.length > 0) {
                result.data.forEach(col => {
                    const option = document.createElement('option');
                    option.value = col.numDocumento;
                    option.textContent = `${col.nombres} ${col.apellidos}`;
                    selectResponsable.appendChild(option);
                });
            } else {
                selectResponsable.innerHTML = '<option value="" disabled>No hay colaboradores disponibles</option>';
            }
        } catch (error) {
            console.error('Error al cargar responsables:', error);
            selectResponsable.innerHTML = '<option value="" disabled>Error al cargar responsables</option>';
        }
    }

    function mostrarVistaPrevia() {
        const data = {
            'Habitación': selectHabitacion.options[selectHabitacion.selectedIndex]?.text,
            'Tipo': form.tipo.value,
            'Prioridad': form.prioridad.value,
            'Responsable': selectResponsable.options[selectResponsable.selectedIndex]?.text,
            'Descripción': form.problemaDescripcion.value,
            'Recurrente': form.frecuencia.value,
        };

        if (form.frecuencia.value === 'Sí') {
            data['Frecuencia'] = form.cantFrecuencia.value;
        }

        let html = '';
        for (const [label, value] of Object.entries(data)) {
            if (value) {
                html += `
                    <div class="preview-item">
                        <span class="preview-label">${label}:</span>
                        <span class="preview-value">${value}</span>
                    </div>
                `;
            }
        }

        previewContent.innerHTML = html;
        mantenimientoPreview.style.display = 'block';
    }

    function mostrarMensaje(container, textElement, message) {
        hideMessages();
        textElement.textContent = message;
        container.style.display = 'block';
        container.scrollIntoView({ behavior: 'smooth', block: 'center' });

        setTimeout(() => {
            container.style.display = 'none';
        }, 5000);
    }

    function hideMessages() {
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    }
});
