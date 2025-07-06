<?php
header('Content-Type: text/html; charset=UTF-8');

// Datos de conexión
$host = "localhost";
$usuario = "root";
$contrasena = "";
$base_datos = "formulario_pqrs";

// Crear conexión
$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}
// Recibir y validar los datos
$id                = isset($_POST['id']) ? intval($_POST['id']) : 0;
$fecha             = $_POST['fecha'] ?? '';
$tipo_pqrs         = $_POST['tipo_pqrs'] ?? '';
$urgencia          = $_POST['urgencia'] ?? '';
$categoria         = $_POST['categoria'] ?? '';
$descripcion       = $_POST['descripcion'] ?? '';
$nombre            = $_POST['nombre'] ?? '';
$apellido          = $_POST['apellido'] ?? '';
$empleado          = $_POST['empleado'] ?? '';
$tipo_documento    = $_POST['tipo_documento'] ?? '';
$numero_documento  = $_POST['numero_documento'] ?? '';

// Validación básica
if (
    empty($fecha) || empty($tipo_pqrs) || empty($urgencia) || empty($categoria) ||
    empty($descripcion) || empty($nombre) || empty($apellido) || empty($empleado) ||
    empty($tipo_documento) || empty($numero_documento)
) {
    die("Por favor, completa todos los campos requeridos.");
}

// Consulta SQL
$sql = "INSERT INTO pqrs (
    id, fecha, tipo_pqrs, urgencia, categoria, descripcion,
    nombre, apellido, empleado, tipo_documento, numero_documento
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

// Preparar la sentencia
$stmt = $conn->prepare($sql);


if (!$stmt) {
    die("Error al preparar la consulta: " . $conn->error);
}

// Vincular parámetros
$stmt->bind_param(
    "isssssssssi",
    $id, $fecha, $tipo_pqrs, $urgencia, $categoria,
    $descripcion, $nombre, $apellido, $empleado,
    $tipo_documento, $numero_documento
);

if ($stmt->execute()) {
    echo '
    <style>
    body {
        background-color: #202020;
        margin: 0;
        padding: 0;
    }
    .mensaje-exito {
        display: flex;
        max-width: 600px;
        margin: 40px auto;
        background-color: #2b2b2b;
        border: 2px solid #ffc400;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.4);
        overflow: hidden;
        font-family: Arial, sans-serif;
        color: #ffffff;
    }
    .mensaje-icono {
        background-color: #333;
        padding: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        min-width: 120px;
    }
    .mensaje-icono img {
        width: 60px;
        height: 60px;
    }
    .mensaje-contenido {
        padding: 20px;
        flex: 1;
    }
    .mensaje-contenido h2 {
        margin: 0;
        font-size: 20px;
        color: #00e676;
    }
    .mensaje-contenido p {
        font-size: 15px;
        margin: 10px 0;
        color: #ccc;
    }
    .mensaje-contenido a.boton {
        display: inline-block;
        padding: 8px 16px;
        background-color: #00c853;
        color: #fff;
        text-decoration: none;
        border-radius: 5px;
        margin-top: 10px;
    }
    .mensaje-contenido a.boton:hover {
        background-color: #009624;
    }
    </style>

    <div class="mensaje-exito">
        <div class="mensaje-icono">
        <img src="Img/FlechaOscura.png" alt="FlechaOscura" class="logo-img">
        </div>
        <div class="mensaje-contenido">
            <h2>REGISTRO EXITOSO</h2>
            <p>Tu solicitud fue registrada correctamente. Ahora puedes hacer <strong style="color: #fff;">seguimiento</strong>.</p>
            <a class="boton" href="index.html">Continuar</a>
        </div>
    </div>';
} else {
    echo "<p style='color:white;'>Error al guardar: " . htmlspecialchars($stmt->error) . "</p>";
}

$stmt->close();
$conn->close();
?>

