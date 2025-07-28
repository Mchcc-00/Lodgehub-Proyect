
    <div class="main-container">

        <header class="top-bar">
            <div class="top-bar-left">
                <img src="../../../public/assets/img/LogoClaroLH.png" alt="Logo Lodgehub" class="top-logo">
            </div>

        <div>
            <?php include '../login/navbar.php'?>
        </div>

            <div class="top-bar-right">
                <i class="fas fa-user-circle user-icon" title="Perfil Usuario"></i>
            </div>
        </header>
<nav>
        <div class="content-area">

            <aside class="sidebar left-sidebar">
                <nav>
                    <ul>
                        <li><a href="../../../6_Reservas/2R/mainReservas.php" >RESERVAS</a></li>
                        <li><a href="#">HABITACIONES</a></li>
                        <li><a href="#">MANTENIMIENTOS</a></li>
                        <li><a href="../PQRS/index.html">PQRS</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/usuarios/lista">USUARIOS</a></li>
                        <li><a href=""></a></li>
                    </ul>
                </nav>
                <div class="sidebar-bottom-icons">
                    <i class="fas fa-headphones"></i>
                    <i class="fas fa-cogs"></i> </div>
            </aside>

            <main class="form-content-container">
                <?php echo $contenido; // <-- ¡AQUÍ SE INYECTA LA VISTA ESPECÍFICA! ?>
            </main>

        </div>
    </div>
    <script src="<?php echo BASE_URL; ?>/assets/js/form-validation.js"></script>
</nav>
