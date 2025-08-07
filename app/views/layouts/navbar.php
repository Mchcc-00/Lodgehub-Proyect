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
                    <li class="breadcrumb-item active text-white" aria-current="page"><?php echo isset($pageTitle) ? $pageTitle : 'Página'; ?></li>
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
                            <span class="d-none d-md-inline"><?php echo isset($userName) ? $userName : 'Admin'; ?></span>
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