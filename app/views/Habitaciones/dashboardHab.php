<?php
if (!isset($habitaciones)) {
    header('Location: ../../controllers/habitacionController.php?accion=listar');
    exit;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="/LODGEHUB/public/assets/css/dashboardHab.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Sistema de Gestión de Habitaciones</title>

</head>
<body>

 <?php include $_SERVER['DOCUMENT_ROOT'] . "/lodgehub/app/views/layouts/nav.php"; ?>


    <?php if (isset($_SESSION['errores']) && count($_SESSION['errores']) > 0): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['errores'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; unset($_SESSION['errores']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['exito'])): ?>
        <div class="alert alert-success" id="successMessage">
            <p><?php echo htmlspecialchars($_SESSION['exito']); ?></p>
            <?php unset($_SESSION['exito']); ?>
        </div>
        <script>
        setTimeout(function() {
            var msg = document.getElementById('successMessage');
            if (msg) msg.style.display = 'none';
        }, 3000);
        </script>
    <?php endif; ?>
    <div class="container py-4">
        <div class="filters" style="display:flex;align-items:center;gap:12px;margin-bottom:32px;">
            <div class="filter-icon" style="margin-right:10px;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polygon points="22,3 2,3 10,12.46 10,19 14,21 14,12.46 22,3"></polygon>
                </svg>
            </div>
            <button class="filter-btn" data-filter="estado">Estado <span class="dropdown-arrow">▼</span></button>
            <button class="filter-btn" data-filter="tamano">Tamaño <span class="dropdown-arrow">▼</span></button>
            <button class="filter-btn" data-filter="tipo">Tipo <span class="dropdown-arrow">▼</span></button>
            <button class="filter-btn" data-filter="precio">Precio <span class="dropdown-arrow">▼</span></button>
            <div style="flex:1;"></div>
            <button class="add-btn custom-add-btn" aria-label="Agregar habitación" onclick="window.location.href='/LODGEHUB/app/views/Habitaciones/formHab.php'">
                <img src="/LODGEHUB/public/img/BotonAgregar.png" alt="Agregar" style="width:38px;height:38px;display:block;margin:auto;pointer-events:none;" />
            </button>
        </div>

        <div class="rooms-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="roomsGrid"></div>

        <script>
        window.rooms = <?php
        $jsHabitaciones = array_map(function($hab) {
            $estado = strtolower($hab['estado_desc'] ?? $hab['estado'] ?? '');
            if ($estado === 'disponible' || $estado === '1') {
                $jsEstado = 'disponible';
            } elseif ($estado === 'reservada' || $estado === '2') {
                $jsEstado = 'reservada';
            } elseif ($estado === 'en uso' || $estado === 'en-uso' || $estado === 'ocupada' || $estado === '3') {
                $jsEstado = 'en-uso';
            } elseif ($estado === 'mantenimiento') {
                $jsEstado = 'mantenimiento';
            } else {
                $jsEstado = 'disponible';
            }
            // Traducir tipo y tamaño si vienen como ID
            $tipo = isset($hab['tipo_desc']) ? $hab['tipo_desc'] : (isset($hab['tipoHabitacion']) ? $hab['tipoHabitacion'] : '');
            if (is_numeric($tipo)) {
                $tipo = ($tipo == 1 ? 'Individual' : ($tipo == 2 ? 'Doble' : ($tipo == 3 ? 'Suite' : $tipo)));
            }
            $tamano = isset($hab['tamano_desc']) ? $hab['tamano_desc'] : (isset($hab['tamano']) ? $hab['tamano'] : '');
            if (is_numeric($tamano)) {
                $tamano = ($tamano == 1 ? 'Pequeña' : ($tamano == 2 ? 'Mediana' : ($tamano == 3 ? 'Grande' : $tamano)));
            }
            return [
                'id' => isset($hab['numero']) ? $hab['numero'] : '',
                'number' => isset($hab['numero']) ? $hab['numero'] : '',
                'precio' => isset($hab['costo']) ? intval($hab['costo']) : '',
                'capacidad' => isset($hab['capacidad']) ? $hab['capacidad'] : '',
                'type' => $tipo,
                'tamano' => $tamano,
                'status' => $jsEstado,
                'info' => isset($hab['informacionAdicional']) ? $hab['informacionAdicional'] : (isset($hab['descripcion']) ? $hab['descripcion'] : '')
            ];
        }, isset($habitaciones) && is_array($habitaciones) ? $habitaciones : []);
        echo json_encode($jsHabitaciones);
        ?>;
        </script>
        <script src="/LODGEHUB/public/assets/js/scriptHab.js"></script>
        <script src="/LODGEHUB/public/assets/js/debugRooms.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
        
    </div>
</body>
</html>