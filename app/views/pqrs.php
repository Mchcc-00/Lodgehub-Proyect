<?php
require_once '../../config/conexionGlobal.php';

// Función para obtener todos los PQRS
function obtenerPQRS() {
    $db = conexionDB();
    if (!$db) return [];
    
    try {
        $stmt = $db->prepare("SELECT p.*, u.nombre as nombreUsuario FROM tp_pqrs p 
                             LEFT JOIN tp_usuarios u ON p.numdocumento = u.numDocumento 
                             ORDER BY p.fechaRegistro DESC");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener PQRS: " . $e->getMessage());
        return [];
    }
}

// Función para obtener un PQRS específico
function obtenerPQRSPorId($id) {
    $db = conexionDB();
    if (!$db) return null;
    
    try {
        $stmt = $db->prepare("SELECT * FROM tp_pqrs WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Error al obtener PQRS: " . $e->getMessage());
        return null;
    }
}

// Función para crear nuevo PQRS
function crearPQRS($datos) {
    $db = conexionDB();
    if (!$db) return false;
    
    try {
        $stmt = $db->prepare("INSERT INTO tp_pqrs (tipo, descripcion, numdocumento, prioridad, categoria, estado) 
                             VALUES (?, ?, ?, ?, ?, ?)");
        return $stmt->execute([
            $datos['tipo'],
            $datos['descripcion'],
            $datos['numdocumento'],
            $datos['prioridad'],
            $datos['categoria'],
            $datos['estado']
        ]);
    } catch (PDOException $e) {
        error_log("Error al crear PQRS: " . $e->getMessage());
        return false;
    }
}

// Función para actualizar PQRS
function actualizarPQRS($id, $datos) {
    $db = conexionDB();
    if (!$db) return false;
    
    try {
        $fechaFinalizacion = $datos['estado'] === 'Finalizado' ? 'NOW()' : 'NULL';
        
        $stmt = $db->prepare("UPDATE tp_pqrs SET 
                             tipo = ?, descripcion = ?, numdocumento = ?, 
                             prioridad = ?, categoria = ?, estado = ?, 
                             respuesta = ?, fechaFinalizacion = $fechaFinalizacion
                             WHERE id = ?");
        return $stmt->execute([
            $datos['tipo'],
            $datos['descripcion'],
            $datos['numdocumento'],
            $datos['prioridad'],
            $datos['categoria'],
            $datos['estado'],
            $datos['respuesta'],
            $id
        ]);
    } catch (PDOException $e) {
        error_log("Error al actualizar PQRS: " . $e->getMessage());
        return false;
    }
}

// Función para eliminar PQRS
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

// Procesar acciones POST
$mensaje = '';
$tipo_mensaje = '';

if ($_POST) {
    if (isset($_POST['accion'])) {
        switch ($_POST['accion']) {
            case 'crear':
                if (crearPQRS($_POST)) {
                    $mensaje = 'PQRS creado exitosamente';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al crear el PQRS';
                    $tipo_mensaje = 'error';
                }
                break;
                
            case 'actualizar':
                if (actualizarPQRS($_POST['id'], $_POST)) {
                    $mensaje = 'PQRS actualizado exitosamente';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al actualizar el PQRS';
                    $tipo_mensaje = 'error';
                }
                break;
                
            case 'eliminar':
                if (eliminarPQRS($_POST['id'])) {
                    $mensaje = 'PQRS eliminado exitosamente';
                    $tipo_mensaje = 'success';
                } else {
                    $mensaje = 'Error al eliminar el PQRS';
                    $tipo_mensaje = 'error';
                }
                break;
        }
    }
}

// Obtener PQRS para editar si se especifica
$pqrs_editar = null;
if (isset($_GET['editar'])) {
    $pqrs_editar = obtenerPQRSPorId($_GET['editar']);
}

// Obtener todos los PQRS
$pqrs_lista = obtenerPQRS();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de PQRS - LodgeHub</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
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
            border-radius: 10px;
            margin-bottom: 30px;
            text-align: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .header h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .mensaje {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
            font-weight: 500;
        }

        .mensaje.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .form-section {
            background: white;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .form-section h2 {
            color: #667eea;
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
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
            padding: 12px;
            border: 2px solid #e1e5e9;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #667eea;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
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

        .btn-primary {
            background-color: #667eea;
            color: white;
        }

        .btn-primary:hover {
            background-color: #5a6fd8;
            transform: translateY(-2px);
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #218838;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-secondary {
            background-color: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e1e5e9;
        }

        .table th {
            background-color: #f8f9fc;
            font-weight: 600;
            color: #555;
            text-transform: uppercase;
            font-size: 12px;
            letter-spacing: 1px;
        }

        .table tbody tr:hover {
            background-color: #f8f9fc;
        }

        .estado {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .estado.pendiente {
            background-color: #fff3cd;
            color: #856404;
        }

        .estado.finalizado {
            background-color: #d4edda;
            color: #155724;
        }

        .prioridad {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
        }

        .prioridad.alto {
            background-color: #f8d7da;
            color: #721c24;
        }

        .prioridad.bajo {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .actions {
            display: flex;
            gap: 5px;
        }

        .descripcion-corta {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .table-container {
                overflow-x: auto;
            }
            
            .actions {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Gestión de PQRS</h1>
            <p>Sistema de Peticiones, Quejas, Reclamos, Sugerencias y Felicitaciones</p>
        </div>

        <?php if ($mensaje): ?>
            <div class="mensaje <?php echo $tipo_mensaje; ?>">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>

        <!-- Formulario para crear/editar PQRS -->
        <div class="form-section">
            <h2><?php echo $pqrs_editar ? 'Editar PQRS #' . $pqrs_editar['id'] : 'Nuevo PQRS'; ?></h2>
            
            <form method="POST">
                <input type="hidden" name="accion" value="<?php echo $pqrs_editar ? 'actualizar' : 'crear'; ?>">
                <?php if ($pqrs_editar): ?>
                    <input type="hidden" name="id" value="<?php echo $pqrs_editar['id']; ?>">
                <?php endif; ?>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label>Tipo:</label>
                        <select name="tipo" required>
                            <option value="">Seleccionar tipo</option>
                            <?php
                            $tipos = ['Peticiones', 'Quejas', 'Reclamos', 'Sugerencias', 'Felicitaciones'];
                            foreach ($tipos as $tipo) {
                                $selected = ($pqrs_editar && $pqrs_editar['tipo'] === $tipo) ? 'selected' : '';
                                echo "<option value='$tipo' $selected>$tipo</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Número de Documento:</label>
                        <input type="text" name="numdocumento" maxlength="15" 
                               value="<?php echo $pqrs_editar ? $pqrs_editar['numdocumento'] : ''; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Prioridad:</label>
                        <select name="prioridad" required>
                            <option value="">Seleccionar prioridad</option>
                            <?php
                            $prioridades = ['Bajo', 'Alto'];
                            foreach ($prioridades as $prioridad) {
                                $selected = ($pqrs_editar && $pqrs_editar['prioridad'] === $prioridad) ? 'selected' : '';
                                echo "<option value='$prioridad' $selected>$prioridad</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Categoría:</label>
                        <select name="categoria" required>
                            <option value="">Seleccionar categoría</option>
                            <?php
                            $categorias = ['Servicio', 'Habitación', 'Atención', 'Otro'];
                            foreach ($categorias as $categoria) {
                                $selected = ($pqrs_editar && $pqrs_editar['categoria'] === $categoria) ? 'selected' : '';
                                echo "<option value='$categoria' $selected>$categoria</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Estado:</label>
                        <select name="estado" required>
                            <?php
                            $estados = ['Pendiente', 'Finalizado'];
                            foreach ($estados as $estado) {
                                $selected = '';
                                if ($pqrs_editar && $pqrs_editar['estado'] === $estado) {
                                    $selected = 'selected';
                                } elseif (!$pqrs_editar && $estado === 'Pendiente') {
                                    $selected = 'selected';
                                }
                                echo "<option value='$estado' $selected>$estado</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Descripción:</label>
                    <textarea name="descripcion" required><?php echo $pqrs_editar ? $pqrs_editar['descripcion'] : ''; ?></textarea>
                </div>

                <?php if ($pqrs_editar): ?>
                    <div class="form-group">
                        <label>Respuesta:</label>
                        <textarea name="respuesta"><?php echo $pqrs_editar['respuesta'] ?? ''; ?></textarea>
                    </div>
                <?php endif; ?>

                <div>
                    <button type="submit" class="btn btn-primary">
                        <?php echo $pqrs_editar ? 'Actualizar PQRS' : 'Crear PQRS'; ?>
                    </button>
                    <?php if ($pqrs_editar): ?>
                        <a href="?" class="btn btn-secondary">Cancelar</a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Lista de PQRS -->
        <div class="form-section">
            <h2>Lista de PQRS (<?php echo count($pqrs_lista); ?>)</h2>
            
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Fecha Registro</th>
                            <th>Tipo</th>
                            <th>Usuario</th>
                            <th>Descripción</th>
                            <th>Prioridad</th>
                            <th>Categoría</th>
                            <th>Estado</th>
                            <th>Fecha Límite</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pqrs_lista)): ?>
                            <tr>
                                <td colspan="10" style="text-align: center; color: #666;">No hay PQRS registrados</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pqrs_lista as $pqrs): ?>
                                <tr>
                                    <td><strong><?php echo $pqrs['id']; ?></strong></td>
                                    <td><?php echo date('d/m/Y H:i', strtotime($pqrs['fechaRegistro'])); ?></td>
                                    <td><?php echo $pqrs['tipo']; ?></td>
                                    <td>
                                        <?php echo $pqrs['nombreUsuario'] ?? $pqrs['numdocumento']; ?>
                                        <br><small><?php echo $pqrs['numdocumento']; ?></small>
                                    </td>
                                    <td class="descripcion-corta" title="<?php echo htmlspecialchars($pqrs['descripcion']); ?>">
                                        <?php echo htmlspecialchars($pqrs['descripcion']); ?>
                                    </td>
                                    <td>
                                        <span class="prioridad <?php echo strtolower($pqrs['prioridad']); ?>">
                                            <?php echo $pqrs['prioridad']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $pqrs['categoria']; ?></td>
                                    <td>
                                        <span class="estado <?php echo strtolower($pqrs['estado']); ?>">
                                            <?php echo $pqrs['estado']; ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php 
                                        echo date('d/m/Y', strtotime($pqrs['fechaLimite']));
                                        if ($pqrs['estado'] === 'Pendiente' && strtotime($pqrs['fechaLimite']) < time()) {
                                            echo '<br><small style="color: #dc3545;">Vencido</small>';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <div class="actions">
                                            <a href="?editar=<?php echo $pqrs['id']; ?>" class="btn btn-warning">Editar</a>
                                            <form method="POST" style="display: inline;" 
                                                  onsubmit="return confirm('¿Está seguro de eliminar este PQRS?');">
                                                <input type="hidden" name="accion" value="eliminar">
                                                <input type="hidden" name="id" value="<?php echo $pqrs['id']; ?>">
                                                <button type="submit" class="btn btn-danger">Eliminar</button>
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
    </div>

    <script>
        // Auto-ocultar mensajes después de 5 segundos
        setTimeout(function() {
            const mensaje = document.querySelector('.mensaje');
            if (mensaje) {
                mensaje.style.transition = 'opacity 0.5s';
                mensaje.style.opacity = '0';
                setTimeout(() => mensaje.remove(), 500);
            }
        }, 5000);

        // Confirmación antes de eliminar
        function confirmarEliminacion(id) {
            return confirm('¿Está seguro de que desea eliminar este PQRS? Esta acción no se puede deshacer.');
        }
    </script>
</body>
</html>