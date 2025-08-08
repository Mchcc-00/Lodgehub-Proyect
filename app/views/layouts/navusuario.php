<nav class="navbar navbar-expand-lg" id="navbar">
    <div class="container-fluid">
            
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
                    
                    <!-- Usuario -->
                    <div class="dropdown">
                        <button class="btn btn-user dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-2"></i>
                            <span class="d-none d-md-inline"><?php echo isset($userName) ? $userName : 'Admin'; ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="../Usuarios/miPerfil.php">
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