<!-- Plantilla de diseño principal para las vistas de Lodgehub -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title ?? 'Lodgehub'); ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <div class="main-container">

        <header class="top-bar">
            <div class="top-bar-left">
                <img src="<?php echo BASE_URL; ?>/assets/img/LogoClaroLH.png" alt="Logo Lodgehub" class="top-logo">
            </div>
            <div class="top-bar-right">
                <i class="fas fa-user-circle user-icon" title="Perfil Usuario"></i>
            </div>
        </header>

    <div class="content-area">

            <aside class="sidebar left-sidebar">
                <nav>
                    <ul>
                        <li><a href="#">HABITACIONES</a></li>
                        <li><a href="#">RESERVAS</a></li>
                        <li><a href="<?php echo BASE_URL; ?>/usuarios/lista">USUARIOS</a></li>
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
    </body>
</html>