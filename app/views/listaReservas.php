<?php
// --- INICIO: LÓGICA DE ENRUTAMIENTO ---
// Si esta página es llamada por AJAX (desde nuestro JavaScript),
// actuará como un controlador y no como una página HTML.
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    // Incluimos y ejecutamos el controlador de reservas de forma segura en el servidor.
    require_once '../controllers/reservasController.php';
    // El controlador se encargará de generar la respuesta JSON y terminar la ejecución.
    exit();
}
// Si no es una llamada AJAX, la página se carga normalmente.
// --- FIN: LÓGICA DE ENRUTAMIENTO ---
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Reservas - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesReservas.css">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        // --- INICIO: CONTROL DE ACCESO ---
        // Solo administradores y colaboradores pueden acceder
        if (!isset($_SESSION['user']['roles']) || !in_array($_SESSION['user']['roles'], ['Administrador', 'Colaborador'])) {
            echo '<div class="container mt-5"><div class="alert alert-danger text-center"><h4><i class="fas fa-lock"></i> Acceso Denegado</h4><p>No tienes los permisos necesarios para gestionar las reservas.</p><a href="homepage.php" class="btn btn-primary mt-3">Volver al Inicio</a></div></div>';
            echo '</body></html>';
            exit();
        }
        
        // Validar que se haya seleccionado un hotel
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_nombre_sesion = $_SESSION['hotel_nombre'] ?? 'No asignado';
        // --- FIN: CONTROL DE ACCESO ---
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">

        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-warning mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Hotel No Seleccionado</h4>
                <p>Para poder gestionar las reservas, primero debes <strong>seleccionar un hotel</strong> desde el panel principal.</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>

        <div class="header">
            <h1><i class="fas fa-calendar-alt"></i> Lista de Reservas</h1>
            <p>Gestiona todas las reservas del hotel: <strong><?php echo htmlspecialchars($hotel_nombre_sesion); ?></strong></p>
        </div>

        <!-- Sección de búsqueda y filtros -->
        <div class="search-section" style="position: relative; z-index: 2;">
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-input" placeholder="Buscar por ID, huésped, habitación...">
                        <button class="btn btn-outline-primary" type="button" id="buscar-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" >
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">Todos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Por Estado</h6></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Activa">Activas</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Pendiente">Pendientes</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Finalizada">Finalizadas</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Cancelada">Canceladas</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-success" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <a href="crearReservas.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva Reserva
                    </a>
                </div>
            </div>
        </div>

        <!-- Mensajes -->
        <div id="success-message" class="success-message" style="display: none;">
            ✅ <strong id="success-text">Operación exitosa</strong>
        </div>

        <div id="error-message" class="error-message" style="display: none;">
            ❌ <strong id="error-text">Error en la operación</strong>
        </div>

        <!-- Tabla de Reservas -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Huésped</th>
                        <th>Habitación</th>
                        <th>Fechas</th>
                        <th>Num Personas</th>
                        <th>Pago Final</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-reservas">
                    <tr>
                        <td colspan="8" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Cargando reservas...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav aria-label="Paginación de Reservas" id="paginacion-container" style="display: none;">
            <ul class="pagination justify-content-center" id="paginacion">
                <!-- Generado dinámicamente -->
            </ul>
        </nav>

        <?php endif; ?>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-edit"></i> Editar Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar">
                        <input type="hidden" id="edit-id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-fechainicio" class="form-label">Fecha de Inicio</label>
                                    <input type="date" class="form-control" id="edit-fechainicio" name="fechainicio">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-fechaFin" class="form-label">Fecha de Fin</label>
                                    <input type="date" class="form-control" id="edit-fechaFin" name="fechaFin">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-pagoFinal" class="form-label">Pago Final</label>
                                    <input type="number" class="form-control" id="edit-pagoFinal" name="pagoFinal" step="0.01">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-estado" class="form-label">Estado</label>
                                    <select class="form-select" id="edit-estado" name="estado">
                                        <option value="Activa">Activa</option>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Finalizada">Finalizada</option>
                                        <option value="Cancelada">Cancelada</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="edit-informacionAdicional" class="form-label">Información Adicional</label>
                            <textarea class="form-control" id="edit-informacionAdicional" name="informacionAdicional" rows="3"></textarea>
                        </div>

                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-edicion">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de visualización completa -->
    <div class="modal fade" id="verModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verModalTitle">
                        <i class="fas fa-file-invoice-dollar"></i> Detalles de la Reserva
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-reserva">
                    <!-- Contenido dinámico -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal fade" id="eliminarModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta reserva?</p>
                    <div class="alert alert-info">
                        <strong id="eliminar-info">Información de la Reserva</strong><br>
                        <small>ID: <span id="eliminar-id">-</span></small>
                    </div>
                    <p class="text-danger">
                        <i class="fas fa-exclamation-triangle"></i> 
                        Esta acción no se puede deshacer.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script>
        // Generar la URL del controlador dinámicamente basada en la ubicación actual
        const CONTROLLER_URL = (() => {
            const currentPath = window.location.pathname;
            const pathParts = currentPath.split('/');
            
            // Remover el último elemento (el archivo actual) y agregar la ruta al controlador
            pathParts.pop(); // Remueve 'listarReservas.php'
            pathParts.push('..', 'controllers', 'reservasController.php');
            
            return pathParts.join('/');
        })();
        
        // Para debug - remover en producción
        console.log('CONTROLLER_URL:', CONTROLLER_URL);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarReservas.js"></script>

</body>
</html>