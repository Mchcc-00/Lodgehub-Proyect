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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 15px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            font-size: 2.5em;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .header p {
            opacity: 0.9;
            font-size: 1.1em;
        }

        .content {
            padding: 30px;
        }

        .actions-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .btn {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(40, 167, 69, 0.3);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            border-left: 4px solid #3498db;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #2c3e50;
        }

        .stat-label {
            color: #7f8c8d;
            margin-top: 5px;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        th {
            background: linear-gradient(135deg, #34495e 0%, #2c3e50 100%);
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
        }

        tr:hover {
            background-color: #f8f9fa;
            transform: scale(1.01);
            transition: all 0.2s ease;
        }

        .estado {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .estado-activa {
            background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
            color: #155724;
        }

        .estado-cancelada {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
        }

        .estado-finalizada {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
        }

        .estado-pendiente {
            background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
            color: #856404;
        }

        .acciones {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .btn-small {
            padding: 6px 12px;
            font-size: 11px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: 600;
            transition: all 0.3s ease;
            text-transform: uppercase;
        }

        .btn-edit {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            color: white;
        }

        .btn-view {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
        }

        .btn-small:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-weight: 500;
        }

        .alert-error {
            background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
            color: #721c24;
            border-left: 4px solid #dc3545;
        }

        .alert-info {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            color: #0c5460;
            border-left: 4px solid #17a2b8;
        }

        .no-data {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .no-data h3 {
            font-size: 1.5em;
            margin-bottom: 15px;
            color: #495057;
        }

        .money {
            color: #28a745;
            font-weight: bold;
        }

        .document-info {
            font-size: 12px;
            color: #6c757d;
            display: block;
        }

        .room-info {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .room-number {
            font-weight: bold;
            color: #2c3e50;
        }

        .room-type {
            font-size: 11px;
            color: #7f8c8d;
            background: #ecf0f1;
            padding: 2px 6px;
            border-radius: 3px;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 10px;
            }
            
            .content {
                padding: 15px;
            }
            
            table {
                font-size: 12px;
            }
            
            .actions-bar {
                flex-direction: column;
                gap: 15px;
            }
            
            .stats {
                grid-template-columns: 1fr;
            }
        }

        .loading {
            text-align: center;
            padding: 40px;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #3498db;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
</head>
<body>
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