<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once '../models/HuespedModel.php';

class HuespedController {
    private $huespedModel;

    public function __construct() {
        $this->huespedModel = new HuespedModel();
    }

    public function crearHuesped() {
        header('Content-Type: application/json');
        
        try {
            // Validar método HTTP
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Validar que todos los campos requeridos estén presentes
            $camposRequeridos = [
                'tipoDocumento',
                'numDocumento', 
                'nombres',
                'apellidos',
                'sexo',
                'numTelefono',
                'correo'
            ];

            foreach ($camposRequeridos as $campo) {
                if (!isset($_POST[$campo]) || empty(trim($_POST[$campo]))) {
                    throw new Exception("El campo {$campo} es requerido");
                }
            }

            // Sanitizar y validar datos
            $datos = [
                'tipoDocumento' => $this->sanitizarTexto($_POST['tipoDocumento']),
                'numDocumento' => $this->sanitizarTexto($_POST['numDocumento']),
                'nombres' => $this->sanitizarTexto($_POST['nombres']),
                'apellidos' => $this->sanitizarTexto($_POST['apellidos']),
                'sexo' => $this->sanitizarTexto($_POST['sexo']),
                'numTelefono' => $this->sanitizarTexto($_POST['numTelefono']),
                'correo' => $this->sanitizarEmail($_POST['correo'])
            ];

            // Validaciones específicas
            $this->validarDatos($datos);

            // Verificar si el huésped ya existe
            if ($this->huespedModel->existeHuesped($datos['numDocumento'])) {
                throw new Exception('Ya existe un huésped con este número de documento');
            }

            // Verificar si el correo ya está registrado
            if ($this->huespedModel->correoExiste($datos['correo'])) {
                throw new Exception('Ya existe un huésped con este correo electrónico');
            }

            // Crear el huésped
            $resultado = $this->huespedModel->crearHuesped($datos);

            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Huésped creado exitosamente',
                    'data' => $datos
                ]);
            } else {
                throw new Exception('Error al crear el huésped en la base de datos');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerHuespedes() {
        header('Content-Type: application/json');
        
        try {
            // NUEVO: Soporte para paginación
            $pagina = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
            $registrosPorPagina = isset($_GET['registros']) ? max(1, min(50, intval($_GET['registros']))) : 10;
            
            if (isset($_GET['paginado']) && $_GET['paginado'] === 'true') {
                $resultado = $this->huespedModel->obtenerHuespedesPaginados($pagina, $registrosPorPagina);
                echo json_encode([
                    'success' => true,
                    'data' => $resultado
                ]);
            } else {
                $huespedes = $this->huespedModel->obtenerTodosLosHuespedes();
                echo json_encode([
                    'success' => true,
                    'data' => $huespedes
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function obtenerHuesped($numDocumento) {
        header('Content-Type: application/json');
        
        try {
            if (empty($numDocumento)) {
                throw new Exception('Número de documento es requerido');
            }

            $huesped = $this->huespedModel->obtenerHuespedPorDocumento($numDocumento);
            
            if ($huesped) {
                echo json_encode([
                    'success' => true,
                    'data' => $huesped
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Huésped no encontrado'
                ]);
            }
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function actualizarHuesped() {
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Método no permitido');
            }

            // Validar que el número de documento esté presente
            if (!isset($_POST['numDocumento']) || empty(trim($_POST['numDocumento']))) {
                throw new Exception('Número de documento es requerido');
            }

            $numDocumento = $this->sanitizarTexto($_POST['numDocumento']);

            // Verificar si el huésped existe
            if (!$this->huespedModel->existeHuesped($numDocumento)) {
                throw new Exception('El huésped no existe');
            }

            // Preparar datos para actualizar (solo los campos que se envíen)
            $datos = [];
            $camposPermitidos = ['nombres', 'apellidos', 'numTelefono', 'correo', 'sexo'];

            foreach ($camposPermitidos as $campo) {
                if (isset($_POST[$campo]) && !empty(trim($_POST[$campo]))) {
                    if ($campo === 'correo') {
                        $datos[$campo] = $this->sanitizarEmail($_POST[$campo]);
                    } else {
                        $datos[$campo] = $this->sanitizarTexto($_POST[$campo]);
                    }
                }
            }

            if (empty($datos)) {
                throw new Exception('No hay datos para actualizar');
            }

            // Validar datos
            $this->validarDatosActualizacion($datos);

            // CORREGIDO: Verificar si el correo ya está registrado por otro huésped
            if (isset($datos['correo'])) {
                if ($this->huespedModel->correoExiste($datos['correo'], $numDocumento)) {
                    throw new Exception('Ya existe otro huésped con este correo electrónico');
                }
            }

            $resultado = $this->huespedModel->actualizarHuesped($numDocumento, $datos);

            if ($resultado) {
                // Obtener los datos actualizados para devolverlos
                $huespedActualizado = $this->huespedModel->obtenerHuespedPorDocumento($numDocumento);
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Huésped actualizado exitosamente',
                    'data' => $huespedActualizado
                ]);
            } else {
                throw new Exception('Error al actualizar el huésped');
            }

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    public function eliminarHuesped() {
    header('Content-Type: application/json');
    try {
        // Depuración: log del documento recibido
        // error_log('Intentando eliminar: ' . ($_POST['numDocumento'] ?? 'NO RECIBIDO'));

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new Exception('Método no permitido');
        }

        if (!isset($_POST['numDocumento']) || empty(trim($_POST['numDocumento']))) {
            error_log('No se recibió numDocumento en POST');
            throw new Exception('Número de documento es requerido');
        }

        $numDocumento = $this->sanitizarTexto($_POST['numDocumento']);
        error_log('Documento a eliminar (sanitizado): ' . $numDocumento);

        // Verificar si el huésped existe
        if (!$this->huespedModel->existeHuesped($numDocumento)) {
            error_log('El huésped no existe en la base de datos');
            throw new Exception('El huésped no existe');
        }

        // Verificar si se puede eliminar
        if (method_exists($this->huespedModel, 'puedeEliminar')) {
            if (!$this->huespedModel->puedeEliminar($numDocumento)) {
                error_log('No se puede eliminar porque tiene reservas activas');
                throw new Exception('No se puede eliminar el huésped porque tiene reservas activas');
            }
        }

        $resultado = $this->huespedModel->eliminarHuesped($numDocumento);

        error_log('Resultado de eliminación: ' . ($resultado ? 'OK' : 'FALLÓ'));

        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Huésped eliminado exitosamente'
            ]);
        } else {
            throw new Exception('Error al eliminar el huésped');
        }

    } catch (Exception $e) {
        error_log('Error en eliminarHuesped: ' . $e->getMessage());
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
}

    // NUEVO: Buscar huéspedes
    public function buscarHuespedes() {
        header('Content-Type: application/json');
        
        try {
            $termino = isset($_GET['termino']) ? trim($_GET['termino']) : '';
            
            if (empty($termino)) {
                throw new Exception('Término de búsqueda es requerido');
            }

            if (strlen($termino) < 2) {
                throw new Exception('El término de búsqueda debe tener al menos 2 caracteres');
            }

            $huespedes = $this->huespedModel->buscarHuespedes($termino);
            
            echo json_encode([
                'success' => true,
                'data' => $huespedes,
                'total' => count($huespedes)
            ]);
            
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

    private function sanitizarEmail($email) {
        return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    }

    private function validarDatos($datos) {
        // Validar tipo de documento
        $tiposPermitidos = [
            'Cedula de Ciudadania',      // CORREGIDO: Sin tildes para coincidir con BD
            'Tarjeta de Identidad',
            'Cedula de Extranjeria',     // CORREGIDO: Sin tildes
            'Pasaporte',
            'Registro Civil'
        ];

        if (!in_array($datos['tipoDocumento'], $tiposPermitidos)) {
            throw new Exception('Tipo de documento no válido');
        }

        // Validar sexo
        $sexosPermitidos = ['Hombre', 'Mujer', 'Otro', 'Prefiero no decirlo'];
        if (!in_array($datos['sexo'], $sexosPermitidos)) {
            throw new Exception('Sexo no válido');
        }

        // Validar número de documento
        if (strlen($datos['numDocumento']) < 5 || strlen($datos['numDocumento']) > 15) {
            throw new Exception('El número de documento debe tener entre 5 y 15 caracteres');
        }

        if (!preg_match('/^[0-9A-Za-z]+$/', $datos['numDocumento'])) {
            throw new Exception('El número de documento solo puede contener letras y números');
        }

        // CORREGIDO: Validar nombres y apellidos con soporte para acentos
        if (strlen($datos['nombres']) < 2 || strlen($datos['nombres']) > 50) {
            throw new Exception('Los nombres deben tener entre 2 y 50 caracteres');
        }

        if (strlen($datos['apellidos']) < 2 || strlen($datos['apellidos']) > 50) {
            throw new Exception('Los apellidos deben tener entre 2 y 50 caracteres');
        }

        // CORREGIDO: Regex mejorado para acentos
        if (!preg_match('/^[a-zA-ZÀ-ÿñÑ\s]+$/u', $datos['nombres'])) {
            throw new Exception('Los nombres solo pueden contener letras y espacios');
        }

        if (!preg_match('/^[a-zA-ZÀ-ÿñÑ\s]+$/u', $datos['apellidos'])) {
            throw new Exception('Los apellidos solo pueden contener letras y espacios');
        }

        // CORREGIDO: Validar teléfono más estricto
        if (strlen($datos['numTelefono']) < 7 || strlen($datos['numTelefono']) > 15) {
            throw new Exception('El número de teléfono debe tener entre 7 y 15 caracteres');
        }

        if (!preg_match('/^[0-9+\-\s()]{7,15}$/', $datos['numTelefono'])) {
            throw new Exception('El formato del teléfono no es válido');
        }

        // Validar correo
        if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('El formato del correo electrónico no es válido');
        }

        if (strlen($datos['correo']) > 30) {
            throw new Exception('El correo electrónico no puede tener más de 30 caracteres');
        }
    }

    private function validarDatosActualizacion($datos) {
        // Validar campos individuales solo si están presentes
        if (isset($datos['nombres'])) {
            if (strlen($datos['nombres']) < 2 || strlen($datos['nombres']) > 50) {
                throw new Exception('Los nombres deben tener entre 2 y 50 caracteres');
            }
            if (!preg_match('/^[a-zA-ZÀ-ÿñÑ\s]+$/u', $datos['nombres'])) {
                throw new Exception('Los nombres solo pueden contener letras y espacios');
            }
        }

        if (isset($datos['apellidos'])) {
            if (strlen($datos['apellidos']) < 2 || strlen($datos['apellidos']) > 50) {
                throw new Exception('Los apellidos deben tener entre 2 y 50 caracteres');
            }
            if (!preg_match('/^[a-zA-ZÀ-ÿñÑ\s]+$/u', $datos['apellidos'])) {
                throw new Exception('Los apellidos solo pueden contener letras y espacios');
            }
        }

        if (isset($datos['numTelefono'])) {
            if (strlen($datos['numTelefono']) < 7 || strlen($datos['numTelefono']) > 15) {
                throw new Exception('El número de teléfono debe tener entre 7 y 15 caracteres');
            }
            if (!preg_match('/^[0-9+\-\s()]{7,15}$/', $datos['numTelefono'])) {
                throw new Exception('El formato del teléfono no es válido');
            }
        }

        if (isset($datos['correo'])) {
            if (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
                throw new Exception('El formato del correo electrónico no es válido');
            }
            if (strlen($datos['correo']) > 30) {
                throw new Exception('El correo electrónico no puede tener más de 30 caracteres');
            }
        }

        if (isset($datos['sexo'])) {
            $sexosPermitidos = ['Hombre', 'Mujer', 'Otro', 'Prefiero no decirlo'];
            if (!in_array($datos['sexo'], $sexosPermitidos)) {
                throw new Exception('Sexo no válido');
            }
        }
    }
}

// Manejo de rutas MEJORADO
if (isset($_GET['action'])) {
    $controller = new HuespedController();
    
    switch ($_GET['action']) {
        case 'crear':
            $controller->crearHuesped();
            break;
        case 'obtener':
            $controller->obtenerHuespedes();
            break;
        case 'obtenerPorDocumento':
            $numDocumento = $_GET['numDocumento'] ?? '';
            $controller->obtenerHuesped($numDocumento);
            break;
        case 'actualizar':
            $controller->actualizarHuesped();
            break;
        case 'eliminar':
            $controller->eliminarHuesped();
            break;
        case 'buscar':           // NUEVO
            $controller->buscarHuespedes();
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
        $controller = new HuespedController();
        $controller->crearHuesped();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Método no permitido'
        ]);
    }
}
?>