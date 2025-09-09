<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['numDocumento']) || !isset($_SESSION['user'])) {
    header('Location: login.php?mensaje=Sesión expirada');
    exit();
}

// Incluir conexión a la base de datos
require_once '../../config/conexionGlobal.php';

try {
    // Obtener información del usuario desde la base de datos
    $db = conexionDB();
    $stmt = $db->prepare("SELECT * FROM tp_usuarios WHERE numDocumento = ?");
    $stmt->execute([$_SESSION['numDocumento']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception("Usuario no encontrado en la base de datos");
    }
} catch (Exception $e) {
    echo "Error al cargar el perfil: " . $e->getMessage();
    exit();
}

// Función para formatear la fecha
function formatearFecha($fecha) {
    return date('d/m/Y', strtotime($fecha));
}

// Función para calcular la edad
function calcularEdad($fechaNacimiento) {
    $fecha_nacimiento = new DateTime($fechaNacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fecha_nacimiento);
    return $edad->y;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Perfil - <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?></title>
    <link rel="stylesheet" href="../../public/assets/css/stylesMiPerfil.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNav.css">
    <link rel="stylesheet" href="../../public/assets/css/modals/modalStyles.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">


</head>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>


<body>
    <div class="main-content">
        <div class="content-wrapper">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-content">
                    <div class="profile-avatar-container">
                        <div class="profile-avatar">
                            <?php if (!empty($usuario['foto']) && file_exists($usuario['foto'])): ?>
                                <img src="<?php echo htmlspecialchars($usuario['foto']); ?>" alt="Foto de perfil">
                            <?php else: ?>
                                <i class="fas fa-user"></i>
                            <?php endif; ?>
                        </div>
                    </div>
                    <h1 class="profile-name">
                        <?php echo htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']); ?>
                    </h1>
                    <p class="profile-role">
                        <?php echo htmlspecialchars($usuario['roles']); ?>
                    </p>
                    <span class="profile-status <?php echo ($usuario['sesionCaducada'] == '1' || $usuario['sesionCaducada'] == 1) ? 'status-active' : 'status-inactive'; ?>">
                        <?php echo ($usuario['sesionCaducada'] == '1' || $usuario['sesionCaducada'] == 1) ? 'Activo' : 'Inactivo'; ?>
                    </span>
                </div>
            </div>

            <!-- Personal Information Section -->
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-user-circle"></i>
                    Información Personal
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Número de Documento</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['numDocumento']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Tipo de Documento</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['tipoDocumento']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Nombres</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['nombres']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Apellidos</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['apellidos']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Sexo</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['sexo']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Fecha de Nacimiento</div>
                        <div class="info-value">
                            <?php echo formatearFecha($usuario['fechaNacimiento']); ?>
                            <small style="color: #666; display: block; margin-top: 4px;">
                                (<?php echo calcularEdad($usuario['fechaNacimiento']); ?> años)
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Contact Information Section -->
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-address-book"></i>
                    Información de Contacto
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Correo Electrónico</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['correo']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Número de Teléfono</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['numTelefono']); ?></div>
                    </div>
                </div>
            </div>

            <!-- System Information Section -->
            <div class="profile-section">
                <h2 class="section-title">
                    <i class="fas fa-cog"></i>
                    Información del Sistema
                </h2>
                <div class="info-grid">
                    <div class="info-item">
                        <div class="info-label">Rol en el Sistema</div>
                        <div class="info-value"><?php echo htmlspecialchars($usuario['roles']); ?></div>
                    </div>

                    <div class="info-item">
                        <div class="info-label">Estado de la Sesión</div>
                        <div class="info-value">
                            <span class="profile-status <?php echo ($usuario['sesionCaducada'] == '1' || $usuario['sesionCaducada'] == 1) ? 'status-active' : 'status-inactive'; ?>">
                                <?php echo ($usuario['sesionCaducada'] == '1' || $usuario['sesionCaducada'] == 1) ? 'Activo' : 'Inactivo'; ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="actions">
                <a href="editarPerfil.php" class="btn-change-password">
                    <i class="fas fa-edit"></i> Editar Perfil
                </a>
                <a href="contraseña.php" class="btn-change-password">
                    <i class="fas fa-key"></i> Cambiar Contraseña
                </a>
                <a href="cerrarSesion.php" class="btn-change-password">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </a>
            </div>
        </div>
    </div>

<!-- Bootstrap JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="../../public/assets/js/modals/perfilModal.js"></script>
<script src="../../public/assets/js/modals/modalManager.js"></script>
<?php include '../../public/assets/modals/editarPerfilModal.php'; ?>
    <script>
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;
    
    sidebar.classList.toggle('show');
    body.classList.toggle('sidebar-open');
    
    // Solo mostrar overlay en móvil
    if (window.innerWidth < 992) {
        overlay.classList.toggle('show');
    }
    
    // Cambiar el ícono del botón collapse
    const collapseBtn = document.querySelector('.btn-collapse-sidebar i');
    if (collapseBtn) {
        if (sidebar.classList.contains('show')) {
            collapseBtn.className = 'fas fa-chevron-left';
        } else {
            collapseBtn.className = 'fas fa-chevron-right';
        }
    }
}

// Función para redireccionar
function redirectTo(url) {
    console.log('Redirigiendo a:', url);
    // Aquí puedes cambiar por window.location.href = url; cuando tengas las URLs reales
    alert('Redirigiendo a: ' + url);
}

// Mostrar fecha actual
function updateDate() {
    const now = new Date();
    const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
    };
    const dateElement = document.getElementById('currentDate');
    if (dateElement) {
        dateElement.textContent = now.toLocaleDateString('es-ES', options);
    }
}

// Cerrar sidebar al hacer clic en un enlace solo en móvil
document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                toggleSidebar();
            }
        });
    });
    
    // Manejar resize de ventana
    window.addEventListener('resize', function() {
        const overlay = document.getElementById('sidebarOverlay');
        
        if (window.innerWidth >= 992) {
            overlay.classList.remove('show');
        }
    });
    
    // Inicializar fecha
    updateDate();
    // Actualizar fecha cada minuto
    setInterval(updateDate, 60000);
});
</script>

        
</body>
</html>