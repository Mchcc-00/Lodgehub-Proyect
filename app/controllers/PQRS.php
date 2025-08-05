<?php
header('Content-Type: text/html; charset=UTF-8');

// Función de conexión PDO
function conexionDB() {
    try {
        $dsn = "mysql:host=localhost;dbname=Lodgehub;charset=utf8";
        $db = new PDO($dsn, 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $db;
    } catch (PDOException $e) {
        die("Error de conexión: " . $e->getMessage());
    }
}

// Conectar a la base de datos
$conn = conexionDB();

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

// Unificar nombre y apellido en "solicitante"
$solicitante = trim($nombre . ' ' . $apellido);

// Validación básica
if (
    empty($fecha) || empty($tipo_pqrs) || empty($urgencia) || empty($categoria) ||
    empty($descripcion) || empty($solicitante) || empty($empleado) ||
    empty($tipo_documento) || empty($numero_documento)
) {
    die("Por favor, completa todos los campos requeridos.");
}

// Consulta SQL
$sql = "INSERT INTO pqrs (
    id, fecha, tipo_pqrs, urgencia, categoria, descripcion,
    solicitante, empleado, tipo_documento, numero_documento
) VALUES (
    :id, :fecha, :tipo_pqrs, :urgencia, :categoria, :descripcion,
    :solicitante, :empleado, :tipo_documento, :numero_documento
)";

try {
    $stmt = $conn->prepare($sql);
    $ejecutado = $stmt->execute([
        ':id' => $id,
        ':fecha' => $fecha,
        ':tipo_pqrs' => $tipo_pqrs,
        ':urgencia' => $urgencia,
        ':categoria' => $categoria,
        ':descripcion' => $descripcion,
        ':solicitante' => $solicitante,
        ':empleado' => $empleado,
        ':tipo_documento' => $tipo_documento,
        ':numero_documento' => $numero_documento
    ]);

    if ($ejecutado) {
        ?>
        <style>
        body {
            background-color: #a8d9f0;
            margin: 0;
            padding: 0;
        }
        .mensaje-exito {
            display: flex;
            max-width: 600px;
            margin: 40px auto;
            background-color: #a8d9f0;
            border: 2px solid #2c6fab;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(44, 111, 171, 0.4);
            overflow: hidden;
            font-family: Arial, sans-serif;
            color: #000;
        }
        .mensaje-icono {
            background-color: #2c6fab;
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
            color: #2c6fab;
        }
        .mensaje-contenido p {
            font-size: 15px;
            margin: 10px 0;
            color: #000;
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
                <img src="../../public/img/flecha flechita_claro.png" alt="Flechaclara">
            </div>
            <div class="mensaje-contenido">
                <h2>REGISTRO EXITOSO</h2>
                <p>Tu solicitud fue registrada correctamente. Serás redirigido en unos segundos...</p>
                <a class="boton" href="/lodgehub-proyect/app/views/PQRS/crud.php">Volver al formulario</a>
            </div>
        </div>

        <script>
            setTimeout(function() {
                window.location.href = "/lodgehub-proyect/app/views/PQRS/crud.php";
            }, 4000);
        </script>
        <?php
    } else {
        echo "<p style='color:red;'>Error al guardar: " . htmlspecialchars($stmt->errorInfo()[2]) . "</p>";
    }

    $stmt->closeCursor();
    $conn = null;

} catch (PDOException $e) {
    die("Error al ejecutar la consulta: " . $e->getMessage());
}
?>

