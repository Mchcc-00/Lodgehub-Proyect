// Script para gestión de hoteles
class HotelManager {
  constructor() {
    this.apiUrl = "/api/v1/hotel.php"; // SOLUCIÓN: Ruta de API corregida para producción
    this.currentEditingId = null;
    this.initializeEventListeners();
    // this.loadHotels(); // La lista de hoteles ya no se muestra en esta página.
    this.initializeCharacterCounters();
  }

  // Inicializar event listeners
  initializeEventListeners() {
    const form = document.getElementById("hotel-form");
    if (form) {
      form.addEventListener("submit", this.handleFormSubmit.bind(this));
    }

    const fotoInput = document.getElementById("foto");
    if (fotoInput) {
      fotoInput.addEventListener("change", this.previewImage.bind(this));
    }
  }

  // Inicializar contadores de caracteres
  initializeCharacterCounters() {
    const counters = [
      { input: "nombre", counter: "nombre-counter", max: 100 },
      { input: "direccion", counter: "direccion-counter", max: 200 },
      { input: "descripcion", counter: "descripcion-counter", max: 1000 },
    ];

    counters.forEach(({ input, counter, max }) => {
      const inputElement = document.getElementById(input);
      const counterElement = document.getElementById(counter);

      if (inputElement && counterElement) {
        inputElement.addEventListener("input", () => {
          const length = inputElement.value.length;
          counterElement.textContent = `${length}/${max}`;
          counterElement.style.color = length > max * 0.9 ? "#e74c3c" : "#666";
        });
      }
    });
  }

  // Manejar envío del formulario
  async handleFormSubmit(e) {
    e.preventDefault();

    // Limpiar errores anteriores
    this.clearErrors();

    const form = e.target;
    const formData = new FormData(form);

    // Validaciones del lado del cliente
    if (!this.validateForm(formData)) {
      return;
    }

    try {
      const response = await fetch(this.apiUrl, {
        method: "POST", // Siempre POST para FormData con archivos
        body: formData,
      });

      const result = await response.json();

      if (result.success) {
        // Redirección a la página principal con un mensaje de éxito.
        window.location.href = `/app/views/homepage.php?status=hotel_success&message=${encodeURIComponent(result.message)}`;
      } else {
        if (result.errors) {
          this.displayErrors(result.errors);
        }
        this.showMessage(result.message, "error");
      }
    } catch (error) {
      this.showMessage("Error de conexión: " + error.message, "error");
    }
  }

  // Obtener datos del formulario
  // Esta función ya no es necesaria, FormData la reemplaza.
  // La mantenemos por si se usa en otro lugar, pero la validación usará FormData.

  // Validar formulario del lado del cliente
  validateForm(formData) {
    let isValid = true;
    const nit = formData.get("nit").trim();
    const nombre = formData.get("nombre").trim();
    const numDocumento = formData.get("numDocumento").trim();
    const correo = formData.get("correo").trim();

    // Validar NIT
    if (!nit) {
      this.showFieldError("nit", "El NIT es obligatorio");
      isValid = false;
    } else if (!/^[0-9\-]+$/.test(nit)) {
      this.showFieldError(
        "nit",
        "El NIT solo puede contener números y guiones"
      );
      isValid = false;
    }

    // Validar nombre
    if (!nombre) {
      this.showFieldError("nombre", "El nombre del hotel es obligatorio");
      isValid = false;
    }

    // Validar documento del administrador
    if (!numDocumento) {
      this.showFieldError(
        "numDocumento",
        "El documento del administrador es obligatorio"
      );
      isValid = false;
    }

    // Validar correo si se proporciona
    if (correo && !this.isValidEmail(correo)) {
      this.showFieldError(
        "correo",
        "El formato del correo electrónico no es válido"
      );
      isValid = false;
    }

    return isValid;
  }

  // Validar email
  isValidEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Validar URL
  isValidUrl(url) {
    try {
      new URL(url);
      return true;
    } catch {
      return false;
    }
  }

  // Cargar lista de hoteles
  async loadHotels() {
    // Esta función ya no se usa en la página de agregar/editar,
    // pero se mantiene por si el script se reutiliza en una página de listado.
  }

  // Mostrar lista de hoteles
  displayHotels(hotels) {
    const container = document.getElementById("hotelsList");

    if (!hotels || hotels.length === 0) {
      container.innerHTML = `
                <div class="no-data">
                    <i class="fas fa-hotel"></i>
                    <p>No hay hoteles registrados</p>
                </div>
            `;
      return;
    }

    const hotelCards = hotels
      .map(
        (hotel) => `
            <div class="hotel-card" data-id="${hotel.id}">
                <div class="hotel-header">
                    <div class="hotel-info">
                        <h3><i class="fas fa-hotel"></i> ${hotel.nombre}</h3>
                        <p class="hotel-nit"><strong>NIT:</strong> ${
                          hotel.nit
                        }</p>
                    </div>
                    <div class="hotel-actions">
                        <button class="btn-edit" onclick="hotelManager.editHotel(${
                          hotel.id
                        })" title="Editar">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-delete" onclick="hotelManager.deleteHotel(${
                          hotel.id
                        })" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                
                <div class="hotel-details">
                    ${
                      hotel.direccion
                        ? `<p><i class="fas fa-map-marker-alt"></i> ${hotel.direccion}</p>`
                        : ""
                    }
                    ${
                      hotel.telefono
                        ? `<p><i class="fas fa-phone"></i> ${hotel.telefono}</p>`
                        : ""
                    }
                    ${
                      hotel.correo
                        ? `<p><i class="fas fa-envelope"></i> ${hotel.correo}</p>`
                        : ""
                    }
                    
                    <div class="admin-info">
                        <p><strong>Administrador:</strong> 
                        ${
                          hotel.nombres && hotel.apellidos
                            ? `${hotel.nombres} ${hotel.apellidos} (${hotel.numDocumentoAdmin})`
                            : hotel.numDocumentoAdmin
                        }
                        </p>
                    </div>
                    
                    ${
                      hotel.descripcion
                        ? `
                        <div class="hotel-description">
                            <h4>Descripción:</h4>
                            <p>${hotel.descripcion}</p>
                        </div>
                    `
                        : ""
                    }
                    
                    ${
                      hotel.foto
                        ? `
                        <div class="hotel-image">
                            <img src="${hotel.foto}" alt="${hotel.nombre}" 
                                 onerror="this.style.display='none'" loading="lazy">
                        </div>
                    `
                        : ""
                    }
                </div>
            </div>
        `
      )
      .join("");

    container.innerHTML = hotelCards;
  }

  // Editar hotel
  async editHotel(id) {
    try {
      const response = await fetch(`${this.apiUrl}?id=${id}`);
      const result = await response.json();

      if (result.success) {
        this.populateForm(result.data);
        this.currentEditingId = id;

        // Cambiar textos del formulario
        document.getElementById("submitText").textContent = "Actualizar Hotel";
        document.getElementById("cancelBtn").style.display = "inline-block";

        // Scroll al formulario
        document
          .querySelector(".form-container")
          .scrollIntoView({ behavior: "smooth" });
      } else {
        this.showMessage(
          "Error al cargar datos del hotel: " + result.message,
          "error"
        );
      }
    } catch (error) {
      this.showMessage("Error de conexión: " + error.message, "error");
    }
  }

  // Poblar formulario con datos del hotel
  populateForm(hotel) {
    document.getElementById("nit").value = hotel.nit || "";
    document.getElementById("nombre").value = hotel.nombre || "";
    document.getElementById("numDocumento").value =
      hotel.numDocumentoAdmin || "";
    document.getElementById("telefono").value = hotel.telefono || "";
    document.getElementById("correo").value = hotel.correo || "";
    // No se puede poblar un input de tipo file, pero podemos mostrar la imagen actual
    const previewContainer = document.getElementById("image-preview-container");
    const previewImage = document.getElementById("image-preview");
    // Aquí se necesitaría la URL completa de la imagen para mostrarla.
    document.getElementById("direccion").value = hotel.direccion || "";
    document.getElementById("descripcion").value = hotel.descripcion || "";

    // Actualizar contadores
    this.updateCharacterCounters();
  }

  // Actualizar contadores de caracteres
  updateCharacterCounters() {
    const counters = [
      { input: "nombre", counter: "nombre-counter", max: 100 },
      { input: "direccion", counter: "direccion-counter", max: 200 },
      { input: "descripcion", counter: "descripcion-counter", max: 1000 },
    ];

    counters.forEach(({ input, counter, max }) => {
      const inputElement = document.getElementById(input);
      const counterElement = document.getElementById(counter);

      if (inputElement && counterElement) {
        const length = inputElement.value.length;
        counterElement.textContent = `${length}/${max}`;
      }
    });
  }

  // Previsualizar imagen
  previewImage(e) {
    const input = e.target;
    const previewContainer = document.getElementById("image-preview-container");
    const previewImage = document.getElementById("image-preview");

    if (input.files && input.files[0]) {
      const reader = new FileReader();
      reader.onload = function (e) {
        previewImage.src = e.target.result;
        previewContainer.style.display = "block";
      };
      reader.readAsDataURL(input.files[0]);
    } else {
      previewContainer.style.display = "none";
    }
  }

  // Eliminar hotel
  async deleteHotel(id) {
    if (
      !confirm(
        "¿Estás seguro de que deseas eliminar este hotel? Esta acción no se puede deshacer."
      )
    ) {
      return;
    }

    try {
      const response = await fetch(this.apiUrl, {
        method: "DELETE",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify({ id: id }),
      });

      const result = await response.json();

      if (result.success) {
        this.showMessage(result.message, "success");
        this.loadHotels();
      } else {
        this.showMessage("Error al eliminar hotel: " + result.message, "error");
      }
    } catch (error) {
      this.showMessage("Error de conexión: " + error.message, "error");
    }
  }

  // Cancelar edición
  cancelEdit() {
    this.resetForm();
    this.currentEditingId = null;
    document.getElementById("submitText").textContent = "Guardar Hotel";
    document.getElementById("cancelBtn").style.display = "none";
  }

  // Resetear formulario
  resetForm() {
    document.getElementById("hotel-form").reset();
    this.clearErrors();
    this.updateCharacterCounters();
  }

  // Limpiar errores
  clearErrors() {
    const errorElements = document.querySelectorAll(".error");
    errorElements.forEach((element) => {
      element.textContent = "";
      element.style.display = "none";
    });

    const formGroups = document.querySelectorAll(".form-group");
    formGroups.forEach((group) => {
      group.classList.remove("has-error");
    });
  }

  // Mostrar error en campo específico
  showFieldError(fieldName, message) {
    const errorElement = document.getElementById(fieldName + "-error");
    const formGroup = document.getElementById(fieldName).closest(".form-group");

    if (errorElement) {
      errorElement.textContent = message;
      errorElement.style.display = "block";
    }

    if (formGroup) {
      formGroup.classList.add("has-error");
    }
  }

  // Mostrar errores múltiples
  displayErrors(errors) {
    Object.keys(errors).forEach((fieldName) => {
      this.showFieldError(fieldName, errors[fieldName]);
    });
  }

  // Mostrar mensaje general
  showMessage(message, type = "info") {
    const messageContainer = document.getElementById("form-messages");

    const alertClass =
      type === "success"
        ? "alert-success"
        : type === "error"
        ? "alert-danger"
        : "alert-info";

    const icon =
      type === "success"
        ? "check-circle"
        : type === "error"
        ? "exclamation-triangle"
        : "info-circle";

    messageContainer.innerHTML = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="fas fa-${icon}"></i>
                ${message}
                <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
            </div>
        `;

    // Auto-ocultar después de 5 segundos para mensajes de éxito
    if (type === "success") {
      setTimeout(() => {
        const alert = messageContainer.querySelector(".alert");
        if (alert) {
          alert.remove();
        }
      }, 5000);
    }
  }
}

// Función global para cancelar edición (llamada desde HTML)
function cancelEdit() {
  if (window.hotelManager) {
    window.hotelManager.cancelEdit();
  }
}

// Inicializar cuando el DOM esté listo
document.addEventListener("DOMContentLoaded", function () {
  window.hotelManager = new HotelManager();
});
