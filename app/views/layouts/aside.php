<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aside Component</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<!-- Aside/Sidebar -->
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
                <a class="nav-link active" href="../homepage/homepage.php">
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

<!-- Botón para abrir sidebar -->
<button class="btn-open-sidebar" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>

<!-- Contenido principal de ejemplo -->
<main class="main-content">
    <div class="container-fluid p-4">
        <h1>Contenido Principal</h1>
        <p>Este es el área donde va tu contenido principal. El sidebar se puede abrir y cerrar desde cualquier dispositivo.</p>
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Panel de Control</h5>
                        <p class="card-text">Utiliza el botón flotante o la flecha dentro del sidebar para abrir/cerrar la navegación.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<style>
/* Variables CSS */
:root {
    --primary-color: #437baf;
    --sidebar-width: 280px;
}

/* Sidebar principal */
.sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: var(--sidebar-width);
    height: 100vh;
    background: linear-gradient(135deg, var(--primary-color), #2c5282);
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

/* Header del sidebar */
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
    color: white;
}

/* Botón collapse dentro del sidebar */
.btn-collapse-sidebar {
    background: none;
    border: none;
    color: white;
    padding: 0.5rem;
    border-radius: 4px;
    transition: background 0.3s ease;
    font-size: 1rem;
}

.btn-collapse-sidebar:hover {
    background: rgba(255,255,255,0.1);
}

/* Navegación del sidebar */
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

.sidebar-nav .nav-link span {
    font-weight: 500;
}

/* Footer del sidebar */
.sidebar-footer {
    padding: 1.5rem;
    border-top: 1px solid rgba(255,255,255,0.1);
}

.sidebar-footer .btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

/* Botón toggle sidebar */
.btn-open-sidebar {
    position: fixed;
    top: 20px;
    left: 20px;
    z-index: 1030;
    background: var(--primary-color);
    color: white;
    border: none;
    width: 50px;
    height: 50px;
    border-radius: 50%;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.btn-open-sidebar:hover {
    background: #2c5282;
    transform: scale(1.05);
}

/* Cuando el sidebar está abierto, mover el botón */
.sidebar-open .btn-open-sidebar {
    left: 300px;
}

/* Overlay para móvil */
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

/* Ajustes para el contenido principal */
.main-content {
    transition: margin-left 0.3s ease;
    margin-left: 0;
    min-height: 100vh;
    background-color: #f8f9fa;
}

.sidebar-open .main-content {
    margin-left: var(--sidebar-width);
}

/* Responsive */
@media (max-width: 991.98px) {
    .sidebar-overlay.show {
        display: block;
    }
    
    /* En móvil, el contenido no se desplaza */
    .sidebar-open .main-content {
        margin-left: 0;
    }
    
    .sidebar-open .btn-open-sidebar {
        left: 20px;
    }
}

/* Scrollbar personalizado para el sidebar */
.sidebar-nav::-webkit-scrollbar {
    width: 4px;
}

.sidebar-nav::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
}

.sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 2px;
}
</style>

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
            // En desktop, quitar overlay pero mantener estado del sidebar
            overlay.classList.remove('show');
        }
    });
    
    // Inicializar el ícono correcto del botón collapse
    const collapseBtn = document.querySelector('.btn-collapse-sidebar i');
    const sidebar = document.getElementById('sidebar');
    if (collapseBtn && !sidebar.classList.contains('show')) {
        collapseBtn.className = 'fas fa-chevron-right';
    }
});
</script>

</body>
</html>