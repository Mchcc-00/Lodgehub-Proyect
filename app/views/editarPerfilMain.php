<?php
session_start();

// Verificar si el usuario está logueado
if (!isset($_SESSION['numDocumento']) || !isset($_SESSION['user'])) {
    header('Location: login.php?mensaje=Sesión expirada');
    exit();
}

// Incluir conexión a la base de datos
require_once '../../config/conexionGlobal.php';

try {
    $db = conexionDB();
    
    // Procesar formulario si se envió
    if ($_POST) {
        $nombres = trim($_POST['nombres']);
        $apellidos = trim($_POST['apellidos']);
        $numTelefono = trim($_POST['numTelefono']);
        $correo = trim($_POST['correo']);
        $sexo = $_POST['sexo'];
        $fechaNacimiento = $_POST['fechaNacimiento'];
        
        // Validaciones básicas
        if (empty($nombres) || empty($apellidos) || empty($numTelefono) || empty($correo)) {
            $error = "Todos los campos obligatorios deben ser completados.";
        } else {
            // Actualizar en la base de datos
            $stmt = $db->prepare("UPDATE tp_usuarios SET nombres = ?, apellidos = ?, numTelefono = ?, correo = ?, sexo = ?, fechaNacimiento = ? WHERE numDocumento = ?");
            
            if ($stmt->execute([$nombres, $apellidos, $numTelefono, $correo, $sexo, $fechaNacimiento, $_SESSION['numDocumento']])) {
                // Actualizar variables de sesión
                $_SESSION['nombres'] = $nombres;
                $_SESSION['apellidos'] = $apellidos;
                
                $exito = "Perfil actualizado correctamente.";
            } else {
                $error = "Error al actualizar el perfil.";
            }
        }
    }
    
    // Obtener información actual del usuario
    $stmt = $db->prepare("SELECT * FROM tp_usuarios WHERE numDocumento = ?");
    $stmt->execute([$_SESSION['numDocumento']]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$usuario) {
        throw new Exception("Usuario no encontrado en la base de datos");
    }
} catch (Exception $e) {
    echo "Error al cargar el perfil: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil - LodgeHub</title>
    <link rel="stylesheet" href="../../public/assets/css/stylesEditarPerfil.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNavUsuario.css">

</head>
    <?php 
        $paginaActual = "Editar Perfil";
        include 'layouts/navusuario.php';
    ?>
<body>
    <div class="container">
        <div class="header">
            <h1>Editar Mi Perfil</h1>
        </div>

        <div class="form-content">
            <?php if (isset($exito)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($exito); ?></div>
            <?php endif; ?>

            <?php if (isset($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label" for="nombres">Nombres *</label>
                        <input type="text" id="nombres" name="nombres" class="form-input" 
                               value="<?php echo htmlspecialchars($usuario['nombres']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="apellidos">Apellidos *</label>
                        <input type="text" id="apellidos" name="apellidos" class="form-input" 
                               value="<?php echo htmlspecialchars($usuario['apellidos']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="correo">Correo Electrónico *</label>
                        <input type="email" id="correo" name="correo" class="form-input" 
                               value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="numTelefono">Número de Teléfono *</label>
                        <input type="tel" id="numTelefono" name="numTelefono" class="form-input" 
                               value="<?php echo htmlspecialchars($usuario['numTelefono']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="sexo">Sexo</label>
                        <select id="sexo" name="sexo" class="form-select">
                            <option value="Hombre" <?php echo ($usuario['sexo'] == 'Hombre') ? 'selected' : ''; ?>>Hombre</option>
                            <option value="Mujer" <?php echo ($usuario['sexo'] == 'Mujer') ? 'selected' : ''; ?>>Mujer</option>
                            <option value="Otro" <?php echo ($usuario['sexo'] == 'Otro') ? 'selected' : ''; ?>>Otro</option>
                            <option value="Prefiero no decirlo" <?php echo ($usuario['sexo'] == 'Prefiero no decirlo') ? 'selected' : ''; ?>>Prefiero no decirlo</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label class="form-label" for="fechaNacimiento">Fecha de Nacimiento</label>
                        <input type="date" id="fechaNacimiento" name="fechaNacimiento" class="form-input" 
                               value="<?php echo $usuario['fechaNacimiento']; ?>">
                    </div>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    <a href="miPerfilMain.php" class="btn btn-secondary">Volver</a>
                </div>
            </form>
        </div>
    </div>
</body>