document.addEventListener('DOMContentLoaded', () => {
    const API_URL = '/app/controllers/tipoHabitacionController.php';
    const form = document.getElementById('form-crear-tipo-habitacion');
    if (!form) return; // Salir si el formulario no existe en la página

    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const resetBtn = document.getElementById('reset-btn');

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        window.scrollTo(0, 0); // Scroll hacia arriba para ver el mensaje
        setTimeout(() => { elem.style.display = 'none'; }, 5000);
    };

    const ocultarMensajes = () => {
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    };

    // Enviar formulario
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        ocultarMensajes();

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            mostrarMensaje('Por favor, completa todos los campos requeridos.', 'error');
            return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Guardando...`;

        try {
            const response = await fetch(`${API_URL}?action=crear`, {
                method: 'POST',
                body: formData
            });

            const contentType = response.headers.get("content-type");
            if (!contentType || !contentType.includes("application/json")) {
                const textResponse = await response.text();
                throw new Error('Respuesta inesperada del servidor: ' + textResponse);
            }

            const resultado = await response.json();

            if (resultado.success) {
                mostrarMensaje(resultado.message, 'success');
                form.reset();
                form.classList.remove('was-validated');
                setTimeout(() => {
                    window.location.href = 'listaTipoHabitacion.php';
                }, 2000);
            } else {
                throw new Error(resultado.message || 'Error desconocido al crear el tipo de habitación.');
            }
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });

    // Botón de limpiar
    resetBtn.addEventListener('click', () => {
        form.reset();
        form.classList.remove('was-validated');
        ocultarMensajes();
    });
});
