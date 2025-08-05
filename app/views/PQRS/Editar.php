<?php
require_once('../../../config/conexionGlobal.php');
$pdo = conexionDB();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $pdo->prepare("SELECT * FROM pqrs WHERE id = ?");
    $stmt->execute([$id]);
    $pqrs = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
    echo "ID no válido";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar PQRS</title>
    <link rel="stylesheet" href="../../../public/assets/css/stylescrud.css">
    <style>
        body {
            background: linear-gradient(to bottom right, #d9f3ff, #b3e0ff);
            font-family: Arial, sans-serif;
        }
        .form-card {
            background-color: #0b3b4e;
            padding: 20px;
            width: 400px;
            margin: 50px auto;
            border-radius: 10px;
            color: white;
        }
        .form-card input, .form-card select {
            width: 100%;
            padding: 8px;
            margin-bottom: 12px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
        }
        .form-card button {
            background-color: #00c4b4;
            color: #fff;
            border: none;
            padding: 10px;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
            font-size: 15px;
        }
    </style>
</head>
<body>

<form action="../../controllers/Editarcontroller.php" method="POST" class="form-card">
    <!-- ID oculto -->
    <input type="hidden" name="id" value="<?= htmlspecialchars($pqrs['id']) ?>">

    <label>Fecha Registro:</label>
    <input type="date" name="fecha" value="<?= htmlspecialchars($pqrs['fecha']) ?>">

    <label>Nivel de Urgencia:</label>
    <select name="urgencia">
        <option value="1" <?= $pqrs['urgencia'] == 1 ? 'selected' : '' ?>>Alta</option>
        <option value="2" <?= $pqrs['urgencia'] == 2 ? 'selected' : '' ?>>Media</option>
        <option value="3" <?= $pqrs['urgencia'] == 3 ? 'selected' : '' ?>>Baja</option>
    </select>

    <label>Categoría:</label>
    <select name="categoria">
        <option value="1" <?= $pqrs['categoria'] == 1 ? 'selected' : '' ?>>Mantenimiento</option>
        <option value="2" <?= $pqrs['categoria'] == 2 ? 'selected' : '' ?>>Servicio</option>
        <option value="3" <?= $pqrs['categoria'] == 3 ? 'selected' : '' ?>>Otro</option>
    </select>

    <label>Tipo:</label>
    <select name="tipo_pqrs">
        <option value="1" <?= $pqrs['tipo_pqrs'] == 1 ? 'selected' : '' ?>>Petición</option>
        <option value="2" <?= $pqrs['tipo_pqrs'] == 2 ? 'selected' : '' ?>>Queja</option>
        <option value="3" <?= $pqrs['tipo_pqrs'] == 3 ? 'selected' : '' ?>>Reclamo</option>
        <option value="4" <?= $pqrs['tipo_pqrs'] == 4 ? 'selected' : '' ?>>Sugerencia</option>
    </select>

    <label>Nombre completo del solicitante:</label>
    <input type="text" name="solicitante" value="<?= htmlspecialchars($pqrs['solicitante']) ?>" required>

    <label>Registra:</label>
    <input type="text" name="registra" value="<?= htmlspecialchars($pqrs['empleado']) ?>">

    <label>Estado:</label>
    <select name="estado">
        <option value="1" <?= $pqrs['estado'] == 1 ? 'selected' : '' ?>>Pendiente</option>
        <option value="2" <?= $pqrs['estado'] == 2 ? 'selected' : '' ?>>Solucionado</option>
        <option value="3" <?= $pqrs['estado'] == 3 ? 'selected' : '' ?>>En Proceso</option>
    </select>

    <button type="submit">Actualizar</button>
</form>

</body>
</html>
