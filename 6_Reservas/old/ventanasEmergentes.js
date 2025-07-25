document.getElementById("NuevaReservaForm").addEventListener("click", function () {
    const confirmar = confirm("¿Deseas crear una nueva reserva?");
    if (confirmar) {
        window.location.href = "/Lodgehub/6_Reservas/NewReservas/crearReserva.php";
    }
});


function mostrarModal(id) {
    const modal = document.getElementById('miModal' + id);
    if (modal) {
        modal.style.display = 'block';
    }
}

function cerrarModal(id) {
    const modal = document.getElementById('miModal' + id);
    if (modal) {
        modal.style.display = 'none';
    }
}

//cerrar modal al hacer clic fuera del contenido
window.onclick = function(event) {
    const modales = document.querySelectorAll('.modal');
    modales.forEach(modal => {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
};