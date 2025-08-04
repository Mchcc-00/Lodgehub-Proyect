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
  <link rel="stylesheet" href="../../../public/assets/css/stylescrud.css">
</head>
<body>
  <div class="crud-container">

    <!-- Filtros y botones -->
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

      <!-- Botón + para agregar PQRS -->
      <button class="add-button">+</button>
    </div>

    <!-- Tabla PQRS -->
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
        </tr>
      </thead>
      <tbody id="tabla-pqrs">
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
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</body>
</html>
