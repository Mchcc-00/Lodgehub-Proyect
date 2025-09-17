<?php
require_once 'validarSesion.php';
$currentPage = 'Habitaciones'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipos de Habitación - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesMisColaboradores.css"> <!-- Estilos para las listas -->
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
        
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_id = $_SESSION['hotel_id'] ?? null;
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    
    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder gestionar los tipos de habitación, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
        <div class="header">
            <h1><i class="fas fa-door-closed"></i> Gestión de Tipos de Habitación</h1>
            <p>Administra los diferentes tipos de habitación disponibles en tu hotel.</p>
        </div>

        <!-- Mensajes de alerta -->
        <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
        <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

        <!-- Sección de búsqueda y filtros -->
        <div class="search-section">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" placeholder="Buscar por descripción..." id="buscar-input">
                        <button class="btn btn-outline-primary" type="button" id="buscar-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <a href="listaHabitaciones.php" class="btn btn-secondary" title="Volver a la lista de habitaciones">
                        <i class="fas fa-arrow-left"></i> Volver
                    </a>
                    <button class="btn btn-success" id="refresh-btn" title="Recargar">
                        <i class="fas fa-sync-alt"></i>
                    </button>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#formModal" id="btn-crear">
                        <i class="fas fa-plus"></i> Crear Nuevo
                    </button>
                </div>
            </div>
        </div>

        <!-- Tabla de Tipos de Habitación -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descripción</th>
                        <th>N° de Habitaciones</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-tipos-habitacion">
                    <!-- Contenido generado por JS -->
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <div id="paginacion-container">
            <ul class="pagination justify-content-center" id="paginacion">
                <!-- Paginación generada por JS -->
            </ul>
        </div>
        <?php endif; ?>
    </div>

    <!-- Modal para Crear/Editar -->
    <div class="modal fade" id="formModal" tabindex="-1" aria-labelledby="modalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="modalLabel"><i class="fas fa-pencil-alt"></i> Formulario de Tipo de Habitación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="tipo-habitacion-form">
                        <input type="hidden" id="id_tipo" name="id">
                        <input type="hidden" name="id_hotel" value="<?php echo htmlspecialchars($hotel_id); ?>">
                        
                        <div class="mb-3">
                            <label for="descripcion" class="form-label">Descripción <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" required maxlength="20">
                            <div class="invalid-feedback">La descripción es obligatoria (máx. 20 caracteres).</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-btn">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Eliminación -->
    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="eliminarModalLabel"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar el tipo de habitación <strong id="eliminar-info"></strong>?</p>
                    <p class="text-danger"><strong>Advertencia:</strong> Esta acción no se puede deshacer. Si existen habitaciones asociadas a este tipo, no se podrá eliminar.</p>
                    <input type="hidden" id="eliminar-id">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion-btn">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarTipoHabitacion.js"></script>

</body>
</html>