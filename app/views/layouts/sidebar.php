<?php
require_once 'validarSesion.php';
// Asumiendo que tienes el rol del usuario en una sesión o variable
$userRole = $_SESSION['user']['roles'] ?? 'Colaborador'; // 'Administrador' o 'Colaborador'

// Verificar si hay un hotel seleccionado en la sesión
$hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);

// Configurar los elementos del menú según el rol
$menuItems = [];

if ($userRole === 'Administrador') {
    $menuItems = [
        [
            'page' => 'home',
            'text' => 'Home',
            'icon' => 'fas fa-home',
            'hasSubmenu' => true,
            'submenu' => [
                [
                    'page' => 'Mi hotel',
                    'href' => 'homepage.php',
                    'icon' => 'fas fa-info-circle',
                    'text' => 'Mi hotel'
                ],
                [
                    'page' => 'Reservas',
                    'href' => '../../6_Reservas/2R/mainReservas.php',
                    'icon' => 'fas fa-calendar-check',
                    'text' => 'Reservas'
                ],
                [
                    'page' => 'Huéspedes',
                    'href' => '../huespedes/dashboard.php',
                    'icon' => 'fas fa-user-friends',
                    'text' => 'Huéspedes'
                ],
                [
                    'page' => 'Habitaciones',
                    'href' => 'listaHabitaciones.php',
                    'icon' => 'fas fa-bed',
                    'text' => 'Habitaciones'
                ],
                [
                    'page' => 'Colaboradores',
                    'href' => 'listaMisColaboradores.php', // Enlace siempre activo
                    'icon' => 'fas fa-users',
                    'text' => 'Mis Colaboradores',
                    'disabled' => !$hotelSeleccionado, // Añadimos una propiedad para deshabilitar
                    'tooltip' => !$hotelSeleccionado ? 'Seleccione un hotel en el Home para gestionar colaboradores' : ''
                ],                
                [
                    'page' => 'Mantenimiento',
                    'href' => 'listaMantenimiento.php',
                    'icon' => 'fas fa-tools',
                    'text' => 'Mantenimiento'
                ]
            ]
        ],
        [
            'page' => 'pqrs',
            'href' => 'listaPQRS.php',
            'icon' => 'fas fa-comments',
            'text' => 'PQRS',
            'hasSubmenu' => false
        ]
    ];
} else { // Colaborador
    $menuItems = [
        [
            'page' => 'home',
            'text' => 'Home',
            'icon' => 'fas fa-home',
            'hasSubmenu' => true,
            'submenu' => [
                [
                    'page' => 'info Hotel',
                    'href' => 'homepage.php',
                    'icon' => 'fas fa-info-circle',
                    'text' => 'Info Hotel'
                ],
                [
                    'page' => 'reservas',
                    'href' => '../6_Reservas/2R/mainReservas.php',
                    'icon' => 'fas fa-calendar-check',
                    'text' => 'Reservas'
                ],
                [
                    'page' => 'Habitaciones',
                    'href' => 'verHabitaciones.php',
                    'icon' => 'fas fa-bed',
                    'text' => 'Habitaciones'
                ]
            ]
        ],
        [
            'page' => 'pqrs',
            'href' => '../PQRS/index.php',
            'icon' => 'fas fa-comments',
            'text' => 'PQRS',
            'hasSubmenu' => false
        ]
    ];
}

// Función para verificar si algún submenú está activo
function isSubmenuActive($submenu, $currentPage) {
    if (!$submenu) return false;
    foreach ($submenu as $item) {
        if (isset($currentPage) && $currentPage == $item['page']) {
            return true;
        }
    }
    return false;
}
?>

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
            <?php foreach ($menuItems as $item): ?>
            <li class="nav-item">
                <?php if ($item['hasSubmenu']): ?>
                    <!-- Elemento con submenú -->
                    <a class="nav-link submenu-toggle <?php echo isSubmenuActive($item['submenu'], $currentPage) ? 'active' : ''; ?>" 
                       href="#" onclick="toggleSubmenu('<?php echo $item['page']; ?>')">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span><?php echo $item['text']; ?></span>
                        <i class="fas fa-chevron-down submenu-arrow"></i>
                    </a>
                    
                    <!-- Submenú -->
                    <ul class="submenu" id="submenu-<?php echo $item['page']; ?>">
                        <?php foreach ($item['submenu'] as $subitem): ?>
                        <li class="nav-item">
                            <a class="nav-link submenu-item 
                                <?php echo (isset($currentPage) && $currentPage == $subitem['page']) ? 'active' : ''; ?>
                                <?php echo !empty($subitem['disabled']) ? 'disabled-link' : ''; ?>" 
                               href="<?php echo $subitem['href']; ?>"
                               <?php if (!empty($subitem['tooltip'])): ?>
                                   title="<?php echo htmlspecialchars($subitem['tooltip']); ?>"
                               <?php endif; ?>
                            >
                                <i class="<?php echo $subitem['icon']; ?>"></i>
                                <span><?php echo $subitem['text']; ?></span>
                            </a>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <!-- Elemento sin submenú -->
                    <a class="nav-link <?php echo (isset($currentPage) && $currentPage == $item['page']) ? 'active' : ''; ?>" 
                       href="<?php echo $item['href']; ?>">
                        <i class="<?php echo $item['icon']; ?>"></i>
                        <span><?php echo $item['text']; ?></span>
                    </a>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <a href="../../index.php" class="btn btn-danger w-100">
            <i class="fas fa-sign-out-alt"></i>
            Volver al inicio
        </a>
    </div>
</aside>

</script>
