<?php

require_once '../../config/conexionGlobal.php';

nuevaHabitacion();

function nuevaHabitacion() {

    $db = conexionDB();

    try {
        $db->beginTransaction();

        $numeroHabitacion = $_POST["numeroNewHab"];
        $costoHabitacion = $_POST["costoNewHab"];
        $capacidad = $_POST["capacidadPersonasNewHab"];
        $tipoHabitacion = $_POST["tipoNewHab"];
        $descripcionHabitacion = $_POST["descripcionNewHab"];
        $estadoHabitacion = 'Disponible';
        $estadoMantenimiento = 'Inactivo';
        
        $rutaFoto = null; // Inicializamos la ruta de la foto como nula

        // 1. Manejar la subida de la imagen
        if (isset($_FILES['fotoNewHab']) && $_FILES['fotoNewHab']['error'] == UPLOAD_ERR_OK) {
            
            $nombreArchivo = uniqid() . '_' . $_FILES['fotoNewHab']['name'];
            $directorioDestino = '../../public/assets/img/habitaciones/';
            $rutaCompleta = $directorioDestino . $nombreArchivo;

            // Asegurarse de que el directorio de destino existe
            if (!is_dir($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            // Mover el archivo temporal al destino final
            if (move_uploaded_file($_FILES['fotoNewHab']['tmp_name'], $rutaCompleta)) {
                // Si la subida fue exitosa, guardamos la ruta relativa a la carpeta raíz del proyecto
                $rutaFoto = 'public/assets/img/habitaciones/' . $nombreArchivo;
            } else {
                throw new Exception("Error al mover el archivo subido.");
            }
        }
        
        // 2. Preparar y ejecutar la consulta SQL
        $sql = "INSERT INTO tp_habitaciones (numero, costo, capacidad, tipoHabitacion, foto, descripcion, estado, estadoMantenimiento) VALUES (:numero, :costo, :capacidad, :tipoHabitacion, :foto, :descripcion, :estado, :estadoMantenimiento)";
        
        $stmt = $db->prepare($sql);

        $stmt->bindParam(':numero', $numeroHabitacion, PDO::PARAM_STR);
        $stmt->bindParam(':costo', $costoHabitacion, PDO::PARAM_STR);
        $stmt->bindParam(':capacidad', $capacidad, PDO::PARAM_INT);
        $stmt->bindParam(':tipoHabitacion', $tipoHabitacion, PDO::PARAM_INT);
        
        // Vinculamos la ruta de la foto como un string
        $stmt->bindParam(':foto', $rutaFoto, PDO::PARAM_STR);
        
        $stmt->bindParam(':descripcion', $descripcionHabitacion, PDO::PARAM_STR);
        $stmt->bindParam(':estado', $estadoHabitacion, PDO::PARAM_STR);
        $stmt->bindParam(':estadoMantenimiento', $estadoMantenimiento, PDO::PARAM_STR);
        
        $stmt->execute();
        
        $db->commit();

        header("Location: ../views/verHabitaciones.php");
        exit();

    } catch (PDOException $e) {
        $db->rollBack();
        echo "Error de base de datos: " . $e->getMessage();
    } catch (Exception $e) {
        $db->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
?>