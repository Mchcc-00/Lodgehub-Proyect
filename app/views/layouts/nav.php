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
                    <li class="breadcrumb-item active text-white" aria-current="page">Dashboard</li>
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
                <a class="nav-link active" href="../../views/homepage/homepage.php">
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
        <!-- Header de la página -->
        <div class="page-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="page-title">
                        <i class="fas fa-tachometer-alt text-primary me-2"></i>
                        Dashboard Principal
                    </h2>
                    <p class="page-subtitle text-muted">Resumen general del sistema LODGEHUB</p>
                </div>
                <div class="date-display">
                    <i class="fas fa-calendar-day me-2"></i>
                    <span id="currentDate"></span>
                </div>
            </div>
        </div>

        <!-- Dashboard Sections -->
        <div class="dashboard-sections">
            <!-- Sección Reservas -->
            <section class="dashboard-section reservas-section">
                <h3 class="section-title">
                    <span class="icon">📅</span>
                    Reservas
                </h3>
                <div class="cards-grid">
                    <div class="stats-card card-info" onclick="redirectTo('#reservas-hoy-inician')">
                        <div class="stats-icon bg-primary">
                            <i class="fas fa-calendar-plus"></i>
                        </div>
                        <div class="stats-content">
                            <h3>12</h3>
                            <p>Inician Hoy</p>
                        </div>
                    </div>
                    
                    <div class="stats-card card-warning" onclick="redirectTo('#reservas-hoy-terminan')">
                        <div class="stats-icon bg-warning">
                            <i class="fas fa-calendar-minus"></i>
                        </div>
                        <div class="stats-content">
                            <h3>8</h3>
                            <p>Terminan Hoy</p>
                        </div>
                    </div>
                    
                    <div class="stats-card card-success" onclick="redirectTo('#reservas-activas')">
                        <div class="stats-icon bg-success">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                        <div class="stats-content">
                            <h3>45</h3>
                            <p>Activas</p>
                        </div>
                    </div>
                    
                    <div class="stats-card card-pending" onclick="redirectTo('#reservas-pendientes')">
                        <div class="stats-icon bg-secondary">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stats-content">
                            <h3>23</h3>
                            <p>Pendientes</p>
                        </div>
                    </div>
                    
                    <div class="stats-card card-inactive" onclick="redirectTo('#reservas-inactivas')">
                        <div class="stats-icon bg-danger">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <div class="stats-content">
                            <h3>7</h3>
                            <p>Inactivas</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección Mantenimiento -->
            <section class="dashboard-section mantenimiento-section">
                <h3 class="section-title">
                    <span class="icon">🔧</span>
                    Mantenimiento
                </h3>
                <div class="cards-grid maintenance-grid">
                    <div class="stats-card card-danger" onclick="redirectTo('#mantenimiento-pendientes')">
                        <div class="stats-icon bg-danger">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>15</h3>
                            <p>Pendientes</p>
                        </div>
                    </div>
                    
                    <div class="stats-card card-process" onclick="redirectTo('#mantenimiento-proceso')">
                        <div class="stats-icon bg-warning">
                            <i class="fas fa-cog fa-spin"></i>
                        </div>
                        <div class="stats-content">
                            <h3>6</h3>
                            <p>En Proceso</p>
                        </div>
                    </div>
                    
                    <div class="stats-card card-completed" onclick="redirectTo('#mantenimiento-finalizados')">
                        <div class="stats-icon bg-success">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>28</h3>
                            <p>Finalizados</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Sección PQRS -->
            <section class="dashboard-section pqrs-section">
                <h3 class="section-title">
                    <span class="icon">📞</span>
                    PQRS
                </h3>
                <div class="cards-grid pqrs-grid">
                    <div class="stats-card pqrs-high" onclick="redirectTo('#pqrs-alta')">
                        <div class="stats-icon bg-danger">
                            <i class="fas fa-exclamation-circle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>3</h3>
                            <p>Gravedad Alta</p>
                            <small class="text-muted">Sin responder</small>
                        </div>
                    </div>
                    
                    <div class="stats-card pqrs-medium" onclick="redirectTo('#pqrs-media')">
                        <div class="stats-icon bg-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>7</h3>
                            <p>Gravedad Media</p>
                            <small class="text-muted">Sin responder</small>
                        </div>
                    </div>
                    
                    <div class="stats-card pqrs-low" onclick="redirectTo('#pqrs-baja')">
                        <div class="stats-icon bg-info">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="stats-content">
                            <h3>12</h3>
                            <p>Gravedad Baja</p>
                            <small class="text-muted">Sin responder</small>
                        </div>
                    </div>
                    
                    <div class="stats-card pqrs-answered" onclick="redirectTo('#pqrs-respondidos')">
                        <div class="stats-icon bg-success">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="stats-content">
                            <h3>156</h3>
                            <p>Respondidos</p>
                            <small class="text-muted">Total resueltos</small>
                        </div>
                    </div>
                </div>
                
                <div class="pqrs-actions">
                    <button class="btn btn-primary btn-lg" onclick="redirectTo('/pqrs/todos')">
                        <i class="fas fa-list me-2"></i>
                        Ver Todos los PQRS
                    </button>
                </div>
            </section>
        </div>
    </div>
</main>

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
    padding: 1.5rem;
}

/* PAGE HEADER */
.page-header {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
}

.page-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #333;
    margin: 0;
}

.page-subtitle {
    margin: 0;
    font-size: 0.95rem;
}

.date-display {
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
}

/* DASHBOARD SECTIONS */
.dashboard-sections {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.dashboard-section {
    background: white;
    border-radius: 15px;
    padding: 1.5rem;
    box-shadow: 0 2px 20px rgba(0,0,0,0.05);
}

.section-title {
    font-size: 1.4rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.icon {
    font-size: 1.5rem;
}

/* CARDS GRID */
.cards-grid {
    display: grid;
    gap: 1rem;
}

.reservas-section .cards-grid {
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
}

.maintenance-grid {
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
}

.pqrs-grid {
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
}

/* STATS CARDS */
.stats-card {
    background: white;
    border-radius: 12px;
    padding: 1.2rem;
    display: flex;
    align-items: center;
    box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid #f0f0f0;
}

.stats-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
}

.stats-icon {
    width: 50px;
    height: 50px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.3rem;
    margin-right: 1rem;
    flex-shrink: 0;
}

.stats-content h3 {
    font-size: 1.6rem;
    font-weight: 700;
    margin: 0;
    color: #333;
}

.stats-content p {
    margin: 0;
    color: #666;
    font-size: 0.85rem;
    font-weight: 500;
}

.stats-content small {
    font-size: 0.75rem;
    color: #999;
}

/* PQRS Colors */
.pqrs-high {
    border-left: 4px solid #dc3545;
}

.pqrs-medium {
    border-left: 4px solid #fd7e14;
}

.pqrs-low {
    border-left: 4px solid #0dcaf0;
}

.pqrs-answered {
    border-left: 4px solid #198754;
}

/* PQRS Actions */
.pqrs-actions {
    margin-top: 1.5rem;
    text-align: center;
}

.pqrs-actions .btn {
    padding: 0.8rem 2rem;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pqrs-actions .btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.15);
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
        padding: 1.2rem;
    }
    
    .dashboard-section {
        padding: 1.2rem;
    }
    
    .stats-card {
        flex-direction: column;
        text-align: center;
        padding: 1rem;
    }
    
    .stats-icon {
        margin-right: 0;
        margin-bottom: 0.8rem;
    }
    
    .cards-grid {
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    }
}

@media (max-width: 767.98px) {
    .page-header .d-flex {
        flex-direction: column;
        align-items: flex-start !important;
        gap: 1rem;
    }
    
    .cards-grid {
        grid-template-columns: 1fr 1fr;
        gap: 0.8rem;
    }
    
    .stats-card {
        padding: 0.8rem;
    }
    
    .stats-content h3 {
        font-size: 1.4rem;
    }
    
    .section-title {
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .cards-grid {
        grid-template-columns: 1fr;
    }
    
    .reservas-section .cards-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}
</style>