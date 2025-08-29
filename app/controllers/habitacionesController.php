<?php
require_once '../models/habitacionesModel.php';

class RoomController {
    private $roomModel;
    
    public function __construct($database) {
        $this->roomModel = new Room($database);
    }
    
    // Mostrar lista de habitaciones
    public function list() {
        $rooms = $this->roomModel->getAllRooms();
        $roomTypes = $this->roomModel->getRoomTypes();
        include '../views/listaHabitaciones.php';
    }
    
    // Mostrar formulario de creación
    public function create() {
        $roomTypes = $this->roomModel->getRoomTypes();
        include '../views/crearHabitacion.php';
    }
    
    // Procesar creación de habitación
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $errors = [];
            
            // Validaciones
            $numero = trim($_POST['numero']);
            $costo = trim($_POST['costo']);
            $capacidad = trim($_POST['capacidad']);
            $tipoHabitacion = trim($_POST['tipoHabitacion']);
            
            if (empty($numero)) {
                $errors[] = "El número de habitación es obligatorio";
            } elseif ($this->roomModel->roomExists($numero)) {
                $errors[] = "El número de habitación ya existe";
            }
            
            if (empty($costo) || !is_numeric($costo) || $costo <= 0) {
                $errors[] = "El costo debe ser un número mayor a 0";
            }
            
            if (empty($capacidad) || !is_numeric($capacidad) || $capacidad <= 0) {
                $errors[] = "La capacidad debe ser un número mayor a 0";
            }
            
            if (empty($tipoHabitacion)) {
                $errors[] = "Debe seleccionar un tipo de habitación";
            }
            
            // Procesar imagen si se subió
            $fotoPath = null;
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['foto']['name'];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($extension, $allowed)) {
                    $newFilename = 'room_' . $numero . '_' . time() . '.' . $extension;
                    $uploadPath = 'uploads/rooms/' . $newFilename;
                    
                    // Crear directorio si no existe
                    if (!file_exists('uploads/rooms/')) {
                        mkdir('uploads/rooms/', 0777, true);
                    }
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                        $fotoPath = $uploadPath;
                    } else {
                        $errors[] = "Error al subir la imagen";
                    }
                } else {
                    $errors[] = "Solo se permiten archivos JPG, JPEG, PNG y GIF";
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'numero' => $numero,
                    'costo' => $costo,
                    'capacidad' => $capacidad,
                    'tipoHabitacion' => $tipoHabitacion,
                    'foto' => $fotoPath,
                    'descripcion' => trim($_POST['descripcion']),
                    'estado' => $_POST['estado'] ?? 'Disponible',
                    'descripcionMantenimiento' => trim($_POST['descripcionMantenimiento']),
                    'estadoMantenimiento' => $_POST['estadoMantenimiento'] ?? 'Activo'
                ];
                
                if ($this->roomModel->createRoom($data)) {
                    $_SESSION['success'] = "Habitación creada exitosamente";
                    header('Location: /../views/listaHabitaciones.php?controller=room&action=index');
                    exit;
                } else {
                    $errors[] = "Error al crear la habitación";
                }
            }
            
            $_SESSION['errors'] = $errors;
            $_SESSION['form_data'] = $_POST;
            header('Location:/../views/listaHabitaciones.php?controller=room&action=create');
            exit;
        }
    }
    
    // Mostrar formulario de edición
    public function edit() {
        $numero = $_GET['numero'] ?? '';
        $room = $this->roomModel->getRoomByNumber($numero);
        
        if (!$room) {
            $_SESSION['error'] = "Habitación no encontrada";
            header('Location: index.php?controller=room&action=index');
            exit;
        }
        
        $roomTypes = $this->roomModel->getRoomTypes();
        include 'views/rooms/edit.php';
    }
    
    // Procesar actualización de habitación
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numero = $_POST['numero_original'];
            $errors = [];
            
            // Validaciones similares al store
            $costo = trim($_POST['costo']);
            $capacidad = trim($_POST['capacidad']);
            $tipoHabitacion = trim($_POST['tipoHabitacion']);
            
            if (empty($costo) || !is_numeric($costo) || $costo <= 0) {
                $errors[] = "El costo debe ser un número mayor a 0";
            }
            
            if (empty($capacidad) || !is_numeric($capacidad) || $capacidad <= 0) {
                $errors[] = "La capacidad debe ser un número mayor a 0";
            }
            
            if (empty($tipoHabitacion)) {
                $errors[] = "Debe seleccionar un tipo de habitación";
            }
            
            // Procesar nueva imagen si se subió
            $room = $this->roomModel->getRoomByNumber($numero);
            $fotoPath = $room['foto']; // Mantener foto actual por defecto
            
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                $filename = $_FILES['foto']['name'];
                $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                if (in_array($extension, $allowed)) {
                    $newFilename = 'room_' . $numero . '_' . time() . '.' . $extension;
                    $uploadPath = 'uploads/rooms/' . $newFilename;
                    
                    if (move_uploaded_file($_FILES['foto']['tmp_name'], $uploadPath)) {
                        // Eliminar foto anterior si existe
                        if ($room['foto'] && file_exists($room['foto'])) {
                            unlink($room['foto']);
                        }
                        $fotoPath = $uploadPath;
                    } else {
                        $errors[] = "Error al subir la imagen";
                    }
                } else {
                    $errors[] = "Solo se permiten archivos JPG, JPEG, PNG y GIF";
                }
            }
            
            if (empty($errors)) {
                $data = [
                    'costo' => $costo,
                    'capacidad' => $capacidad,
                    'tipoHabitacion' => $tipoHabitacion,
                    'foto' => $fotoPath,
                    'descripcion' => trim($_POST['descripcion']),
                    'estado' => $_POST['estado'],
                    'descripcionMantenimiento' => trim($_POST['descripcionMantenimiento']),
                    'estadoMantenimiento' => $_POST['estadoMantenimiento']
                ];
                
                if ($this->roomModel->updateRoom($numero, $data)) {
                    $_SESSION['success'] = "Habitación actualizada exitosamente";
                    header('Location: index.php?controller=room&action=index');
                    exit;
                } else {
                    $errors[] = "Error al actualizar la habitación";
                }
            }
            
            $_SESSION['errors'] = $errors;
            header('Location: index.php?controller=room&action=edit&numero=' . $numero);
            exit;
        }
    }
    
    // Eliminar habitación
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $numero = $_POST['numero'];
            
            // Obtener información de la habitación para eliminar foto
            $room = $this->roomModel->getRoomByNumber($numero);
            
            if ($this->roomModel->deleteRoom($numero)) {
                // Eliminar foto si existe
                if ($room['foto'] && file_exists($room['foto'])) {
                    unlink($room['foto']);
                }
                $_SESSION['success'] = "Habitación eliminada exitosamente";
            } else {
                $_SESSION['error'] = "Error al eliminar la habitación";
            }
            
            header('Location: index.php?controller=room&action=index');
            exit;
        }
    }
    
    // Filtrar por estado
    public function filterByStatus() {
        $estado = $_GET['estado'] ?? '';
        if ($estado) {
            $rooms = $this->roomModel->getRoomsByStatus($estado);
        } else {
            $rooms = $this->roomModel->getAllRooms();
        }
        
        $roomTypes = $this->roomModel->getRoomTypes();
        include 'views/rooms/index.php';
    }
}
?>