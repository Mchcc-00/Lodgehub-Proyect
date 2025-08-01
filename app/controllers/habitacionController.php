<?php
// controllers/habitacionController.php
require_once '../../config/conexionGlobal.php';

$accion = $_REQUEST['accion'] ?? 'listar';

switch ($accion) {
    case 'crear':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Recoger y limpiar datos
            $numero = trim($_POST['numero'] ?? '');
            $costo = trim($_POST['costo'] ?? '');
            $tipoHabitacion = intval($_POST['tipoHabitacion'] ?? 0);
            $tamano = intval($_POST['tamano'] ?? 0);
            $capacidad = trim($_POST['capacidad'] ?? '');
            $estado = intval($_POST['estado'] ?? 0);

            $errores = [];


            // Validaciones
            if ($numero === '') {
                $errores[] = "El número de habitación es obligatorio.";
            }

            if ($costo === '') {
                $errores[] = "El costo de reserva es obligatorio.";
            } elseif (!is_numeric($costo) || floatval($costo) <= 0) {
                $errores[] = "El costo de reserva debe ser un número positivo.";
            }

            if ($tipoHabitacion <= 0) {
                $errores[] = "El tipo de habitación es obligatorio.";
            }

            if ($tamano <= 0) {
                $errores[] = "El tamaño de la habitación es obligatorio.";
            }

            if ($capacidad === '') {
                $errores[] = "La capacidad es obligatoria.";
            } elseif (!is_numeric($capacidad) || intval($capacidad) <= 0) {
                $errores[] = "La capacidad debe ser un número positivo.";
            }

            if ($estado <= 0) {
                $errores[] = "El estado es obligatorio.";
            }

            if (count($errores) > 0) {
                // Mostrar errores
                foreach ($errores as $error) {
                    echo "<p style='color:red;'>$error</p>";
                }
                echo "<p><a href='../views/dashboardHab.php'>Volver al formulario</a></p>";
                exit;
            }

            // GUARDAR EN LA BASE DE DATOS CON PDO
            try {
                $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, tamano, estado) VALUES (?, ?, ?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$numero, $costo, $capacidad, $tipoHabitacion, $tamano, $estado]);
                // Redirigir o mostrar mensaje de éxito
                header("Location: habitacionController.php?accion=listar&exito=1");
                exit;
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error al guardar: " . $e->getMessage() . "</p>";
                echo "<p><a href='../views/dashboardHab.php'>Volver al formulario</a></p>";
                exit;
            }
        }
        break;

    case 'editar':
        $numero = $_GET['numero'] ?? '';
        if ($numero === '') {
            echo "Número de habitación inválido.";
            exit;
        }
        $stmt = $pdo->prepare("SELECT * FROM tp_habitaciones WHERE numero=?");
        $stmt->execute([$numero]);
        $habitacion = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$habitacion) {
            echo "Habitación no encontrada.";
            exit;
        }
        include '../views/editarHab.php';
        break;

    case 'actualizar':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numero = trim($_POST['numero'] ?? '');
            $costo = trim($_POST['costo'] ?? '');
            $tipoHabitacion = intval($_POST['tipoHabitacion'] ?? 0);
            $tamano = intval($_POST['tamano'] ?? 0);
            $capacidad = trim($_POST['capacidad'] ?? '');
            $estado = intval($_POST['estado'] ?? 0);

            $errores = [];
            if ($numero === '') $errores[] = "El número de habitación es obligatorio.";
            if ($costo === '' || !is_numeric($costo) || floatval($costo) < 0) $errores[] = "El costo debe ser un número positivo o cero.";
            if ($tipoHabitacion <= 0) $errores[] = "El tipo de habitación es obligatorio.";
            if ($tamano <= 0) $errores[] = "El tamaño de la habitación es obligatorio.";
            if ($capacidad === '' || !is_numeric($capacidad) || intval($capacidad) <= 0) $errores[] = "La capacidad debe ser un número positivo.";
            if ($estado <= 0) $errores[] = "El estado es obligatorio.";

            if ($errores) {
                foreach ($errores as $error) echo "<p style='color:red;'>$error</p>";
                echo "<p><a href='../views/editarHab.php?numero=$numero'>Volver</a></p>";
                exit;
            }

            try {
                $sql = "UPDATE tp_habitaciones SET costo=?, capacidad=?, tipoHabitacion=?, tamano=?, estado=? WHERE numero=?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$costo, $capacidad, $tipoHabitacion, $tamano, $estado, $numero]);
                header("Location: habitacionController.php?accion=listar&exito=1");
                exit;
            } catch (PDOException $e) {
                echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
                exit;
            }
        }
        break;

    case 'eliminar':
        $numero = $_GET['numero'] ?? '';
        try {
            $stmt = $pdo->prepare("DELETE FROM tp_habitaciones WHERE numero=?");
            $stmt->execute([$numero]);
            header("Location: habitacionController.php?accion=listar&exito=1");
            exit;
        } catch (PDOException $e) {
            echo "<p style='color:red;'>Error: " . $e->getMessage() . "</p>";
            exit;
        }
        break;

    case 'listar':
    default:
        // Listar habitaciones ordenadas por número de menor a mayor, con JOIN para mostrar descripciones
        $sql = "SELECT h.*, t.descripcion AS tipo_desc, tm.descripcion AS tamano_desc, e.descripcion AS estado_desc
                FROM tp_habitaciones h
                JOIN td_tipohabitacion t ON h.tipoHabitacion = t.id
                JOIN td_tamano tm ON h.tamano = tm.id
                JOIN td_estado e ON h.estado = e.id
                ORDER BY h.numero ASC";
        $stmt = $pdo->query($sql);
        $habitaciones = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include '../views/dashboardHab.php';
        break;
}
