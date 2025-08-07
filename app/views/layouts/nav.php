<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LODGEHUB - Vista Completa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg" id="navbar">
    <div class="container-fluid">
        <!-- Botón para toggle sidebar -->
        <button class="btn-sidebar-toggle me-3" onclick="toggleSidebar()" title="Abrir/Cerrar Menú">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Botón hamburguesa para móvil -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" 
                aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Logo/Brand -->
            <div class="navbar-brand-container">
                <span class="navbar-brand">
                    <i class="fas fa-hotel me-2"></i>
                    LODGEHUB
                </span>
            </div>
            
            <!-- Breadcrumb -->
            <nav aria-label="breadcrumb" class="mx-auto d-none d-lg-block">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="#" class="text-white-50">Inicio</a></li>
                    <li class="breadcrumb-item active text-white" aria-current="page">Usuarios</li>
                </ol>
            </nav>
            
            <!-- Área de usuario -->
            <div class="navbar-user">
                <div class="d-flex align-items-center gap-3">
                    <!-- Notificaciones -->
                    <div class="dropdown">
                        <button class="btn btn-notification" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-bell"></i>
                            <span class="notification-badge">3</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end notification-dropdown">
                            <li class="dropdown-header">Notificaciones</li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-calendar text-primary me-2"></i>
                                Nueva reserva recibida
                                <small class="text-muted d-block">Hace 5 min</small>
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-tools text-warning me-2"></i>
                                Mantenimiento programado
                                <small class="text-muted d-block">Hace 1 hora</small>
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                        </ul>
                    </div>
                    
                    <!-- Usuario -->
                    <div class="dropdown">
                        <button class="btn btn-user dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <span class="d-none d-md-inline">Admin</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-user me-2"></i>Mi Perfil
                            </a></li>
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-cog me-2"></i>Configuración
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="../homepage/cerrarSesion.php">
                                <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                            </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>

<!-- ASIDE/SIDEBAR -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <h4>LODGEHUB</h4>
        <button class="btn-collapse-sidebar" onclick="toggleSidebar()">
            <i class="fas fa-chevron-right"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link" href="../../views/homepage/homepage.php">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../../../6_Reservas/2R/mainReservas.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Reservas</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../../../app/views/Habitaciones/dashboardHab.php">
                    <i class="fas fa-bed"></i>
                    <span>Habitaciones</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link active" href="../../../app/views/Usuarios/lista.php">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../../../MANTENIMIENTO/views/dashboard.php">
                    <i class="fas fa-tools"></i>
                    <span>Mantenimiento</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../PQRS/index.php">
                    <i class="fas fa-comments"></i>
                    <span>PQRS</span>
                </a>
            </li>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../homepage/cerrarSesion.php" class="btn btn-danger w-100">
            <i class="fas fa-sign-out-alt"></i>
            Cerrar sesión
        </a>
    </div>
</aside>

<!-- Overlay para móvil -->
<div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

<!-- CONTENIDO PRINCIPAL -->
<main class="main-content" id="mainContent">
    <div class="content-wrapper">
        <!-- Header de la página -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">
                        <i class="fas fa-users text-primary me-2"></i>
                        Gestión de Usuarios
                    </h2>
                    <p class="page-subtitle text-muted">Administra los usuarios del sistema</p>
                </div>
                <button class="btn btn-primary">
                    <i class="fas fa-plus me-2"></i>
                    Nuevo Usuario
                </button>
            </div>
        </div>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-content">
                        <h3>248</h3>
                        <p>Total Usuarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-user-check"></i>
                    </div>
                    <div class="stats-content">
                        <h3>195</h3>
                        <p>Activos</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-user-clock"></i>
                    </div>
                    <div class="stats-content">
                        <h3>28</h3>
                        <p>Pendientes</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stats-card">
                    <div class="stats-icon bg-danger">
                        <i class="fas fa-user-times"></i>
                    </div>
                    <div class="stats-content">
                        <h3>25</h3>
                        <p>Bloqueados</p>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Tabla de usuarios -->
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Lista de Usuarios</h5>
                    <div class="d-flex gap-2">
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" class="form-control" placeholder="Buscar usuarios...">
                        </div>
                        <select class="form-select" style="width: auto;">
                            <option>Todos</option>
                            <option>Activos</option>
                            <option>Inactivos</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Último acceso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-primary">JD</div>
                                        <div class="ms-3">
                                            <h6 class="mb-0">Juan Pérez</h6>
                                            <small class="text-muted">ID: #001</small>
                                        </div>
                                    </div>
                                </td>
                                <td>juan.perez@lodgehub.com</td>
                                <td><span class="badge bg-success">Administrador</span></td>
                                <td><span class="badge bg-success">Activo</span></td>
                                <td>Hace 2 horas</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-info">MG</div>
                                        <div class="ms-3">
                                            <h6 class="mb-0">María García</h6>
                                            <small class="text-muted">ID: #002</small>
                                        </div>
                                    </div>
                                </td>
                                <td>maria.garcia@lodgehub.com</td>
                                <td><span class="badge bg-info">Recepcionista</span></td>
                                <td><span class="badge bg-success">Activo</span></td>
                                <td>Hace 1 día</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-warning">AL</div>
                                        <div class="ms-3">
                                            <h6 class="mb-0">Ana López</h6>
                                            <small class="text-muted">ID: #003</small>
                                        </div>
                                    </div>
                                </td>
                                <td>ana.lopez@lodgehub.com</td>
                                <td><span class="badge bg-secondary">Usuario</span></td>
                                <td><span class="badge bg-warning">Pendiente</span></td>
                                <td>Hace 3 días</td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-primary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-outline-info" title="Ver">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-outline-danger" title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <nav class="mt-4">
                    <ul class="pagination pagination-sm justify-content-center">
                        <li class="page-item disabled">
                            <a class="page-link" href="#">Anterior</a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link" href="#">Siguiente</a>
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</main>

<style>
/* Variables CSS */
:root {
    --primary-color: #437baf;
    --primary-dark: #2c5282;
    --sidebar-width: 280px;
    --navbar-height: 70px;
}

/* Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f8f9fa;
}

/* NAVBAR STYLES */
#navbar {
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark)) !important;
    box-shadow: 0 2px 20px rgba(0,0,0,0.1);
    border: none;
    padding: 0;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1050;
    height: var(--navbar-height);
}

.navbar .container-fluid {
    padding: 0.8rem 1.5rem;
    height: 100%;
}

.btn-sidebar-toggle {
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.2);
    color: white;
    padding: 0.5rem 0.8rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.btn-sidebar-toggle:hover {
    background: rgba(255,255,255,0.2);
    color: white;
    transform: scale(1.05);
}

.navbar-brand {
    color: white !important;
    font-weight: 700;
    font-size: 1.3rem;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.breadcrumb {
    background: none;
    padding: 0;
}

.btn-notification {
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    position: relative;
    transition: all 0.3s ease;
}

.btn-notification:hover {
    background: rgba(255,255,255,0.2);
    color: white;
    transform: scale(1.05);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: #dc3545;
    color: white;
    font-size: 0.7rem;
    padding: 0.2rem 0.4rem;
    border-radius: 50%;
    min-width: 18px;
    height: 18px;
    display: flex;
    align-items: center;
    justify-content: center;
}

.btn-user {
    background: rgba(255,255,255,0.1);
    border: 2px solid rgba(255,255,255,0.2);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 25px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.btn-user:hover {
    background: rgba(255,255,255,0.2);
    color: white;
    transform: translateY(-2px);
}

/* SIDEBAR STYLES */
.sidebar {
    position: fixed;
    top: var(--navbar-height);
    left: 0;
    width: var(--sidebar-width);
    height: calc(100vh - var(--navbar-height));
    background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
    color: white;
    transition: transform 0.3s ease;
    z-index: 1040;
    display: flex;
    flex-direction: column;
    box-shadow: 2px 0 10px rgba(0,0,0,0.1);
    transform: translateX(-100%);
}

.sidebar.show {
    transform: translateX(0);
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(255,255,255,0.1);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sidebar-header h4 {
    margin: 0;
    font-weight: bold;
    font-size: 1.2rem;
}

.btn-collapse-sidebar {
    background: none;
    border: none;
    color: white;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background 0.3s ease;
}

.btn-collapse-sidebar:hover {
    background: rgba(255,255,255,0.1);
}

.sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    overflow-y: auto;
}

.sidebar-nav .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 1rem 1.5rem;
    display: flex;
    align-items: center;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    background: none;
}

.sidebar-nav .nav-link:hover {
    background: rgba(255,255,255,0.1);
    color: white;
    transform: translateX(5px);
}

.sidebar-nav .nav-link.active {
    background: rgba(255,255,255,0.15);
    color: white;
    border-right: 3px solid white;
}

.sidebar-nav .nav-link i {
    width: 20px;
    margin-right: 1rem;
    text-align: center;
    font-size: 1.1rem;
}

.sidebar-footer {
    padding: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0,0,0,0.5);
    z-index: 1035;
    display: none;
}

/* MAIN CONTENT STYLES */
.main-content {
    margin-top: var(--navbar-height);
    margin-left: 0;
    transition: margin-left 0.3s ease;
    min-height: calc(100vh - var(--navbar-height));
}

.sidebar-open .main-content {
    margin-left: var(--sidebar-width);
}

.content-wrapper {
    padding: 2rem;
}

/* PAGE HEADER */
.page-header {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
}

.page-title {
    font-size: 1.8rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.page-subtitle {
    margin: 0;
    font-size: 1rem;
}

/* STATS CARDS */
.stats-card {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.stats-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
}

.stats-icon {
    width: 60px;
    height: 60px;
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-right: 1rem;
}

.stats-content h3 {
    font-size: 1.8rem;
    font-weight: 700;
    margin: 0;
    color: #333;
}

.stats-content p {
    margin: 0;
    color: #666;
    font-size: 0.9rem;
}

/* CARD STYLES */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}

.card-header {
    background: white;
    border-bottom: 1px solid #eee;
    padding: 1.5rem;
}

.search-box {
    position: relative;
}

.search-box i {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: #999;
}

.search-box input {
    padding-left: 2.5rem;
    border: 1px solid #ddd;
    border-radius: 8px;
}

/* TABLE STYLES */
.table {
    margin: 0;
}

.table th {
    border-top: none;
    border-bottom: 2px solid #eee;
    font-weight: 600;
    color: #333;
    padding: 1rem;
}

.table td {
    padding: 1rem;
    vertical-align: middle;
    border-top: 1px solid #f0f0f0;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem;
}

/* DROPDOWN STYLES */
.dropdown-menu {
    background: white;
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 40px rgba(0,0,0,0.15);
    padding: 0.5rem;
    margin-top: 0.5rem;
    animation: fadeInDown 0.3s ease;
}

.dropdown-item {
    padding: 0.7rem 1rem;
    border-radius: 8px;
    transition: all 0.3s ease;
}

.dropdown-item:hover {
    background: var(--primary-color);
    color: white;
}

.notification-dropdown {
    min-width: 300px;
}

.dropdown-header {
    font-weight: 600;
    color: #333;
    padding: 0.7rem 1rem;
}

@keyframes fadeInDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* RESPONSIVE */
@media (max-width: 991.98px) {
    .sidebar-open .main-content {
        margin-left: 0;
    }
    
    .sidebar-overlay.show {
        display: block;
    }
    
    .content-wrapper {
        padding: 1rem;
    }
    
    .page-header {
        padding: 1.5rem;
    }
    
    .stats-card {
        text-align: center;
        flex-direction: column;
    }
    
    .stats-icon {
        margin-right: 0;
        margin-bottom: 1rem;
    }
    
    .d-flex.justify-content-between.align-items-center {
        flex-direction: column;
        gap: 1rem;
        align-items: stretch !important;
    }
}

@media (max-width: 767.98px) {
    .table-responsive {
        font-size: 0.9rem;
    }
    
    .btn-group-sm .btn {
        padding: 0.25rem 0.4rem;
    }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

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