<?php
// Iniciar sesión para poder leer las variables de mensajes
session_start();

// --- LÓGICA PARA OBTENER DATOS ---
require_once __DIR__ . '/../../../app/Models/Usuario.php';
require_once __DIR__ . '/../../../config/conexionGlobal.php';

$db = conexionDB();
$usuarioModel = new Usuario($db);
$usuarios = $usuarioModel->obtenerTodos();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="../../../public/assets/css/styles.css"> 
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
</head>
<body>
    
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/lodgehub/app/views/layouts/nav.php"; ?>

    <div class="container mt-4">

        <?php if (isset($_SESSION['mensaje_exito'])): ?>
            <div class="alert alert-success" role="alert">
                <?php 
                    echo $_SESSION['mensaje_exito']; 
                    unset($_SESSION['mensaje_exito']); // Borra el mensaje después de mostrarlo
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['mensaje_error'])): ?>
            <div class="alert alert-danger" role="alert">
                <?php 
                    echo $_SESSION['mensaje_error']; 
                    unset($_SESSION['mensaje_error']); // Borra el mensaje después de mostrarlo
                ?>
            </div>
        <?php endif; ?>
        <div class="user-header">
            <h2 class="form-title">Lista de Usuarios</h2>
            <a href="crear.php" class="btn-add" title="Agregar Usuario">
                <i class="fas fa-plus"></i>
            </a>
        </div>
        
        <table id="tablaUsuarios" class="table table-striped table-bordered" style="width:100%">
            <thead class="user-table-header">
                <tr>
                    <th>Tipo de documento</th>
                    <th>Número de documento</th>
                    <th>Nombres</th>
                    <th>Apellidos</th>
                    <th>Sexo</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="user-table-body">
                <?php if (empty($usuarios)): ?>
                    <tr>
                        <td colspan="9" style="text-align: center;">No hay usuarios registrados.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($usuario['tipo_documento']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['numDocumento']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['nombres']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['apellidos']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['sexo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['numTelefono']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['correo']); ?></td>
                            <td><?php echo htmlspecialchars($usuario['rol']); ?></td>
                            <td class="action-links">
                                <a href="editar.php?id=<?php echo htmlspecialchars($usuario['numDocumento']); ?>" class="btn-edit" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="eliminar.php?id=<?php echo htmlspecialchars($usuario['numDocumento']); ?>" class="btn-delete" title="Eliminar" onclick="return confirm('¿Estás seguro...?');"><i class="fas fa-trash-alt"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <footer class="form-footer">
            lodgehubgroup © 2025
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
</body>
</html>