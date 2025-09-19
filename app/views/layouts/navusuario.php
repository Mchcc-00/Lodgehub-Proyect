<nav class="navbar">
    <div class="nav-left">
        <a href="/index.php" class="nav-logo">
            <img src="/public/img/LogoClaroLHSinTitulo.png" alt="LODGEHUB">
            <div class="logo">LODGEHUB</div>
        </a>
    </div>

    <!-- <div class="nav-center">
        <a href="../index.php" class="nav-link active">Inicio</a>
        <a href="../plazaHotel.php" class="nav-link">Hotel</a>
    </div> -->

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item active" aria-current="page"><a href="index.php"><?php echo $paginaActual; ?></a></li>
        </ol>
    </nav>

    <div class="nav-right">
        <?php
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (isset($_SESSION['nombres'])) {
        ?>
            <div class="dropdown">
                <button class="profile-btn dropdown-toggle" onclick="document.getElementById('dropdown-menu').classList.toggle('show')">
                    👤 <?php echo htmlspecialchars($_SESSION['nombres']); ?>
                </button>
                <div id="dropdown-menu" class="dropdown-menu">
                    <li>
                        <a class="dropdown-item" href="/app/views/miPerfilMain.php">
                            <i class="fas fa-user me-2 gradien"></i>Mi Perfil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="/app/views/homepage.php">
                            <i class="fas fa-hotel me-2"></i>Mi hotel
                        </a>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li>
                        <a class="dropdown-item text-danger" href="/app/views/cerrarSesion.php">
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
            echo '<a href="/app/views/login.php" class="profile-btn">👤 Iniciar sesión</a>';
        }
        ?>
    </div>
</nav>