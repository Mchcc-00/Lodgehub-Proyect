<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de PQRS - LodgeHub</title>
    <!-- Bootstrap y Font Awesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    
    <!-- Estilos de los layouts -->
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesPqrs.css">
</head>
<body>

    <?php
        require_once 'validarSesion.php';
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        // VALIDACIÓN: Asegurarse de que un hotel ha sido seleccionado
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
        $hotel_id = $_SESSION['hotel_id'] ?? null;
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <!-- Campo oculto para que JS pueda leer el ID del hotel -->
        <input type="hidden" id="hotel-id-context" value="<?php echo htmlspecialchars($hotel_id); ?>">

        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder gestionar las PQRS, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
        <div class="header">
            <h1>Lista de PQRS</h1>
            <p>Gestiona todas las Peticiones, Quejas, Reclamos, Sugerencias y Felicitaciones</p>
        </div>

        <!-- Sección de búsqueda y filtros -->
        <div class="search-section" style="position: relative; z-index: 2;">
            <div class="row align-items-center mb-3">
                <div class="col-md-4">
                    <div class="input-group">
                        <input type="text" class="form-control" id="buscar-input" placeholder="Buscar por ID, documento o descripción...">
                        <button class="btn btn-outline-primary" type="button" id="buscar-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-8 text-end">
                    <div class="btn-group me-2">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            <i class="fas fa-filter"></i> Filtros
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">Todos</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Por Estado</h6></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Pendiente">Pendientes</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Finalizado">Finalizados</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><h6 class="dropdown-header">Por Tipo</h6></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Peticiones">Peticiones</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Quejas">Quejas</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Reclamos">Reclamos</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Sugerencias">Sugerencias</a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Felicitaciones">Felicitaciones</a></li>
                        </ul>
                    </div>
                    <button class="btn btn-success" id="refresh-btn">
                        <i class="fas fa-sync-alt"></i> Actualizar
                    </button>
                    <a href="crearPqrs.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Nueva PQRS
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

        <!-- Tabla de PQRS -->
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha Registro</th>
                        <th>Tipo</th>
                        <th>Descripción</th>
                        <th>Usuario</th>
                        <th>Prioridad</th>
                        <th>Estado</th>
                        <th>Fecha Límite</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="tabla-pqrs">
                    <tr>
                        <td colspan="9" class="loading">
                            <i class="fas fa-spinner fa-spin"></i> Cargando PQRS...
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        <nav aria-label="Paginación de PQRS" id="paginacion-container" style="display: none;">
            <ul class="pagination" id="paginacion">
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
                        <i class="fas fa-edit"></i> Editar PQRS
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar">
                        <input type="hidden" id="edit-id">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-tipo" class="form-label">Tipo <span class="required">*</span></label>
                                    <select class="form-select" id="edit-tipo" name="tipo" required>
                                        <option value="Peticiones">Peticiones</option>
                                        <option value="Quejas">Quejas</option>
                                        <option value="Reclamos">Reclamos</option>
                                        <option value="Sugerencias">Sugerencias</option>
                                        <option value="Felicitaciones">Felicitaciones</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-prioridad" class="form-label">Prioridad <span class="required">*</span></label>
                                    <select class="form-select" id="edit-prioridad" name="prioridad" required>
                                        <option value="Bajo">Bajo</option>
                                        <option value="Alto">Alto</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-categoria" class="form-label">Categoría <span class="required">*</span></label>
                                    <select class="form-select" id="edit-categoria" name="categoria" required>
                                        <option value="Servicio">Servicio</option>
                                        <option value="Habitación">Habitación</option>
                                        <option value="Atención">Atención</option>
                                        <option value="Otro">Otro</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-estado" class="form-label">Estado <span class="required">*</span></label>
                                    <select class="form-select" id="edit-estado" name="estado" required>
                                        <option value="Pendiente">Pendiente</option>
                                        <option value="Finalizado">Finalizado</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit-descripcion" class="form-label">Descripción <span class="required">*</span></label>
                            <textarea class="form-control" id="edit-descripcion" name="descripcion" rows="4" required maxlength="1000"></textarea>
                        </div>

                        <div class="mb-3" id="respuesta-container" style="display: none;">
                            <label for="edit-respuesta" class="form-label">Respuesta</label>
                            <textarea class="form-control" id="edit-respuesta" name="respuesta" rows="4" maxlength="1000"></textarea>
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
                    <h5 class="modal-title">
                        <i class="fas fa-eye"></i> Detalles de PQRS
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-pqrs">
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar esta PQRS?</p>
                    <div class="alert alert-info">
                        <strong id="eliminar-info">Información de PQRS</strong><br>
                        <small>ID: <span id="eliminar-id">-</span></small>
                    </div>
                    <p class="text-danger">
                        <i class="fas fa-warning"></i> 
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarPqrs.js"></script>

</body>
</html>