    // Nuevo huesped
    function NuevoHuesped() {
            const registrarHuesped = confirm("¿Deseas registrar un nuevo huesped?");
            if (registrarHuesped) {
                window.location.href = "crearReservaNewHuesped.php";
            };
        };

    // Botón limpiar
    function limpiarFormulario(event) {
            const limpiar = confirm("¿Limpiar formulario?");
            if (limpiar) {
                document.querySelector("form").reset();
            }event.preventDefault();
        };

    // Botón Cancelar
    function cancelarReserva() {
            const cancelar = confirm("¿Estás seguro de que deseas cancelar la reserva?");
            if (cancelar) {
                document.querySelector("form").reset();
                window.location.href = "crearReserva.php";
            }
        };

    // Función para aplicar el filtro numérico
    function aplicarFiltroNumerico() {
        // Elimina todo lo que no sea dígito
            this.value = this.value.replace(/\D/g, '');
            if (this.value.length > 2) {
            this.value = this.value.slice(0, 2);
        }
    };

    // Formateo de campo Total a Pagar como moneda
    function formatCurrency() {
        // Elimina todo lo que no sea dígito
        let valor = this.value.replace(/\D/g, "");
        if (valor) {
            // Convierte a número y divide por 100 para decimales
            valor = (parseInt(valor, 10) / 100).toFixed(2);
            this.value = new Intl.NumberFormat('es-CO', { style: 'currency', currency: 'COP' }).format(valor);
        } else {
            this.value = "";
        }
    };

    // Validación de campos del formulario
    function verificarCampos(event) {
        // Validación de campos
        let errores = [];

        // Email
        const email = document.getElementById("emailHuesped").value.trim();
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errores.push("El correo electrónico no es válido.");
        }
        // Fecha de inicio y fin
        const fechaInicio = document.getElementById("fechaInicio").value.trim();
        const fechaFin = document.getElementById("fechaFin").value.trim();
        if(fechaInicio > fechaFin) {
            errores.push("La fecha de inicio no puede ser posterior a la fecha de salida.");
        }
        // Número de documento
        const numDocumento = document.getElementById("numDocumentoHuesped").value.trim();
        if (!/^.{10,15}$/.test(numDocumento)) {
            errores.push("El número de documento debe tener entre 10 y 15 dígitos.");
        }
        // Teléfono (contacto)
        const contacto = document.getElementById("contactoHuesped").value.trim();
        if (!/^.{10,15}$/.test(contacto)) {
            errores.push("El número de contacto debe tener entre 10 y 15 dígitos.");
        }
        // Numero documento del empleado
        const numEmpleado = document.getElementById("numEmpleadoReserva").value.trim();
        if (!/.{10,15}$/.test(numEmpleado)) {
            errores.push("El número de documento debe tener entre 10 y 15 dígitos.");
        }
        // Confirmación de registro
        if (errores.length > 0) {
            alert(errores.join("\n"));
            event.preventDefault();
        }else{
        // Si no hay errores, se muestra la confirmación
            const confirmar = confirm("¿Deseas registrar esta reserva?");
            if (!confirmar) {
                event.preventDefault();
            }else {
                console.log("Formulario de reserva confirmado, enviando datos...");
                document.querySelector("form").submit();
            }
        }
    };





// Asignación de eventos a los elementos del DOM
document.addEventListener("DOMContentLoaded", function () {

//Llamado de funciones

    // Nuevo huesped
    const newHuesp = document.getElementById("registrarHuesped");
        if(newHuesp) {
            newHuesp.addEventListener("click", NuevoHuesped);
        };

    // Botón limpiar
    const btnLimpiar = document.getElementById("btnLimpiarFormulario");
        if(btnLimpiar) {
            btnLimpiar.addEventListener("click", limpiarFormulario);
        };

    // Botón Cancelar
    const btnCancelar = document.getElementById("btnCancelarReserva");
        if(btnCancelar) {  
            btnCancelar.addEventListener("click", cancelarReserva);
        };
    
    // Formateo numerico
    const inputsNumericos = ["numAdultos", "numNinos", "numDiscapacitados"];
    inputsNumericos.forEach(id => {
        const input = document.getElementById(id);
        if (input) {
            input.addEventListener("input", aplicarFiltroNumerico);
        }
    });

    // Formateo campo Pago como moneda
    const totalPagoInput = document.getElementById("totalPago");
        if (totalPagoInput) {
            totalPagoInput.addEventListener("input", formatCurrency);
        };

    // Validación y envio form
    const form = document.getElementById("formRegistrarReserva");
        if (form) {
            form.addEventListener("submit", verificarCampos);
        };
});













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