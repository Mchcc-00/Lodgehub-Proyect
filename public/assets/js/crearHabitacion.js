document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('form-crear-habitacion');
    if (!form) return;

    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const successText = document.getElementById('success-text');
    const errorText = document.getElementById('error-text');
    const fotoInput = document.getElementById('foto');
    const imagePreview = document.getElementById('image-preview');
    const previewContainer = document.getElementById('preview-container');
    const resetBtn = document.getElementById('reset-btn');

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? successText : errorText;
        
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        window.scrollTo(0, 0);

        setTimeout(() => {
            elem.style.display = 'none';
        }, 5000);
    };

    const enviarFormulario = async (e) => {
        e.preventDefault();

        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            mostrarMensaje('Por favor, completa todos los campos requeridos.', 'error');
            return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        const originalBtnHtml = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        try {
            const response = await fetch('/app/controllers/habitacionesController.php?action=crear', {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (resultado.success) {
                mostrarMensaje('¡Habitación creada exitosamente!', 'success');
                form.reset();
                form.classList.remove('was-validated');
                previewContainer.style.display = 'none';
                imagePreview.src = '#';
            } else {
                throw new Error(resultado.message || 'Ocurrió un error desconocido.');
            }
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalBtnHtml;
        }
    };

    const mostrarPreviewImagen = () => {
        const file = fotoInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                imagePreview.src = e.target.result;
                previewContainer.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none';
            imagePreview.src = '#';
        }
    };

    const limpiarFormulario = () => {
        form.reset();
        form.classList.remove('was-validated');
        previewContainer.style.display = 'none';
        imagePreview.src = '#';
        window.scrollTo(0, 0);
    };

    form.addEventListener('submit', enviarFormulario);
    fotoInput.addEventListener('change', mostrarPreviewImagen);
    resetBtn.addEventListener('click', limpiarFormulario);
});
