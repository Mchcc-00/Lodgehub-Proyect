document.addEventListener("DOMContentLoaded", function () {
  // --- VALIDACIONES DE FORMULARIO ---

  // Validación en tiempo real de la contraseña
  const passwordField = document.getElementById("password");
  if (passwordField) {
    console.log("Campo de contraseña encontrado. Agregando validaciones.");
    passwordField.addEventListener("input", updatePasswordStrength);
  }

  // Validación de confirmación de contraseña
  const confirmPasswordField = document.getElementById("confirmar_password");
  if (confirmPasswordField) {
    console.log("Campo de confirmar contraseña encontrado.");
    confirmPasswordField.addEventListener("input", function () {
      const password = document.getElementById("password").value;
      const confirmPassword = this.value;
      const indicator = document.querySelector(".password-match-indicator");

      if (confirmPassword) {
        if (password === confirmPassword) {
          indicator.textContent = "✓ Las contraseñas coinciden";
          indicator.style.color = "#22c55e";
          indicator.className = "password-match-indicator success";
        } else {
          indicator.textContent = "✗ Las contraseñas no coinciden";
          indicator.style.color = "#ef4444";
          indicator.className = "password-match-indicator error";
        }
      } else {
        indicator.textContent = "";
        indicator.className = "password-match-indicator";
      }
    });
  }

  // Validación del correo electrónico
  const emailField = document.getElementById("correo");
  if (emailField) {
    console.log("Campo de correo encontrado.");
    emailField.addEventListener("blur", function () {
      const email = this.value;
      if (email && !validateEmail(email)) {
        this.classList.add("error");
        showFieldError(this, "Formato de correo electrónico no válido");
      } else {
        this.classList.remove("error");
        hideFieldError(this);
      }
    });
  }

  // Validación del teléfono
  const phoneField = document.getElementById("numTelefono");
  if (phoneField) {
    console.log("Campo de teléfono encontrado.");
    phoneField.addEventListener("blur", function () {
      const phone = this.value;
      if (phone && !validatePhone(phone)) {
        this.classList.add("error");
        showFieldError(this, "Formato de teléfono no válido");
      } else {
        this.classList.remove("error");
        hideFieldError(this);
      }
    });
  }

  // Validación del documento
  const docNumberField = document.getElementById("numDocumento");
  const docTypeField = document.getElementById("tipoDocumento");

  if (docNumberField && docTypeField) {
    console.log("Campos de documento encontrados.");

    function validateDocumentField() {
      const docNumber = docNumberField.value;
      const docType = docTypeField.value;

      if (docNumber && docType && !validateDocument(docNumber, docType)) {
        docNumberField.classList.add("error");
        showFieldError(
          docNumberField,
          "Número de documento no válido para el tipo seleccionado"
        );
      } else {
        docNumberField.classList.remove("error");
        hideFieldError(docNumberField);
      }
    }

    docNumberField.addEventListener("blur", validateDocumentField);
    docTypeField.addEventListener("change", validateDocumentField);
  }

  // Validación de nombres
  const nombresField = document.getElementById("nombres");
  if (nombresField) {
    console.log("Campo de nombres encontrado.");
    nombresField.addEventListener("blur", function () {
      const nombres = this.value;
      if (nombres && !validateName(nombres)) {
        this.classList.add("error");
        showFieldError(
          this,
          "Los nombres solo pueden contener letras y espacios"
        );
      } else {
        this.classList.remove("error");
        hideFieldError(this);
      }
    });
  }

  // Validación de apellidos
  const apellidosField = document.getElementById("apellidos");
  if (apellidosField) {
    console.log("Campo de apellidos encontrado.");
    apellidosField.addEventListener("blur", function () {
      const apellidos = this.value;
      if (apellidos && !validateName(apellidos)) {
        this.classList.add("error");
        showFieldError(
          this,
          "Los apellidos solo pueden contener letras y espacios"
        );
      } else {
        this.classList.remove("error");
        hideFieldError(this);
      }
    });
  }

  // Validación de fecha de nacimiento
  const birthDateField = document.getElementById("fechaNacimiento");
  if (birthDateField) {
    console.log("Campo de fecha de nacimiento encontrado.");
    birthDateField.addEventListener("change", function () {
      const birthDate = this.value;
      if (birthDate && !validateBirthDate(birthDate)) {
        this.classList.add("error");
        showFieldError(this, "Debe tener entre 13 y 120 años");
      } else {
        this.classList.remove("error");
        hideFieldError(this);
      }
    });
  }

  // Preview de imagen
  const fotoField = document.getElementById("foto");
  if (fotoField) {
    console.log("Campo de foto encontrado.");
    fotoField.addEventListener("change", function (e) {
      const file = e.target.files[0];
      const preview = document.querySelector(".file-preview");
      const fileText = document.querySelector(".file-text");

      if (file) {
        // Verificar tamaño del archivo (2MB máximo)
        if (file.size > 2 * 1024 * 1024) {
          alert("El archivo es muy grande. Máximo 2MB.");
          this.value = "";
          if (preview) preview.innerHTML = "";
          if (fileText) fileText.textContent = "Seleccionar imagen";
          return;
        }

        // Verificar tipo de archivo
        const allowedTypes = ["image/jpeg", "image/png", "image/gif"];
        if (!allowedTypes.includes(file.type)) {
          alert("Solo se permiten archivos JPG, PNG y GIF.");
          this.value = "";
          if (preview) preview.innerHTML = "";
          if (fileText) fileText.textContent = "Seleccionar imagen";
          return;
        }

        // Mostrar preview
        if (preview && fileText) {
          const reader = new FileReader();
          reader.onload = function (e) {
            preview.innerHTML = `<img src="${e.target.result}" style="max-width: 100px; max-height: 100px; border-radius: 8px; object-fit: cover;">`;
          };
          reader.readAsDataURL(file);
          fileText.textContent = file.name;
        }
      } else {
        if (preview) preview.innerHTML = "";
        if (fileText) fileText.textContent = "Seleccionar imagen";
      }
    });
  }

  console.log("Todas las validaciones de formulario han sido configuradas.");
});

// --- FUNCIONES DE VALIDACIÓN ---

// Validación de fortaleza de contraseña
function checkPasswordStrength(password) {
  let strength = 0;
  let feedback = [];

  if (password.length >= 8) {
    strength += 1;
  } else {
    feedback.push("Mínimo 8 caracteres");
  }

  if (password.match(/[a-z]/)) {
    strength += 1;
  } else {
    feedback.push("Una minúscula");
  }

  if (password.match(/[A-Z]/)) {
    strength += 1;
  } else {
    feedback.push("Una mayúscula");
  }

  if (password.match(/[0-9]/)) {
    strength += 1;
  } else {
    feedback.push("Un número");
  }

  if (password.match(/[\W_]/)) {
    strength += 1;
  } else {
    feedback.push("Un carácter especial");
  }

  return { strength, feedback };
}

// Actualizar indicador de fortaleza de contraseña
function updatePasswordStrength() {
  const password = document.getElementById("password").value;
  const strengthBar = document.querySelector(".strength-bar");
  const strengthText = document.querySelector(".strength-text");

  if (!strengthBar || !strengthText) return;

  if (password === "") {
    strengthBar.style.width = "0%";
    strengthBar.className = "strength-bar";
    strengthText.textContent = "Fortaleza de contraseña";
    return;
  }

  const result = checkPasswordStrength(password);
  const percentage = (result.strength / 5) * 100;

  strengthBar.style.width = percentage + "%";

  // Remover todas las clases de fortaleza
  strengthBar.classList.remove("weak", "medium", "strong");

  if (result.strength <= 2) {
    strengthBar.classList.add("weak");
    strengthText.textContent = "Débil - Falta: " + result.feedback.join(", ");
  } else if (result.strength <= 4) {
    strengthBar.classList.add("medium");
    strengthText.textContent = "Media - Falta: " + result.feedback.join(", ");
  } else {
    strengthBar.classList.add("strong");
    strengthText.textContent = "Fuerte";
  }
}

// Validación de formato de correo electrónico
function validateEmail(email) {
  const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  return regex.test(email);
}

// Validación de número de teléfono
function validatePhone(phone) {
  // Permitir números con o sin código de país, espacios, guiones y paréntesis
  const regex = /^[\+]?[0-9\s\-\(\)]+$/;
  return regex.test(phone) && phone.replace(/\D/g, "").length >= 7;
}

// Validación de número de documento
function validateDocument(docNumber, docType) {
  // Remover espacios y convertir a mayúsculas
  docNumber = docNumber.replace(/\s/g, "").toUpperCase();

  switch (docType) {
    case "Cédula de Ciudadanía":
    case "Tarjeta de Identidad":
      return (
        /^[0-9]+$/.test(docNumber) &&
        docNumber.length >= 6 &&
        docNumber.length <= 15
      );
    case "Cedula de Extranjeria":
      return (
        /^[0-9]+$/.test(docNumber) &&
        docNumber.length >= 6 &&
        docNumber.length <= 15
      );
    case "Pasaporte":
      return (
        /^[A-Z0-9]+$/.test(docNumber) &&
        docNumber.length >= 6 &&
        docNumber.length <= 15
      );
    case "Registro Civil":
      return (
        /^[0-9]+$/.test(docNumber) &&
        docNumber.length >= 8 &&
        docNumber.length <= 15
      );
    default:
      return false;
  }
}

// Validación de nombres y apellidos
function validateName(name) {
  // Solo letras, espacios y acentos
  const regex = /^[a-zA-ZÀ-ÿ\s]+$/;
  return regex.test(name) && name.trim().length >= 2;
}

// Validación de fecha de nacimiento
function validateBirthDate(dateString) {
  const date = new Date(dateString);
  const today = new Date();
  const age = today.getFullYear() - date.getFullYear();

  // Verificar que la fecha sea válida y la persona tenga entre 13 y 120 años
  return date instanceof Date && !isNaN(date) && age >= 13 && age <= 120;
}

// Función para mostrar errores en campos específicos
function showFieldError(field, message) {
  // Remover mensaje de error anterior si existe
  hideFieldError(field);

  // Crear y mostrar nuevo mensaje de error
  const errorDiv = document.createElement("div");
  errorDiv.className = "field-error-message";
  errorDiv.textContent = message;
  errorDiv.style.color = "#ef4444";
  errorDiv.style.fontSize = "0.75rem";
  errorDiv.style.marginTop = "0.25rem";

  field.parentNode.appendChild(errorDiv);
}

// Función para ocultar errores en campos específicos
function hideFieldError(field) {
  const existingError = field.parentNode.querySelector(".field-error-message");
  if (existingError) {
    existingError.remove();
  }
}
// --- FUNCIONES DE NAVEGACIÓN ENTRE SECCIONES ---

function toggleRequiredFields(sectionIndex) {
  const sections = document.querySelectorAll(".form-section");
  sections.forEach((section, i) => {
    const fields = section.querySelectorAll("[required]");
    fields.forEach((field) => {
      if (i === sectionIndex) {
        field.setAttribute("required", "required");
      } else {
        field.removeAttribute("required");
      }
    });
  });
}

// --- NAVEGACIÓN ENTRE SECCIONES DEL FORMULARIO ---
document.addEventListener("DOMContentLoaded", function () {
  let currentSection = 0;
  const sections = document.querySelectorAll(".form-section");
  const nextBtn = document.getElementById("nextBtn");
  const prevBtn = document.getElementById("prevBtn");
  const submitBtn = document.getElementById("submitBtn");

  function updateProgressIndicator(index) {
    const steps = document.querySelectorAll(".progress-indicator .step");
    steps.forEach((step, i) => {
      step.classList.toggle("active", i === index);
    });
  }

  function showSection(index) {
    sections.forEach((section, i) => {
      section.classList.toggle("active", i === index);
    });
    prevBtn.style.display = index > 0 ? "inline-block" : "none";
    nextBtn.style.display =
      index < sections.length - 1 ? "inline-block" : "none";
    submitBtn.style.display =
      index === sections.length - 1 ? "inline-block" : "none";
    toggleRequiredFields(index);
    updateProgressIndicator(index);
  }

  if (nextBtn) {
    nextBtn.addEventListener("click", function () {
      if (validateSection(currentSection + 1)) {
        currentSection++;
        showSection(currentSection);
      }
    });
  }

  if (prevBtn) {
    prevBtn.addEventListener("click", function () {
      currentSection--;
      showSection(currentSection);
    });
  }

  showSection(currentSection);
});

// Función para validar toda una sección antes de avanzar
function validateSection(sectionNumber) {
  const section = document.getElementById(`section-${sectionNumber}`);
  if (!section) return true; // Si no existe la sección, asumimos que es válida

  const requiredFields = section.querySelectorAll("[required]");
  let isValid = true;

  requiredFields.forEach((field) => {
    const value = field.value.trim();

    if (!value) {
      field.classList.add("error");
      showFieldError(field, "Este campo es obligatorio");
      isValid = false;
    } else {
      // Validaciones específicas por tipo de campo
      switch (field.id) {
        case "numDocumento":
          const docType = document.getElementById("tipoDocumento").value;
          if (!validateDocument(value, docType)) {
            field.classList.add("error");
            showFieldError(field, "Número de documento no válido");
            isValid = false;
          }
          break;
        case "nombres":
        case "apellidos":
          if (!validateName(value)) {
            field.classList.add("error");
            showFieldError(field, "Solo se permiten letras y espacios");
            isValid = false;
          }
          break;
        case "correo":
          if (!validateEmail(value)) {
            field.classList.add("error");
            showFieldError(field, "Formato de correo no válido");
            isValid = false;
          }
          break;
        case "numTelefono":
          if (!validatePhone(value)) {
            field.classList.add("error");
            showFieldError(field, "Formato de teléfono no válido");
            isValid = false;
          }
          break;
        case "fechaNacimiento":
          if (!validateBirthDate(value)) {
            field.classList.add("error");
            showFieldError(field, "Fecha de nacimiento no válida");
            isValid = false;
          }
          break;
        case "password":
          const passwordCheck = checkPasswordStrength(value);
          if (passwordCheck.strength < 5) {
            field.classList.add("error");
            showFieldError(
              field,
              "La contraseña no cumple todos los requisitos"
            );
            isValid = false;
          }
          break;
        case "confirmar_password":
          const originalPassword = document.getElementById("password").value;
          if (value !== originalPassword) {
            field.classList.add("error");
            showFieldError(field, "Las contraseñas no coinciden");
            isValid = false;
          }
          break;
      }

      // Si no hay errores específicos, remover la clase de error
      if (isValid || !field.classList.contains("error")) {
        field.classList.remove("error");
        hideFieldError(field);
      }
    }
  });

  return isValid;
}
