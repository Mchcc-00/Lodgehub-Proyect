<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Gestión de Habitaciones</title>
    <link rel="stylesheet" href="/LODGEHUB/public/assets/css/dashboardHab.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    
<?php
include $_SERVER['DOCUMENT_ROOT'] . "/lodgehub/app/views/layouts/nav.php";

require_once dirname(__DIR__, 3) . '/config/conexionGlobal.php';

$pdo = conexionDB();

$sql = "SELECT h.*, t.descripcion AS tipo_desc, tm.descripcion AS tamano_desc, e.descripcion AS estado_desc
        FROM tp_habitaciones h
        JOIN td_tipohabitacion t ON h.tipoHabitacion = t.id
        JOIN td_tamano tm ON h.tamano = tm.id
        JOIN td_estado e ON h.estado = e.id
        ORDER BY h.numero ASC";
$stmt = $pdo->query($sql);
$habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
    <?php if (isset($_SESSION['errores']) && count($_SESSION['errores']) > 0): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['errores'] as $error): ?>
                <p><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; unset($_SESSION['errores']); ?>
        </div>
    <?php endif; ?>
    <?php if (isset($_GET['exito']) && $_GET['exito'] == '1'): ?>
        <div class="alert alert-success">
            <p>Habitación creada/eliminada exitosamente.</p>
        </div>
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
            <button class="add-btn" aria-label="Agregar habitación" onclick="window.location.href='formHab.php'">+</button>
        </div>

        <div class="rooms-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 g-4" id="roomsGrid"></div>

        <script>
            window.rooms = <?php
            // Mapear los campos PHP a los nombres esperados por JS
            $jsHabitaciones = array_map(function($hab) {
                $estado = strtolower($hab['estado_desc']);
                if ($estado === 'disponible') {
                    $jsEstado = 'disponible';
                } elseif ($estado === 'reservada') {
                    $jsEstado = 'reservada';
                } elseif ($estado === 'en uso' || $estado === 'en-uso' || $estado === 'ocupada') {
                    $jsEstado = 'en-uso';
                } elseif ($estado === 'mantenimiento') {
                    $jsEstado = 'mantenimiento';
                } else {
                    $jsEstado = 'disponible';
                }
                return [
                    'numero' => $hab['numero'],
                    'precio' => $hab['costo'],
                    'capacidad' => $hab['capacidad'],
                    'tipo' => $hab['tipo_desc'],
                    'tamano' => $hab['tamano_desc'],
                    'estado' => $jsEstado,
                    'informacionAdicional' => isset($hab['informacionAdicional']) ? $hab['informacionAdicional'] : ''
                ];
            }, isset($habitaciones) && is_array($habitaciones) ? $habitaciones : []);
            echo json_encode($jsHabitaciones);
            ?>;
        </script>
        <script src="../../../public/assets/js/scriptHab.js"></script>
        <script src="../../../public/assets/js/debugRooms.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    </div>
</body>
</html>