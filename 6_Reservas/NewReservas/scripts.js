// Nuevo huesped
function NuevoHuesped() {
    const registrarHuesped = confirm("¿Deseas registrar un nuevo huesped?");
    if (registrarHuesped) {
        window.location.href = "crearReservaNewHuesped.php";
    }
}

// Generar form reserva (función llamada por el botón "Generar Reserva" en buscarHuespedExist.php)
function generarReserva(event) {
    event.preventDefault(); // Evita el envío automático inicial del formulario
    const generar = confirm("¿Desea generar una nueva reserva?");
    if (generar) {
        event.target.submit(); // Envía el formulario si se confirma
    }
}

// Botón limpiar para CUALQUIER formulario
function limpiarFormulario(event) {
    const limpiar = confirm("¿Limpiar formulario?");
    if (limpiar) {
        // Busca el formulario al que pertenece el botón que disparó el evento
        event.target.closest("form").reset(); 
    }
    event.preventDefault(); // Siempre previene el comportamiento por defecto del botón
}

// Botón Cancelar para CUALQUIER formulario
function cancelarReserva(event) { // Recibe el evento para encontrar el formulario
    const cancelar = confirm("¿Estás seguro de que deseas cancelar la reserva?");
    if (cancelar) {
        // Busca el formulario al que pertenece el botón que disparó el evento
        event.target.closest("form").reset(); 
        window.location.href = "crearReserva.php"; // Redirige a la página principal de crear reserva
    }
    // No se necesita preventDefault aquí si se redirige o si el confirm es false.
}

// Función para aplicar el filtro numérico
function aplicarFiltroNumerico() {
    this.value = this.value.replace(/\D/g, '');
    // Considera si este if(this.value.length > 2) es deseado para todos los campos numéricos.
    // Para adultos/niños/discapacitados podría ser, pero si tienes otros numéricos más largos, podría ser un problema.
    if (this.value.length > 2) {
        this.value = this.value.slice(0, 2);
    }
}

// Formateo de campo Total a Pagar como moneda
function formatCurrency() {
    let valor = this.value.replace(/\D/g, "");
    if (valor) {
        valor = (parseInt(valor, 10) / 100).toFixed(2);
        this.value = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(valor);
    } else {
        this.value = "";
    }
}

// VALIDACIONES PARA FORMULARIO DE NUEVO HUÉSPED (en crearReservaNewHuesped.php)
function verificarCampos(event) {
    let errores = [];

    const nombres = document.getElementById("nombresHuesped").value.trim();
    if (nombres === "") {
        errores.push("El campo Nombres es obligatorio.");
    }
    const apellidos = document.getElementById("apellidosHuesped").value.trim();
    if (apellidos === "") {
        errores.push("El campo Apellidos es obligatorio.");
    }
    const email = document.getElementById("emailHuesped").value.trim();
    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errores.push("El correo electrónico no es válido.");
    }
    const fechaInicio = document.getElementById("fechaInicio").value.trim();
    const fechaFin = document.getElementById("fechaFin").value.trim();
    if(fechaInicio === "" || fechaFin === "") { // Agregamos validación para que no estén vacías
        errores.push("Las fechas de inicio y salida son obligatorias.");
    } else if(fechaInicio > fechaFin) {
        errores.push("La fecha de inicio no puede ser posterior a la fecha de salida.");
    }
    const numDocumento = document.getElementById("numDocumentoHuesped").value.trim();
    if (!/^.{10,15}$/.test(numDocumento)) {
        errores.push("El número de documento debe tener entre 10 y 15 dígitos.");
    }
    const contacto = document.getElementById("contactoHuesped").value.trim();
    if (!/^.{10,15}$/.test(contacto)) {
        errores.push("El número de contacto debe tener entre 10 y 15 dígitos.");
    }
    const habitacion = document.getElementById("numHabitacionReserva").value.trim();
    if (habitacion === "") {
        errores.push("El número de habitación es obligatorio.");
    }
    const numEmpleado = document.getElementById("numEmpleadoReserva").value.trim();
    if (!/^.{10,15}$/.test(numEmpleado)) { // Ajusté el regex por consistencia
        errores.push("El número de documento del empleado debe tener entre 10 y 15 dígitos.");
    }

    const totalPagoInput = document.getElementById("totalPago"); // Referencia el elemento INPUT
    const totalPagoValue = totalPagoInput.value.trim(); // Obtiene el valor formateado del input

    if (totalPagoValue === "" || totalPagoValue === "$ 0,00" || parseFloat(totalPagoValue.replace(/[^0-9,-]+/g,"").replace(",", ".")) <= 0) {
        errores.push("El total a pagar es obligatorio y debe ser mayor que cero.");
    }

    if (errores.length > 0) {
        alert(errores.join("\n"));
        event.preventDefault();
    } else {
        const confirmar = confirm("¿Deseas registrar esta reserva?");
        if (!confirmar) {
            event.preventDefault();
        } else {
            console.log("Formulario de reserva de huésped nuevo confirmado, enviando datos...");
            // Aseguramos que 'valorLimpio' se calcule a partir del valor actual en el input
            let valorLimpio = totalPagoValue.replace(/[^0-9,-]+/g, "").replace(",", ".");
            
            // Asignamos el valor limpio al input ANTES de que el formulario se envíe
            totalPagoInput.value = valorLimpio;
        }
    }
}

// VALIDACIONES PARA FORMULARIO DE HUÉSPED EXISTENTE (en generarReserva.php)
function verificarCamposExist(event) {
    let errores = [];

    // ELIMINÉ LAS VALIDACIONES PARA NOMBRES, APELLIDOS, EMAIL, DOCUMENTO DEL HUESPED, CONTACTO
    // YA QUE ESOS CAMPOS NO SON EDITABLES EN generarReserva.php Y SOLO SE MUESTRAN COMO TEXTO.

    const fechaInicioExist = document.getElementById("fechaInicioExist").value.trim();
    const fechaFinExist = document.getElementById("fechaFinExist").value.trim();
    if(fechaInicioExist === "" || fechaFinExist === "") { // Agregamos validación para que no estén vacías
        errores.push("Las fechas de inicio y salida son obligatorias.");
    } else if(fechaInicioExist > fechaFinExist) {
        errores.push("La fecha de inicio no puede ser posterior a la fecha de salida.");
    }
    
    const habitacion = document.getElementById("numHabitacionReservaExist").value.trim();
    if (habitacion === "") {
        errores.push("El número de habitación es obligatorio.");
    }

    const numEmpleado = document.getElementById("numEmpleadoReservaExist").value.trim();
    if (!/^.{10,15}$/.test(numEmpleado)) {
        errores.push("El número de documento del empleado debe tener entre 10 y 15 dígitos.");
    }

    const totalPagoInput = document.getElementById("totalPagoExist"); // Referencia el elemento INPUT
    const totalPagoValue = totalPagoInput.value.trim(); // Obtiene el valor formateado del input

    if (totalPagoValue === "" || totalPagoValue === "$ 0,00" || parseFloat(totalPagoValue.replace(/[^0-9,-]+/g,"").replace(",", ".")) <= 0) {
        errores.push("El total a pagar es obligatorio y debe ser mayor que cero.");
    }

    if (errores.length > 0) {
        alert(errores.join("\n"));
        event.preventDefault();
    } else {
        const confirmar = confirm("¿Deseas registrar esta reserva?");
        if (!confirmar) {
            event.preventDefault();
        } else {
            console.log("Formulario de reserva de huésped nuevo confirmado, enviando datos...");
            // Aseguramos que 'valorLimpio' se calcule a partir del valor actual en el input
            let valorLimpio = totalPagoValue.replace(/[^0-9,-]+/g, "").replace(",", ".");
            
            // Asignamos el valor limpio al input ANTES de que el formulario se envíe
            totalPagoInput.value = valorLimpio;
        }
    }
}


// Asignación de eventos a los elementos del DOM
document.addEventListener("DOMContentLoaded", function () {

    // --- LÓGICA PARA crearReserva.php y crearReservaNewHuesped.php ---

    // Nuevo huesped (botón que redirige a crearReservaNewHuesped.php)
    const newHuesp = document.getElementById("registrarHuesped");
    if(newHuesp) {
        newHuesp.addEventListener("click", NuevoHuesped);
    }

    // Lógica del buscador de huésped (en crearReserva.php)
    const formBuscador = document.getElementById('buscadorHuesped');
    const infoDiv = document.getElementById('infoHuesped');

    if (formBuscador && infoDiv) {
        formBuscador.addEventListener('submit', function(e) {
            e.preventDefault();

            const documento = document.getElementById('buscarHuesped').value;

            fetch('buscarHuespedExist.php?buscarHuesped=' + encodeURIComponent(documento))
                .then(response => response.text())
                .then(data => {
                    infoDiv.innerHTML = data; 
                    // Aquí, una vez que el HTML de buscarHuespedExist.php se carga,
                    // debes RE-ADJUNTAR el evento al botón 'Generar Reserva' si lo tiene.
                    // Generar form reserva
                    const generarForm = document.getElementById("formReserva");
                        if(generarForm) {
                            generarForm.addEventListener("submit", generarReserva);
                        };
                })
                .catch(error => {
                    console.error("Error al buscar huésped:", error);
                    infoDiv.innerHTML = 'No se encontró ningún huésped con ese documento o hubo un error.';
                });
        });
    }

    // Validación y envío del formulario de NUEVO HUÉSPED (en crearReservaNewHuesped.php)
    const formNewReserva = document.getElementById("formRegistrarReserva");
    if (formNewReserva) {
        formNewReserva.addEventListener("submit", verificarCampos);
    }

    // Botones Limpiar y Cancelar para el formulario de NUEVO HUÉSPED
    // (Asegúrate de que estos IDs existan en crearReservaNewHuesped.php si los usas allí)
    const btnLimpiar = document.getElementById("btnLimpiarFormulario");
    if(btnLimpiar) {
        btnLimpiar.addEventListener("click", limpiarFormulario);
    }
    const btnCancelar = document.getElementById("btnCancelarReserva");
    if(btnCancelar) {   
        btnCancelar.addEventListener("click", cancelarReserva);
    }

    // Formateo numérico para NUEVO HUÉSPED
    const inputsNumericos = ["numAdultos", "numNinos", "numDiscapacitados"];
    inputsNumericos.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", aplicarFiltroNumerico);
        }
    });

    // Formateo campo Pago como moneda para NUEVO HUÉSPED
    const totalPagoInput = document.getElementById("totalPago");
    if (totalPagoInput) {
        totalPagoInput.addEventListener("input", formatCurrency);
    }


    // --- LÓGICA ESPECÍFICA PARA generarReserva.php (Huésped Existente) ---

    // Botón limpiar para el formulario de HUÉSPED EXISTENTE
    const btnLimpiarExist = document.getElementById("btnLimpiarFormularioExist");
    if(btnLimpiarExist) {
        btnLimpiarExist.addEventListener("click", limpiarFormulario);
    }

    // Botón Cancelar para el formulario de HUÉSPED EXISTENTE
    const btnCancelarExist = document.getElementById("btnCancelarReservaExist");
    if(btnCancelarExist) {   
        btnCancelarExist.addEventListener("click", cancelarReserva);
    }

    // Formateo numérico para HUÉSPED EXISTENTE
    const inputsNumericosExist = ["numAdultosExist", "numNinosExist", "numDiscapacitadosExist"];
    inputsNumericosExist.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", aplicarFiltroNumerico);
        }
    });

    // Formateo campo Pago como moneda para HUÉSPED EXISTENTE
    const totalPagoInputExist = document.getElementById("totalPagoExist");
    if (totalPagoInputExist) {
        totalPagoInputExist.addEventListener("input", formatCurrency);
    }
    
    // Validación y envío del formulario de HUÉSPED EXISTENTE
    const formExistReserva = document.getElementById("formRegistrarReservaExist");
    if (formExistReserva) {
        formExistReserva.addEventListener("submit", verificarCamposExist);
    }
});