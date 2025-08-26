<nav class="navbar">
    <div class="nav-left">
        <a href="index.php" class="nav-logo">
            <img src="/lodgehub/public/img/LogoClaroLHSinTitulo.png" alt="LODGEHUB">
            <div class="logo">LODGEHUB</div>
        </a>
    </div>

    <!-- <div class="nav-center">
        <a href="../index.php" class="nav-link active">Inicio</a>
        <a href="../plazaHotel.php" class="nav-link">Hotel</a>
    </div> -->

    <?php $paginaActual = "Inicio"; ?>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"><a href="homeUsuario.php"><?php echo $paginaActual; ?></a></li>
        </ol>
    </nav>

    <div class="nav-right">
        <?php
        session_start();
        if (isset($_SESSION['nombres'])) {
        ?>
            <div class="dropdown">
                <button class="profile-btn dropdown-toggle" onclick="document.getElementById('dropdown-menu').classList.toggle('show')">
                    👤 <?php echo htmlspecialchars($_SESSION['nombres']); ?>
                </button>
                <div id="dropdown-menu" class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="miPerfil.php">
                            <i class="fas fa-user me-2"></i>Mi Perfil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-cog me-2"></i>Configuración
                        </a>
                    </li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="app/views/cerrarSesion.php">
                            <i class="fas fa-sign-out-alt me-2"></i>Cerrar Sesión
                        </a>
                    </li>

                </div>
            </div>
            <script>
                document.addEventListener('click', function(event) {
                    var dropdown = document.getElementById('dropdown-menu');
                    if (!event.target.closest('.dropdown')) {
                        dropdown.classList.remove('show');
                    }
                });
            </script>
        <?php
        } else {
            echo '<a href="app/views/login.php" class="profile-btn">👤 Iniciar sesión</a>';
        }
        ?>
    </div>
</nav>