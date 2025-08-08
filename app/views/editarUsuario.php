<?php
session_start();
// Incluimos los archivos necesarios para la lógica
require_once __DIR__ . '/../../../app/Models/Usuario.php';
require_once __DIR__ . '/../../../config/conexionGlobal.php';

// Creamos la conexión y el modelo
$db = conexionDB();
$usuarioModel = new Usuario($db);

// --- SECCIÓN PARA PROCESAR LA ACTUALIZACIÓN (CUANDO SE ENVÍA EL FORMULARIO CON POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['numDocumento'] ?? null;
    $datos = $_POST;

    if ($id) {
        $exito = $usuarioModel->actualizar($id, $datos);
        if ($exito) {
            // Guardamos el mensaje de éxito en la sesión
            $_SESSION['mensaje_exito'] = "Usuario actualizado correctamente.";
            // Redirigimos a la lista (URL limpia)
            header('Location: lista.php');
            exit();
        }
    }
    // Si falla, guardamos el error en la sesión y redirigimos de vuelta
    $_SESSION['mensaje_error'] = "No se pudo actualizar el usuario.";
    header('Location: editar.php?id=' . $id);
    exit();
}

// --- SECCIÓN PARA MOSTRAR EL FORMULARIO CON LOS DATOS (CUANDO SE CARGA LA PÁGINA CON GET) ---
$id_a_editar = $_GET['id'] ?? null;
if (!$id_a_editar) {
    die("Error: No se proporcionó un ID de usuario.");
}
$usuario = $usuarioModel->obtenerPorId($id_a_editar);
if (!$usuario) {
    die("Error: Usuario no encontrado.");
}
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Editar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../../public/assets/css/styles.css">
    
</head>

<body>
    <?php include $_SERVER['DOCUMENT_ROOT'] . "/lodgehub/app/views/layouts/nav.php"; ?>

    <div class="container mt-4">
        <h2 class="form-title">Editar Usuario</h2>

        <form action="editar.php" method="post">

            <input type="hidden" name="numDocumento" value="<?php echo htmlspecialchars($usuario['numDocumento']); ?>">

            <div class="form-grid">
                <div class="form-group">
                    <label>Número de Documento (No editable)</label>
                    <input type="text" value="<?php echo htmlspecialchars($usuario['numDocumento']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Nombres (No editable)</label>
                    <input type="text" value="<?php echo htmlspecialchars($usuario['nombres']); ?>" disabled>
                </div>
                <div class="form-group">
                    <label>Apellidos (No editable)</label>
                    <input type="text" value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="numTelefono">Número de teléfono</label>
                    <input type="tel" id="numTelefono" name="numTelefono" value="<?php echo htmlspecialchars($usuario['numTelefono']); ?>">
                </div>
                <div class="form-group">
                    <label for="telEmergencia">Teléfono de emergencia</label>
                    <input type="tel" id="telEmergencia" name="telEmergencia" value="<?php echo htmlspecialchars($usuario['telEmergencia']); ?>">
                </div>
                <div class="form-group">
                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" required value="<?php echo htmlspecialchars($usuario['correo']); ?>">
                </div>
                <div class="form-group">
                    <label for="direccion">Dirección</label>
                    <input type="text" id="direccion" name="direccion" value="<?php echo htmlspecialchars($usuario['direccion']); ?>">
                </div>
                <div class="form-group">
                    <label for="sexo">Sexo</label>
                    <select id="sexo" name="sexo">
                        <option value="1" <?php if ($usuario['sexo'] == '1') echo 'selected'; ?>>Hombre</option>
                        <option value="2" <?php if ($usuario['sexo'] == '2') echo 'selected'; ?>>Mujer</option>
                        <option value="3" <?php if ($usuario['sexo'] == '3') echo 'selected'; ?>>Otro</option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="button" class="btn btn-secondary" onclick="window.location.href='lista.php'">Cancelar</button>
                <button type="submit" class="btn btn-primary">Actualizar Usuario</button>
            </div>
        </form>

        <footer class="form-footer">
            lodgehubgroup © 2025
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>