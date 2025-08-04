<?php       

require_once '../../config/conexionGlobal.php';

$pdo = conexionDB();

// Controlador de habitaciones
require_once '../models/Habitacion.php';
Habitacion::setPdoConnection($pdo);

// Función para centralizar la validación de los datos
function validateHabitacionData() {
    $errores = [];

    // Usar filter_input para obtener y sanear los datos
    $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
    $costo = filter_input(INPUT_POST, 'costo', FILTER_VALIDATE_FLOAT, ['options' => ['min_range' => 0]]);
    $tipoHabitacion = filter_input(INPUT_POST, 'tipoHabitacion', FILTER_VALIDATE_INT);
    $tamano = filter_input(INPUT_POST, 'tamano', FILTER_VALIDATE_INT);
    $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $estado = filter_input(INPUT_POST, 'estado', FILTER_VALIDATE_INT);

    // Las validaciones de "obligatorio" se hacen aquí
    if ($numero === false || $numero === '') {
        $errores[] = "El número de habitación es obligatorio.";
    }
    if ($costo === false) {
        $errores[] = "El costo de reserva debe ser un número positivo o cero.";
    }
    if ($tipoHabitacion === false) {
        $errores[] = "El tipo de habitación es obligatorio.";
    }
    if ($tamano === false) {
        $errores[] = "El tamaño de la habitación es obligatorio.";
    }
    if ($capacidad === false) {
        $errores[] = "La capacidad debe ser un número positivo.";
    }
    if ($estado === false) {
        $errores[] = "El estado es obligatorio.";
    }
    
    // Devolvemos el array de errores, que estará vacío si no hay errores
    return [
        'errores' => $errores,
        'datos' => [
            'numero' => $numero,
            'costo' => $costo,
            'tipoHabitacion' => $tipoHabitacion,
            'tamano' => $tamano,
            'capacidad' => $capacidad,
            'estado' => $estado
        ]
    ];
}

$accion = $_REQUEST['accion'] ?? 'listar';

switch ($accion) {
    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numero = trim($_POST['numero'] ?? '');
            $costo = trim($_POST['precio'] ?? '');
            $tipo = trim($_POST['tipo'] ?? '');
            $tamano = trim($_POST['tamano'] ?? '');
            $capacidad = trim($_POST['capacidad'] ?? '');
            $info = trim($_POST['info'] ?? '');
            $estado = trim($_POST['estado'] ?? '');

            $errores = [];
            if ($numero === '' || !is_numeric($numero) || intval($numero) <= 0) $errores[] = "El número de habitación es inválido.";
            if ($costo === '' || !is_numeric($costo) || floatval($costo) < 0) $errores[] = "El costo debe ser un número positivo o cero.";
            if ($tipo === '') $errores[] = "El tipo de habitación es obligatorio.";
            if ($tamano === '') $errores[] = "El tamaño de la habitación es obligatorio.";
            if ($capacidad === '' || !is_numeric($capacidad) || intval($capacidad) <= 0) $errores[] = "La capacidad debe ser un número positivo.";
            if ($estado === '') $errores[] = "El estado es obligatorio.";

            if ($errores) {
                $_SESSION['errores'] = $errores;
                header("Location: ../../../app/views/Habitaciones/editarHab.php?numero=$numero");
                exit;
            }

            try {
                $sql = "UPDATE tp_habitaciones SET costo=?, tipoHabitacion=?, tamano=?, capacidad=?, informacionAdicional=?, estado=? WHERE numero=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$costo, $tipo, $tamano, $capacidad, $info, $estado, $numero]);
                $_SESSION['exito'] = "Habitación actualizada exitosamente.";
                header("Location: habitacionController.php?accion=listar&exito=1");
                exit;
            } catch (PDOException $e) {
                $_SESSION['errores'] = ["Error al actualizar: " . $e->getMessage()];
                header("Location: ../../../app/views/Habitaciones/editarHab.php?numero=$numero");
                exit;
            }
        }
        break;
    case 'eliminar':
        $numero = filter_input(INPUT_GET, 'numero', FILTER_SANITIZE_STRING);
        if ($numero === false || $numero === '') {
            $_SESSION['errores'] = ["Número de habitación inválido."];
            header("Location: habitacionController.php?accion=listar");
            exit;
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM tp_habitaciones WHERE numero = ?");
            $stmt->execute([$numero]);
            $_SESSION['exito'] = "Habitación eliminada exitosamente.";
        } catch (PDOException $e) {
            $_SESSION['errores'] = ["Error al eliminar: " . $e->getMessage()];
        }
        header("Location: habitacionController.php?accion=listar");
        exit;
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validacion = validateHabitacionData();

            if (count($validacion['errores']) > 0) {
                $_SESSION['errores'] = $validacion['errores'];
                header("Location: habitacionController.php?accion=listar"); // Redirige de vuelta al formulario
                exit;
            }

            extract($validacion['datos']);

            try {
                $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, tamano, estado) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$numero, $costo, $capacidad, $tipoHabitacion, $tamano, $estado]);
                
                $_SESSION['exito'] = "Habitación creada exitosamente.";
                header("Location: habitacionController.php?accion=listar");
                exit;
            } catch (PDOException $e) {
                $_SESSION['errores'] = ["Error al guardar: " . $e->getMessage()];
                header("Location: ../../../app/views/Habitaciones/dashboardHab.php");
                exit;
            }
        }
        break;

    case 'editar':
        $numero = filter_input(INPUT_GET, 'numero', FILTER_SANITIZE_STRING);
        if ($numero === false || $numero === '') {
            $_SESSION['errores'] = ["Número de habitación inválido."];
            header("Location: habitacionController.php?accion=listar");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM tp_habitaciones WHERE numero=?");
        $stmt->execute([$numero]);
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
        include '../../../app/views/Habitaciones/editarHab.php';
        break;

    case 'listar':
    default:
        // Lógica de listado, no requiere cambios de seguridad mayores
        $sql = "SELECT h.*, t.descripcion AS tipo_desc, tm.descripcion AS tamano_desc, e.descripcion AS estado_desc
                FROM tp_habitaciones h
                JOIN td_tipohabitacion t ON h.tipoHabitacion = t.id
                JOIN td_tamano tm ON h.tamano = tm.id
                JOIN td_estado e ON h.estado = e.id
                ORDER BY h.numero ASC";
        $stmt = $pdo->query($sql);
        $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Limpiar mensajes de sesión para la próxima carga
        
        
        include '../../../app/views/Habitaciones/dashboardHab.php';
        break;
}