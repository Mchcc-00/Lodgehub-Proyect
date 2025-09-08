<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// Incluir conexión a base de datos
require_once __DIR__ . '/../../config/conexionGlobal.php';

// Función para enviar respuesta JSON
function sendResponse($success, $message = '', $data = null) {
    $response = ['success' => $success];
    if ($message) $response['message'] = $message;
    if ($data !== null) $response = array_merge($response, $data);
    echo json_encode($response);
    exit;
}

// Función para validar datos de mantenimiento
function validarDatosMantenimiento($datos, $isUpdate = false) {
    $errores = [];
    
    // Campos requeridos
    $camposRequeridos = ['id_habitacion', 'tipo', 'problemaDescripcion', 'frecuencia', 'cantFrecuencia', 'prioridad', 'numDocumento', 'estado', 'id_hotel'];
    if ($isUpdate) $camposRequeridos[] = 'id';
    
    foreach ($camposRequeridos as $campo) {
        if (!isset($datos[$campo]) || trim($datos[$campo]) === '') {
            $errores[] = "El campo {$campo} es obligatorio";
        }
    }
    
    if (!empty($errores)) return $errores;
    
    // Validar valores específicos
    $tiposValidos = ['Limpieza', 'Estructura', 'Eléctrico', 'Otro'];
    if (!in_array($datos['tipo'], $tiposValidos)) {
        $errores[] = 'Tipo de mantenimiento no válido';
    }
    
    $frecuenciasValidas = ['Sí', 'No'];
    if (!in_array($datos['frecuencia'], $frecuenciasValidas)) {
        $errores[] = 'Valor de frecuencia no válido';
    }
    
    $cantFrecuenciasValidas = ['Diario', 'Semanal', 'Quincenal', 'Mensual'];
    if (!in_array($datos['cantFrecuencia'], $cantFrecuenciasValidas)) {
        $errores[] = 'Cantidad de frecuencia no válida';
    }
    
    $prioridadesValidas = ['Bajo', 'Alto'];
    if (!in_array($datos['prioridad'], $prioridadesValidas)) {
        $errores[] = 'Prioridad no válida';
    }
    
    $estadosValidos = ['Pendiente', 'Finalizado'];
    if (!in_array($datos['estado'], $estadosValidos)) {
        $errores[] = 'Estado no válido';
    }
    
    // Validar longitud de la descripción
    $descripcion = trim($datos['problemaDescripcion']);
    if (strlen($descripcion) < 5) {
        $errores[] = 'La descripción debe tener al menos 5 caracteres';
    }
    if (strlen($descripcion) > 50) {
        $errores[] = 'La descripción no puede exceder 50 caracteres';
    }
    
    return $errores;
}

try {
    // Obtener la acción del parámetro GET o del cuerpo JSON
    $accion = $_GET['accion'] ?? '';
    $id_hotel = $_GET['id_hotel'] ?? 0;
    
    // Si es POST, obtener datos del cuerpo JSON
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        if ($input) {
            if (isset($input['accion'])) {
                $accion = $input['accion'];
            }
            if (isset($input['id_hotel'])) {
                $id_hotel = $input['id_hotel'];
            }
        }
    }
    
    if (!$id_hotel || $id_hotel <= 0) {
        throw new Exception('ID de hotel no válido');
    }
    
    switch ($accion) {
        case 'obtener_habitaciones':
            // Obtener habitaciones del hotel
            $query = "SELECT id, numero FROM tp_habitaciones WHERE id_hotel = ? ORDER BY numero ASC";
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_hotel);
            $stmt->execute();
            
            $resultado = $stmt->get_result();
            $habitaciones = [];
            
            while ($row = $resultado->fetch_assoc()) {
                $habitaciones[] = [
                    'id' => (int)$row['id'],
                    'numero' => $row['numero']
                ];
            }
            
            sendResponse(true, '', ['habitaciones' => $habitaciones, 'total' => count($habitaciones)]);
            break;
            
        case 'obtener_usuarios':
            // Obtener usuarios que pueden ser responsables
            $query = "SELECT u.numDocumento, u.nombres, u.apellidos 
                        FROM tp_usuarios u 
                        WHERE u.id_hotel = ? 
                        AND u.estado = 'Activo'
                        ORDER BY u.nombres ASC, u.apellidos ASC";
            
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_hotel);
            $stmt->execute();
            
            $resultado = $stmt->get_result();
            $usuarios = [];
            
            while ($row = $resultado->fetch_assoc()) {
                $usuarios[] = [
                    'numDocumento' => $row['numDocumento'],
                    'nombres' => $row['nombres'],
                    'apellidos' => $row['apellidos']
                ];
            }
            
            sendResponse(true, '', ['usuarios' => $usuarios, 'total' => count($usuarios)]);
            break;
            
        case 'obtener_mantenimientos':
            // Obtener mantenimientos del hotel
            $query = "SELECT 
                        m.id,
                        m.id_habitacion,
                        h.numero as numero_habitacion,
                        m.tipo,
                        m.problemaDescripcion,
                        m.fechaRegistro,
                        m.ultimaActualizacion,
                        m.frecuencia,
                        m.cantFrecuencia,
                        m.prioridad,
                        m.numDocumento,
                        CONCAT(u.nombres, ' ', u.apellidos) as nombre_responsable,
                        m.estado
                        FROM tp_mantenimiento m
                        INNER JOIN tp_habitaciones h ON m.id_habitacion = h.id
                        INNER JOIN tp_usuarios u ON m.numDocumento = u.numDocumento
                        WHERE m.id_hotel = ?
                        ORDER BY 
                        CASE m.estado 
                            WHEN 'Pendiente' THEN 1 
                            WHEN 'Finalizado' THEN 2 
                        END,
                        CASE m.prioridad 
                            WHEN 'Alto' THEN 1 
                            WHEN 'Bajo' THEN 2 
                        END,
                        m.fechaRegistro DESC";
            
            $stmt = $conexion->prepare($query);
            $stmt->bind_param("i", $id_hotel);
            $stmt->execute();
            
            $resultado = $stmt->get_result();
            $mantenimientos = [];
            
            while ($row = $resultado->fetch_assoc()) {
                $mantenimientos[] = [
                    'id' => (int)$row['id'],
                    'id_habitacion' => (int)$row['id_habitacion'],
                    'numero_habitacion' => $row['numero_habitacion'],
                    'tipo' => $row['tipo'],
                    'problemaDescripcion' => $row['problemaDescripcion'],
                    'fechaRegistro' => $row['fechaRegistro'],
                    'ultimaActualizacion' => $row['ultimaActualizacion'],
                    'frecuencia' => $row['frecuencia'],
                    'cantFrecuencia' => $row['cantFrecuencia'],
                    'prioridad' => $row['prioridad'],
                    'numDocumento' => $row['numDocumento'],
                    'nombre_responsable' => $row['nombre_responsable'],
                    'estado' => $row['estado']
                ];
            }
            
            sendResponse(true, '', ['mantenimientos' => $mantenimientos, 'total' => count($mantenimientos)]);
            break;
            
        case 'crear':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                throw new Exception('Datos no válidos');
            }
            
            // Validar datos
            $errores = validarDatosMantenimiento($input);
            if (!empty($errores)) {
                throw new Exception(implode(', ', $errores));
            }
            
            // Validar que la habitación existe y pertenece al hotel
            $queryHab = "SELECT id FROM tp_habitaciones WHERE id = ? AND id_hotel = ?";
            $stmtHab = $conexion->prepare($queryHab);
            $stmtHab->bind_param("ii", $input['id_habitacion'], $input['id_hotel']);
            $stmtHab->execute();
            if ($stmtHab->get_result()->num_rows === 0) {
                throw new Exception('La habitación seleccionada no es válida');
            }
            $stmtHab->close();
            
            // Validar que el usuario existe
            $queryUser = "SELECT numDocumento FROM tp_usuarios WHERE numDocumento = ? AND id_hotel = ? AND estado = 'Activo'";
            $stmtUser = $conexion->prepare($queryUser);
            $stmtUser->bind_param("si", $input['numDocumento'], $input['id_hotel']);
            $stmtUser->execute();
            if ($stmtUser->get_result()->num_rows === 0) {
                throw new Exception('El usuario responsable no es válido');
            }
            $stmtUser->close();
            
            // Insertar mantenimiento
            $queryInsert = "INSERT INTO tp_mantenimiento (
                                id_habitacion, tipo, problemaDescripcion, frecuencia, cantFrecuencia, 
                                prioridad, numDocumento, estado, id_hotel, fechaRegistro, ultimaActualizacion
                            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
            
            $stmt = $conexion->prepare($queryInsert);
            $stmt->bind_param(
                "issssssis", 
                $input['id_habitacion'], $input['tipo'], trim($input['problemaDescripcion']),
                $input['frecuencia'], $input['cantFrecuencia'], $input['prioridad'],
                $input['numDocumento'], $input['estado'], $input['id_hotel']
            );
            
            if ($stmt->execute()) {
                sendResponse(true, 'Tarea de mantenimiento creada exitosamente', ['id' => $conexion->insert_id]);
            } else {
                throw new Exception('Error al crear la tarea de mantenimiento: ' . $stmt->error);
            }
            break;
            
        case 'actualizar':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                throw new Exception('Datos no válidos');
            }
            
            // Validar datos
            $errores = validarDatosMantenimiento($input, true);
            if (!empty($errores)) {
                throw new Exception(implode(', ', $errores));
            }
            
            if (!is_numeric($input['id']) || $input['id'] <= 0) {
                throw new Exception('ID de mantenimiento no válido');
            }
            
            // Verificar que el mantenimiento existe
            $queryExiste = "SELECT id FROM tp_mantenimiento WHERE id = ? AND id_hotel = ?";
            $stmtExiste = $conexion->prepare($queryExiste);
            $stmtExiste->bind_param("ii", $input['id'], $input['id_hotel']);
            $stmtExiste->execute();
            if ($stmtExiste->get_result()->num_rows === 0) {
                throw new Exception('La tarea de mantenimiento no existe');
            }
            $stmtExiste->close();
            
            // Validar habitación y usuario
            $queryHab = "SELECT id FROM tp_habitaciones WHERE id = ? AND id_hotel = ?";
            $stmtHab = $conexion->prepare($queryHab);
            $stmtHab->bind_param("ii", $input['id_habitacion'], $input['id_hotel']);
            $stmtHab->execute();
            if ($stmtHab->get_result()->num_rows === 0) {
                throw new Exception('La habitación seleccionada no es válida');
            }
            $stmtHab->close();
            
            $queryUser = "SELECT numDocumento FROM tp_usuarios WHERE numDocumento = ? AND id_hotel = ? AND estado = 'Activo'";
            $stmtUser = $conexion->prepare($queryUser);
            $stmtUser->bind_param("si", $input['numDocumento'], $input['id_hotel']);
            $stmtUser->execute();
            if ($stmtUser->get_result()->num_rows === 0) {
                throw new Exception('El usuario responsable no es válido');
            }
            $stmtUser->close();
            
            // Actualizar mantenimiento
            $queryUpdate = "UPDATE tp_mantenimiento SET 
                                id_habitacion = ?, tipo = ?, problemaDescripcion = ?, frecuencia = ?, 
                                cantFrecuencia = ?, prioridad = ?, numDocumento = ?, estado = ?, 
                                ultimaActualizacion = NOW()
                            WHERE id = ? AND id_hotel = ?";
            
            $stmt = $conexion->prepare($queryUpdate);
            $stmt->bind_param(
                "issssssiil", 
                $input['id_habitacion'], $input['tipo'], trim($input['problemaDescripcion']),
                $input['frecuencia'], $input['cantFrecuencia'], $input['prioridad'],
                $input['numDocumento'], $input['estado'], $input['id'], $input['id_hotel']
            );
            
            if ($stmt->execute()) {
                sendResponse(true, 'Tarea de mantenimiento actualizada exitosamente');
            } else {
                throw new Exception('Error al actualizar la tarea de mantenimiento: ' . $stmt->error);
            }
            break;
            
        case 'eliminar':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || !isset($input['id'])) {
                throw new Exception('ID de mantenimiento no proporcionado');
            }
            
            if (!is_numeric($input['id']) || $input['id'] <= 0) {
                throw new Exception('ID de mantenimiento no válido');
            }
            
            $id_mantenimiento = (int)$input['id'];
            
            // Verificar que existe
            $queryVerificar = "SELECT id FROM tp_mantenimiento WHERE id = ? AND id_hotel = ?";
            $stmtVerificar = $conexion->prepare($queryVerificar);
            $stmtVerificar->bind_param("ii", $id_mantenimiento, $id_hotel);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->get_result()->num_rows === 0) {
                throw new Exception('La tarea de mantenimiento no existe');
            }
            $stmtVerificar->close();
            
            // Eliminar
            $queryEliminar = "DELETE FROM tp_mantenimiento WHERE id = ? AND id_hotel = ?";
            $stmt = $conexion->prepare($queryEliminar);
            $stmt->bind_param("ii", $id_mantenimiento, $id_hotel);
            
            if ($stmt->execute()) {
                sendResponse(true, 'Tarea de mantenimiento eliminada exitosamente');
            } else {
                throw new Exception('Error al eliminar la tarea de mantenimiento: ' . $stmt->error);
            }
            break;
            
        // MANEJO ESPECIAL: Si no hay acción específica, devolver todos los datos
        case '':
        default:
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                // Obtener habitaciones
                $queryHab = "SELECT id, numero FROM tp_habitaciones WHERE id_hotel = ? ORDER BY numero ASC";
                $stmtHab = $conexion->prepare($queryHab);
                $stmtHab->bind_param("i", $id_hotel);
                $stmtHab->execute();
                $resultadoHab = $stmtHab->get_result();
                $habitaciones = [];
                while ($row = $resultadoHab->fetch_assoc()) {
                    $habitaciones[] = ['id' => (int)$row['id'], 'numero' => $row['numero']];
                }
                
                // Obtener usuarios
                $queryUser = "SELECT u.numDocumento, u.nombres, u.apellidos FROM tp_usuarios u WHERE u.id_hotel = ? AND u.estado = 'Activo' ORDER BY u.nombres ASC";
                $stmtUser = $conexion->prepare($queryUser);
                $stmtUser->bind_param("i", $id_hotel);
                $stmtUser->execute();
                $resultadoUser = $stmtUser->get_result();
                $usuarios = [];
                while ($row = $resultadoUser->fetch_assoc()) {
                    $usuarios[] = ['numDocumento' => $row['numDocumento'], 'nombres' => $row['nombres'], 'apellidos' => $row['apellidos']];
                }
                
                // Obtener mantenimientos
                $queryMant = "SELECT m.id, m.id_habitacion, h.numero as numero_habitacion, m.tipo, m.problemaDescripcion, m.fechaRegistro, m.ultimaActualizacion, m.frecuencia, m.cantFrecuencia, m.prioridad, m.numDocumento, CONCAT(u.nombres, ' ', u.apellidos) as nombre_responsable, m.estado FROM tp_mantenimiento m INNER JOIN tp_habitaciones h ON m.id_habitacion = h.id INNER JOIN tp_usuarios u ON m.numDocumento = u.numDocumento WHERE m.id_hotel = ? ORDER BY CASE m.estado WHEN 'Pendiente' THEN 1 WHEN 'Finalizado' THEN 2 END, CASE m.prioridad WHEN 'Alto' THEN 1 WHEN 'Bajo' THEN 2 END, m.fechaRegistro DESC";
                $stmtMant = $conexion->prepare($queryMant);
                $stmtMant->bind_param("i", $id_hotel);
                $stmtMant->execute();
                $resultadoMant = $stmtMant->get_result();
                $mantenimientos = [];
                while ($row = $resultadoMant->fetch_assoc()) {
                    $mantenimientos[] = [
                        'id' => (int)$row['id'], 'id_habitacion' => (int)$row['id_habitacion'],
                        'numero_habitacion' => $row['numero_habitacion'], 'tipo' => $row['tipo'],
                        'problemaDescripcion' => $row['problemaDescripcion'], 'fechaRegistro' => $row['fechaRegistro'],
                        'ultimaActualizacion' => $row['ultimaActualizacion'], 'frecuencia' => $row['frecuencia'],
                        'cantFrecuencia' => $row['cantFrecuencia'], 'prioridad' => $row['prioridad'],
                        'numDocumento' => $row['numDocumento'], 'nombre_responsable' => $row['nombre_responsable'],
                        'estado' => $row['estado']
                    ];
                }
                
                sendResponse(true, '', ['habitaciones' => $habitaciones, 'usuarios' => $usuarios, 'mantenimientos' => $mantenimientos]);
            } else {
                throw new Exception('Acción no válida');
            }
            break;
    }
    
} catch (Exception $e) {
    sendResponse(false, $e->getMessage());
} finally {
    // Cerrar conexiones
    if (isset($stmt)) {
        $stmt->close();
    }
    if (isset($conexion)) {
        $conexion->close();
    }
}
?>