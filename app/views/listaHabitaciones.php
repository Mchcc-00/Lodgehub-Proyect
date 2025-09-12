<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Habitaciones - LodgeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet">
    <link href="../../public/assets/css/stylesHabitaciones.css" rel="stylesheet">

</head>

<body>
    <?php
    include "layouts/sidebar.php";
    include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <!-- Envolvemos el contenido en la estructura main/content-wrapper para la animación del sidebar -->
    <main class="main-content">
        <div class="content-wrapper container-fluid"> <!-- Usamos container-fluid para un ancho completo adaptable -->
            <!-- Header -->
            <div class="header">
                <h1><i class="fas fa-bed"></i> Gestión de Habitaciones</h1>
                <p>Administra las habitaciones de tus hoteles de manera eficiente</p>
            </div>

            <!-- Mensajes -->
            <div id="success-message" class="success-message">
                <i class="fas fa-check-circle"></i>
                <span id="success-text"></span>
            </div>

            <div id="error-message" class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <span id="error-text"></span>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="search-section">
                <div class="row align-items-end mb-1">
                    <div class="col-md-2 mb-2 mb-md-0">
                        <label for="filtro-estado" class="form-label">Estado</label>
                        <select id="filtro-estado" class="form-select">
                            <option value="">Todos los estados</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Reservada">Reservada</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2 mb-md-0">
                        <label for="filtro-estado" class="form-label">Tipo de habitación</label>
                        <select id="filtro-estado" class="form-select">
                            <option value="">Todos los tipos</option>
                            <option value="Disponible">Disponible</option>
                            <option value="Reservada">Reservada</option>
                            <option value="Ocupada">Ocupada</option>
                            <option value="Mantenimiento">Mantenimiento</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2 mb-md-0">
                        <label for="filtro-numero" class="form-label">Buscar por número</label>
                        <input type="text" id="filtro-numero" class="form-control md-4" placeholder="Número de habitación">
                    </div>
                    <div class="col-md-5 text-md-end">
                        <button type="button" id="btn-buscar" class="btn btn-primary ms-2">
                            <i class="fas fa-search"></i> Buscar
                        </button>
                        <a href="crearHabitacion.php" class="btn btn-success ms-2">
                            <i class="fas fa-plus"></i> Nueva Habitación
                        </a>
                    </div>
                </div>
            </div>
            <!-- Estadísticas rápidas -->
            <div class="row mb-3">
            <div class="col-md-3">
                <div class="card bg-primary text-white">
                    <div class="card-body text-center">
                        <h4 id="total-colaboradores">0</h4>
                        <p class="mb-0">Total</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-info text-white">
                    <div class="card-body text-center">
                        <h4 id="total-colaboradores-rol">0</h4>
                        <p class="mb-0">Colaboradores</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <h4 id="total-usuarios">0</h4>
                        <p class="mb-0">Usuarios</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card bg-warning text-white">
                    <div class="card-body text-center">
                        <h4 id="pendientes-password">0</h4>
                        <p class="mb-0">Cambio Contraseña</p>
                    </div>
                </div>
            </div>
        </div>


            <!-- Loading -->
            <div id="loading" class="text-center py-5" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
                <p class="mt-2">Cargando habitaciones...</p>
            </div>

            <!-- Grid de habitaciones -->
            <div id="habitaciones-grid" class="habitaciones-grid">
                <?php if (empty($habitaciones)): ?>
                    <div class="no-habitaciones">
                        <i class="fas fa-bed fa-5x custom-icon"></i>
                        <h3 class="mt-3 text-muted">No hay habitaciones</h3>
                        <p class="text-muted">Comienza creando tu primera habitación</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($habitaciones as $habitacion): ?>
                        <div class="habitacion-card" data-id="<?php echo $habitacion['id']; ?>">
                            <div class="habitacion-image">
                                <?php if (!empty($habitacion['foto'])): ?>
                                    <img src="<?php echo htmlspecialchars($habitacion['foto']); ?>"
                                        alt="Habitación <?php echo htmlspecialchars($habitacion['numero']); ?>">
                                <?php else: ?>
                                    <div class="no-image">
                                        <i class="fas fa-bed"></i>
                                        <span>Sin imagen</span>
                                    </div>
                                <?php endif; ?>

                                <?php
                                // Lógica para asignar colores de Bootstrap según el estado
                                $estado = $habitacion['estado'];
                                $badge_class = '';
                                switch ($estado) {
                                    case 'Disponible':
                                        $badge_class = 'bg-success';
                                        break;
                                    case 'Reservada':
                                        $badge_class = 'bg-info';
                                        break;
                                    case 'Ocupada':
                                        $badge_class = 'bg-warning text-dark';
                                        break;
                                    case 'Mantenimiento':
                                        $badge_class = 'bg-danger';
                                        break;
                                    default:
                                        $badge_class = 'bg-secondary';
                                }
                                ?>
                                <div class="habitacion-estado">
                                    <span class="badge rounded-pill fs-6 <?php echo $badge_class; ?>"><?php echo htmlspecialchars($estado); ?></span>
                                </div>
                            </div>

                            <div class="habitacion-content">
                                <div class="habitacion-header">
                                    <h3 class="habitacion-numero">
                                        <i class="fas fa-door-open"></i>
                                        Habitación <?php echo htmlspecialchars($habitacion['numero']); ?>
                                    </h3>
                                    <span class="habitacion-hotel">
                                        <?php echo htmlspecialchars($habitacion['hotel_nombre']); ?>
                                    </span>
                                </div>

                                <div class="habitacion-info">
                                    <div class="info-item">
                                        <i class="fas fa-tag"></i>
                                        <span><?php echo htmlspecialchars($habitacion['tipo_descripcion']); ?></span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-users"></i>
                                        <span><?php echo $habitacion['capacidad']; ?> personas</span>
                                    </div>
                                    <div class="info-item">
                                        <i class="fas fa-dollar-sign"></i>
                                        <span>$<?php echo number_format($habitacion['costo'], 0, ',', '.'); ?>/noche</span>
                                    </div>
                                </div>

                                <?php if (!empty($habitacion['descripcion'])): ?>
                                    <div class="habitacion-descripcion">
                                        <p><?php echo htmlspecialchars(substr($habitacion['descripcion'], 0, 100)); ?>
                                            <?php echo strlen($habitacion['descripcion']) > 100 ? '...' : ''; ?></p>
                                    </div>
                                <?php endif; ?>

                                <?php if ($habitacion['estado'] == 'Mantenimiento' && !empty($habitacion['descripcionMantenimiento'])): ?>
                                    <div class="habitacion-mantenimiento">
                                        <i class="fas fa-tools"></i>
                                        <small><?php echo htmlspecialchars($habitacion['descripcionMantenimiento']); ?></small>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <div class="habitacion-actions">
                                <button type="button" class="btn btn-info btn-sm" onclick="verDetalles(<?php echo $habitacion['id']; ?>)">
                                    <i class="fas fa-eye"></i>
                                </button>
                                <a href="?action=editar&id=<?php echo $habitacion['id']; ?>" class="btn btn-warning btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button type="button" class="btn btn-danger btn-sm" onclick="eliminarHabitacion(<?php echo $habitacion['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Modal de detalles -->
            <div class="modal fade" id="modalDetalles" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-bed"></i> Detalles de la Habitación
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body" id="detalles-content">
                            <!-- Contenido cargado dinámicamente -->
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal de confirmación eliminar -->
            <div class="modal fade" id="modalEliminar" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="fas fa-exclamation-triangle text-danger"></i>
                                Confirmar Eliminación
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <p>¿Estás seguro de que deseas eliminar esta habitación?</p>
                            <p class="text-danger mb-0">
                                <strong>Esta acción no se puede deshacer.</strong>
                            </p>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="button" id="btn-confirmar-eliminar" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Eliminar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Scripts -->
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
            <script src="../../public/assets/js/habitaciones.js"></script>
</body>

</html>