
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
    const inputAdultos = document.getElementById("numAdultos");
    const inputMenores = document.getElementById("numNinos");
    const inputDiscapacitados = document.getElementById("numDiscapacitados");

    function aplicarFiltroNumerico(input) {
        input.addEventListener("input", function () {
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 2) {
            this.value = this.value.slice(0, 2);
        }
        });
    };

    [
        inputAdultos,
        inputMenores,
        inputDiscapacitados,
    ].forEach(aplicarFiltroNumerico);

// Validación del formulario de registro de huesped
if (form && btn) {
    form.addEventListener("submit", function(event) {

        // Validación de campos
        let errores = [];

        // Email
        const email = document.getElementById("emailHuesped").value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errores.push("El correo electrónico no es válido.");
        }

        const fechaInicio = document.getElementById("fechaInicio").value.trim();
        const fechaFin = document.getElementById("fechaFin").value.trim();
        if(fechaInicio > fechaFin) {
            errores.push("La fecha de inicio no puede ser posterior a la fecha de salida.");
        }

        // Número de documento
        const numDocumento = document.getElementById("numDocumentoHuesped").value.trim();
        if (!/^\d{10,15}$/.test(numDocumento)) {
            errores.push("El número de documento debe tener entre 10 y 15 dígitos.");
        }
        // Teléfono (contacto)
        const contacto = document.getElementById("contactoHuesped").value.trim();
        if (!/^\d{10,15}$/.test(contacto)) {
            errores.push("El número de contacto debe tener entre 10 y 15 dígitos.");
        }

        //
        const numEmpleado = document.getElementById("numEmpleadoReserva").value.trim();
        if (!/^\d{10,15}$/.test(numEmpleado)) {
            errores.push("El número de documento debe tener entre 10 y 15 dígitos.");
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