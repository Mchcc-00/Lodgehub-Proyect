document.addEventListener("DOMContentLoaded", function () {
  console.log("Evento DOMContentLoaded disparado. Script iniciado.");

  // --- LÓGICA PARA EL SELECT DE ROLES ---
  const rolSelect = document.getElementById("roles");
  const adminFieldsDiv = document.getElementById("admin-fields");
  const rntInput = document.getElementById("rnt");
  const nitInput = document.getElementById("nit");

  // Verificamos si los elementos existen
  if (rolSelect && adminFieldsDiv && rntInput && nitInput) {
    console.log("Elementos para la lógica de roles encontrados.");

    rolSelect.addEventListener("change", function () {
      // Usamos this.value para obtener el valor del select que cambió
      console.log("El rol ha cambiado a: ", this.value);

      if (this.value === "1") {
        console.log("Rol de Administrador seleccionado. Mostrando campos.");
        adminFieldsDiv.style.display = "block";
        rntInput.required = true;
        nitInput.required = true;
      } else {
        console.log("Otro rol seleccionado. Ocultando campos.");
        adminFieldsDiv.style.display = "none";
        rntInput.required = false;
        nitInput.required = false;
        rntInput.value = "";
        nitInput.value = "";
      }
    });

    // Disparamos el evento para el estado inicial
    console.log("Disparando evento 'change' inicial.");
    rolSelect.dispatchEvent(new Event("change"));

  } else {
    console.error("Error: Uno o más elementos para la lógica de roles no se encontraron en el HTML (roles, admin-fields, rnt, nit).");
  }
  
  // Aquí puedes añadir la lógica de validación del formulario si quieres
  // const form = document.querySelector("form");
  // if (form) { ... }
});
