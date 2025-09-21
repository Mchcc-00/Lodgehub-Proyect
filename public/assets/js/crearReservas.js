document.addEventListener('DOMContentLoaded', () => {
    // --- CONSTANTES Y VARIABLES ---
    const form = document.getElementById('form-crear-reserva');
    if (!form) return;

    const buscarHuespedInput = document.getElementById('buscar-huesped');
    const sugerenciasHuesped = document.getElementById('sugerencias-huesped');
    const huespedSeleccionadoDiv = document.getElementById('huesped-seleccionado');
    const hueNumDocumentoInput = document.getElementById('hue_numDocumento');

    const fechaInicioInput = document.getElementById('fechainicio');
    const fechaFinInput = document.getElementById('fechaFin');
    const habitacionSelect = document.getElementById('id_habitacion');
    const detallesHabitacionDiv = document.getElementById('detalles-habitacion');

    const previewBtn = document.getElementById('preview-btn');
    const reservaPreviewDiv = document.getElementById('reserva-preview');
    const previewContentDiv = document.getElementById('preview-content');
    const costoTotalSpan = document.getElementById('costo-total');
    const pagoFinalInput = document.getElementById('pagoFinal');

    const successMessage = document.getElementById('success-message');
    const errorMessage = document.getElementById('error-message');
    const errorText = document.getElementById('error-text');

    let habitacionesDisponibles = [];
    let debounceTimer;

    // --- FUNCIONES ---

    const mostrarMensaje = (mensaje, tipo = 'success') => {
        const elem = tipo === 'success' ? successMessage : errorMessage;
        const textElem = tipo === 'success' ? document.getElementById('success-text') : errorText;
        textElem.textContent = mensaje;
        elem.style.display = 'block';
        window.scrollTo(0, 0);
        setTimeout(() => { elem.style.display = 'none'; }, 5000);
    };

    // --- BÚSQUEDA DE HUÉSPEDES ---
    const buscarHuespedes = async (termino) => {
        if (termino.length < 2) {
            sugerenciasHuesped.innerHTML = '';
            return;
        }
        try {
            const response = await fetch(`/app/controllers/huespedController.php?action=buscar&termino=${encodeURIComponent(termino)}`);
            const resultado = await response.json();
            sugerenciasHuesped.innerHTML = '';
            if (resultado.success && resultado.data.length > 0) {
                resultado.data.forEach(huesped => {
                    const item = document.createElement('a');
                    item.href = '#';
                    item.className = 'list-group-item list-group-item-action';
                    item.textContent = `${huesped.nombres} ${huesped.apellidos} (${huesped.numDocumento})`;
                    item.addEventListener('click', (e) => {
                        e.preventDefault();
                        seleccionarHuesped(huesped);
                    });
                    sugerenciasHuesped.appendChild(item);
                });
            } else {
                sugerenciasHuesped.innerHTML = '<div class="list-group-item">No se encontraron huéspedes. <a href="crearHuesped.php" target="_blank">Crear nuevo</a></div>';
            }
        } catch (error) {
            console.error('Error buscando huéspedes:', error);
        }
    };

    const seleccionarHuesped = (huesped) => {
        buscarHuespedInput.value = `${huesped.nombres} ${huesped.apellidos}`;
        hueNumDocumentoInput.value = huesped.numDocumento;
        huespedSeleccionadoDiv.innerHTML = `<strong>Huésped:</strong> ${huesped.nombres} ${huesped.apellidos} | <strong>Doc:</strong> ${huesped.numDocumento}`;
        huespedSeleccionadoDiv.style.display = 'block';
        sugerenciasHuesped.innerHTML = '';
        hueNumDocumentoInput.dispatchEvent(new Event('change')); // Para validación
    };

    // --- BÚSQUEDA DE HABITACIONES DISPONIBLES ---
    const buscarHabitacionesDisponibles = async () => {
        const fechaInicio = fechaInicioInput.value;
        const fechaFin = fechaFinInput.value;
        const hotelId = document.getElementById('id_hotel').value; // Leer el ID del hotel

        if (!fechaInicio || !fechaFin || new Date(fechaFin) <= new Date(fechaInicio) || !hotelId) {
            habitacionSelect.innerHTML = '<option value="">Fechas no válidas</option>';
            habitacionSelect.disabled = true;
            return;
        }

        habitacionSelect.innerHTML = '<option value="">Buscando habitaciones...</option>';
        habitacionSelect.disabled = true;

        try {
            const response = await fetch(`/app/controllers/reservasController.php?action=habitacionesDisponibles&fechainicio=${fechaInicio}&fechaFin=${fechaFin}&id_hotel=${hotelId}`);
            const resultado = await response.json();

            habitacionSelect.innerHTML = '';
            if (resultado.success && resultado.data.length > 0) {
                habitacionesDisponibles = resultado.data;
                habitacionSelect.innerHTML = '<option value="">Selecciona una habitación</option>';
                resultado.data.forEach(hab => {
                    const option = document.createElement('option');
                    option.value = hab.id;
                    option.textContent = `N° ${hab.numero} - ${hab.tipo_descripcion} ($${parseFloat(hab.costo).toLocaleString('es-CO')})`;
                    habitacionSelect.appendChild(option);
                });
                habitacionSelect.disabled = false;
            } else {
                habitacionSelect.innerHTML = '<option value="">No hay habitaciones disponibles</option>';
            }
        } catch (error) {
            console.error('Error buscando habitaciones:', error);
            habitacionSelect.innerHTML = '<option value="">Error al buscar</option>';
        }
    };

    const mostrarDetallesHabitacion = (idHabitacion) => {
        const habitacion = habitacionesDisponibles.find(h => h.id == idHabitacion);
        if (habitacion) {
            document.getElementById('hab-tipo').textContent = habitacion.tipo_descripcion;
            document.getElementById('hab-capacidad').textContent = habitacion.capacidad;
            document.getElementById('hab-costo').textContent = parseFloat(habitacion.costo).toLocaleString('es-CO');
            detallesHabitacionDiv.style.display = 'block';
        } else {
            detallesHabitacionDiv.style.display = 'none';
        }
    };

    // --- CÁLCULO Y VISTA PREVIA ---
    const calcularYMostrarResumen = () => {
        const idHabitacion = habitacionSelect.value;
        const habitacion = habitacionesDisponibles.find(h => h.id == idHabitacion);
        const fechaInicio = new Date(fechaInicioInput.value + 'T00:00:00');
        const fechaFin = new Date(fechaFinInput.value + 'T00:00:00');

        if (!habitacion || isNaN(fechaInicio) || isNaN(fechaFin) || fechaFin <= fechaInicio) {
            reservaPreviewDiv.style.display = 'none';
            return false;
        }

        const diffTime = Math.abs(fechaFin - fechaInicio);
        const noches = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        const costoTotal = noches * parseFloat(habitacion.costo);

        previewContentDiv.innerHTML = `
            <p><strong>Huésped:</strong> ${buscarHuespedInput.value}</p>
            <p><strong>Check-in:</strong> ${fechaInicio.toLocaleDateString()}</p>
            <p><strong>Check-out:</strong> ${fechaFin.toLocaleDateString()}</p>
            <p><strong>Noches:</strong> ${noches}</p>
            <p><strong>Habitación:</strong> N° ${habitacion.numero} (${habitacion.tipo_descripcion})</p>
            <p><strong>Costo por noche:</strong> $${parseFloat(habitacion.costo).toLocaleString('es-CO')}</p>
        `;

        costoTotalSpan.textContent = costoTotal.toLocaleString('es-CO', { minimumFractionDigits: 2 });
        pagoFinalInput.value = costoTotal;
        reservaPreviewDiv.style.display = 'block';
        return true;
    };

    // --- ENVÍO DE FORMULARIO ---
    const enviarFormulario = async (e) => {
        e.preventDefault();
        if (!form.checkValidity() || !calcularYMostrarResumen()) {
            form.classList.add('was-validated');
            mostrarMensaje('Por favor, completa todos los campos requeridos y calcula el resumen.', 'error');
            return;
        }

        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        try {
            const response = await fetch('/app/controllers/reservasController.php?action=crear', {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();

            if (resultado.success) {
                mostrarMensaje('¡Reserva creada exitosamente!', 'success');
                form.reset();
                form.classList.remove('was-validated');
                huespedSeleccionadoDiv.style.display = 'none';
                detallesHabitacionDiv.style.display = 'none';
                reservaPreviewDiv.style.display = 'none';
            } else {
                throw new Error(resultado.message || 'Ocurrió un error desconocido.');
            }
        } catch (error) {
            mostrarMensaje(error.message, 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-save"></i> Guardar Reserva';
        }
    };

    // --- EVENT LISTENERS ---

    buscarHuespedInput.addEventListener('keyup', (e) => {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            buscarHuespedes(e.target.value);
        }, 300);
    });

    document.addEventListener('click', (e) => {
        if (!sugerenciasHuesped.contains(e.target) && e.target !== buscarHuespedInput) {
            sugerenciasHuesped.innerHTML = '';
        }
    });

    fechaInicioInput.addEventListener('change', buscarHabitacionesDisponibles);
    fechaFinInput.addEventListener('change', buscarHabitacionesDisponibles);

    habitacionSelect.addEventListener('change', (e) => {
        mostrarDetallesHabitacion(e.target.value);
        reservaPreviewDiv.style.display = 'none'; // Ocultar resumen si cambia la habitación
    });

    previewBtn.addEventListener('click', () => {
        if (!form.checkValidity()) {
            form.classList.add('was-validated');
            mostrarMensaje('Por favor, completa todos los campos requeridos antes de ver el resumen.', 'error');
        } else {
            if (!calcularYMostrarResumen()) {
                mostrarMensaje('No se pudo calcular el resumen. Verifica los datos.', 'error');
            }
        }
    });

    document.getElementById('reset-btn').addEventListener('click', () => {
        form.reset();
        form.classList.remove('was-validated');
        huespedSeleccionadoDiv.style.display = 'none';
        detallesHabitacionDiv.style.display = 'none';
        reservaPreviewDiv.style.display = 'none';
        habitacionSelect.innerHTML = '<option value="">Selecciona las fechas primero</option>';
        habitacionSelect.disabled = true;
    });

    form.addEventListener('submit', enviarFormulario);
});
