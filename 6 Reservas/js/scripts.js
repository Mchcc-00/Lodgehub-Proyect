document.addEventListener("DOMContentLoaded", function () {

    // Validacion solo numeros
    const inputDocumento = document.getElementById("NumDocumentoHuesped");
    const inputContacto = document.getElementById("NumContactoHuesped");
    const inputAdultos = document.getElementById("CantidadAdultos");
    const inputMenores = document.getElementById("CantidadMenores");
    const inputDiscapacitados = document.getElementById("CantidadDiscapacitados");
    const inputHabitacion = document.getElementById("NumeroHabitacion");
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





    // Botón Cancelar
    document.getElementById("BotonCancelarReserva").addEventListener("click", function () {
        const confirmar = confirm("¿Estás seguro de que deseas cancelar la reserva?");
        if (confirmar) {
            document.querySelector("form").reset();
        }
    });

    // Botón Reservar
document.getElementById("BotonReservarReserva").addEventListener("click", function () {
    const confirmar = confirm("¿Deseas confirmar esta reserva?");
    if (confirmar) {

        console.log("Formulario confirmado, enviando datos...");
        document.querySelector("form").submit();

    } else {
        console.log("Reserva cancelada.");
    }
});
});