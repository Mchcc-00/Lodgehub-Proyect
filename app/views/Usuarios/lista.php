<?php
// Iniciar sesión para poder leer las variables de mensajes
session_start();

// --- LÓGICA PARA OBTENER DATOS ---
require_once __DIR__ . '/../../../app/Models/Usuario.php';
require_once __DIR__ . '/../../../config/conexionGlobal.php';

$db = conexionDB();
$usuarioModel = new Usuario($db);
$usuarios = $usuarioModel->obtenerTodos();

// Variables para las plantillas
$pageTitle = "Usuarios";
$userName = "Admin"; // Puedes cambiar esto por el nombre del usuario logueado
$currentPage = "usuarios";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LODGEHUB - Lista de Usuarios</title>
    
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts (nav y sidebar) -->
    <link href="../../../public/assets/css/styleNav.css" rel="stylesheet">
    <!-- Estilos originales de la vista -->
    <link rel="stylesheet" href="../../../public/assets/css/styles.css">
    
</head>
<body>

<?php 
// Incluir las plantillas de navegación
include '../Layouts/sidebar.php';
include '../Layouts/navbar.php'; 
?>

<!-- CONTENIDO PRINCIPAL -->
<main class="main-content" id="mainContent">
    <div class="container mt-4">

        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php 
                    echo $_SESSION['mensaje_exito']; 
                    unset($_SESSION['mensaje_exito']); // Borra el mensaje después de mostrarlo
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?php 
                    echo $_SESSION['mensaje_error']; 
                    unset($_SESSION['mensaje_error']); // Borra el mensaje después de mostrarlo
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="user-header">
            <h2 class="form-title">
                <i class="fas fa-users text-primary me-2"></i>
                Lista de Usuarios
            </h2>
            <a href="crear.php" class="btn-add" title="Agregar Usuario">
                Agregar Usuario
            </a>
        </div>
        
        <table class="user-table">
            <thead class="user-table-header">
                <tr>
                    <th>Tipo de documento</th>
                    <th>Número de documento</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Sexo</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="user-table-body">
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">
                            <i class="fas fa-users fa-3x text-muted mb-3"></i>
                            <p class="text-muted">No hay usuarios registrados.</p>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['tipo_documento']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['numDocumento']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombres']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['sexo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['numTelefono']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                            <td>
                                <?php 
                                $rolClass = '';
                                switch(strtolower($usuario['rol'])) {
                                    case 'administrador':
                                        $rolClass = 'bg-danger';
                                        break;
                                    case 'recepcionista':
                                        $rolClass = 'bg-info';
                                        break;
                                    case 'usuario':
                                        $rolClass = 'bg-secondary';
                                        break;
                                    default:
                                        $rolClass = 'bg-primary';
                                }
                                ?>
                                <span class="badge <?php echo $rolClass; ?>">
                                    <?php echo htmlspecialchars($usuario['rol']); ?>
                                </span>
                            </td>
                            <td class="action-links">
                                <a href="editar.php?id=<?php echo htmlspecialchars($usuario['numDocumento']); ?>" 
                                   class="btn-edit" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="eliminar.php?id=<?php echo htmlspecialchars($usuario['numDocumento']); ?>" 
                                   class="btn-delete" title="Eliminar" 
                                   onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">
                                    <i class="fas fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <footer class="form-footer">
            <i class="fas fa-hotel me-2"></i>
            lodgehubgroup © 2025
        </footer>
    </div>
</main>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>

<!-- Script para el funcionamiento del sidebar (debe ir después de Bootstrap) -->
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
});
</script>

</body>
</html>