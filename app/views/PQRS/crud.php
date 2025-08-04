<?php
require_once('../../../config/conexionGlobal.php');

$pdo = conexionDB();

try {
    $stmt = $pdo->query("
        SELECT 
            id, 
            fecha, 
            urgencia, 
            categoria, 
            tipo_pqrs, 
            CONCAT(nombre, ' ', apellido) AS solicitante, 
            empleado, 
            estado
        FROM pqrs
    ");
    $pqrs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al consultar la base de datos: " . $e->getMessage();
    $pqrs = [];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>CRUD PQRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="../../../public/assets/css/stylescrud.css">
</head>
<body>
    <div class="crud-container">

        <div class="filters">
            <input type="text" id="search" placeholder="Buscar PQRS" />

            <select id="estado-filter">
                <option>Estado</option>
                <option>Solucionado</option>
                <option>Pendiente</option>
            </select>

            <select id="urgencia-filter">
                <option>Urgencia</option>
                <option>Alta</option>
                <option>Media</option>
                <option>Baja</option>
            </select>

            <select id="categoria-filter">
                <option>Categoría</option>
                <option>Habitación</option>
                <option>Servicio</option>
            </select>

            <select id="tipo-filter">
                <option>Tipo</option>
                <option>Queja</option>
                <option>Sugerencia</option>
            </select>

            <button class="add-button">+</button>
        </div>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Fecha registro</th>
                    <th>Nivel de urgencia</th>
                    <th>Categoría</th>
                    <th>Tipo</th>
                    <th>Solicitante</th>
                    <th>Registra</th>
                    <th>Estado</th>
                    <th>Acciones</th> 
                </tr>
            </thead>

            <tbody id="tabla-pqrs">
    <?php if (is_array($pqrs) && !empty($pqrs)): ?>
        <?php foreach ($pqrs as $item): ?>
            <tr>
                <td><?= $item['id'] ?></td>
                <td><?= date('d/m/Y', strtotime($item['fecha'])) ?></td>
                <td><?= $item['urgencia'] ?></td>
                <td><?= $item['categoria'] ?></td>
                <td><?= $item['tipo_pqrs'] ?></td>
                <td><?= $item['solicitante'] ?></td>
                <td><?= $item['empleado'] ?></td>
                <td class="<?= $item['estado'] === 'Solucionado' ? 'estado-s' : '' ?>">
                    <?= $item['estado'] ?>
                </td>
                <td>
                    <a href="editar.php?id=<?= $item['id'] ?>" class="btn-crud btn-editar" title="Editar">
                        <i class="fas fa-pencil-alt"></i>
                    </a>
                    <a href="eliminar.php?id=<?= $item['id'] ?>" class="btn-crud btn-eliminar" title="Eliminar">
                        <i class="fas fa-trash-alt"></i>
                    </a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr>
            <td colspan="9" style="text-align: center;">No hay registros de PQRS.</td>
        </tr>
    <?php endif; ?>
</tbody>
        </table>
    </div>
</body>
</html>