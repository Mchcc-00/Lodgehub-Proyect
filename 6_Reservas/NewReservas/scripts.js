
// registrar un nuevo huesped
const newHuesp = document.getElementById("registrarHuesped");
if(newHuesp) {
newHuesp.addEventListener("click", function () {
    const confirmar = confirm("¿Deseas registrar un nuevo huesped?");
    if (confirmar) {
        window.location.href = "crearReservaNewHuesped.php";
    }
})};



const form = document.getElementById("formRegistrarReserva");
const btn = document.getElementById("btnRegistrarReserva");

    // Validacion solo numeros
    const inputAdultos = document.getElementById("CantidadAdultos");
    const inputMenores = document.getElementById("CantidadMenores");
    const inputDiscapacitados = document.getElementById("CantidadDiscapacitados");
    const inputCostoReserva = document.getElementById("TotalPagoHuespued");

    function aplicarFiltroNumerico(input) {
        input.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, '');
        });
    }

    [
        inputDocumento,
        inputContacto,
        inputAdultos,
        inputMenores,
        inputDiscapacitados,
        inputHabitacion,
        inputCostoReserva
    ].forEach(aplicarFiltroNumerico);



// Validación del formulario de registro de huesped
if (form && btn) {
    form.addEventListener("submit", function(event) {

        // Validación de campos
        let errores = [];

        // Email
        const email = document.getElementById("email").value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errores.push("El correo electrónico no es válido.");
        }

        const fechaInicio = document.getElementById("fechaInicio").value.trim();
        const fechaFin = document.getElementById("fechaFin").value.trim();
        if(fechaInicio > fechaFin) {
            errores.push("La fecha de inicio no puede ser posterior a la fecha de salida.");
        }

        // Número de documento
        const numDocumento = document.getElementById("numDocumento").value.trim();
        if (!/^\d{6,10}$/.test(numDocumento)) {
            errores.push("El número de documento debe tener entre 6 y 10 dígitos.");
        }
        // Teléfono (contacto)
        const contacto = document.getElementById("contacto").value.trim();
        if (!/^\d{7,10}$/.test(contacto)) {
            errores.push("El número de contacto debe tener entre 7 y 10 dígitos.");
        }
        // Confirmación de registro
        if (errores.length > 0) {
            alert(errores.join("\n"));
            event.preventDefault();
        }else
        // Si no hay errores, se muestra la confirmación
        // Confirmación centralizada en el submit de ambos formularios
        {
            const confirmar = confirm("¿Deseas registrar esta reserva?");
        if (!confirmar) {
            event.preventDefault();
        } else {
            console.log("Formulario de reserva confirmado, enviando datos...");
            document.querySelector("form").submit();
        }
        }
    });
}


/*
        // Nombres
        const nombres = document.getElementById("nombresHuesped").value.trim();
        if (nombres === "") {
            errores.push("El campo Nombres es obligatorio.");
        }
        // Apellidos
        const apellidos = document.getElementById("apellidosHuesped").value.trim();
        if (apellidos === "") {
            errores.push("El campo Apellidos es obligatorio.");
        }
*/