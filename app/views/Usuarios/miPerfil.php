<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - LODGEHUB</title>
    <link rel="stylesheet" href="../../../public/assets/css/stylesNav.css">
    <link rel="stylesheet" href="../../../public/assets/css/stylesMiPerfil.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    

</head>

    <?php
        include "../layouts/sidebar.php";
        include "../layouts/navbar.php";
    ?>

<body>

<!-- MAIN CONTENT -->
<main class="main-content">
    <div class="content-wrapper">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="profile-content">
                <div class="profile-avatar-container">
                    <div class="profile-avatar" id="profileAvatar">
                        <!-- Aquí se mostrará la imagen o las iniciales -->
                    </div>
                    <div class="avatar-upload" onclick="openImageModal()">
                        <i class="fas fa-camera"></i>
                    </div>
                </div>
                
                <div class="text-center">
                    <h1 class="profile-name" id="profileName">Cargando...</h1>
                    <p class="profile-role" id="profileRole">Cargando...</p>
                    <span class="profile-status status-active" id="profileStatus">Activo</span>
                </div>
                
                <div class="text-center mt-3">
                    <button class="btn btn-edit-profile me-2" onclick="editProfile()">
                        <i class="fas fa-edit me-2"></i>
                        Editar Perfil
                    </button>
                    <button class="btn btn-change-password" onclick="changePassword()">
                        <i class="fas fa-key me-2"></i>
                        Cambiar Contraseña
                    </button>
                </div>
            </div>
        </div>

        <!-- Personal Information -->
        <div class="profile-section">
            <h2 class="section-title">
                <i class="fas fa-user-circle"></i>
                Información Personal
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Nombres</div>
                    <div class="info-value" id="infoNombres">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Apellidos</div>
                    <div class="info-value" id="infoApellidos">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Tipo de Documento</div>
                    <div class="info-value" id="infoTipoDoc">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Número de Documento</div>
                    <div class="info-value" id="infoNumDoc">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Fecha de Nacimiento</div>
                    <div class="info-value" id="infoFechaNac">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Sexo</div>
                    <div class="info-value" id="infoSexo">-</div>
                </div>
            </div>
        </div>

        <!-- Contact Information -->
        <div class="profile-section">
            <h2 class="section-title">
                <i class="fas fa-address-book"></i>
                Información de Contacto
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Correo Electrónico</div>
                    <div class="info-value" id="infoCorreo">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Teléfono</div>
                    <div class="info-value" id="infoTelefono">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Teléfono de Emergencia</div>
                    <div class="info-value" id="infoTelEmergencia">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Dirección</div>
                    <div class="info-value" id="infoDireccion">-</div>
                </div>
            </div>
        </div>

        <!-- Work Information -->
        <div class="profile-section">
            <h2 class="section-title">
                <i class="fas fa-briefcase"></i>
                Información Laboral
            </h2>
            <div class="info-grid">
                <div class="info-item">
                    <div class="info-label">Rol</div>
                    <div class="info-value" id="infoRol">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">Estado de Sesión</div>
                    <div class="info-value" id="infoSesion">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">RNT</div>
                    <div class="info-value" id="infoRNT">-</div>
                </div>
                <div class="info-item">
                    <div class="info-label">NIT</div>
                    <div class="info-value" id="infoNIT">-</div>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal para cambiar foto -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-camera me-2"></i>
                    Cambiar Foto de Perfil
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="imageInput" class="form-label">Seleccionar nueva foto:</label>
                    <input type="file" class="form-control" id="imageInput" accept="image/*" onchange="previewImage(this)">
                    <div class="form-text">Formatos permitidos: JPG, PNG, GIF. Tamaño máximo: 5MB</div>
                </div>
                <div class="text-center">
                    <img id="imagePreview" style="display: none;" class="img-fluid">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" onclick="uploadImage()">
                    <i class="fas fa-upload me-2"></i>
                    Subir Foto
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Datos simulados del usuario (reemplazar con datos reales de la base de datos)
const userData = {
    numDocumento: '12345678',
    nombres: 'Juan Carlos',
    apellidos: 'Pérez González',
    direccion: 'Calle 123 #45-67',
    fechaNacimiento: '1985-06-15',
    numTelefono: '3001234567',
    telEmergencia: '3007654321',
    correo: 'juan.perez@lodgehub.com',
    rnt: '12345',
    nit: '67890',
    foto: null, // URL de la foto o null si no tiene
    sesionCaducada: 'Activo',
    sexo: 'Masculino',
    tipoDocumento: 'Cédula de Ciudadanía',
    roles: 'Administrador'
};

function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;
    
    sidebar.classList.toggle('show');
    body.classList.toggle('sidebar-open');
    
    if (window.innerWidth < 992) {
        overlay.classList.toggle('show');
    }
}

function generateInitials(nombres, apellidos) {
    const primerNombre = nombres.split(' ')[0] || '';
    const segundoApellido = apellidos.split(' ')[1] || apellidos.split(' ')[0] || '';
    
    return (primerNombre.charAt(0) + segundoApellido.charAt(0)).toUpperCase();
}

function loadUserProfile() {
    // Cargar información básica
    document.getElementById('profileName').textContent = `${userData.nombres} ${userData.apellidos}`;
    document.getElementById('profileRole').textContent = userData.roles;
    
    // Configurar avatar
    const avatar = document.getElementById('profileAvatar');
    if (userData.foto && userData.foto !== '') {
        // Si tiene foto, mostrar la imagen
        avatar.innerHTML = `<img src="${userData.foto}" alt="Foto de perfil" onerror="showInitials()">`;
    } else {
        // Si no tiene foto, mostrar iniciales
        showInitials();
    }
    
    // Estado
    const statusElement = document.getElementById('profileStatus');
    if (userData.sesionCaducada === 'Activo') {
        statusElement.className = 'profile-status status-active';
        statusElement.textContent = 'Activo';
    } else {
        statusElement.className = 'profile-status status-inactive';
        statusElement.textContent = 'Inactivo';
    }
    
    // Llenar información detallada
    document.getElementById('infoNombres').textContent = userData.nombres;
    document.getElementById('infoApellidos').textContent = userData.apellidos;
    document.getElementById('infoTipoDoc').textContent = userData.tipoDocumento;
    document.getElementById('infoNumDoc').textContent = userData.numDocumento;
    document.getElementById('infoFechaNac').textContent = formatDate(userData.fechaNacimiento);
    document.getElementById('infoSexo').textContent = userData.sexo;
    document.getElementById('infoCorreo').textContent = userData.correo;
    document.getElementById('infoTelefono').textContent = userData.numTelefono;
    document.getElementById('infoTelEmergencia').textContent = userData.telEmergencia;
    document.getElementById('infoDireccion').textContent = userData.direccion;
    document.getElementById('infoRol').textContent = userData.roles;
    document.getElementById('infoSesion').textContent = userData.sesionCaducada;
    document.getElementById('infoRNT').textContent = userData.rnt || 'No aplica';
    document.getElementById('infoNIT').textContent = userData.nit || 'No aplica';
}

function showInitials() {
    const avatar = document.getElementById('profileAvatar');
    const initials = generateInitials(userData.nombres, userData.apellidos);
    avatar.innerHTML = initials;
}

function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

function openImageModal() {
    const modal = new bootstrap.Modal(document.getElementById('imageModal'));
    modal.show();
}

function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function uploadImage() {
    const input = document.getElementById('imageInput');
    const file = input.files[0];
    
    if (!file) {
        alert('Por favor selecciona una imagen');
        return;
    }
    
    // Validar tamaño del archivo (5MB máximo)
    if (file.size > 5 * 1024 * 1024) {
        alert('La imagen es demasiado grande. El tamaño máximo es 5MB.');
        return;
    }
    
    // Aquí iría el código para subir la imagen al servidor
    // Por ahora, simulamos la carga
    const formData = new FormData();
    formData.append('foto', file);
    formData.append('numDocumento', userData.numDocumento);
    
    // Simular carga exitosa
    setTimeout(() => {
        // Actualizar la foto en la interfaz
        const reader = new FileReader();
        reader.onload = function(e) {
            userData.foto = e.target.result;
            loadUserProfile();
        };
        reader.readAsDataURL(file);
        
        // Cerrar modal
        const modal = bootstrap.Modal.getInstance(document.getElementById('imageModal'));
        modal.hide();
        
        // Limpiar el input
        input.value = '';
        document.getElementById('imagePreview').style.display = 'none';
        
        alert('Foto actualizada correctamente');
    }, 1500);
    
    
}

function editProfile() {
    // Implementar edición de perfil
    alert('Función de editar perfil en desarrollo');
}

function changePassword() {
    // Implementar cambio de contraseña
    alert('Función de cambiar contraseña en desarrollo');
}

// Cargar perfil cuando la página esté lista
document.addEventListener('DOMContentLoaded', function() {
    loadUserProfile();
    
    // Manejar resize de ventana
    window.addEventListener('resize', function() {
        const overlay = document.getElementById('sidebarOverlay');
        if (window.innerWidth >= 992) {
            overlay.classList.remove('show');
        }
    });
});
</script>


</body>
</html>