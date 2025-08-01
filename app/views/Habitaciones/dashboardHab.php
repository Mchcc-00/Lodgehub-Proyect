<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Habitaciones..</title>
    <link rel="stylesheet" href="../public/css/dashboardHab.css">
</head>
<body>
    <div class="container">
        <div class="filters">
            <div class="filter-icon">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3"></polygon>
                </svg>
            </div>
            <button class="filter-btn" data-filter="estado">
                Estado <span class="dropdown-arrow">▼</span>
            </button>
            <button class="filter-btn" data-filter="tamano">
                Tamaño <span class="dropdown-arrow">▼</span>
            </button>
            <button class="filter-btn" data-filter="tipo">
                Tipo <span class="dropdown-arrow">▼</span>
            </button>
            <button class="filter-btn" data-filter="precio">
                Precio <span class="dropdown-arrow">▼</span>
            </button>
            <button class="add-btn" aria-label="Agregar habitación" onclick="window.location.href='../views/filtroHab.php'">+</button>
        </div>

        <div class="rooms-grid" id="roomsGrid">
            <script>
                const rooms = <?php echo json_encode(array_map(function($hab) {
                    $estado = strtolower(is_array($hab) ? $hab['estado'] : $hab->estado);
                    if ($estado == 'en uso') $estado = 'en-uso';
                    return [
                        'id' => is_array($hab) ? $hab['id'] : $hab->id,
                        'number' => is_array($hab) ? $hab['numero'] : $hab->numero,
                        'type' => is_array($hab) ? $hab['tipo'] : $hab->tipo,
                        'status' => $estado,
                        'tamano' => is_array($hab) ? $hab['tamano'] : $hab->tamano,
                        'precio' => is_array($hab) ? $hab['precio'] : $hab->precio,
                        'capacidad' => is_array($hab) ? $hab['capacidad'] : $hab->capacidad,
                        'info' => is_array($hab) ? $hab['info'] : $hab->info
                    ];
                }, $habitaciones)); ?>;
            </script>
            <script src="../public/assets/js/scriptHab.js"></script>
        </div>


    </div>
</body>
</html>