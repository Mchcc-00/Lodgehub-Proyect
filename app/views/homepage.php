<?php
require_once 'validarSesion.php';
require_once 'validarHome.php';

// Crear instancia de DashboardData
try {
    $dashboard = new DashboardData();
    
    // Obtener todos los datos del dashboard
    $dashboardData = $dashboard->getAllDashboardData();
    
} catch (Exception $e) {
    // En caso de error, usar valores por defecto
    $dashboardData = [
        'reservas' => [
            'hoy_inician' => 0,
            'hoy_terminan' => 0,
            'activas' => 0,
            'pendientes' => 0,
            'inactivas' => 0
        ],
        'mantenimiento' => [
            'pendientes' => 0,
            'en_proceso' => 0,
            'finalizados' => 0
        ],
        'pqrs' => [
            'gravedad_alta' => 0,
            'gravedad_media' => 0,
            'gravedad_baja' => 0,
            'respondidos' => 0
        ]
    ];
    error_log("Error en dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="../../public/assets/css/styleHomepage.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNav.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content" id="mainContent">
        <div class="content-wrapper">
            <!-- Header de la página -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="page-title">
                            <i class="fas fa-tachometer-alt text-primary me-2"></i>
                            Tablero Principal
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
                                <h3><?php echo $dashboardData['reservas']['hoy_inician']; ?></h3>
                                <p>Inician Hoy</p>
                            </div>
                        </div>
                        
                        <div class="stats-card card-warning" onclick="redirectTo('#reservas-hoy-terminan')">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-calendar-minus"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['hoy_terminan']; ?></h3>
                                <p>Terminan Hoy</p>
                            </div>
                        </div>
                        
                        <div class="stats-card card-success" onclick="redirectTo('#reservas-activas')">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['activas']; ?></h3>
                                <p>Activas</p>
                            </div>
                        </div>
                        
                        <div class="stats-card card-pending" onclick="redirectTo('#reservas-pendientes')">
                            <div class="stats-icon bg-secondary">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['pendientes']; ?></h3>
                                <p>Pendientes</p>
                            </div>
                        </div>
                        
                        <div class="stats-card card-inactive" onclick="redirectTo('#reservas-inactivas')">
                            <div class="stats-icon bg-danger">
                                <i class="fas fa-calendar-times"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['inactivas']; ?></h3>
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
                                <h3><?php echo $dashboardData['mantenimiento']['pendientes']; ?></h3>
                                <p>Pendientes</p>
                            </div>
                        </div>
                        
                        <div class="stats-card card-process" onclick="redirectTo('#mantenimiento-proceso')">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-cog fa-spin"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['mantenimiento']['en_proceso']; ?></h3>
                                <p>En Proceso</p>
                            </div>
                        </div>
                        
                        <div class="stats-card card-completed" onclick="redirectTo('#mantenimiento-finalizados')">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check-circle"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['mantenimiento']['finalizados']; ?></h3>
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
                                <h3><?php echo $dashboardData['pqrs']['gravedad_alta']; ?></h3>
                                <p>Gravedad Alta</p>
                                <small class="text-muted">Sin responder</small>
                            </div>
                        </div>
                        
                        <div class="stats-card pqrs-medium" onclick="redirectTo('#pqrs-media')">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['pqrs']['gravedad_media']; ?></h3>
                                <p>Gravedad Media</p>
                                <small class="text-muted">Sin responder</small>
                            </div>
                        </div>
                        
                        <div class="stats-card pqrs-low" onclick="redirectTo('#pqrs-baja')">
                            <div class="stats-icon bg-info">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['pqrs']['gravedad_baja']; ?></h3>
                                <p>Gravedad Baja</p>
                                <small class="text-muted">Sin responder</small>
                            </div>
                        </div>
                        
                        <div class="stats-card pqrs-answered" onclick="redirectTo('#pqrs-respondidos')">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-check-double"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['pqrs']['respondidos']; ?></h3>
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

        // Función para redireccionar
        function redirectTo(url) {
            console.log('Redirigiendo a:', url);
            // Aquí puedes cambiar por window.location.href = url; cuando tengas las URLs reales
            alert('Redirigiendo a: ' + url);
        }

        // Inicializar
        updateDate();
        
        // Actualizar fecha cada minuto
        setInterval(updateDate, 60000);
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>