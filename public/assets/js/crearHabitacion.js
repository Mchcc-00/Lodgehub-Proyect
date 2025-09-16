document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-crear-habitacion');
    if (!form) return;

    const successMessage = document.getElementById('success-message');
    const successText = document.getElementById('success-text');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');
    const resetBtn = document.getElementById('reset-btn');
    const fotoInput = document.getElementById('foto');
    const previewContainer = document.getElementById('preview-container');
    const imagePreview = document.getElementById('image-preview');

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        window.scrollTo(0, 0);
        setTimeout(() => { elem.style.display = 'none'; }, 5000);
    };

    const ocultarMensajes = () => {
        if (successMessage) successMessage.style.display = 'none';
        if (errorMessage) errorMessage.style.display = 'none';
    };

    // Vista previa de la imagen
    fotoInput.addEventListener('change', function() {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                previewContainer.style.display = 'block';
            }
            reader.readAsDataURL(this.files[0]);
        } else {
            previewContainer.style.display = 'none';
        }
    });

    // Limpiar formulario
    resetBtn.addEventListener('click', () => {
        form.reset();
        form.classList.remove('was-validated');
        previewContainer.style.display = 'none';
        imagePreview.src = '#';
        ocultarMensajes();
    });

    // Envío del formulario
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        ocultarMensajes();

        if (!form.checkValidity()) {
            e.stopPropagation();
            form.classList.add('was-validated');
            mostrarMensaje('Por favor, completa todos los campos requeridos.', 'error');
            return;
        }

        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        submitBtn.innerHTML = `<i class="fas fa-spinner fa-spin"></i> Guardando...`;

        const formData = new FormData(form);

        try {
            const response = await fetch('../controllers/habitacionesController.php?action=crear', {
                method: 'POST',
                body: formData
            });

            // Verificar si la respuesta es JSON
            const contentType = response.headers.get("content-type");
            if (contentType && contentType.indexOf("application/json") !== -1) {
                const resultado = await response.json();
                if (resultado.success) {
                    mostrarMensaje(resultado.message, 'success');
                    form.reset();
                    form.classList.remove('was-validated');
                    previewContainer.style.display = 'none';
                    imagePreview.src = '#';
                    // Opcional: Redireccionar a la lista después de un momento
                    setTimeout(() => {
                        window.location.href = 'listaHabitaciones.php';
                    }, 2000);
                } else {
                    throw new Error(resultado.message || 'Error desconocido al crear la habitación.');
                }
            } else {
                const textResponse = await response.text();
                throw new Error('Respuesta inesperada del servidor: ' + textResponse);
            }

        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnText;
        }
    });
});
