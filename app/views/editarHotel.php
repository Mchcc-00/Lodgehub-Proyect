<?php

require_once 'validarSesion.php';
// Incluir conexión a la base de datos
require_once '../../config/conexionGlobal.php';

try {
    $db = conexionDB();

    // Obtener el ID del hotel desde la URL
    $hotelId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

    if ($hotelId <= 0) {
        throw new Exception("ID de hotel no válido");
    }

    // Procesar formulario si se envió
    if ($_POST) {
        $nit = trim($_POST['nit']);
        $nombre = trim($_POST['nombre']);
        $direccion = trim($_POST['direccion']);
        $telefono = trim($_POST['telefono']);
        $correo = trim($_POST['correo']);
        $descripcion = trim($_POST['descripcion']);

        // Validaciones básicas
        if (empty($nit) || empty($nombre) || empty($direccion) || empty($telefono) || empty($correo)) {
            $error = "Los campos NIT, nombre, dirección, teléfono y correo son obligatorios.";
        } else {
            // Verificar que el NIT no esté en uso por otro hotel
            $stmtCheck = $db->prepare("SELECT id FROM tp_hotel WHERE nit = ? AND id != ?");
            $stmtCheck->execute([$nit, $hotelId]);

            if ($stmtCheck->fetch()) {
                $error = "El NIT ya está registrado para otro hotel.";
            } else {
                // Procesar la imagen si se subió una nueva
                $fotoPath = null;
                if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
                    $uploadDir = '../../public/uploads/hoteles/';

                    // Crear directorio si no existe
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileExtension = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];

                    if (in_array($fileExtension, $allowedExtensions)) {
                        $fileName = 'hotel_' . $hotelId . '_' . time() . '.' . $fileExtension;
                        $fotoPath = $uploadDir . $fileName;

                        if (!move_uploaded_file($_FILES['foto']['tmp_name'], $fotoPath)) {
                            $error = "Error al subir la imagen.";
                        }
                    } else {
                        $error = "Formato de imagen no válido. Use JPG, JPEG, PNG o GIF.";
                    }
                }

                if (!isset($error)) {
                    // Actualizar en la base de datos
                    if ($fotoPath) {
                        // Actualizar con nueva foto
                        $stmt = $db->prepare("UPDATE tp_hotel SET nit = ?, nombre = ?, direccion = ?, telefono = ?, correo = ?, foto = ?, descripcion = ? WHERE id = ?");
                        $result = $stmt->execute([$nit, $nombre, $direccion, $telefono, $correo, $fotoPath, $descripcion, $hotelId]);
                    } else {
                        // Actualizar sin cambiar la foto
                        $stmt = $db->prepare("UPDATE tp_hotel SET nit = ?, nombre = ?, direccion = ?, telefono = ?, correo = ?, descripcion = ? WHERE id = ?");
                        $result = $stmt->execute([$nit, $nombre, $direccion, $telefono, $correo, $descripcion, $hotelId]);
                    }

                    if ($result) {
                        $exito = "Hotel actualizado correctamente.";

                        // SOLUCIÓN: Actualizar la información del hotel en la sesión
                        // para que los cambios se reflejen inmediatamente en el homepage.
                        if (isset($_SESSION['hotel']) && $_SESSION['hotel']['id'] == $hotelId) {
                            $_SESSION['hotel']['nit'] = $nit;
                            $_SESSION['hotel']['nombre'] = $nombre;
                            $_SESSION['hotel']['direccion'] = $direccion;
                            $_SESSION['hotel']['telefono'] = $telefono;
                            $_SESSION['hotel']['correo'] = $correo;
                            $_SESSION['hotel']['descripcion'] = $descripcion;
                            if ($fotoPath) {
                                $_SESSION['hotel']['foto'] = $fotoPath;
                            }
                            // También actualizamos la variable de conveniencia
                            $_SESSION['hotel_nombre'] = $nombre;
                        }
                    } else {
                        $error = "Error al actualizar el hotel.";
                    }
                }
            }
        }
    }

    // Obtener información actual del hotel
    $stmt = $db->prepare("SELECT * FROM tp_hotel WHERE id = ?");
    $stmt->execute([$hotelId]);
    $hotel = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$hotel) {
        throw new Exception("Hotel no encontrado en la base de datos");
    }

    // Verificar que el usuario actual es el administrador del hotel
    if ($hotel['numDocumentoAdmin'] != $_SESSION['numDocumento']) {
        throw new Exception("No tienes permisos para editar este hotel");
    }
} catch (Exception $e) {
    echo "Error al cargar el hotel: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Hotel - LodgeHub</title>
    <link rel="stylesheet" href="../../public/assets/css/stylesHotel.css">
    <link rel="stylesheet" href="../../public/assets/css/stylesNav.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body>
    <?php
    $paginaActual = "Editar Hotel";
    include 'layouts/navbar.php';
    include 'layouts/sidebar.php';
    ?>
    <script src="../../public/assets/js/sidebar.js"></script>

    <div class="main-content" id="main-content">
        <div class="container">
            <div class="header">
                <h1><i class="fas fa-hotel"></i> Editar Hotel</h1>
            </div>

            <div class="form-content">
                <?php if (isset($exito)): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($exito); ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($error)): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" enctype="multipart/form-data">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="form-label" for="nit">
                                <i class="fas fa-id-card"></i> NIT *
                            </label>
                            <input type="text" id="nit" name="nit" class="form-input"
                                value="<?php echo htmlspecialchars($hotel['nit']); ?>"
                                required maxlength="20">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="nombre">
                                <i class="fas fa-hotel"></i> Nombre del Hotel *
                            </label>
                            <input type="text" id="nombre" name="nombre" class="form-input"
                                value="<?php echo htmlspecialchars($hotel['nombre']); ?>"
                                required maxlength="100">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="direccion">
                                <i class="fas fa-map-marker-alt"></i> Dirección *
                            </label>
                            <input type="text" id="direccion" name="direccion" class="form-input"
                                value="<?php echo htmlspecialchars($hotel['direccion']); ?>"
                                required maxlength="200">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="telefono">
                                <i class="fas fa-phone"></i> Teléfono *
                            </label>
                            <input type="tel" id="telefono" name="telefono" class="form-input"
                                value="<?php echo htmlspecialchars($hotel['telefono']); ?>"
                                required maxlength="15">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="correo">
                                <i class="fas fa-envelope"></i> Correo Electrónico *
                            </label>
                            <input type="email" id="correo" name="correo" class="form-input"
                                value="<?php echo htmlspecialchars($hotel['correo']); ?>"
                                required maxlength="255">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="foto">
                                <i class="fas fa-camera"></i> Foto del Hotel
                            </label>
                            <input type="file" id="foto" name="foto" class="form-input"
                                accept="image/*">
                            <?php if ($hotel['foto']): ?>
                                <div class="current-photo">
                                    <small class="text-muted">Foto actual:</small>
                                    <img src="<?php echo htmlspecialchars($hotel['foto']); ?>"
                                        alt="Foto actual del hotel"
                                        style="max-width: 200px; max-height: 150px; margin-top: 10px; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group description-group">
                        <label class="form-label" for="descripcion">
                            <i class="fas fa-align-left"></i> Descripción
                        </label>
                        <textarea id="descripcion" name="descripcion" class="form-input"
                            rows="4" placeholder="Describe las características y servicios del hotel..."><?php echo htmlspecialchars($hotel['descripcion']); ?></textarea>
                    </div>

                    <div class="actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Guardar Cambios
                        </button>
                        <a href="homepage.php" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>