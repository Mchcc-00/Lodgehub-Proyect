<?php
require_once 'validarSesion.php';
$currentPage = 'Mantenimiento'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Mantenimiento - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesMisColaboradores.css"> <!-- Reutilizamos estilos -->
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <div class="header">
            <h1><i class="fas fa-tools"></i> Gestión de Mantenimiento</h1>
            <p>Administra las tareas de mantenimiento de las habitaciones y áreas del hotel.</p>
        </div>

        <!-- Búsqueda y filtros -->
        <div class="search-section">
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-input" placeholder="Buscar por ID, habitación o descripción...">
                        <button class="btn btn-outline-primary" type="button" id="buscar-btn"><i class="fas fa-search"></i></button>
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="all">Todos los Estados</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="Pendiente">Pendientes</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="Finalizado">Finalizados</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter-group="prioridad" data-filter-value="all">Todas las Prioridades</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter-group="prioridad" data-filter-value="Alto">Prioridad Alta</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter-group="prioridad" data-filter-value="Bajo">Prioridad Baja</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-success" id="refresh-btn"><i class="fas fa-sync-alt"></i></button>
                    <a href="crearMantenimiento.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nuevo Mantenimiento</a>
                </div>
            </div>
        </div>

        <!-- Mensajes de alerta -->
        <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
        <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

        <!-- Tabla de Mantenimientos -->
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Habitación</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Responsable</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-mantenimientos">
                    <!-- Contenido generado por JS -->
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav id="paginacion-container" style="display: none;"><ul class="pagination" id="paginacion"></ul></nav>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Mantenimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar">
                        <input type="hidden" id="edit-id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-tipo" class="form-label">Tipo</label>
                                <select class="form-select" id="edit-tipo" name="tipo">
                                    <option value="Limpieza">Limpieza</option>
                                    <option value="Estructura">Estructura</option>
                                    <option value="Eléctrico">Eléctrico</option>
                                    <option value="Otro">Otro</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-prioridad" class="form-label">Prioridad</label>
                                <select class="form-select" id="edit-prioridad" name="prioridad">
                                    <option value="Bajo">Bajo</option>
                                    <option value="Alto">Alto</option>
                                </select>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-descripcion" class="form-label">Descripción del Problema</label>
                            <input type="text" class="form-control" id="edit-descripcion" name="problemaDescripcion">
                        </div>
                        <div class="mb-3">
                            <label for="edit-estado" class="form-label">Estado</label>
                            <select class="form-select" id="edit-estado" name="estado">
                                <option value="Pendiente">Pendiente</option>
                                <option value="Finalizado">Finalizado</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-observaciones" class="form-label">Observaciones / Solución</label>
                            <textarea class="form-control" id="edit-observaciones" name="observaciones" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-edicion"><i class="fas fa-save"></i> Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de visualización -->
    <div class="modal fade" id="verModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye"></i> Detalles del Mantenimiento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-mantenimiento">
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
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar este registro de mantenimiento?</p>
                    <div class="alert alert-warning">
                        <strong>ID: <span id="eliminar-id"></span></strong><br>
                        <span id="eliminar-info"></span>
                    </div>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion"><i class="fas fa-trash"></i> Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarMantenimiento.js"></script>

</body>
</html>