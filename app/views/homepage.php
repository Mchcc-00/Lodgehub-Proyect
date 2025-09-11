<?php
require_once 'validarSesion.php';
require_once 'validarHome.php'; // Aquí está nuestra nueva clase DashboardData

// Obtener contexto del usuario y hotel actual
$contexto = obtenerContextoUsuarioHotel();
$usuario = $contexto['usuario'];
$hotelInfo = $contexto['hotel'];
$hotel_id_filtro = $contexto['hotel_id_filtro'];
$rol_usuario = $contexto['rol_usuario'];

// Validar acceso
if (!$usuario) {
    header("Location: login.php?mensaje=Sesión no válida");
    exit;
}

// Crear instancia de DashboardData con filtro de hotel
try {
    $dashboard = new DashboardData($hotel_id_filtro, $rol_usuario);

    // Obtener todos los datos del dashboard filtrados por hotel
    $dashboardData = $dashboard->getAllDashboardData();

    // Log para debug (opcional)
    if ($hotel_id_filtro) {
        error_log("Dashboard cargado para usuario {$usuario['numDocumento']} en hotel {$hotel_id_filtro}");
    } else if ($rol_usuario === 'Administrador') {
        error_log("Dashboard cargado para super administrador {$usuario['numDocumento']} - vista global");
    }
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
        ],
        'habitaciones' => [
            'total_habitaciones' => 0,
            'disponibles' => 0,
            'ocupadas' => 0,
            'reservadas' => 0,
            'en_mantenimiento' => 0
        ],
        'hotel_info' => [
            'id' => null,
            'filtrado_por_hotel' => false
        ]
    ];
    error_log("Error en dashboard multi-hotel: " . $e->getMessage());
}

// Información adicional del hotel para mostrar en el banner
if (!$hotelInfo && $rol_usuario !== 'Usuario') {
    // Si no hay hotel asignado y no es usuario final, mostrar mensaje apropiado
    $mostrarBannerSinHotel = true;
} else {
    $mostrarBannerSinHotel = false;
}

// Determinar el mensaje del banner según el tipo de usuario
$mensajeBanner = '';
$tipoAdmin = $contexto['tipo_admin'] ?? 'colaborador';
if ($hotelInfo) {
    switch ($tipoAdmin) {
        case 'super':
            $mensajeBanner = "Vista general del sistema - Mostrando: " . $hotelInfo['nombre'];
            break;
        case 'hotel':
            $mensajeBanner = "Administrando: " . $hotelInfo['nombre'];
            break;
        case 'colaborador':
            $mensajeBanner = "Trabajando en: " . $hotelInfo['nombre'];
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home </title>
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

            <!-- Alertas de estado -->
            <?php if (isset($_GET['status']) && $_GET['status'] === 'hotel_success' && isset($_GET['message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <?php echo htmlspecialchars(urldecode($_GET['message'])); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Banner de información del hotel -->
            <?php if ($hotelInfo): ?>
                <section class="hotel-info-banner">
                    <div class="hotel-photo">
                        <img src="<?php echo !empty($hotelInfo['foto']) ? htmlspecialchars($hotelInfo['foto']) : '../../public/assets/img/default_hotel.png'; ?>"
                            alt="Foto del hotel <?php echo htmlspecialchars($hotelInfo['nombre']); ?>"
                            onerror="this.onerror=null;this.src='../../public/assets/img/default_hotel.png';">
                    </div>

                    <div class="hotel-main-info">
                        <div class="hotel-header">
                            <h3 class="hotel-name"><?php echo htmlspecialchars($hotelInfo['nombre']); ?></h3>

                            <!-- Indicador del tipo de vista -->
                            <?php if (!empty($mensajeBanner)): ?>
                                <!-- SOLUCIÓN: Añadir ms-3 para crear un margen a la izquierda -->
                                <div class="vista-indicator" style="align-self: center;">
                                    <!-- SOLUCIÓN: Cambiar de 'badge' a 'btn' para que se vea como un botón, manteniendo el color. -->
                                    <span class="btn btn-sm btn-<?php echo $tipoAdmin === 'super' ? 'primary' : 'success'; ?> disabled rounded-pill" style="cursor: default;">
                                        <i class="fas fa-<?php echo $tipoAdmin === 'super' ? 'globe' : 'building'; ?> me-1"></i>
                                        <?php echo htmlspecialchars($mensajeBanner); ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>

                        <div class="hotel-details mt-2">
                            <p class="hotel-address mb-2">
                                <i class="fas fa-map-marker-alt me-2"></i>
                                <?php echo htmlspecialchars($hotelInfo['direccion'] ?? 'Dirección no disponible'); ?>
                            </p>
                            <div class="hotel-contact d-flex flex-wrap gap-3">
                                <span>
                                    <i class="fas fa-phone me-2"></i>
                                    <?php echo htmlspecialchars($hotelInfo['telefono'] ?? 'N/A'); ?>
                                </span>
                                <span>
                                    <i class="fas fa-envelope me-2"></i>
                                    <?php echo htmlspecialchars($hotelInfo['correo'] ?? 'N/A'); ?>
                                </span>
                                <?php if (!empty($hotelInfo['nit'])): ?>
                                    <span>
                                        <i class="fas fa-id-card me-2"></i>
                                        NIT: <?php echo htmlspecialchars($hotelInfo['nit']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Acciones del hotel -->
                        <div class="hotel-actions">
                            <a href="plazaHotel.php?id=<?php echo $hotelInfo['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-eye me-2"></i>Ver Información Completa
                            </a>

                            <?php if (in_array($rol_usuario, ['Administrador', 'Colaborador'])): ?>
                            <?php endif; ?>

                            <?php if ($rol_usuario === 'Administrador'): ?>
                                <a href="editarHotel.php?id=<?php echo $hotelInfo['id']; ?>" class="btn btn-outline-warning">
                                    <i class="fas fa-edit me-2"></i>Editar Hotel
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="date-display">
                        <i class="fas fa-calendar-day me-2"></i>
                        <span id="currentDate"></span>
                    </div>

                    <!-- Indicador de filtrado -->
                    <?php if ($dashboardData['hotel_info']['filtrado_por_hotel']): ?>
                    <?php endif; ?>
                </section>

            <?php elseif ($mostrarBannerSinHotel): ?>
                <section class="no-hotel-banner">
                    <div class="no-hotel-content">
                        <i class="fas fa-hotel fa-4x mb-4"></i>

                        <?php if ($rol_usuario === 'Administrador'): ?>
                            <h3>¡Crea tu primer hotel en el sistema!</h3>
                            <p class="mb-4">Como administrador, puedes registrar hoteles y comenzar a gestionar reservas, habitaciones y más.</p>
                            <a href="agregarHoteles.php" class="btn btn-lg">
                                <i class="fas fa-plus-square me-2 gradient-icon"></i>Registrar Primer Hotel
                            </a>
                        <?php else: ?>
                            <h3>No tienes un hotel asignado</h3>
                            <p class="mb-4">Contacta con el administrador para que te asigne a un hotel y puedas acceder a las herramientas de gestión.</p>
                            <a href="contacto.php" class="btn btn-outline-light btn-lg">
                                <i class="fas fa-envelope me-2"></i>Contactar Soporte
                            </a>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Dashboard Sections -->
            <div class="dashboard-sections">
                <!-- Sección Reservas -->
                <section class="dashboard-section reservas-section">
                    <h3 class="section-title">
                        <span class="icon">📅</span>
                        Reservas
                        <?php if ($dashboardData['hotel_info']['filtrado_por_hotel']): ?>
                            <small class="text-muted">- <?php echo htmlspecialchars($hotelInfo['nombre']); ?></small>
                        <?php endif; ?>
                    </h3>
                    <div class="cards-grid">
                        <div class="stats-card card-info" onclick="redirectTo('listaReservas.php?filter=hoy_inician&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-primary">
                                <i class="fas fa-calendar-plus"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['hoy_inician']; ?></h3>
                                <p>Inician Hoy</p>
                            </div>
                        </div>

                        <div class="stats-card card-warning" onclick="redirectTo('reservas.php?filter=hoy_terminan&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-calendar-minus"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['hoy_terminan']; ?></h3>
                                <p>Terminan Hoy</p>
                            </div>
                        </div>

                        <div class="stats-card card-success" onclick="redirectTo('reservas.php?filter=activas&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-success">
                                <i class="fas fa-calendar-check"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['activas']; ?></h3>
                                <p>Activas</p>
                            </div>
                        </div>

                        <div class="stats-card card-pending" onclick="redirectTo('reservas.php?filter=pendientes&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-secondary">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['reservas']['pendientes']; ?></h3>
                                <p>Pendientes</p>
                            </div>
                        </div>

                        <div class="stats-card card-inactive" onclick="redirectTo('reservas.php?filter=inactivas&hotel_id=<?php echo $hotel_id_filtro; ?>')">
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
                        <?php if ($dashboardData['hotel_info']['filtrado_por_hotel']): ?>
                            <small class="text-muted">- <?php echo htmlspecialchars($hotelInfo['nombre']); ?></small>
                        <?php endif; ?>
                    </h3>
                    <div class="cards-grid maintenance-grid">
                        <div class="stats-card card-danger" onclick="redirectTo('listaMantenimiento.php?filter=pendientes&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-danger">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['mantenimiento']['pendientes']; ?></h3>
                                <p>Pendientes</p>
                            </div>
                        </div>

                        <div class="stats-card card-process" onclick="redirectTo('mantenimiento.php?filter=proceso&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-warning">
                                <i class="fas fa-cog fa-spin"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['mantenimiento']['en_proceso']; ?></h3>
                                <p>En Proceso</p>
                            </div>
                        </div>

                        <div class="stats-card card-completed" onclick="redirectTo('mantenimiento.php?filter=finalizados&hotel_id=<?php echo $hotel_id_filtro; ?>')">
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
                        <?php if ($dashboardData['hotel_info']['filtrado_por_hotel']): ?>
                            <small class="text-muted">- <?php echo htmlspecialchars($hotelInfo['nombre']); ?></small>
                        <?php endif; ?>
                    </h3>
                    <div class="cards-grid pqrs-grid">
                        <div class="stats-card pqrs-high" onclick="redirectTo('listaPQRS.php?filter=alta&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-danger">
                                <i class="fas fa-exclamation-circle"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['pqrs']['gravedad_alta']; ?></h3>
                                <p>Gravedad Alta</p>
                                <small class="text-muted">Sin responder</small>
                            </div>
                        </div>


                        <div class="stats-card pqrs-low" onclick="redirectTo('pqrs.php?filter=baja&hotel_id=<?php echo $hotel_id_filtro; ?>')">
                            <div class="stats-icon bg-info">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="stats-content">
                                <h3><?php echo $dashboardData['pqrs']['gravedad_baja']; ?></h3>
                                <p>Gravedad Baja</p>
                                <small class="text-muted">Sin responder</small>
                            </div>
                        </div>

                        <div class="stats-card pqrs-answered" onclick="redirectTo('pqrs.php?filter=respondidos&hotel_id=<?php echo $hotel_id_filtro; ?>')">
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
                        <button class="btn btn-primary btn-lg" onclick="redirectTo('pqrs.php?hotel_id=<?php echo $hotel_id_filtro; ?>')">
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
            if (url && url !== 'undefined') {
                window.location.href = url;
            } else {
                console.log('URL no válida:', url);
            }
        }

        // Función para cambiar hotel (solo para super admins)
        function cambiarHotel(hotelId) {
            if (hotelId) {
                window.location.href = `cambiarHotel.php?hotel_id=${hotelId}&redirect=homepage.php`;
            }
        }

        // Inicializar
        updateDate();

        // Actualizar fecha cada minuto
        setInterval(updateDate, 60000);

        // Debug info (remover en producción)
        console.log('Hotel ID actual:', <?php echo json_encode($hotel_id_filtro); ?>);
        console.log('Rol usuario:', <?php echo json_encode($rol_usuario); ?>);
        console.log('Tipo admin:', <?php echo json_encode($tipoAdmin); ?>);
    </script>

    <!-- Bootstrap JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>