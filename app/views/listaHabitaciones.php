<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Habitaciones - Sistema de Gestión Hotelera</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- CSS personalizado -->
    <link href="../css/habitaciones.css" rel="stylesheet">

    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitaciones.css">
</head>

<body>
    <?php
    include "layouts/sidebar.php";
    include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-bed"></i> Lista de Habitaciones</h1>
            <p>Gestiona todas las habitaciones del hotel: disponibilidad, precios, mantenimiento y más</p>
        </div>

        <!-- Mensajes -->
        <div id="success-message" class="success-message" style="display: none;">
            <i class="fas fa-check-circle"></i> <span id="success-text"></span>
        </div>

        <div id="error-message" class="error-message" style="display: none;">
            <i class="fas fa-exclamation-triangle"></i> <span id="error-text"></span>
        </div>

        <!-- Sección de Búsqueda y Filtros -->
        <div class="search-section">
            <div class="row g-3">
                <div class="col-md-8">
                    <div class="input-group">
                        <input type="text" id="buscar-input" class="form-control" placeholder="Buscar por número, tipo o descripción...">
                        <button class="btn btn-primary" type="button" id="buscar-btn">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="btn-group w-100">
                        <button class="btn btn-outline-secondary dropdown-toggle filter-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-filter"></i> Todos los estados
                        </button>
                        <ul class="dropdown-menu w-100">
                            <li><a class="dropdown-item filter-option" href="#" data-filter="all">
                                    <i class="fas fa-list"></i> Todos los estados
                                </a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Disponible">
                                    <i class="fas fa-check-circle text-success"></i> Disponible
                                </a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Reservada">
                                    <i class="fas fa-clock text-warning"></i> Reservada
                                </a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Ocupada">
                                    <i class="fas fa-user text-danger"></i> Ocupada
                                </a></li>
                            <li><a class="dropdown-item filter-option" href="#" data-filter="Mantenimiento">
                                    <i class="fas fa-wrench text-secondary"></i> Mantenimiento
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row mt-3">
                <div class="col-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" id="refresh-btn">
                            <i class="fas fa-sync-alt"></i> Actualizar
                        </button>
                        <a href="crearHabitacion.php" class="btn btn-success" id="nueva-habitacion-btn">
                            <i class="fas fa-plus"></i> Nueva Habitación
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Grid de Habitaciones -->
        <div class="row" id="habitaciones-container">
            <!-- Las habitaciones se cargarán aquí dinámicamente -->
        </div>

        <!-- Paginación -->
        <div id="paginacion-container" style="display: none;">
            <nav aria-label="Navegación de páginas">
                <ul class="pagination justify-content-center" id="paginacion">
                    <!-- La paginación se genera dinámicamente -->
                </ul>
            </nav>
        </div>
    </div>

    <!-- Modal para Editar Habitación -->
    <div class="modal fade" id="editarModal" tabindex="-1" aria-labelledby="editarModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editarModalLabel">
                        <i class="fas fa-edit"></i> Editar Habitación
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="form-editar">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-numero" class="form-label">Número</label>
                                    <input type="text" class="form-control" id="edit-numero" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-costo" class="form-label">Costo por Noche</label>
                                    <div class="input-group">
                                        <span class="input-group-text">$</span>
                                        <input type="number" class="form-control" id="edit-costo" min="0" step="0.01">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-capacidad" class="form-label">Capacidad</label>
                                    <input type="number" class="form-control" id="edit-capacidad" min="1" max="20">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-tipoHabitacion" class="form-label">Tipo</label>
                                    <select class="form-control" id="edit-tipoHabitacion">
                                        <!-- Se llenan dinámicamente -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-estado" class="form-label">Estado</label>
                                    <select class="form-control" id="edit-estado">
                                        <option value="Disponible">Disponible</option>
                                        <option value="Reservada">Reservada</option>
                                        <option value="Ocupada">Ocupada</option>
                                        <option value="Mantenimiento">Mantenimiento</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="edit-foto" class="form-label">URL Foto</label>
                                    <input type="url" class="form-control" id="edit-foto">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="edit-descripcion" class="form-label">Descripción</label>
                            <textarea class="form-control" id="edit-descripcion" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" id="guardar-edicion">
                        <i class="fas fa-save"></i> Guardar Cambios
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Confirmar Eliminación -->
    <div class="modal fade" id="eliminarModal" tabindex="-1" aria-labelledby="eliminarModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="eliminarModalLabel">
                        <i class="fas fa-trash"></i> Confirmar Eliminación
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <p class="lead">¿Está seguro de que desea eliminar la habitación <strong id="eliminar-numero"></strong>?</p>
                    <p class="text-muted">Esta acción no se puede deshacer.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-danger" id="confirmar-eliminacion">
                        <i class="fas fa-trash"></i> Eliminar Habitación
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para Mantenimiento -->
    <div class="modal fade" id="mantenimientoModal" tabindex="-1" aria-labelledby="mantenimientoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-warning">
                    <h5 class="modal-title text-dark" id="mantenimientoModalLabel">
                        <i class="fas fa-wrench"></i> Poner en Mantenimiento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>¿Desea poner en mantenimiento la habitación <strong id="mantenimiento-numero"></strong>?</p>
                    <div class="mb-3">
                        <label for="descripcion-mantenimiento" class="form-label">Descripción del Mantenimiento</label>
                        <textarea class="form-control" id="descripcion-mantenimiento" rows="3" placeholder="Describe el tipo de mantenimiento a realizar..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-warning" id="confirmar-mantenimiento">
                        <i class="fas fa-wrench"></i> Confirmar Mantenimiento
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/js/bootstrap.bundle.min.js"></script>
    <script src="../js/habitaciones.js"></script>
    <script src="../js/listarHabitaciones.js"></script>

    <script>
        // Funciones globales para usar desde habitaciones.js

        // Obtener habitación por número (ya definida en habitaciones.js)
        async function obtenerHabitacionPorNumero(numero) {
            try {
                const url = `../controllers/HabitacionesController.php?action=obtenerPorNumero&numero=${encodeURIComponent(numero)}`;
                const response = await fetch(url);
                const result = await response.json();

                if (result.success) {
                    return result.data;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error al obtener habitación:', error);
                throw error;
            }
        }

        // Actualizar habitación (ya definida en habitaciones.js)
        async function actualizarHabitacion(numero, datos) {
            try {
                const formData = new FormData();
                formData.append('numero', numero);

                // Agregar solo los campos que se van a actualizar
                Object.keys(datos).forEach(key => {
                    if (datos[key] !== null && datos[key] !== undefined && datos[key] !== '') {
                        formData.append(key, datos[key]);
                    }
                });

                const response = await fetch('../controllers/HabitacionesController.php?action=actualizar', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    return result;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error al actualizar habitación:', error);
                throw error;
            }
        }

        // Eliminar habitación (ya definida en habitaciones.js)
        async function eliminarHabitacion(numero) {
            try {
                const formData = new FormData();
                formData.append('numero', numero);

                const response = await fetch('../controllers/HabitacionesController.php?action=eliminar', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    return result;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error al eliminar habitación:', error);
                throw error;
            }
        }

        // Poner en mantenimiento (ya definida en habitaciones.js)
        async function ponerEnMantenimiento(numero, descripcion = 'Mantenimiento programado') {
            try {
                const formData = new FormData();
                formData.append('numero', numero);
                formData.append('descripcionMantenimiento', descripcion);

                const response = await fetch('../controllers/HabitacionesController.php?action=ponerMantenimiento', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    return result;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error al poner en mantenimiento:', error);
                throw error;
            }
        }

        // Finalizar mantenimiento (ya definida en habitaciones.js)
        async function finalizarMantenimiento(numero) {
            try {
                const formData = new FormData();
                formData.append('numero', numero);

                const response = await fetch('../controllers/HabitacionesController.php?action=finalizarMantenimiento', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();

                if (result.success) {
                    return result;
                } else {
                    throw new Error(result.message);
                }
            } catch (error) {
                console.error('Error al finalizar mantenimiento:', error);
                throw error;
            }
        }
    </script>
</body>

</html>