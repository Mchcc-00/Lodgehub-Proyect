<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Habitaciones..</title>
    <link rel="stylesheet" href="../../../public/assets/css/dashboardHab.css">
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
            <button class="add-btn" aria-label="Agregar habitación" onclick="window.location.href='formHab.php'">+</button>
        </div>

        <div class="rooms-grid" id="roomsGrid">
            <script>
                const rooms = <?php echo json_encode(array_map(function($hab) {
                    // Usar los nombres correctos según la consulta SQL
                    return [
                        'number'    => is_array($hab) ? $hab['numero'] : $hab->numero,
                        'costo'     => is_array($hab) ? $hab['costo'] : $hab->costo,
                        'type'      => is_array($hab) ? $hab['tipo_desc'] : $hab->tipo_desc,
                        'tamano'    => is_array($hab) ? $hab['tamano_desc'] : $hab->tamano_desc,
                        'status'    => strtolower(is_array($hab) ? $hab['estado_desc'] : $hab->estado_desc) === 'en uso' ? 'en-uso' : strtolower(is_array($hab) ? $hab['estado_desc'] : $hab->estado_desc),
                        'capacidad' => is_array($hab) ? $hab['capacidad'] : $hab->capacidad,
                        'info'      => '' // Si no tienes info, puedes dejarlo vacío o eliminar esta línea
                    ];
                }, $habitaciones)); ?>;
            </script>
            <script src="../../../public/assets/js/scriptHab.js"></script>
        </div>


    </div>
</body>
</html>