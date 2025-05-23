document.getElementById("NuevaReservaForm").addEventListener("click", function () {
    const confirmar = confirm("¿Deseas crear una nueva reserva?");
    if (confirmar) {
        window.location.href = "indexNuevaReserva.html"
    }
});


function mostrarModal() {
  document.getElementById("miModal").style.display = "block";
}

function cerrarModal() {
  document.getElementById("miModal").style.display = "none";
}