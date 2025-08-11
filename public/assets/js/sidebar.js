/**
 * SIDEBAR.JS - Script para manejo del sidebar dinámico
 * Funciones: Toggle sidebar, submenús, responsive design
 */

// ========== VARIABLES GLOBALES ==========
let sidebarState = {
    isOpen: false,
    activeSubmenu: null,
    isMobile: window.innerWidth <= 768
};

// ========== FUNCIONES PRINCIPALES ==========

/**
 * Toggle del sidebar principal (para móvil y desktop)
 */
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const body = document.body;
    
    sidebar.classList.toggle('show');
    body.classList.toggle('sidebar-open');
    
    // Solo mostrar overlay en móvil
    if (window.innerWidth < 992) {
        overlay.classList.toggle('show');
    }
    
    // Cambiar el ícono del botón collapse
    const collapseBtn = document.querySelector('.btn-collapse-sidebar i');
    if (collapseBtn) {
        if (sidebar.classList.contains('show')) {
            collapseBtn.className = 'fas fa-chevron-left';
        } else {
            collapseBtn.className = 'fas fa-chevron-right';
        }
    }
}
// Cerrar sidebar al hacer clic en un enlace solo en móvil
document.addEventListener('DOMContentLoaded', function() {
    const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                toggleSidebar();
            }
        });
    });
    
    // Manejar resize de ventana
    window.addEventListener('resize', function() {
        const overlay = document.getElementById('sidebarOverlay');
        
        if (window.innerWidth >= 992) {
            overlay.classList.remove('show');
        }
    });
    
    
})

/**
 * Toggle de submenús
 * @param {string} menuId - ID del submenú a toggle
 */
function toggleSubmenu(menuId) {
    const submenu = document.getElementById('submenu-' + menuId);
    const toggleLink = submenu?.previousElementSibling;
    const arrow = toggleLink?.querySelector('.submenu-arrow');
    
    if (!submenu || !arrow) return;
    
    const isCurrentlyOpen = submenu.style.display === 'block';
    
    // Cerrar otros submenús primero (opcional - para comportamiento accordion)
    closeAllSubmenus();
    
    if (!isCurrentlyOpen) {
        // Abrir este submenú
        submenu.style.display = 'block';
        arrow.style.transform = 'rotate(180deg)';
        toggleLink.classList.add('submenu-open');
        sidebarState.activeSubmenu = menuId;
        
        // Animación suave
        submenu.style.maxHeight = submenu.scrollHeight + 'px';
        submenu.style.opacity = '1';
    } else {
        // Cerrar este submenú
        closeSubmenu(menuId);
    }
    
    // Guardar estado del submenú
    localStorage.setItem('activeSubmenu', sidebarState.activeSubmenu || '');
}

/**
 * Cerrar un submenú específico
 * @param {string} menuId - ID del submenú a cerrar
 */
function closeSubmenu(menuId) {
    const submenu = document.getElementById('submenu-' + menuId);
    const toggleLink = submenu?.previousElementSibling;
    const arrow = toggleLink?.querySelector('.submenu-arrow');
    
    if (!submenu || !arrow) return;
    
    submenu.style.display = 'none';
    submenu.style.maxHeight = '0';
    submenu.style.opacity = '0';
    arrow.style.transform = 'rotate(0deg)';
    toggleLink?.classList.remove('submenu-open');
    
    if (sidebarState.activeSubmenu === menuId) {
        sidebarState.activeSubmenu = null;
    }
}

/**
 * Cerrar todos los submenús
 */
function closeAllSubmenus() {
    const allSubmenus = document.querySelectorAll('.submenu');
    const allArrows = document.querySelectorAll('.submenu-arrow');
    const allToggles = document.querySelectorAll('.submenu-toggle');
    
    allSubmenus.forEach(submenu => {
        submenu.style.display = 'none';
        submenu.style.maxHeight = '0';
        submenu.style.opacity = '0';
    });
    
    allArrows.forEach(arrow => {
        arrow.style.transform = 'rotate(0deg)';
    });
    
    allToggles.forEach(toggle => {
        toggle.classList.remove('submenu-open');
    });
    
    sidebarState.activeSubmenu = null;
}

/**
 * Auto-expandir submenú si una página del submenú está activa
 */
function autoExpandActiveSubmenu() {
    const activeSubmenuItem = document.querySelector('.submenu-item.active');
    
    if (activeSubmenuItem) {
        const parentSubmenu = activeSubmenuItem.closest('.submenu');
        if (parentSubmenu) {
            const menuId = parentSubmenu.id.replace('submenu-', '');
            const arrow = parentSubmenu.previousElementSibling?.querySelector('.submenu-arrow');
            
            // Expandir sin animación para carga inicial
            parentSubmenu.style.display = 'block';
            parentSubmenu.style.maxHeight = parentSubmenu.scrollHeight + 'px';
            parentSubmenu.style.opacity = '1';
            
            if (arrow) {
                arrow.style.transform = 'rotate(180deg)';
            }
            
            parentSubmenu.previousElementSibling?.classList.add('submenu-open');
            sidebarState.activeSubmenu = menuId;
        }
    }
}

/**
 * Manejar cambios de tamaño de ventana (responsive)
 */
function handleResize() {
    const wasMobile = sidebarState.isMobile;
    sidebarState.isMobile = window.innerWidth <= 768;
    
    // Si cambió de móvil a desktop, asegurar que el sidebar esté en estado correcto
    if (wasMobile && !sidebarState.isMobile) {
        document.body.style.overflow = '';
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        
        if (sidebar && overlay) {
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
        }
        sidebarState.isOpen = false;
    }
}

/**
 * Restaurar estado guardado del sidebar
 */
function restoreSidebarState() {
    // Restaurar estado del sidebar principal (solo para desktop)
    if (!sidebarState.isMobile) {
        const savedSidebarState = localStorage.getItem('sidebarOpen');
        if (savedSidebarState === 'true') {
            openSidebar();
        }
    }
    
    // Restaurar submenú activo
    const savedSubmenu = localStorage.getItem('activeSubmenu');
    if (savedSubmenu) {
        const submenu = document.getElementById('submenu-' + savedSubmenu);
        if (submenu) {
            toggleSubmenu(savedSubmenu);
        }
    }
}

/**
 * Manejar clicks fuera del sidebar en móvil
 * @param {Event} event - Evento de click
 */
function handleOutsideClick(event) {
    if (!sidebarState.isMobile || !sidebarState.isOpen) return;
    
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.querySelector('.btn-collapse-sidebar');
    
    if (sidebar && 
        !sidebar.contains(event.target) && 
        !sidebarToggle?.contains(event.target)) {
        closeSidebar();
    }
}

// ========== EVENT LISTENERS ==========

/**
 * Inicialización cuando el DOM está listo
 */
document.addEventListener('DOMContentLoaded', function() {
    console.log('🚀 Sidebar script initialized');
    
    // Auto-expandir submenú activo
    autoExpandActiveSubmenu();
    
    // Restaurar estado guardado
    restoreSidebarState();
    
    // Event listener para resize
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(handleResize, 250);
    });
    
    // Event listener para clicks fuera del sidebar
    document.addEventListener('click', handleOutsideClick);
    
    // Prevenir que clicks dentro del sidebar lo cierren
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.addEventListener('click', function(event) {
            event.stopPropagation();
        });
    }
});

/**
 * Cleanup cuando se va a cerrar la página
 */
window.addEventListener('beforeunload', function() {
    // Guardar estados finales
    localStorage.setItem('sidebarOpen', sidebarState.isOpen.toString());
    localStorage.setItem('activeSubmenu', sidebarState.activeSubmenu || '');
});

// ========== UTILIDADES ADICIONALES ==========

/**
 * Obtener el estado actual del sidebar
 * @returns {Object} Estado del sidebar
 */
function getSidebarState() {
    return { ...sidebarState };
}

/**
 * Forzar cierre de sidebar (útil para navegación)
 */
function forceSidebarClose() {
    if (sidebarState.isOpen) {
        closeSidebar();
    }
}

/**
 * Destacar elemento de menú activo
 * @param {string} pageId - ID de la página activa
 */
function setActivePage(pageId) {
    // Remover active de todos los elementos
    document.querySelectorAll('.nav-link').forEach(link => {
        link.classList.remove('active');
    });
    
    // Agregar active al elemento correspondiente
    const activeLink = document.querySelector(`[data-page="${pageId}"]`);
    if (activeLink) {
        activeLink.classList.add('active');
    }
}