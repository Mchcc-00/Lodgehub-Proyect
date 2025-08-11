<?php
if (!isset($habitaciones)) {
    header('Location: /LODGEHUB/app/controllers/habitacionController.php?accion=listar');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LODGEHUB - Gestión de Habitaciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="/LODGEHUB/public/assets/css/dashboardHab.css">
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
                    <li class="breadcrumb-item active text-white" aria-current="page">Habitaciones</li>
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
                <a class="nav-link active" href="../../../app/views/Habitaciones/dashboardHab.php">
                    <i class="fas fa-bed"></i>
                    <span>Habitaciones</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link" href="../../../app/views/Usuarios/lista.php">
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
        <!-- Mensajes de alerta -->
        <?php if (isset($_SESSION['errores']) && count($_SESSION['errores']) > 0): ?>
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <strong>Error:</strong>
                <?php foreach ($_SESSION['errores'] as $error): ?>
                    <div><?php echo htmlspecialchars($error); ?></div>
                <?php endforeach; unset($_SESSION['errores']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['exito'])): ?>
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert" id="successMessage">
                <i class="fas fa-check-circle me-2"></i>
                <strong>Éxito:</strong> <?php echo htmlspecialchars($_SESSION['exito']); ?>
                <?php unset($_SESSION['exito']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <script>
                setTimeout(function() {
                    var msg = document.getElementById('successMessage');
                    if (msg) {
                        var alert = new bootstrap.Alert(msg);
                        alert.close();
                    }
                }, 3000);
            </script>
        <?php endif; ?>

        <!-- Header de la página -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2 class="page-title">
                        <i class="fas fa-bed text-primary me-2"></i>
                        Gestión de Habitaciones
                    </h2>
                    <p class="page-subtitle text-muted">Administra las habitaciones del hotel</p>
                </div>
                <button class="btn btn-primary btn-lg" onclick="window.location.href='/LODGEHUB/app/views/Habitaciones/formHab.php'">
                    <i class="fas fa-plus me-2"></i>
                    Nueva Habitación
                </button>
            </div>
        </div>

        <!-- Stats de habitaciones -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon bg-success">
                        <i class="fas fa-bed"></i>
                    </div>
                    <div class="stats-content">
                        <h3 id="totalRooms">0</h3>
                        <p>Total Habitaciones</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon bg-primary">
                        <i class="fas fa-door-open"></i>
                    </div>
                    <div class="stats-content">
                        <h3 id="availableRooms">0</h3>
                        <p>Disponibles</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon bg-warning">
                        <i class="fas fa-door-closed"></i>
                    </div>
                    <div class="stats-content">
                        <h3 id="occupiedRooms">0</h3>
                        <p>Ocupadas</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="stats-card">
                    <div class="stats-icon bg-danger">
                        <i class="fas fa-tools"></i>
                    </div>
                    <div class="stats-content">
                        <h3 id="maintenanceRooms">0</h3>
                        <p>Mantenimiento</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros mejorados -->
        <div class="card mb-4">
            <div class="card-body">
                <div class="d-flex flex-wrap align-items-center gap-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-filter text-primary me-2"></i>
                        <span class="fw-semibold">Filtros:</span>
                    </div>
                    
                    <div class="filter-group">
                        <button class="filter-btn" data-filter="estado">
                            <i class="fas fa-circle me-1"></i>
                            Estado <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <button class="filter-btn" data-filter="tamano">
                            <i class="fas fa-expand me-1"></i>
                            Tamaño <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <button class="filter-btn" data-filter="tipo">
                            <i class="fas fa-tag me-1"></i>
                            Tipo <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                    </div>
                    
                    <div class="filter-group">
                        <button class="filter-btn" data-filter="precio">
                            <i class="fas fa-dollar-sign me-1"></i>
                            Precio <i class="fas fa-chevron-down ms-1"></i>
                        </button>
                    </div>

                    <div class="ms-auto">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text" class="form-control" placeholder="Buscar habitación..." id="searchInput">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de habitaciones -->
        <div class="rooms-container">
            <div class="rooms-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-xl-4 g-4" id="roomsGrid">
                <!-- Las habitaciones se cargarán aquí via JavaScript -->
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
    --success-color: #28a745;
    --warning-color: #ffc107;
    --danger-color: #dc3545;
    --info-color: #17a2b8;
}

/* Reset y Base */
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

/* FILTER STYLES */
.filter-btn {
    background: white;
    border: 2px solid #e9ecef;
    color: #495057;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filter-btn:hover {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
    transform: translateY(-2px);
}

.filter-btn.active {
    background: var(--primary-color);
    border-color: var(--primary-color);
    color: white;
}

/* CARD STYLES */
.card {
    border: none;
    border-radius: 15px;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
    overflow: hidden;
}

.card-body {
    padding: 1.5rem;
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

/* ALERT STYLES */
.alert {
    border: none;
    border-radius: 10px;
    padding: 1rem 1.5rem;
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
}

.alert-danger {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
}

/* ROOMS GRID */
.rooms-container {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
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
    
    .filter-group {
        flex: 1;
        min-width: 120px;
    }
    
    .filter-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 767.98px) {
    .d-flex.flex-wrap.align-items-center.gap-3 {
        flex-direction: column;
        align-items: stretch !important;
    }
}
</style>

<script>
// Variables globales
window.rooms = <?php
$jsHabitaciones = array_map(function($hab) {
    $estado = strtolower($hab['estado_desc'] ?? $hab['estado'] ?? '');
    if ($estado === 'disponible' || $estado === '1') {
        $jsEstado = 'disponible';
    } elseif ($estado === 'reservada' || $estado === '2') {
        $jsEstado = 'reservada';
    } elseif ($estado === 'en uso' || $estado === 'en-uso' || $estado === 'ocupada' || $estado === '3') {
        $jsEstado = 'en-uso';
    } elseif ($estado === 'mantenimiento') {
        $jsEstado = 'mantenimiento';
    } else {
        $jsEstado = 'disponible';
    }
    // Traducir tipo y tamaño si vienen como ID
    $tipo = isset($hab['tipo_desc']) ? $hab['tipo_desc'] : (isset($hab['tipoHabitacion']) ? $hab['tipoHabitacion'] : '');
    if (is_numeric($tipo)) {
        $tipo = ($tipo == 1 ? 'Individual' : ($tipo == 2 ? 'Doble' : ($tipo == 3 ? 'Suite' : $tipo)));
    }
    $tamano = isset($hab['tamano_desc']) ? $hab['tamano_desc'] : (isset($hab['tamano']) ? $hab['tamano'] : '');
    if (is_numeric($tamano)) {
        $tamano = ($tamano == 1 ? 'Pequeña' : ($tamano == 2 ? 'Mediana' : ($tamano == 3 ? 'Grande' : $tamano)));
    }
    return [
        'id' => isset($hab['numero']) ? $hab['numero'] : '',
        'number' => isset($hab['numero']) ? $hab['numero'] : '',
        'precio' => isset($hab['costo']) ? intval($hab['costo']) : '',
        'capacidad' => isset($hab['capacidad']) ? $hab['capacidad'] : '',
        'type' => $tipo,
        'tamano' => $tamano,
        'status' => $jsEstado,
        'info' => isset($hab['informacionAdicional']) ? $hab['informacionAdicional'] : (isset($hab['descripcion']) ? $hab['descripcion'] : '')
    ];
}, isset($habitaciones) && is_array($habitaciones) ? $habitaciones : []);
echo json_encode($jsHabitaciones);
?>;

// Función para toggle del sidebar
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

