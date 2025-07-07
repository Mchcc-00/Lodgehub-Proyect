document.querySelector("form").addEventListener("submit", function (event) {
    const primerNombre = document.getElementById("primer_nombre").value.trim();
    const correo = document.getElementById("correo").value.trim();
    const contrasena = document.getElementById("contrasena").value;
    const confirmar_Contrasena = document.getElementById(
        "confirmar_contrasena"
    ).value;
    const primerApellido = document
        .getElementById("primer_apellido")
        .value.trim();
    const segundoApellido = document
        .getElementById("segundo_apellido")
        .value.trim();
    const dni = document.getElementById("dni").value.trim();
    const telefono = document.getElementById("telefono").value.trim();
    const telEmergencia = document.getElementById("tel_emergencia").value.trim();
    const direccion = document.getElementById("direccion").value.trim();
    const rnt = document.getElementById("rnt").value.trim();
    const nit = document.getElementById("nit").value.trim();
    const rol = document.getElementById("rol").value.trim();
    const tipoDocumento = document.getElementById("tipo_documento").value.trim();
    const sexo = document.getElementById("sexo").value.trim();

    if (!primerNombre) {
        alert("El primer nombre es obligatorioo.");
        event.preventDefault();
        return;
    }
    if (!primerApellido) {
        alert("El primer apellido es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!segundoApellido) {
        alert("El segundo apellido es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!dni) {
        alert("El DNI es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!telefono) {
        alert("El número de teléfono es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!telEmergencia) {
        alert("El teléfono de emergencia es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!direccion) {
        alert("La dirección es obligatoria.");
        event.preventDefault();
        return;
    }
    if (!rnt) {
        alert("El RNT es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!nit) {
        alert("El NIT es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!rol) {
        alert("El rol es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!tipoDocumento) {
        alert("El tipo de documento es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!sexo) {
        alert("El sexo es obligatorio.");
        event.preventDefault();
        return;
    }
    if (!correo || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
        alert("Por favor, ingresa un correo electrónico válido.");
        event.preventDefault();
        return;
    }

    if (contrasena !== confirmarContrasena) {
        alert("Las contraseñas no coinciden.");
        event.preventDefault();
        return;
    }
    if (contrasena.length < 8) {
        alert("La contraseña debe tener al menos 8 caracteres.");
        event.preventDefault();
        return;
    }
    if (!/[A-Z]/.test(contrasena)) {
        alert("La contraseña debe contener al menos una letra mayúscula.");
        event.preventDefault();
        return;
    }
    if (!/[a-z]/.test(contrasena)) {
        alert("La contraseña debe contener al menos una letra minúscula.");
        event.preventDefault();
        return;
    }
    if (!/[0-9]/.test(contrasena)) {
        alert("La contraseña debe contener al menos un número.");
        event.preventDefault();
        return;
    }
    if (!/[\W_]/.test(contrasena)) {
        alert("La contraseña debe contener al menos un carácter especial.");
        event.preventDefault();
        return;
    }
    if (/\s/.test(contrasena)) {
        alert("La contraseña no debe contener espacios en blanco.");
        event.preventDefault();
        return;
    }

    
});
