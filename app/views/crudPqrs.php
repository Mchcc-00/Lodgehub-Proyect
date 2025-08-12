<?php
require_once '../../config/conexionGlobal.php';

// =============================================
// FUNCIONES DEL MODELO (CRUD)
// =============================================

function obtenerTodosPQRS($filtros = []) {
    $db = conexionDB();
    if (!$db) return [];
    
    try {
        $sql = "SELECT p.*, u.nombre as nombreUsuario, u.email as emailUsuario 
                FROM tp_pqrs p 
                LEFT JOIN tp_usuarios u ON p.numdocumento = u.numDocumento 
                WHERE 1=1";
        
        $params = [];
        
        // Aplicar filtros si existen
        if (!empty($filtros['tipo'])) {
            $sql .= " AND p.tipo = ?";
            $params[] = $filtros['tipo'];
        }
        
        if (!empty($filtros['estado'])) {
            $sql .= " AND p.estado = ?";
            $params[] = $filtros['estado'];
        }
        
        if (!empty($filtros['prioridad'])) {
            $sql .= " AND p.prioridad = ?";
            $params[] = $filtros['prioridad'];
        }
        
        if (!empty($filtros['categoria'])) {
            $sql .= " AND p.categoria = ?";
            $params[] = $filtros['categoria'];
        }
        
        if (!empty($filtros['numdocumento'])) {
            $sql .= " AND p.numdocumento LIKE ?";
            $params[] = '%' . $filtros['numdocumento'] . '%';
        }
        
        $sql .= " ORDER BY p.fechaRegistro DESC";
        
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener PQRS: " . $e->getMessage());
        return [];
    }
}

function obtenerPQRSPorId($id) {
    $db = conexionDB();
    if (!$db) return false;
    
    try {
        $stmt = $db->prepare("SELECT p.*, u.nombre as nombreUsuario, u.email as emailUsuario 
                             FROM tp_pqrs p 
                             LEFT JOIN tp_usuarios u ON p.numdocumento = u.numDocumento 
                             WHERE p.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener PQRS: " . $e->getMessage());
        return false;
    }
}

function crearPQRS($datos) {
    $db = conexionDB();
    if (!$db) return false;
    
    try {
        $stmt = $db->prepare("INSERT INTO tp_pqrs (tipo, descripcion, numdocumento, prioridad, categoria, estado) 
                             VALUES (?, ?, ?, ?, ?, ?)");
        
        $resultado = $stmt->execute([
            $datos['tipo'],
            $datos['descripcion'],
            $datos['numdocumento'],
            $datos['prioridad'],
            $datos['categoria'],
            $datos['estado'] ?? 'Pendiente'
        ]);
        
        return $resultado ? $db->lastInsertId() : false;
    } catch (PDOException $e) {
        error_log("Error al crear PQRS: " . $e->getMessage());
        return false;
    }
}

function actualizarPQRS($id, $datos) {
    $db = conexionDB();
    if (!$db) return false;
    
    try {
        $sql = "UPDATE tp_pqrs SET 
                tipo = ?, 
                descripcion = ?, 
                numdocumento = ?, 
                prioridad = ?, 
                categoria = ?, 
                estado = ?, 
                respuesta = ?";
        
        $params = [
            $datos['tipo'],
            $datos['descripcion'],
            $datos['numdocumento'],
            $datos['prioridad'],
            $datos['categoria'],
            $datos['estado'],
            $datos['respuesta'] ?? null
        ];
        
        // Si el estado cambia a 'Finalizado', actualizar fechaFinalizacion
        if ($datos['estado'] === 'Finalizado') {
            $sql .= ", fechaFinalizacion = NOW()";
        } elseif ($datos['estado'] === 'Pendiente') {
            $sql .= ", fechaFinalizacion = NULL";
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $id;
        
        $stmt = $db->prepare($sql);
        return $stmt->execute($params);
    } catch (PDOException $e) {
        error_log("Error al actualizar PQRS: " . $e->getMessage());
        return false;
    }
}

function eliminarPQRS($id) {
    $db = conexionDB();
    if (!$db) return false;
    
    try {
        $stmt = $db->prepare("DELETE FROM tp_pqrs WHERE id = ?");
        return $stmt->execute([$id]);
    } catch (PDOException $e) {
        error_log("Error al eliminar PQRS: " . $e->getMessage());
        return false;
    }
}

function obtenerEstadisticas() {
    $db = conexionDB();
    if (!$db) return [];
    
    try {
        $stats = [];
        
        // Total de PQRS
        $stmt = $db->prepare("SELECT COUNT(*) as total FROM tp_pqrs");
        $stmt->execute();
        $stats['total'] = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        
        // Por estado
        $stmt = $db->prepare("SELECT estado, COUNT(*) as cantidad FROM tp_pqrs GROUP BY estado");
        $stmt->execute();
        $stats['por_estado'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Por tipo
        $stmt = $db->prepare("SELECT tipo, COUNT(*) as cantidad FROM tp_pqrs GROUP BY tipo");
        $stmt->execute();
        $stats['por_tipo'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // PQRS vencidos
        $stmt = $db->prepare("SELECT COUNT(*) as vencidos FROM tp_pqrs WHERE fechaLimite < CURDATE() AND estado = 'Pendiente'");
        $stmt->execute();
        $stats['vencidos'] = $stmt->fetch(PDO::FETCH_ASSOC)['vencidos'];
        
        return $stats;
    } catch (PDOException $e) {
        error_log("Error al obtener estadísticas: " . $e->getMessage());
        return [];
    }
}

function validarDatos($datos) {
    $errores = [];
    
    if (empty($datos['tipo']) || !in_array($datos['tipo'], ['Peticiones', 'Quejas', 'Reclamos', 'Sugerencias', 'Felicitaciones'])) {
        $errores[] = "El tipo es requerido y debe ser válido";
    }
    
    if (empty($datos['descripcion']) || strlen(trim($datos['descripcion'])) < 10) {
        $errores[] = "La descripción es requerida y debe tener al menos 10 caracteres";
    }
    
    if (empty($datos['numdocumento']) || !preg_match('/^[0-9]{7,15}$/', $datos['numdocumento'])) {
        $errores[] = "El número de documento debe tener entre 7 y 15 dígitos";
    }
    
    if (empty($datos['prioridad']) || !in_array($datos['prioridad'], ['Bajo', 'Alto'])) {
        $errores[] = "La prioridad es requerida y debe ser válida";
    }
    
    if (empty($datos['categoria']) || !in_array($datos['categoria'], ['Servicio', 'Habitación', 'Atención', 'Otro'])) {
        $errores[] = "La categoría es requerida y debe ser válida";
    }
    
    if (empty($datos['estado']) || !in_array($datos['estado'], ['Pendiente', 'Finalizado'])) {
        $errores[] = "El estado es requerido y debe ser válido";
    }
    
    // Si está finalizado, debe tener respuesta
    if ($datos['estado'] === 'Finalizado' && empty(trim($datos['respuesta'] ?? ''))) {
        $errores[] = "La respuesta es requerida cuando el estado es 'Finalizado'";
    }
    
    return $errores;
}

// =============================================
// PROCESAMIENTO DE ACCIONES
// =============================================

$accion = $_GET['accion'] ?? 'listar';
$id = $_GET['id'] ?? null;
$mensaje = '';
$tipo_mensaje = '';
$errores = [];

// Procesar formularios POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    switch ($accion) {
        case 'crear':
            $errores = validarDatos($_POST);
            if (empty($errores)) {
                if (crearPQRS($_POST)) {
                    header("Location: ?mensaje=PQRS creado exitosamente&tipo=success");
                    exit;
                } else {
                    $errores[] = "Error al crear el PQRS";
                }
            }
            break;
            
        case 'editar':
            $errores = validarDatos($_POST);
            if (empty($errores)) {
                if (actualizarPQRS($id, $_POST)) {
                    header("Location: ?mensaje=PQRS actualizado exitosamente&tipo=success");
                    exit;
                } else {
                    $errores[] = "Error al actualizar el PQRS";
                }
            }
            break;
            
        case 'eliminar':
            if (eliminarPQRS($id)) {
                header("Location: ?mensaje=PQRS eliminado exitosamente&tipo=success");
                exit;
            } else {
                header("Location: ?mensaje=Error al eliminar el PQRS&tipo=error");
                exit;
            }
            break;
    }
}

// Obtener datos según la acción
$pqrs_lista = [];
$pqrs = null;
$estadisticas = [];

if ($accion === 'listar' || $accion === '') {
    $filtros = array_filter($_GET, function($key) {
        return in_array($key, ['tipo', 'estado', 'prioridad', 'categoria', 'numdocumento']);
    }, ARRAY_FILTER_USE_KEY);
    
    $pqrs_lista = obtenerTodosPQRS($filtros);
    $estadisticas = obtenerEstadisticas();
} elseif ($accion === 'editar' || $accion === 'ver') {
    $pqrs = obtenerPQRSPorId($id);
    if (!$pqrs) {
        header("Location: ?mensaje=PQRS no encontrado&tipo=error");
        exit;
    }
}

// Mensajes de la URL
if (isset($_GET['mensaje'])) {
    $mensaje = $_GET['mensaje'];
    $tipo_mensaje = $_GET['tipo'] ?? 'info';
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD PQRS - LodgeHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .mensaje {
            padding: 15px 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            font-weight: 500;
            border-left: 4px solid;
        }

        .mensaje.success {
            background-color: #d4edda;
            color: #155724;
            border-color: #28a745;
        }

        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border-color: #dc3545;
        }

        .mensaje.info {
            background-color: #d1ecf1;
            color: #0c5460;
            border-color: #17a2b8;
        }

        .nav-tabs {
            display: flex;
            background: white;
            border-radius: 10px;
            padding: 5px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }

        .nav-tab {
            flex: 1;
            text-align: center;
            padding: 12px 20px;
            text-decoration: none;
            color: #666;
            border-radius: 8px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-tab.active {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .nav-tab:hover {
            background-color: #f8f9fc;
            transform: translateY(-1px);
        }

        .section {
            background: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .section h2 {
            color: #667eea;
            margin-bottom: 25px;
            font-size: 1.8rem;
            border-bottom: 3px solid #f0f0f0;
            padding-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
            padding: 25px;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
            transform: translateY(0);
            transition: transform 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card h3 {
            font-size: 1rem;
            margin-bottom: 10px;
            opacity: 0.9;
        }

        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
        }

        .stat-card.pendiente {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
        }

        .stat-card.finalizado {
            background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%);
        }

        .stat-card.vencido {
            background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 8px;
            color: #555;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            padding: 12px 15px;
            border: 2px solid #e1e5e9;
            border-radius: 10px;
            font-size: 14px;
            transition: all 0.3s ease;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }

        .btn {
            padding: 12px 25px;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            transition: all 0.3s ease;
            margin-right: 10px;
            margin-bottom: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #fc466b 0%, #3f5efb 100%);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #74b9ff 0%, #0984e3 100%);
            color: white;
        }

        .table-container {
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #f0f0f0;
        }

        .table th {
            background: linear-gradient(135deg, #f8f9fc 0%, #e9ecef 100%);
            font-weight: 700;
            color: #555;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .table tbody tr {
            transition: background-color 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .badge.estado-pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge.estado-finalizado {
            background-color: #d4edda;
            color: #155724;
        }

        .badge.prioridad-alto {
            background-color: #f8d7da;
            color: #721c24;
        }

        .badge.prioridad-bajo {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .acciones {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .filtros-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            align-items: end;
        }

        .errores {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            border-left: 4px solid #dc3545;
        }

        .errores ul {
            margin: 0;
            padding-left: 20px;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .form-grid,
            .filtros-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-tabs {
                flex-direction: column;
            }
            
            .acciones {
                flex-direction: column;
            }

            .table-container {
                overflow-x: auto;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎯 Sistema PQRS</h1>
            <p>Gestión completa de Peticiones, Quejas, Reclamos, Sugerencias y Felicitaciones</p>
        </div>

        <!-- Navegación -->
        <div class="nav-tabs">
            <a href="?" class="nav-tab <?php echo ($accion === 'listar' || $accion === '') ? 'active' : ''; ?>">📋 Lista de PQRS</a>
            <a href="?accion=crear" class="nav-tab <?php echo $accion === 'crear' ? 'active' : ''; ?>">➕ Crear Nuevo</a>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($errores)): ?>
            <div class="errores">
                <strong>Por favor corrige los siguientes errores:</strong>
                <ul>
                    <?php foreach ($errores as $error): ?>
                        <li><?php echo htmlspecialchars($error); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <?php if ($accion === 'listar' || $accion === ''): ?>
            <!-- VISTA: LISTAR PQRS -->
            
            <!-- Estadísticas -->
            <div class="section">
                <h2>📊 Dashboard</h2>
                <div class="stats-grid">
                    <div class="stat-card">
                        <h3>Total PQRS</h3>
                        <div class="stat-number"><?php echo $estadisticas['total'] ?? 0; ?></div>
                    </div>
                    <div class="stat-card pendiente">
                        <h3>Pendientes</h3>
                        <div class="stat-number">
                            <?php 
                            $pendientes = 0;
                            foreach ($estadisticas['por_estado'] ?? [] as $estado) {
                                if ($estado['estado'] === 'Pendiente') {
                                    $pendientes = $estado['cantidad'];
                                    break;
                                }
                            }
                            echo $pendientes;
                            ?>
                        </div>
                    </div>
                    <div class="stat-card finalizado">
                        <h3>Finalizados</h3>
                        <div class="stat-number">
                            <?php 
                            $finalizados = 0;
                            foreach ($estadisticas['por_estado'] ?? [] as $estado) {
                                if ($estado['estado'] === 'Finalizado') {
                                    $finalizados = $estado['cantidad'];
                                    break;
                                }
                            }
                            echo $finalizados;
                            ?>
                        </div>
                    </div>
                    <div class="stat-card vencido">
                        <h3>Vencidos</h3>
                        <div class="stat-number"><?php echo $estadisticas['vencidos'] ?? 0; ?></div>
                    </div>
                </div>
            </div>

            <!-- Filtros -->
            <div class="section">
                <h2>🔍 Filtrar PQRS</h2>
                <form method="GET">
                    <div class="filtros-grid">
                        <div class="form-group">
                            <label>Tipo:</label>
                            <select name="tipo">
                                <option value="">Todos los tipos</option>
                                <option value="Peticiones" <?php echo ($_GET['tipo'] ?? '') === 'Peticiones' ? 'selected' : ''; ?>>Peticiones</option>
                                <option value="Quejas" <?php echo ($_GET['tipo'] ?? '') === 'Quejas' ? 'selected' : ''; ?>>Quejas</option>
                                <option value="Reclamos" <?php echo ($_GET['tipo'] ?? '') === 'Reclamos' ? 'selected' : ''; ?>>Reclamos</option>
                                <option value="Sugerencias" <?php echo ($_GET['tipo'] ?? '') === 'Sugerencias' ? 'selected' : ''; ?>>Sugerencias</option>
                                <option value="Felicitaciones" <?php echo ($_GET['tipo'] ?? '') === 'Felicitaciones' ? 'selected' : ''; ?>>Felicitaciones</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Estado:</label>
                            <select name="estado">
                                <option value="">Todos los estados</option>
                                <option value="Pendiente" <?php echo ($_GET['estado'] ?? '') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Finalizado" <?php echo ($_GET['estado'] ?? '') === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Prioridad:</label>
                            <select name="prioridad">
                                <option value="">Todas</option>
                                <option value="Alto" <?php echo ($_GET['prioridad'] ?? '') === 'Alto' ? 'selected' : ''; ?>>Alto</option>
                                <option value="Bajo" <?php echo ($_GET['prioridad'] ?? '') === 'Bajo' ? 'selected' : ''; ?>>Bajo</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Categoría:</label>
                            <select name="categoria">
                                <option value="">Todas</option>
                                <option value="Servicio" <?php echo ($_GET['categoria'] ?? '') === 'Servicio' ? 'selected' : ''; ?>>Servicio</option>
                                <option value="Habitación" <?php echo ($_GET['categoria'] ?? '') === 'Habitación' ? 'selected' : ''; ?>>Habitación</option>
                                <option value="Atención" <?php echo ($_GET['categoria'] ?? '') === 'Atención' ? 'selected' : ''; ?>>Atención</option>
                                <option value="Otro" <?php echo ($_GET['categoria'] ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>N° Documento:</label>
                            <input type="text" name="numdocumento" placeholder="Buscar por documento" 
                                   value="<?php echo htmlspecialchars($_GET['numdocumento'] ?? ''); ?>">
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
                            <a href="?" class="btn btn-secondary">🗑️ Limpiar</a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Tabla de PQRS -->
            <div class="section">
                <h2>📋 Lista de PQRS (<?php echo count($pqrs_lista); ?>)</h2>
                
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Fecha</th>
                                <th>Tipo</th>
                                <th>Usuario</th>
                                <th>Descripción</th>
                                <th>Prioridad</th>
                                <th>Estado</th>
                                <th>Límite</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pqrs_lista)): ?>
                                <tr>
                                    <td colspan="9" style="text-align: center; color: #666; padding: 40px;">
                                        📭 No hay PQRS registrados
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($pqrs_lista as $pqrs): ?>
                                    <tr>
                                        <td><strong>#<?php echo $pqrs['id']; ?></strong></td>
                                        <td><?php echo date('d/m/Y<\b\r>H:i', strtotime($pqrs['fechaRegistro'])); ?></td>
                                        <td><?php echo $pqrs['tipo']; ?></td>
                                        <td>
                                            <div>
                                                <strong><?php echo $pqrs['nombreUsuario'] ?? 'N/A'; ?></strong><br>
                                                <small><?php echo $pqrs['numdocumento']; ?></small>
                                            </div>
                                        </td>
                                        <td style="max-width: 200px;" title="<?php echo htmlspecialchars($pqrs['descripcion']); ?>">
                                            <?php echo substr($pqrs['descripcion'], 0, 80) . (strlen($pqrs['descripcion']) > 80 ? '...' : ''); ?>
                                        </td>
                                        <td>
                                            <span class="badge prioridad-<?php echo strtolower($pqrs['prioridad']); ?>">
                                                <?php echo $pqrs['prioridad']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge estado-<?php echo strtolower($pqrs['estado']); ?>">
                                                <?php echo $pqrs['estado']; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php 
                                            $fecha_limite = date('d/m/Y', strtotime($pqrs['fechaLimite']));
                                            $vencido = $pqrs['estado'] === 'Pendiente' && strtotime($pqrs['fechaLimite']) < time();
                                            ?>
                                            <span style="color: <?php echo $vencido ? '#dc3545' : '#333'; ?>">
                                                <?php echo $fecha_limite; ?>
                                                <?php if ($vencido): ?>
                                                    <br><small><strong>⚠️ Vencido</strong></small>
                                                <?php endif; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="acciones">
                                                <a href="?accion=ver&id=<?php echo $pqrs['id']; ?>" 
                                                   class="btn btn-info" title="Ver detalles">👁️</a>
                                                <a href="?accion=editar&id=<?php echo $pqrs['id']; ?>" 
                                                   class="btn btn-warning" title="Editar">✏️</a>
                                                <form method="POST" style="display: inline;" 
                                                      onsubmit="return confirm('¿Está seguro de eliminar este PQRS?');">
                                                    <button type="submit" class="btn btn-danger" 
                                                            formaction="?accion=eliminar&id=<?php echo $pqrs['id']; ?>"
                                                            title="Eliminar">🗑️</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        <?php elseif ($accion === 'crear'): ?>
            <!-- VISTA: CREAR PQRS -->
            <div class="section">
                <h2>➕ Crear Nuevo PQRS</h2>
                
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tipo: *</label>
                            <select name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="Peticiones" <?php echo ($_POST['tipo'] ?? '') === 'Peticiones' ? 'selected' : ''; ?>>Peticiones</option>
                                <option value="Quejas" <?php echo ($_POST['tipo'] ?? '') === 'Quejas' ? 'selected' : ''; ?>>Quejas</option>
                                <option value="Reclamos" <?php echo ($_POST['tipo'] ?? '') === 'Reclamos' ? 'selected' : ''; ?>>Reclamos</option>
                                <option value="Sugerencias" <?php echo ($_POST['tipo'] ?? '') === 'Sugerencias' ? 'selected' : ''; ?>>Sugerencias</option>
                                <option value="Felicitaciones" <?php echo ($_POST['tipo'] ?? '') === 'Felicitaciones' ? 'selected' : ''; ?>>Felicitaciones</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Número de Documento: *</label>
                            <input type="text" name="numdocumento" maxlength="15" placeholder="Ej: 12345678"
                                   value="<?php echo htmlspecialchars($_POST['numdocumento'] ?? ''); ?>" required>
                            <small>Entre 7 y 15 dígitos</small>
                        </div>

                        <div class="form-group">
                            <label>Prioridad: *</label>
                            <select name="prioridad" required>
                                <option value="">Seleccionar prioridad</option>
                                <option value="Bajo" <?php echo ($_POST['prioridad'] ?? '') === 'Bajo' ? 'selected' : ''; ?>>Bajo</option>
                                <option value="Alto" <?php echo ($_POST['prioridad'] ?? '') === 'Alto' ? 'selected' : ''; ?>>Alto</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Categoría: *</label>
                            <select name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="Servicio" <?php echo ($_POST['categoria'] ?? '') === 'Servicio' ? 'selected' : ''; ?>>Servicio</option>
                                <option value="Habitación" <?php echo ($_POST['categoria'] ?? '') === 'Habitación' ? 'selected' : ''; ?>>Habitación</option>
                                <option value="Atención" <?php echo ($_POST['categoria'] ?? '') === 'Atención' ? 'selected' : ''; ?>>Atención</option>
                                <option value="Otro" <?php echo ($_POST['categoria'] ?? '') === 'Otro' ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Estado: *</label>
                            <select name="estado" required>
                                <option value="Pendiente" <?php echo ($_POST['estado'] ?? 'Pendiente') === 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Finalizado" <?php echo ($_POST['estado'] ?? '') === 'Finalizado' ? 'selected' : ''; ?>>Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción: *</label>
                        <textarea name="descripcion" required placeholder="Describe detalladamente el PQRS (mínimo 10 caracteres)"><?php echo htmlspecialchars($_POST['descripcion'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group" id="respuesta-group" style="<?php echo ($_POST['estado'] ?? 'Pendiente') === 'Finalizado' ? '' : 'display: none;'; ?>">
                        <label>Respuesta: *</label>
                        <textarea name="respuesta" placeholder="Respuesta al PQRS (requerida si está finalizado)"><?php echo htmlspecialchars($_POST['respuesta'] ?? ''); ?></textarea>
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-success">💾 Crear PQRS</button>
                        <a href="?" class="btn btn-secondary">❌ Cancelar</a>
                    </div>
                </form>
            </div>

        <?php elseif ($accion === 'editar'): ?>
            <!-- VISTA: EDITAR PQRS -->
            <div class="section">
                <h2>✏️ Editar PQRS #<?php echo $pqrs['id']; ?></h2>
                
                <form method="POST">
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Tipo: *</label>
                            <select name="tipo" required>
                                <option value="">Seleccionar tipo</option>
                                <option value="Peticiones" <?php echo (($_POST['tipo'] ?? $pqrs['tipo']) === 'Peticiones') ? 'selected' : ''; ?>>Peticiones</option>
                                <option value="Quejas" <?php echo (($_POST['tipo'] ?? $pqrs['tipo']) === 'Quejas') ? 'selected' : ''; ?>>Quejas</option>
                                <option value="Reclamos" <?php echo (($_POST['tipo'] ?? $pqrs['tipo']) === 'Reclamos') ? 'selected' : ''; ?>>Reclamos</option>
                                <option value="Sugerencias" <?php echo (($_POST['tipo'] ?? $pqrs['tipo']) === 'Sugerencias') ? 'selected' : ''; ?>>Sugerencias</option>
                                <option value="Felicitaciones" <?php echo (($_POST['tipo'] ?? $pqrs['tipo']) === 'Felicitaciones') ? 'selected' : ''; ?>>Felicitaciones</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Número de Documento: *</label>
                            <input type="text" name="numdocumento" maxlength="15" 
                                   value="<?php echo htmlspecialchars($_POST['numdocumento'] ?? $pqrs['numdocumento']); ?>" required>
                        </div>

                        <div class="form-group">
                            <label>Prioridad: *</label>
                            <select name="prioridad" required>
                                <option value="">Seleccionar prioridad</option>
                                <option value="Bajo" <?php echo (($_POST['prioridad'] ?? $pqrs['prioridad']) === 'Bajo') ? 'selected' : ''; ?>>Bajo</option>
                                <option value="Alto" <?php echo (($_POST['prioridad'] ?? $pqrs['prioridad']) === 'Alto') ? 'selected' : ''; ?>>Alto</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Categoría: *</label>
                            <select name="categoria" required>
                                <option value="">Seleccionar categoría</option>
                                <option value="Servicio" <?php echo (($_POST['categoria'] ?? $pqrs['categoria']) === 'Servicio') ? 'selected' : ''; ?>>Servicio</option>
                                <option value="Habitación" <?php echo (($_POST['categoria'] ?? $pqrs['categoria']) === 'Habitación') ? 'selected' : ''; ?>>Habitación</option>
                                <option value="Atención" <?php echo (($_POST['categoria'] ?? $pqrs['categoria']) === 'Atención') ? 'selected' : ''; ?>>Atención</option>
                                <option value="Otro" <?php echo (($_POST['categoria'] ?? $pqrs['categoria']) === 'Otro') ? 'selected' : ''; ?>>Otro</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Estado: *</label>
                            <select name="estado" required>
                                <option value="Pendiente" <?php echo (($_POST['estado'] ?? $pqrs['estado']) === 'Pendiente') ? 'selected' : ''; ?>>Pendiente</option>
                                <option value="Finalizado" <?php echo (($_POST['estado'] ?? $pqrs['estado']) === 'Finalizado') ? 'selected' : ''; ?>>Finalizado</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Descripción: *</label>
                        <textarea name="descripcion" required><?php echo htmlspecialchars($_POST['descripcion'] ?? $pqrs['descripcion']); ?></textarea>
                    </div>

                    <div class="form-group" id="respuesta-group" style="<?php echo (($_POST['estado'] ?? $pqrs['estado']) === 'Finalizado') ? '' : 'display: none;'; ?>">
                        <label>Respuesta: *</label>
                        <textarea name="respuesta" placeholder="Respuesta al PQRS (requerida si está finalizado)"><?php echo htmlspecialchars($_POST['respuesta'] ?? $pqrs['respuesta'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-grid" style="margin-top: 20px; background: #f8f9fc; padding: 15px; border-radius: 10px;">
                        <div class="form-group">
                            <label>Fecha de Registro:</label>
                            <input type="text" value="<?php echo date('d/m/Y H:i', strtotime($pqrs['fechaRegistro'])); ?>" readonly>
                        </div>
                        <div class="form-group">
                            <label>Fecha Límite:</label>
                            <input type="text" value="<?php echo date('d/m/Y', strtotime($pqrs['fechaLimite'])); ?>" readonly>
                        </div>
                        <?php if ($pqrs['fechaFinalizacion']): ?>
                        <div class="form-group">
                            <label>Fecha Finalización:</label>
                            <input type="text" value="<?php echo date('d/m/Y H:i', strtotime($pqrs['fechaFinalizacion'])); ?>" readonly>
                        </div>
                        <?php endif; ?>
                    </div>

                    <div style="margin-top: 30px;">
                        <button type="submit" class="btn btn-warning">💾 Actualizar PQRS</button>
                        <a href="?" class="btn btn-secondary">❌ Cancelar</a>
                    </div>
                </form>
            </div>

        <?php elseif ($accion === 'ver'): ?>
            <!-- VISTA: VER DETALLES PQRS -->
            <div class="section">
                <h2>👁️ Detalles del PQRS #<?php echo $pqrs['id']; ?></h2>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>ID:</label>
                        <input type="text" value="#<?php echo $pqrs['id']; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Tipo:</label>
                        <input type="text" value="<?php echo $pqrs['tipo']; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Usuario:</label>
                        <input type="text" value="<?php echo ($pqrs['nombreUsuario'] ?? 'N/A') . ' (' . $pqrs['numdocumento'] . ')'; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Prioridad:</label>
                        <input type="text" value="<?php echo $pqrs['prioridad']; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Categoría:</label>
                        <input type="text" value="<?php echo $pqrs['categoria']; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Estado:</label>
                        <input type="text" value="<?php echo $pqrs['estado']; ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Fecha Registro:</label>
                        <input type="text" value="<?php echo date('d/m/Y H:i:s', strtotime($pqrs['fechaRegistro'])); ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label>Fecha Límite:</label>
                        <input type="text" value="<?php echo date('d/m/Y', strtotime($pqrs['fechaLimite'])); ?>" readonly>
                    </div>
                    
                    <?php if ($pqrs['fechaFinalizacion']): ?>
                    <div class="form-group">
                        <label>Fecha Finalización:</label>
                        <input type="text" value="<?php echo date('d/m/Y H:i:s', strtotime($pqrs['fechaFinalizacion'])); ?>" readonly>
                    </div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea readonly><?php echo htmlspecialchars($pqrs['descripcion']); ?></textarea>
                </div>
                
                <?php if ($pqrs['respuesta']): ?>
                <div class="form-group">
                    <label>Respuesta:</label>
                    <textarea readonly><?php echo htmlspecialchars($pqrs['respuesta']); ?></textarea>
                </div>
                <?php endif; ?>
                
                <div style="margin-top: 30px;">
                    <a href="?accion=editar&id=<?php echo $pqrs['id']; ?>" class="btn btn-warning">✏️ Editar</a>
                    <a href="?" class="btn btn-secondary">🔙 Volver a la Lista</a>
                </div>
            </div>

        <?php endif; ?>
    </div>

    <script>
        // Mostrar/ocultar campo de respuesta según el estado
        document.addEventListener('DOMContentLoaded', function() {
            const estadoSelect = document.querySelector('select[name="estado"]');
            const respuestaGroup = document.getElementById('respuesta-group');
            
            if (estadoSelect && respuestaGroup) {
                estadoSelect.addEventListener('change', function() {
                    if (this.value === 'Finalizado') {
                        respuestaGroup.style.display = 'block';
                        respuestaGroup.querySelector('textarea').required = true;
                    } else {
                        respuestaGroup.style.display = 'none';
                        respuestaGroup.querySelector('textarea').required = false;
                    }
                });
            }
        });

        // Auto-ocultar mensajes después de 5 segundos
        setTimeout(function() {
            const mensaje = document.querySelector('.mensaje');
            if (mensaje) {
                mensaje.style.transition = 'opacity 0.5s';
                mensaje.style.opacity = '0';
                setTimeout(() => mensaje.remove(), 500);
            }
        }, 5000);

        // Confirmación mejorada para eliminar
        function confirmarEliminacion(event) {
            const confirmacion = confirm(
                '⚠️ ¿Está seguro de eliminar este PQRS?\n\n' +
                'Esta acción no se puede deshacer.\n\n' +
                '✅ Presione OK para confirmar\n' +
                '❌ Presione Cancelar para conservar'
            );
            
            if (!confirmacion) {
                event.preventDefault();
                return false;
            }
            return true;
        }

        // Validación del formulario
        function validarFormulario(form) {
            const descripcion = form.querySelector('textarea[name="descripcion"]');
            const numdocumento = form.querySelector('input[name="numdocumento"]');
            const estado = form.querySelector('select[name="estado"]');
            const respuesta = form.querySelector('textarea[name="respuesta"]');
            
            // Validar descripción
            if (descripcion && descripcion.value.trim().length < 10) {
                alert('⚠️ La descripción debe tener al menos 10 caracteres.');
                descripcion.focus();
                return false;
            }
            
            // Validar número de documento
            if (numdocumento && !/^[0-9]{7,15}$/.test(numdocumento.value)) {
                alert('⚠️ El número de documento debe tener entre 7 y 15 dígitos numéricos.');
                numdocumento.focus();
                return false;
            }
            
            // Validar respuesta si está finalizado
            if (estado && estado.value === 'Finalizado' && respuesta && respuesta.value.trim() === '') {
                alert('⚠️ La respuesta es requerida cuando el estado es "Finalizado".');
                respuesta.focus();
                return false;
            }
            
            return true;
        }

        // Aplicar validación a todos los formularios
        document.addEventListener('DOMContentLoaded', function() {
            const forms = document.querySelectorAll('form[method="POST"]');
            forms.forEach(form => {
                form.addEventListener('submit', function(event) {
                    if (!validarFormulario(this)) {
                        event.preventDefault();
                        return false;
                    }
                });
            });
        });
    </script>
</body>
</html>