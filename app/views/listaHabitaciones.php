<?php
require_once 'validarSesion.php';
$currentPage = 'Habitaciones'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Habitaciones - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitaciones.css">
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        // VALIDACIÓN: Asegurarse de que un hotel ha sido seleccionado
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_id = $_SESSION['hotel_id'] ?? null;

        // Cargar tipos de habitación para el filtro
        $tiposHabitacion = [];
        if ($hotelSeleccionado) {
            require_once '../models/habitacionesModel.php';
            $habitacionesModel = new HabitacionesModel();
            $tiposHabitacion = $habitacionesModel->obtenerTiposHabitacion($hotel_id);
        }
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder gestionar las habitaciones, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
            <div class="header">
                <h1><i class="fas fa-bed custom-icon"></i> Gestión de Habitaciones</h1>
                <p>Administra todas las habitaciones de tu hotel.</p>
            </div>

            <!-- Búsqueda y filtros -->
            <div class="search-section">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="buscar-input" class="form-label">Buscar</label>
                        <input type="text" class="form-control" id="buscar-input" placeholder="Por número, tipo o descripción...">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro-estado" class="form-label">Estado</label>
                        <select id="filtro-estado" class="form-select">
                            <option value="all">Todos</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Reservada">Reservada</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtro-tipo" class="form-label">Tipo</label>
                        <select id="filtro-tipo" class="form-select">
                            <option value="all">Todos</option>
                            <?php foreach ($tiposHabitacion as $tipo): ?>
                                <option value="<?php echo htmlspecialchars($tipo['id']); ?>"><?php echo htmlspecialchars($tipo['descripcion']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <a href="crearHabitacion.php" class="btn btn-success w-100"><i class="fas fa-plus"></i> Nueva</a>
                    </div>
                </div>
            </div>

            <!-- Mensajes de alerta -->
            <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
            <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

            <!-- Grid de Habitaciones -->
            <div id="loading" class="text-center p-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando habitaciones...</p>
            </div>
            <div class="habitaciones-grid" id="habitaciones-grid">
                <!-- Las tarjetas de habitaciones se insertarán aquí -->
            </div>

            <!-- Paginación -->
            <nav id="paginacion-container" style="display: none;"><ul class="pagination justify-content-center" id="paginacion"></ul></nav>
        <?php endif; ?>
    </div>

    <!-- Modal de Edición -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel"><i class="fas fa-edit"></i> Editar Habitación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar-habitacion" novalidate enctype="multipart/form-data">
                        <input type="hidden" id="edit-id" name="id">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="edit-numero" class="form-label">Número</label>
                                <input type="text" class="form-control" id="edit-numero" name="numero" required>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-tipoHabitacion" class="form-label">Tipo</label>
                                <select class="form-select" id="edit-tipoHabitacion" name="tipoHabitacion" required>
                                    <?php foreach ($tiposHabitacion as $tipo): ?>
                                        <option value="<?php echo htmlspecialchars($tipo['id']); ?>"><?php echo htmlspecialchars($tipo['descripcion']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-costo" class="form-label">Costo por Noche</label>
                                <input type="number" class="form-control" id="edit-costo" name="costo" required min="0">
                            </div>
                            <div class="col-md-6">
                                <label for="edit-capacidad" class="form-label">Capacidad</label>
                                <input type="number" class="form-control" id="edit-capacidad" name="capacidad" required min="1">
                            </div>
                            <div class="col-md-12">
                                <label for="edit-descripcion" class="form-label">Descripción</label>
                                <textarea class="form-control" id="edit-descripcion" name="descripcion" rows="3"></textarea>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-estado" class="form-label">Estado</label>
                                <select class="form-select" id="edit-estado" name="estado" required>
                                    <option value="Disponible">Disponible</option>
                                    <option value="Ocupada">Ocupada</option>
                                    <option value="Reservada">Reservada</option>
                                    <option value="Mantenimiento">Mantenimiento</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="edit-foto" class="form-label">Cambiar Foto</label>
                                <input type="file" class="form-control" id="edit-foto" name="foto" accept="image/*">
                            </div>
                            <div class="col-12" id="edit-foto-preview-container">
                                <label class="form-label">Foto Actual</label><br>
                                <img id="edit-foto-preview" src="" alt="Foto actual" style="max-width: 150px; border-radius: 8px;">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-guardar-edicion"><i class="fas fa-save"></i> Guardar Cambios</button>
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
                    <p>¿Estás seguro de que deseas eliminar la habitación <strong id="eliminar-numero-habitacion"></strong>?</p>
                    <p class="text-danger small">Esta acción no se puede deshacer. Si la habitación tiene reservas asociadas, no se podrá eliminar.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="btn-confirmar-eliminacion">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Visualización -->
    <div class="modal fade" id="verModal" tabindex="-1" aria-labelledby="verModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verModalLabel"><i class="fas fa-eye"></i> Detalles de la Habitación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="detalles-habitacion">
                    <!-- Contenido dinámico generado por JS -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarHabitaciones.js"></script>

</body>
</html>