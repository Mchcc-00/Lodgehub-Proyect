document.addEventListener('DOMContentLoaded', () => {
    const API_URL = '/app/controllers/mantenimientoController.php';
    const form = document.getElementById('form-crear-mantenimiento');
    const selectFrecuencia = document.getElementById('frecuencia');
    const grupoCantFrecuencia = document.getElementById('grupo-cantFrecuencia');
    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const previewBtn = document.getElementById('preview-btn');
    const previewSection = document.getElementById('mantenimiento-preview');
    const previewContent = document.getElementById('preview-content');

    // Obtener los selects después de la simplificación
    const selectHabitacion = document.getElementById('id_habitacion');
    const selectResponsable = document.getElementById('numDocumento');

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        setTimeout(() => { elem.style.display = 'none'; }, 5000);
    };

    // Mostrar/ocultar campo de frecuencia
    selectFrecuencia.addEventListener('change', (e) => {
        grupoCantFrecuencia.style.display = e.target.value === 'Sí' ? 'block' : 'none';
    });

    // Mostrar vista previa
    previewBtn.addEventListener('click', () => {
        const formData = new FormData(form);
        const datos = Object.fromEntries(formData.entries());

        // Obtener el texto de las opciones seleccionadas para una mejor visualización
        const habitacionTexto = selectHabitacion.options[selectHabitacion.selectedIndex]?.text || 'No seleccionada';
        const responsableTexto = selectResponsable.options[selectResponsable.selectedIndex]?.text || 'No seleccionado';

        const previewHTML = `
            <div class="preview-item">
                <span class="preview-label">Hotel:</span>
                <span class="preview-value">${datos.hotel_nombre || 'No asignado'}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Habitación:</span>
                <span class="preview-value">${habitacionTexto}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Tipo:</span>
                <span class="preview-value">${datos.tipo || 'No especificado'}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Prioridad:</span>
                <span class="preview-value">${datos.prioridad || 'No especificado'}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Responsable:</span>
                <span class="preview-value">${responsableTexto}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Descripción:</span>
                <span class="preview-value">${datos.problemaDescripcion || 'Sin descripción'}</span>
            </div>
        `;

        previewContent.innerHTML = previewHTML;
        previewSection.style.display = 'block';
        // Hacer scroll para que el usuario vea la vista previa
        previewSection.scrollIntoView({ behavior: 'smooth', block: 'center' });
    });

    // Enviar formulario
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            return;
        }
        form.classList.remove('was-validated');

        const formData = new FormData(form);
        
        // Si la frecuencia es 'No', asegurarse de que cantFrecuencia no se envíe o sea un valor por defecto
        if (formData.get('frecuencia') === 'No') {
            formData.set('cantFrecuencia', 'Diario'); // O el valor por defecto de tu ENUM
        }

        try {
            const response = await fetch(`${API_URL}?action=crear`, {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                mostrarMensaje(resultado.message, 'success');
                form.reset();
                grupoCantFrecuencia.style.display = 'none';
                // Opcional: redirigir a la lista después de un tiempo
                setTimeout(() => {
                    window.location.href = 'listaMantenimiento.php';
                }, 2000);
            } else {
                mostrarMensaje(resultado.message, 'error');
            }
        } catch (error) {
            console.error('Error al enviar el formulario:', error);
            mostrarMensaje('Error de conexión al intentar guardar.', 'error');
        }
    });

    // Botón de limpiar
    document.getElementById('reset-btn').addEventListener('click', () => {
        form.reset();
        form.classList.remove('was-validated');
        grupoCantFrecuencia.style.display = 'none';
        previewSection.style.display = 'none'; // Ocultar vista previa al limpiar
    });
});
