<?php
require_once('../../config/conexionGlobal.php');

// Inicializar variables
$reservas = [];
$mensaje = '';
$error = '';

try {
    // Crear conexión PDO
    $pdo = conexionDB();
    
    if (!$pdo) {
        throw new Exception("Error: No se pudo conectar a la base de datos");
    }
    
    // Consulta SQL optimizada para obtener todas las reservas
    $sql = "SELECT 
                r.id,
                r.pagoFinal,
                r.fechainicio,
                r.fechaFin,
                r.cantidadAdultos,
                r.cantidadNinos,
                r.cantidadDiscapacitados,
                r.motivoReserva,
                r.metodoPago,
                r.informacionAdicional,
                r.estado,
                r.fechaRegistro,
                h.nombre AS hotel_nombre,
                hab.numero AS habitacion_numero,
                hab.tipo AS habitacion_tipo,
                CONCAT(u.nombre, ' ', u.apellido) AS usuario_completo,
                CONCAT(hue.nombre, ' ', hue.apellido) AS huesped_completo,
                u.numDocumento AS usuario_documento,
                hue.numDocumento AS huesped_documento
            FROM tp_reservas r
            INNER JOIN tp_hotel h ON r.id_hotel = h.id
            INNER JOIN tp_habitaciones hab ON r.id_habitacion = hab.id
            INNER JOIN tp_usuarios u ON r.us_numDocumento = u.numDocumento
            INNER JOIN tp_huespedes hue ON r.hue_numDocumento = hue.numDocumento
            ORDER BY r.fechaRegistro DESC";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $reservas = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (empty($reservas)) {
        $mensaje = "No hay reservas registradas en el sistema.";
    }
    
} catch (PDOException $e) {
    $error = "Error en la consulta de base de datos: " . $e->getMessage();
} catch (Exception $e) {
    $error = $e->getMessage();
}

// Función para formatear el estado con colores
function getEstadoClass($estado) {
    $estados = [
        'Activa' => 'activa',
        'Cancelada' => 'cancelada',
        'Finalizada' => 'finalizada',
        'Pendiente' => 'pendiente'
    ];
    return $estados[$estado] ?? 'pendiente';
}

// Función para formatear fechas
function formatearFecha($fecha, $incluirHora = false) {
    if ($incluirHora) {
        return date('d/m/Y H:i', strtotime($fecha));
    }
    return date('d/m/Y', strtotime($fecha));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Reservas - LodgeHub</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link href="../../public/assets/css/stylesNav.css" rel="stylesheet"> 
    <link href="../../public/assets/css/stylesReservas.css" rel="stylesheet"> 
    
</head>
<body>
    <?php
        include "layouts/sidebar.php";
        include "layouts/navbar.php";
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="container">
        <div class="header">
            <h1>📋 Gestión de Reservas</h1>
            <p>Panel de administración de reservas del sistema LodgeHub</p>
        </div>

        <div class="content">
            <!-- Mostrar errores -->
            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <strong>❌ Error:</strong> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <!-- Barra de acciones -->
            <div class="actions-bar">
                <div>
                    <a href="crearReservas.php" class="btn">
                        ➕ Nueva Reserva
                    </a>
                    <a href="reporteReservas.php" class="btn btn-secondary">
                        📊 Generar Reporte
                    </a>
                </div>
                <div>
                    <span>Total de registros: <strong><?php echo count($reservas); ?></strong></span>
                </div>
            </div>

            <!-- Estadísticas rápidas -->
            <?php if (!empty($reservas)): ?>
                <?php
                $activas = array_filter($reservas, fn($r) => $r['estado'] === 'Activa');
                $pendientes = array_filter($reservas, fn($r) => $r['estado'] === 'Pendiente');
                $finalizadas = array_filter($reservas, fn($r) => $r['estado'] === 'Finalizada');
                $canceladas = array_filter($reservas, fn($r) => $r['estado'] === 'Cancelada');
                ?>
                
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($activas); ?></div>
                        <div class="stat-label">Activas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($pendientes); ?></div>
                        <div class="stat-label">Pendientes</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($finalizadas); ?></div>
                        <div class="stat-label">Finalizadas</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo count($canceladas); ?></div>
                        <div class="stat-label">Canceladas</div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Mensaje informativo -->
            <?php if (!empty($mensaje) && empty($error)): ?>
                <div class="alert alert-info">
                    <strong>ℹ️ Información:</strong> <?php echo htmlspecialchars($mensaje); ?>
                </div>
            <?php endif; ?>

            <!-- Tabla de reservas -->
            <?php if (!empty($reservas)): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Hotel</th>
                                <th>Habitación</th>
                                <th>Usuario</th>
                                <th>Huésped</th>
                                <th>Fechas</th>
                                <th>Ocupantes</th>
                                <th>Motivo</th>
                                <th>Pago</th>
                                <th>Estado</th>
                                <th>Registro</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo htmlspecialchars($reserva['id']); ?></strong>
                                    </td>
                                    <td><?php echo htmlspecialchars($reserva['hotel_nombre']); ?></td>
                                    <td>
                                        <div class="room-info">
                                            <span class="room-number">
                                                Hab. <?php echo htmlspecialchars($reserva['habitacion_numero']); ?>
                                            </span>
                                            <span class="room-type">
                                                <?php echo htmlspecialchars($reserva['habitacion_tipo']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($reserva['usuario_completo']); ?>
                                        <span class="document-info">
                                            Doc: <?php echo htmlspecialchars($reserva['usuario_documento']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo htmlspecialchars($reserva['huesped_completo']); ?>
                                        <span class="document-info">
                                            Doc: <?php echo htmlspecialchars($reserva['huesped_documento']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>Desde:</strong> <?php echo formatearFecha($reserva['fechainicio']); ?><br>
                                        <strong>Hasta:</strong> <?php echo formatearFecha($reserva['fechaFin']); ?>
                                    </td>
                                    <td>
                                        👥 <?php echo ($reserva['cantidadAdultos'] ?? 0); ?> adultos<br>
                                        👶 <?php echo ($reserva['cantidadNinos'] ?? 0); ?> niños<br>
                                        ♿ <?php echo ($reserva['cantidadDiscapacitados'] ?? 0); ?> disc.
                                    </td>
                                    <td><?php echo htmlspecialchars($reserva['motivoReserva']); ?></td>
                                    <td>
                                        <span class="money">
                                            $<?php echo number_format($reserva['pagoFinal'], 0, ',', '.'); ?>
                                        </span><br>
                                        <small><?php echo htmlspecialchars($reserva['metodoPago']); ?></small>
                                    </td>
                                    <td>
                                        <span class="estado estado-<?php echo getEstadoClass($reserva['estado']); ?>">
                                            <?php echo htmlspecialchars($reserva['estado']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php echo formatearFecha($reserva['fechaRegistro'], true); ?>
                                    </td>
                                    <td class="acciones">
                                        <a href="verReserva.php?id=<?php echo $reserva['id']; ?>" 
                                           class="btn-small btn-view" title="Ver detalles">
                                            👁️
                                        </a>
                                        <a href="editarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                           class="btn-small btn-edit" title="Editar">
                                            ✏️
                                        </a>
                                        <a href="eliminarReserva.php?id=<?php echo $reserva['id']; ?>" 
                                           class="btn-small btn-delete" title="Eliminar"
                                           onclick="return confirm('⚠️ ¿Estás seguro de eliminar la reserva #<?php echo $reserva['id']; ?>?\n\nEsta acción no se puede deshacer.')">
                                            🗑️
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="no-data">
                    <h3>📋 No hay reservas registradas</h3>
                    <p>Comienza creando tu primera reserva haciendo clic en el botón "Nueva Reserva".</p>
                    <br>
                    <a href="crearReservas.php" class="btn">➕ Crear Primera Reserva</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Confirmación mejorada para eliminar
        function confirmarEliminacion(id, hotel, habitacion) {
            const mensaje = `⚠️ CONFIRMAR ELIMINACIÓN\n\n` +
                          `Reserva: #${id}\n` +
                          `Hotel: ${hotel}\n` +
                          `Habitación: ${habitacion}\n\n` +
                          `¿Estás seguro de que deseas eliminar esta reserva?\n` +
                          `Esta acción no se puede deshacer.`;
            
            return confirm(mensaje);
        }

        // Auto-actualizar cada 5 minutos
        setInterval(function() {
            location.reload();
        }, 300000);

        // Mostrar mensaje de carga al hacer clic en enlaces
        document.addEventListener('DOMContentLoaded', function() {
            const links = document.querySelectorAll('a[href*=".php"]');
            links.forEach(link => {
                link.addEventListener('click', function() {
                    if (!this.href.includes('eliminar')) {
                        document.body.style.opacity = '0.7';
                        document.body.style.pointerEvents = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>