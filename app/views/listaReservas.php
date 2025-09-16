<?php
require_once 'validarSesion.php';
$currentPage = 'Reservas'; // Para activar el item en el sidebar
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - LodgeHub</title>
    <!-- Dependencias -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesMisColaboradores.css"> <!-- Reutilizamos estilos para la tabla -->
</head>
<body>

    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";

        // VALIDACIÓN: Asegurarse de que un hotel ha sido seleccionado
        $hotelSeleccionado = isset($_SESSION['hotel_id']) && !empty($_SESSION['hotel_id']);
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <?php if (!$hotelSeleccionado): ?>
            <div class="alert alert-danger mt-4" role="alert">
                <h4 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> ¡Acción Requerida!</h4>
                <p>Para poder gestionar las reservas, primero debes <strong>seleccionar un hotel</strong> desde el panel principal (Home).</p>
                <hr>
                <p class="mb-0">Por favor, regresa al <a href="homepage.php" class="alert-link">Home</a> y elige el hotel donde deseas trabajar.</p>
            </div>
        <?php else: ?>
            <div class="header">
                <h1><i class="fas fa-calendar-check"></i> Gestión de Reservas</h1>
                <p>Administra todas las reservas de los huéspedes en el hotel.</p>
            </div>

            <!-- Búsqueda y filtros -->
            <div class="search-section">
                <div class="row align-items-center mb-3">
                    <div class="col-md-4">
                        <div class="input-group">
                            <input type="text" class="form-control" id="buscar-input" placeholder="Buscar por ID, huésped o habitación...">
                            <button class="btn btn-outline-primary" type="button" id="buscar-btn"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                    <div class="col-md-8 text-end">
                        <div class="btn-group me-2">
                            <button class="btn btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-filter"></i> Filtrar por Estado
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="all">Todos los Estados</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="Activa">Activas</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="Pendiente">Pendientes</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="Finalizada">Finalizadas</a></li>
                                <li><a class="dropdown-item filter-option" href="#" data-filter-group="estado" data-filter-value="Cancelada">Canceladas</a></li>
                            </ul>
                        </div>
                        <button class="btn btn-success" id="refresh-btn"><i class="fas fa-sync-alt"></i></button>
                        <a href="crearReservas.php" class="btn btn-primary"><i class="fas fa-plus"></i> Nueva Reserva</a>
                    </div>
                </div>
            </div>

            <!-- Mensajes de alerta -->
            <div id="success-message" class="success-message" style="display: none;">✅ <strong id="success-text"></strong></div>
            <div id="error-message" class="error-message" style="display: none;">❌ <strong id="error-text"></strong></div>

            <!-- Tabla de Reservas -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Huésped</th>
                            <th>Habitación</th>
                            <th>Fecha Inicio</th>
                            <th>Fecha Fin</th>
                            <th>Estado</th>
                            <th>Total</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="tabla-reservas">
                        <!-- Contenido generado por JS -->
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <nav id="paginacion-container" style="display: none;"><ul class="pagination" id="paginacion"></ul></nav>
        <?php endif; // Fin del bloque de validación de hotel ?>
    </div>

    <!-- Modal de edición -->
    <div class="modal fade" id="editarModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-edit"></i> Editar Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar-reserva" novalidate>
                        <input type="hidden" id="edit-id">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="edit-fechainicio" class="form-label">Fecha Inicio</label>
                                <input type="date" class="form-control" id="edit-fechainicio" name="fechainicio">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="edit-fechaFin" class="form-label">Fecha Fin</label>
                                <input type="date" class="form-control" id="edit-fechaFin" name="fechaFin">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-pagoFinal" class="form-label">Pago Final</label>
                            <input type="number" class="form-control" id="edit-pagoFinal" name="pagoFinal" step="0.01">
                        </div>
                        <div class="mb-3">
                            <label for="edit-estado" class="form-label">Estado</label>
                            <select class="form-select" id="edit-estado" name="estado">
                                <option value="Activa">Activa</option>
                                <option value="Pendiente">Pendiente</option>
                                <option value="Finalizado">Finalizado</option>
                                <option value="Cancelada">Cancelada</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="edit-informacionAdicional" class="form-label">Información Adicional</label>
                            <textarea class="form-control" id="edit-informacionAdicional" name="informacionAdicional" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="guardar-edicion-reserva"><i class="fas fa-save"></i> Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de visualización -->
    <div class="modal fade" id="verModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-eye"></i> Detalles de la Reserva</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body" id="detalles-reserva">
                    <!-- Contenido generado por JS -->
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
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle"></i> Confirmar Acción</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>¿Estás seguro de que deseas eliminar/cancelar esta reserva?</p>
                    <div class="alert alert-warning">
                        <strong>ID: <span id="eliminar-id"></span></strong><br>
                        <span id="eliminar-info-reserva"></span>
                    </div>
                    <p class="text-danger">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion-reserva"><i class="fas fa-trash"></i> Confirmar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/listarReservas.js"></script>

</body>
</html>