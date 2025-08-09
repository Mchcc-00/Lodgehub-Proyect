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
                <a class="nav-link <?php echo (isset($currentPage) && $currentPage == 'home') ? 'active' : ''; ?>" href="homepage.php">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($currentPage) && $currentPage == 'reservas') ? 'active' : ''; ?>" href="../../6_Reservas/2R/mainReservas.php">
                    <i class="fas fa-calendar-check"></i>
                    <span>Reservas</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($currentPage) && $currentPage == 'habitaciones') ? 'active' : ''; ?>" href="../../../app/views/Habitaciones/dashboardHab.php">
                    <i class="fas fa-bed"></i>
                    <span>Habitaciones</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($currentPage) && $currentPage == 'usuarios') ? 'active' : ''; ?>" href="../../app/views/listaColaborador.php">
                    <i class="fas fa-users"></i>
                    <span>Usuarios</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($currentPage) && $currentPage == 'mantenimiento') ? 'active' : ''; ?>" href="../../../MANTENIMIENTO/views/dashboard.php">
                    <i class="fas fa-tools"></i>
                    <span>Mantenimiento</span>
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo (isset($currentPage) && $currentPage == 'pqrs') ? 'active' : ''; ?>" href="../PQRS/index.php">
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