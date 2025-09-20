<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
// SOLUCIÓN: Corregir el nombre del archivo para que coincida con las mayúsculas/minúsculas (pqrsModel.php)
require_once '../models/pqrsModel.php';

class PqrsController {
    private $pqrsModel;

    public function __construct() {
        $this->pqrsModel = new PqrsModel();
    }

    public function crearPqrs() {
        header('Content-Type: application/json');
        
        try {
            // Validar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Validar que todos los campos requeridos estén presentes
            $camposRequeridos = [
                'tipo',
                'descripcion', 
                'numDocumento',
                'prioridad',
                'categoria',
                'id_hotel' // Campo requerido
            ];

            foreach ($camposRequeridos as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    throw new Exception("El campo {$campo} es requerido");
                }
            }

            // Sanitizar y validar datos
            $datos = [
                'tipo' => $this->sanitizarTexto($_POST['tipo']),
                'descripcion' => $this->sanitizarTexto($_POST['descripcion']),
                'numDocumento' => $this->sanitizarTexto($_POST['numDocumento']),
                'prioridad' => $this->sanitizarTexto($_POST['prioridad']),
                'categoria' => $this->sanitizarTexto($_POST['categoria']),
                'id_hotel' => intval($_POST['id_hotel']),
                'estado' => 'Pendiente' // Estado por defecto
            ];

            // Validaciones específicas
            $this->validarDatos($datos);

            // Verificar si el usuario existe
            if (!$this->pqrsModel->usuarioExiste($datos['numDocumento'])) {
                throw new Exception('El usuario con el documento proporcionado no existe');
            }

            // Crear la PQRS
            $resultado = $this->pqrsModel->crearPqrs($datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'PQRS creada exitosamente',
                    'data' => $datos
                ]);
            } else {
                throw new Exception('Error al crear la PQRS en la base de datos');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerPqrs() {
        header('Content-Type: application/json');
        
        try {
            // Soporte para paginación y filtros
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $registrosPorPagina = isset($_GET['registros']) ? max(1, min(50, intval($_GET['registros']))) : 10;
            $filtro = isset($_GET['filtro']) ? $this->sanitizarTexto($_GET['filtro']) : null;
            $id_hotel = isset($_GET['id_hotel']) ? intval($_GET['id_hotel']) : null;
            
            if (isset($_GET['paginado']) && $_GET['paginado'] === 'true') {
                $resultado = $this->pqrsModel->obtenerPqrsPaginadas($pagina, $registrosPorPagina, $filtro, $id_hotel);
                echo json_encode([
                    'success' => true,
                    'data' => $resultado
                ]);
            } else {
                $pqrs = $this->pqrsModel->obtenerTodasLasPqrs($filtro, $id_hotel);
                echo json_encode([
                    'success' => true,
                    'data' => $pqrs
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerPqrsPorId($id) {
        header('Content-Type: application/json');
        
        try {
            if (empty($id) || !is_numeric($id)) {
                throw new Exception('ID de PQRS es requerido y debe ser numérico');
            }

            $pqrs = $this->pqrsModel->obtenerPqrsPorId($id);
            
            if ($pqrs) {
                echo json_encode([
                    'success' => true,
                    'data' => $pqrs
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'PQRS no encontrada'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actualizarPqrs() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Validar que el ID esté presente
            if (!isset($_POST['id']) || empty(trim($_POST['id']))) {
                throw new Exception('ID de PQRS es requerido');
            }

            $id = intval($_POST['id']);

            // Verificar si la PQRS existe
            if (!$this->pqrsModel->pqrsExiste($id)) {
                throw new Exception('La PQRS no existe');
            }

            // Preparar datos para actualizar (solo los campos que se envíen)
            $datos = [];
            $camposPermitidos = ['tipo', 'descripcion', 'prioridad', 'categoria', 'estado', 'respuesta'];

            foreach ($camposPermitidos as $campo) {
                if (isset($_POST[$campo]) && $_POST[$campo] !== '') {
                    $datos[$campo] = $this->sanitizarTexto($_POST[$campo]);
                }
            }

            if (empty($datos)) {
                throw new Exception('No hay datos para actualizar');
            }

            // Si se está marcando como finalizado, agregar fecha de finalización
            if (isset($datos['estado']) && $datos['estado'] === 'Finalizado') {
                $datos['fechaFinalizacion'] = date('Y-m-d H:i:s');
            }

            // Validar datos
            $this->validarDatosActualizacion($datos);

            $resultado = $this->pqrsModel->actualizarPqrs($id, $datos);

            if ($resultado) {
                // Obtener los datos actualizados para devolverlos
                $pqrsActualizada = $this->pqrsModel->obtenerPqrsPorId($id);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'PQRS actualizada exitosamente',
                    'data' => $pqrsActualizada
                ]);
            } else {
                throw new Exception('Error al actualizar la PQRS');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminarPqrs() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            if (!isset($_POST['id']) || empty(trim($_POST['id']))) {
                throw new Exception('ID de PQRS es requerido');
            }

            $id = intval($_POST['id']);

            // Verificar si la PQRS existe
            if (!$this->pqrsModel->pqrsExiste($id)) {
                throw new Exception('La PQRS no existe');
            }

            $resultado = $this->pqrsModel->eliminarPqrs($id);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'PQRS eliminada exitosamente'
                ]);
            } else {
                throw new Exception('Error al eliminar la PQRS');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Buscar PQRS
    public function buscarPqrs() {
        header('Content-Type: application/json');
        
        try {
            $termino = isset($_GET['termino']) ? trim($_GET['termino']) : '';
            $id_hotel = isset($_GET['id_hotel']) ? intval($_GET['id_hotel']) : null;
            
            if (empty($termino)) {
                throw new Exception('Término de búsqueda es requerido');
            }

            if (strlen($termino) < 2 && strlen($termino) > 0) {
                throw new Exception('El término de búsqueda debe tener al menos 2 caracteres.');
            }

            $pqrs = $this->pqrsModel->buscarPqrs($termino, $id_hotel);
            
            echo json_encode([
                'success' => true,
                'data' => $pqrs,
                'total' => count($pqrs)
            ]);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    // Validar usuario
    public function validarUsuario() {
        header('Content-Type: application/json');
        
        try {
            $numDocumento = isset($_GET['numDocumento']) ? trim($_GET['numDocumento']) : '';
            
            if (empty($numDocumento)) {
                throw new Exception('Número de documento es requerido');
            }

            $usuario = $this->pqrsModel->obtenerUsuarioPorDocumento($numDocumento);
            
            if ($usuario) {
                echo json_encode([
                    'success' => true,
                    'data' => $usuario
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Usuario no encontrado'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function sanitizarTexto($texto) {
        return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
    }

    private function validarDatos($datos) {
        // Validar tipo
        $tiposPermitidos = ['Peticiones', 'Quejas', 'Reclamos', 'Sugerencias', 'Felicitaciones'];
        if (!in_array($datos['tipo'], $tiposPermitidos)) {
            throw new Exception('Tipo de PQRS no válido');
        }

        // Validar prioridad
        $prioridadesPermitidas = ['Bajo', 'Alto'];
        if (!in_array($datos['prioridad'], $prioridadesPermitidas)) {
            throw new Exception('Prioridad no válida');
        }

        // Validar categoría
        $categoriasPermitidas = ['Servicio', 'Habitación', 'Atención', 'Otro'];
        if (!in_array($datos['categoria'], $categoriasPermitidas)) {
            throw new Exception('Categoría no válida');
        }

        // Validar estado
        if (isset($datos['estado'])) {
            $estadosPermitidos = ['Pendiente', 'Finalizado'];
            if (!in_array($datos['estado'], $estadosPermitidos)) {
                throw new Exception('Estado no válido');
            }
        }

        // Validar número de documento
        if (strlen($datos['numDocumento']) < 5 || strlen($datos['numDocumento']) > 15) {
            throw new Exception('El número de documento debe tener entre 5 y 15 caracteres');
        }

        if (!preg_match('/^[0-9A-Za-z]+$/', $datos['numDocumento'])) {
            throw new Exception('El número de documento solo puede contener letras y números');
        }

        // Validar descripción
        if (strlen($datos['descripcion']) < 10 || strlen($datos['descripcion']) > 1000) {
            throw new Exception('La descripción debe tener entre 10 y 1000 caracteres');
        }
    }

    private function validarDatosActualizacion($datos) {
        // Validar campos individuales solo si están presentes
        if (isset($datos['tipo'])) {
            $tiposPermitidos = ['Peticiones', 'Quejas', 'Reclamos', 'Sugerencias', 'Felicitaciones'];
            if (!in_array($datos['tipo'], $tiposPermitidos)) {
                throw new Exception('Tipo de PQRS no válido');
            }
        }

        if (isset($datos['prioridad'])) {
            $prioridadesPermitidas = ['Bajo', 'Alto'];
            if (!in_array($datos['prioridad'], $prioridadesPermitidas)) {
                throw new Exception('Prioridad no válida');
            }
        }

        if (isset($datos['categoria'])) {
            $categoriasPermitidas = ['Servicio', 'Habitación', 'Atención', 'Otro'];
            if (!in_array($datos['categoria'], $categoriasPermitidas)) {
                throw new Exception('Categoría no válida');
            }
        }

        if (isset($datos['estado'])) {
            $estadosPermitidos = ['Pendiente', 'Finalizado'];
            if (!in_array($datos['estado'], $estadosPermitidos)) {
                throw new Exception('Estado no válido');
            }
        }

        if (isset($datos['descripcion'])) {
            if (strlen($datos['descripcion']) < 10 || strlen($datos['descripcion']) > 1000) {
                throw new Exception('La descripción debe tener entre 10 y 1000 caracteres');
            }
        }

        if (isset($datos['respuesta'])) {
            if (strlen($datos['respuesta']) > 1000) {
                throw new Exception('La respuesta no puede tener más de 1000 caracteres');
            }
        }
    }
}

// Manejo de rutas
if (isset($_GET['action'])) {
    $controller = new PqrsController();
    
    switch ($_GET['action']) {
        case 'crear':
            $controller->crearPqrs();
            break;
        case 'obtener':
            $controller->obtenerPqrs();
            break;
        case 'obtenerPorId':
            $id = $_GET['id'] ?? '';
            $controller->obtenerPqrsPorId($id);
            break;
        case 'actualizar':
            $controller->actualizarPqrs();
            break;
        case 'eliminar':
            $controller->eliminarPqrs();
            break;
        case 'buscar':
            $controller->buscarPqrs();
            break;
        case 'validarUsuario':
            $controller->validarUsuario();
            break;
        default:
            echo json_encode([
                'success' => false,
                'message' => 'Acción no válida'
            ]);
    }
} else {
    // Si no hay action, asumir que es crear (POST directo)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $controller = new PqrsController();
        $controller->crearPqrs();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    }
}
?>