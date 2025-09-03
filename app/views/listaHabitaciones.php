<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Habitaciones</title>
    <link rel="stylesheet" href="../../public/assets/css/stylesHabitacion.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNav.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>
<body>
    <?php
        include 'layouts/sidebar.php'; 
        include 'layouts/navbar.php'; 
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">      
            <!-- Contenido Principal -->
            <main class="content">
                <div class="page-header">
                    <h1><i class="fas fa-bed"></i> Gestión de Habitaciones</h1>
                    <div class="page-actions">
                        <a href="crearHabitacion.php?controller=room&action=create" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Nueva Habitación
                        </a>
                    </div>
                </div>

                <!-- Mensajes de éxito/error -->
                <?php if (isset($_SESSION['success'])): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                    </div>
                <?php endif; ?>

                <!-- Filtros -->
                <div class="filters-container">
                    <div class="filters">
                        <select id="statusFilter" class="filter-select">
                            <option value="">Todos los estados</option>
                            <option value="Disponible" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Disponible') ? 'selected' : ''; ?>>Disponible</option>
                            <option value="Reservada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Reservada') ? 'selected' : ''; ?>>Reservada</option>
                            <option value="Ocupada" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Ocupada') ? 'selected' : ''; ?>>Ocupada</option>
                            <option value="Mantenimiento" <?php echo (isset($_GET['estado']) && $_GET['estado'] == 'Mantenimiento') ? 'selected' : ''; ?>>Mantenimiento</option>
                        </select>
                        
                        <div class="search-container">
                            <input type="text" id="searchInput" placeholder="Buscar habitación..." class="search-input">
                            <i class="fas fa-search search-icon"></i>
                        </div>
                    </div>
                </div>

                <!-- Grid de Habitaciones -->
                <div class="rooms-grid" id="roomsGrid">
                    <?php if (!empty($rooms)): ?>
                        <?php foreach ($rooms as $room): ?>
                            <div class="room-card" data-estado="<?php echo $room['estado']; ?>" data-numero="<?php echo $room['numero']; ?>" data-tipo="<?php echo $room['tipoHabitacion']; ?>">
                                <div class="room-image">
                                    <?php if ($room['foto'] && file_exists($room['foto'])): ?>
                                        <img src="<?php echo $room['foto']; ?>" alt="Habitación <?php echo $room['numero']; ?>" loading="lazy">
                                    <?php else: ?>
                                        <div class="no-image">
                                            <i class="fas fa-bed"></i>
                                            <span>Sin imagen</span>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="room-status status-<?php echo strtolower($room['estado']); ?>">
                                        <i class="fas fa-circle"></i>
                                        <?php echo $room['estado']; ?>
                                    </div>
                                </div>
                                
                                <div class="room-info">
                                    <div class="room-header">
                                        <h3 class="room-number">Habitación <?php echo $room['numero']; ?></h3>
                                        <span class="room-type"><?php echo $room['tipoDescripcion']; ?></span>
                                    </div>
                                    
                                    <div class="room-details">
                                        <div class="detail-item">
                                            <i class="fas fa-dollar-sign"></i>
                                            <span>$<?php echo number_format($room['costo'], 2); ?></span>
                                        </div>
                                        <div class="detail-item">
                                            <i class="fas fa-users"></i>
                                            <span><?php echo $room['capacidad']; ?> personas</span>
                                        </div>
                                    </div>
                                    
                                    <?php if ($room['descripcion']): ?>
                                        <p class="room-description"><?php echo htmlspecialchars(substr($room['descripcion'], 0, 100)) . (strlen($room['descripcion']) > 100 ? '...' : ''); ?></p>
                                    <?php endif; ?>
                                    
                                    <?php if ($room['estado'] === 'Mantenimiento' && $room['descripcionMantenimiento']): ?>
                                        <div class="maintenance-info">
                                            <i class="fas fa-tools"></i>
                                            <small><?php echo htmlspecialchars($room['descripcionMantenimiento']); ?></small>
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div class="room-actions">
                                        <a href="index.php?controller=room&action=edit&numero=<?php echo $room['numero']; ?>" 
                                           class="btn btn-sm btn-secondary" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger delete-room" 
                                                data-numero="<?php echo $room['numero']; ?>" 
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="empty-state">
                            <i class="fas fa-bed"></i>
                            <h3>No hay habitaciones registradas</h3>
                            <p>Comienza agregando tu primera habitación</p>
                            <a href="crearHabitacion.php?controller=room&action=create" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Crear Primera Habitación
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal de confirmación de eliminación -->
    <div class="modal" id="deleteModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-exclamation-triangle"></i> Confirmar Eliminación</h3>
                <button class="modal-close" id="closeModal">&times;</button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar la habitación <strong id="roomNumberToDelete"></strong>?</p>
                <p class="text-danger">Esta acción no se puede deshacer.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelDelete">Cancelar</button>
                <form method="POST" action="index.php?controller=room&action=delete" style="display: inline;">
                    <input type="hidden" id="numeroToDelete" name="numero">
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Eliminar
                    </button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/assets/js/habitaciones.js"></script>
</body>
</html>