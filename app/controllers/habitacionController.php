
<?php
session_start();

require_once '../../config/conexionGlobal.php';

$pdo = conexionDB();

// Controlador de habitaciones
require_once '../models/Habitacion.php';

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
    $informacionAdicional = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);

    // Las validaciones de "obligatorio" se hacen aquí
    if ($numero === false || $numero === '') {
        $errores[] = "El número de habitación es obligatorio.";
    }
    if ($costo === false) {
        $errores[] = "El costo de reserva debe ser un número positivo o cero.";
    }
    if ($tipoHabitacion === false) {
        $errores[] = "El tipo de habitación es obligatorio.";
    } else {
        // Validar que el tipo de habitación exista en la tabla td_tipohabitacion
        global $pdo;
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM td_tipohabitacion WHERE id = ?");
        $stmt->execute([$tipoHabitacion]);
        if ($stmt->fetchColumn() == 0) {
            $errores[] = "El tipo de habitación seleccionado no existe.";
        }
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
            'estado' => $estado,
            'informacionAdicional' => $informacionAdicional
        ]
    ];
}

$accion = $_REQUEST['accion'] ?? 'listar';

switch ($accion) {

    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numero_original = filter_input(INPUT_POST, 'numero_original', FILTER_SANITIZE_STRING);
            $numero = filter_input(INPUT_POST, 'numero', FILTER_SANITIZE_STRING);
            $costo = filter_input(INPUT_POST, 'costo', FILTER_VALIDATE_FLOAT);
            $tipoHabitacion = filter_input(INPUT_POST, 'tipoHabitacion', FILTER_VALIDATE_INT);
            $tamano = filter_input(INPUT_POST, 'tamano', FILTER_VALIDATE_INT);
            $capacidad = filter_input(INPUT_POST, 'capacidad', FILTER_VALIDATE_INT);
            $estado = filter_input(INPUT_POST, 'estado', FILTER_VALIDATE_INT);
            $informacionAdicional = filter_input(INPUT_POST, 'informacionAdicional', FILTER_SANITIZE_STRING);
            if ($informacionAdicional === null || $informacionAdicional === false) {
                $informacionAdicional = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
            }

            // Validación básica
            $errores = [];
            if ($numero === false || $numero === '') $errores[] = "El número de habitación es obligatorio.";
            if ($costo === false) $errores[] = "El costo de reserva debe ser un número válido.";
            if ($tipoHabitacion === false) $errores[] = "El tipo de habitación es obligatorio.";
            if ($tamano === false) $errores[] = "El tamaño es obligatorio.";
            if ($capacidad === false) $errores[] = "La capacidad es obligatoria.";
            if ($estado === false) $errores[] = "El estado es obligatorio.";

            if (count($errores) > 0) {
                $_SESSION['errores'] = $errores;
                header("Location: " . $_SERVER['PHP_SELF'] . "?accion=editar&numero=" . urlencode($numero_original));
                exit;
            }

            try {
                $sql = "UPDATE tp_habitaciones SET numero=?, costo=?, capacidad=?, tipoHabitacion=?, tamano=?, estado=?, informacionAdicional=? WHERE numero=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$numero, $costo, $capacidad, $tipoHabitacion, $tamano, $estado, $informacionAdicional, $numero_original]);
                $_SESSION['exito'] = "Habitación actualizada exitosamente.";
            } catch (PDOException $e) {
                $_SESSION['errores'] = ["Error al actualizar: " . $e->getMessage()];
            }
            header("Location: " . $_SERVER['PHP_SELF'] . "?accion=listar");
            exit;
        }
        break;
    case 'eliminar':
        $numero = filter_input(INPUT_GET, 'numero', FILTER_SANITIZE_STRING);
        if ($numero === false || $numero === '') {
            $_SESSION['errores'] = ["Número de habitación inválido."];
            header("Location: " . $_SERVER['PHP_SELF'] . "?accion=listar");
            exit;
        }
        try {
            $stmt = $pdo->prepare("DELETE FROM tp_habitaciones WHERE numero = ?");
            $stmt->execute([$numero]);
            $_SESSION['exito'] = "Habitación eliminada exitosamente.";
        } catch (PDOException $e) {
            $_SESSION['errores'] = ["Error al eliminar: " . $e->getMessage()];
        }
        header("Location: " . $_SERVER['PHP_SELF'] . "?accion=listar");
        exit;
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $validacion = validateHabitacionData();

            if (count($validacion['errores']) > 0) {
                $_SESSION['errores'] = $validacion['errores'];
                header("Location: ../views/Habitaciones/formHab.php"); // Redirige de vuelta al formulario
                exit;
            }

            extract($validacion['datos']);

            try {
                $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, tamano, estado, informacionAdicional) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$numero, $costo, $capacidad, $tipoHabitacion, $tamano, $estado, $informacionAdicional]);
                $_SESSION['exito'] = "Habitación creada exitosamente.";
                header("Location: " . $_SERVER['PHP_SELF'] . "?accion=listar");
                exit;
            } catch (PDOException $e) {
                $_SESSION['errores'] = ["Error al guardar: " . $e->getMessage()];
                header("Location: ../views/Habitaciones/formHab.php");
                exit;
            }
        }
        break;

    case 'editar':
        $numero = filter_input(INPUT_GET, 'numero', FILTER_SANITIZE_STRING);
        if ($numero === false || $numero === '') {
            $_SESSION['errores'] = ["Número de habitación inválido."];
            header("Location: " . $_SERVER['PHP_SELF'] . "?accion=listar");
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM tp_habitaciones WHERE numero=?");
        $stmt->execute([$numero]);
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
        include '../views/Habitaciones/editarHab.php';
        break;

    case 'listar':
    default:
        // Lógica de listado, no requiere cambios de seguridad mayores
        $sql = "SELECT * FROM tp_habitaciones ORDER BY numero ASC";
        $stmt = $pdo->query($sql);
        $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Limpiar mensajes de sesión para la próxima carga
        include '../views/Habitaciones/dashboardHab.php';
        break;
}