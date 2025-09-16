document.addEventListener('DOMContentLoaded', () => {
    const API_URL = '../controllers/huespedController.php';
    const form = document.getElementById('form-crear-huesped');
    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const previewBtn = document.getElementById('preview-btn');
    const previewSection = document.getElementById('huesped-preview');
    const previewContent = document.getElementById('preview-content');

    const numDocumentoInput = document.getElementById('numDocumento');
    const correoInput = document.getElementById('correo');
    const documentoFeedback = document.getElementById('documento-feedback');
    const correoFeedback = document.getElementById('correo-feedback');

    let debounceTimer;

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        window.scrollTo(0, 0);
        setTimeout(() => { elem.style.display = 'none'; }, 5000);
    };

    const verificarExistencia = async (campo, valor, feedbackElement) => {
        if (!valor) {
            feedbackElement.style.display = 'none';
            return;
        }

        feedbackElement.textContent = 'Verificando...';
        feedbackElement.className = 'checking';
        feedbackElement.style.display = 'block';

        try {
            const response = await fetch(`${API_URL}?action=verificar&campo=${campo}&valor=${encodeURIComponent(valor)}`);
            const resultado = await response.json();

            if (resultado.success) {
                if (resultado.data.existe) {
                    feedbackElement.textContent = `Este ${campo} ya está registrado.`;
                    feedbackElement.className = 'invalid';
                } else {
                    feedbackElement.textContent = `El ${campo} está disponible.`;
                    feedbackElement.className = 'valid';
                }
            } else {
                feedbackElement.style.display = 'none';
            }
        } catch (error) {
            console.error(`Error al verificar ${campo}:`, error);
            feedbackElement.style.display = 'none';
        }
    };

    const mostrarVistaPrevia = () => {
        const formData = new FormData(form);
        const datos = Object.fromEntries(formData.entries());

        const previewHTML = `
            <div class="preview-item">
                <span class="preview-label">Documento:</span>
                <span class="preview-value">${datos.tipoDocumento || ''} ${datos.numDocumento || ''}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Nombre Completo:</span>
                <span class="preview-value">${datos.nombres || ''} ${datos.apellidos || ''}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Correo:</span>
                <span class="preview-value">${datos.correo || ''}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Teléfono:</span>
                <span class="preview-value">${datos.numTelefono || ''}</span>
            </div>
            <div class="preview-item">
                <span class="preview-label">Sexo:</span>
                <span class="preview-value">${datos.sexo || ''}</span>
            </div>
        `;

        previewContent.innerHTML = previewHTML;
        previewSection.style.display = 'block';
        previewSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
    };

    const enviarFormulario = async (e) => {
        e.preventDefault();
        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            mostrarMensaje('Por favor, corrige los campos marcados en rojo.', 'error');
            return;
        }
        form.classList.remove('was-validated');

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        try {
            const response = await fetch(`${API_URL}?action=crear`, {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (response.ok && resultado.success) {
                mostrarMensaje(resultado.message, 'success');
                form.reset();
                previewSection.style.display = 'none';
                documentoFeedback.style.display = 'none';
                correoFeedback.style.display = 'none';
                setTimeout(() => {
                    window.location.href = 'listaHuesped.php';
                }, 2000);
            } else {
                mostrarMensaje(resultado.message || 'Error desconocido.', 'error');
            }
        } catch (error) {
            console.error('Error al enviar el formulario:', error);
            mostrarMensaje('Error de conexión al intentar guardar el huésped.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Huésped';
        }
    };

    // --- EVENT LISTENERS ---

    numDocumentoInput.addEventListener('keyup', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            verificarExistencia('numDocumento', e.target.value, documentoFeedback);
        }, 500);
    });

    correoInput.addEventListener('keyup', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            verificarExistencia('correo', e.target.value, correoFeedback);
        }, 500);
    });

    previewBtn.addEventListener('click', mostrarVistaPrevia);

    form.addEventListener('submit', enviarFormulario);

    document.getElementById('reset-btn').addEventListener('click', () => {
        form.reset();
        form.classList.remove('was-validated');
        previewSection.style.display = 'none';
        documentoFeedback.style.display = 'none';
        correoFeedback.style.display = 'none';
    });
});
