<?php
session_start();
header('Content-Type: application/json');

// Incluir archivos necesarios
require_once '../../config/conexionGlobal.php';
require_once '/models/misColaboradoresModel.php';

// Instancia de la base de datos
$db = conexionDB();
$usuario = new Usuario($db);

// Función para respuesta JSON
function enviarRespuesta($success, $message, $data = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit;
}

// Verificar método de la petición
$metodo = $_SERVER['REQUEST_METHOD'];
$accion = isset($_GET['accion']) ? $_GET['accion'] : '';

try {
    switch ($metodo) {
        case 'POST':
            if ($accion === 'crear') {
                crearColaborador();
            } elseif ($accion === 'actualizar') {
                actualizarColaborador();
            } elseif ($accion === 'cambiarPassword') {
                cambiarPassword();
            } else {
                // Manejar formulario tradicional
                if (isset($_POST['numDocumento'])) {
                    crearColaborador();
                } else {
                    enviarRespuesta(false, "Acción no válida", null, 400);
                }
            }
            break;

        case 'GET':
            if ($accion === 'listar') {
                listarColaboradores();
            } elseif ($accion === 'obtener') {
                obtenerColaborador();
            } elseif ($accion === 'verificarDocumento') {
                verificarDocumento();
            } elseif ($accion === 'verificarCorreo') {
                verificarCorreo();
            } else {
                enviarRespuesta(false, "Acción no válida", null, 400);
            }
            break;

        case 'DELETE':
            if ($accion === 'eliminar') {
                eliminarColaborador();
            } else {
                enviarRespuesta(false, "Acción no válida", null, 400);
            }
            break;

        default:
            enviarRespuesta(false, "Método no permitido", null, 405);
    }

} catch (Exception $e) {
    enviarRespuesta(false, "Error interno del servidor: " . $e->getMessage(), null, 500);
}

// Función para crear colaborador
function crearColaborador() {
    global $usuario;

    // Obtener datos del formulario
    $usuario->numDocumento = $_POST['numDocumento'] ?? '';
    $usuario->tipoDocumento = $_POST['tipoDocumento'] ?? '';
    $usuario->nombres = $_POST['nombres'] ?? '';
    $usuario->apellidos = $_POST['apellidos'] ?? '';
    $usuario->numTelefono = $_POST['numTelefono'] ?? '';
    $usuario->correo = $_POST['correo'] ?? '';
    $usuario->sexo = $_POST['sexo'] ?? '';
    $usuario->fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
    $usuario->password = $_POST['password'] ?? '';
    $usuario->roles = $_POST['roles'] ?? '';
    $usuario->solicitarContraseña = isset($_POST['solicitarContraseña']) ? '1' : '0';

    // Validar datos
    $errores = $usuario->validar();

    // Validar confirmación de contraseña
    $confirmarPassword = $_POST['confirmarPassword'] ?? '';
    if ($usuario->password !== $confirmarPassword) {
        $errores[] = "Las contraseñas no coinciden";
    }

    // Verificar si el documento ya existe
    if ($usuario->existeDocumento($usuario->numDocumento)) {
        $errores[] = "Ya existe un colaborador con este número de documento";
    }

    // Verificar si el correo ya existe
    if ($usuario->existeCorreo($usuario->correo)) {
        $errores[] = "Ya existe un colaborador con este correo electrónico";
    }

    if (!empty($errores)) {
        enviarRespuesta(false, implode(', ', $errores), null, 422);
    }

    // Procesar foto si se subió una nueva
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        // Obtener foto anterior para eliminarla
        $usuarioAnterior = new Usuario($GLOBALS['db']);
        $usuarioAnterior->numDocumento = $documentoOriginal;
        if ($usuarioAnterior->leerUno() && !empty($usuarioAnterior->foto)) {
            $usuario->eliminarFoto($usuarioAnterior->foto);
        }

        $resultadoFoto = $usuario->subirFoto($_FILES['foto']);
        if ($resultadoFoto['success']) {
            $usuario->foto = $resultadoFoto['filename'];
        } else {
            enviarRespuesta(false, $resultadoFoto['message'], null, 422);
        }
    }

    // Actualizar colaborador
    if ($usuario->actualizar()) {
        enviarRespuesta(true, "Colaborador actualizado exitosamente", [
            'documento' => $usuario->numDocumento,
            'nombres' => $usuario->nombres,
            'apellidos' => $usuario->apellidos
        ]);
    } else {
        enviarRespuesta(false, "Error al actualizar el colaborador", null, 500);
    }
}

// Función para eliminar colaborador
function eliminarColaborador() {
    global $usuario;

    // Obtener documento del body (método DELETE)
    $input = json_decode(file_get_contents('php://input'), true);
    $documento = $input['documento'] ?? '';

    if (empty($documento)) {
        enviarRespuesta(false, "Documento requerido", null, 400);
    }

    $usuario->numDocumento = $documento;

    // Obtener datos del colaborador antes de eliminar (para eliminar foto)
    if ($usuario->leerUno() && !empty($usuario->foto)) {
        $usuario->eliminarFoto($usuario->foto);
    }

    // Eliminar colaborador
    $usuario->numDocumento = $documento;
    if ($usuario->eliminar()) {
        enviarRespuesta(true, "Colaborador eliminado exitosamente");
    } else {
        enviarRespuesta(false, "Error al eliminar el colaborador", null, 500);
    }
}

// Función para cambiar contraseña
function cambiarPassword() {
    global $usuario;

    $documento = $_POST['documento'] ?? '';
    $nuevaPassword = $_POST['nueva_password'] ?? '';
    $confirmarPassword = $_POST['confirmar_password'] ?? '';
    $solicitarCambio = isset($_POST['solicitar_cambio']) ? '1' : '0';

    if (empty($documento) || empty($nuevaPassword) || empty($confirmarPassword)) {
        enviarRespuesta(false, "Todos los campos son obligatorios", null, 400);
    }

    if (strlen($nuevaPassword) < 6) {
        enviarRespuesta(false, "La contraseña debe tener al menos 6 caracteres", null, 422);
    }

    if ($nuevaPassword !== $confirmarPassword) {
        enviarRespuesta(false, "Las contraseñas no coinciden", null, 422);
    }

    $usuario->numDocumento = $documento;
    $usuario->password = $nuevaPassword;
    $usuario->solicitarContraseña = $solicitarCambio;

    if ($usuario->cambiarPassword()) {
        enviarRespuesta(true, "Contraseña cambiada exitosamente");
    } else {
        enviarRespuesta(false, "Error al cambiar la contraseña", null, 500);
    }
}

// Función para verificar si existe un documento
function verificarDocumento() {
    global $usuario;

    $documento = $_GET['documento'] ?? '';
    $documentoOriginal = $_GET['documentoOriginal'] ?? null;

    if (empty($documento)) {
        enviarRespuesta(false, "Documento requerido", null, 400);
    }

    $existe = $usuario->existeDocumento($documento, $documentoOriginal);
    
    enviarRespuesta(true, $existe ? "Documento ya existe" : "Documento disponible", [
        'existe' => $existe
    ]);
}

// Función para verificar si existe un correo
function verificarCorreo() {
    global $usuario;

    $correo = $_GET['correo'] ?? '';
    $documentoOriginal = $_GET['documentoOriginal'] ?? null;

    if (empty($correo)) {
        enviarRespuesta(false, "Correo requerido", null, 400);
    }

    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        enviarRespuesta(false, "Formato de correo inválido", null, 422);
    }

    $existe = $usuario->existeCorreo($correo, $documentoOriginal);
    
    enviarRespuesta(true, $existe ? "Correo ya existe" : "Correo disponible", [
        'existe' => $existe
    ]);
}

// Manejo de errores específicos
function manejarErrores() {
    if (error_get_last()) {
        $error = error_get_last();
        enviarRespuesta(false, "Error del servidor: " . $error['message'], null, 500);
    }
}

register_shutdown_function('manejarErrores');
?>// Procesar foto si se subió
    $usuario->foto = null;
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] !== UPLOAD_ERR_NO_FILE) {
        $resultadoFoto = $usuario->subirFoto($_FILES['foto']);
        if ($resultadoFoto['success']) {
            $usuario->foto = $resultadoFoto['filename'];
        } else {
            enviarRespuesta(false, $resultadoFoto['message'], null, 422);
        }
    }

    // Crear colaborador
    if ($usuario->crear()) {
        enviarRespuesta(true, "Colaborador creado exitosamente", [
            'documento' => $usuario->numDocumento,
            'nombres' => $usuario->nombres,
            'apellidos' => $usuario->apellidos
        ]);
    } else {
        enviarRespuesta(false, "Error al crear el colaborador", null, 500);
    }
}

// Función para listar colaboradores
function listarColaboradores() {
    global $usuario;

    // Parámetros de paginación
    $pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
    $porPagina = isset($_GET['porPagina']) ? (int)$_GET['porPagina'] : 10;
    $offset = ($pagina - 1) * $porPagina;

    // Parámetros de búsqueda y filtro
    $busqueda = isset($_GET['busqueda']) ? trim($_GET['busqueda']) : '';
    $filtro = isset($_GET['filtro']) ? $_GET['filtro'] : 'all';
    $valorFiltro = isset($_GET['valorFiltro']) ? $_GET['valorFiltro'] : '';

    // Obtener colaboradores
    $stmt = $usuario->leer($offset, $porPagina, $busqueda, $filtro, $valorFiltro);
    $colaboradores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Obtener total para paginación
    $total = $usuario->contarTotal($busqueda, $filtro, $valorFiltro);
    $totalPaginas = ceil($total / $porPagina);

    enviarRespuesta(true, "Colaboradores obtenidos exitosamente", [
        'colaboradores' => $colaboradores,
        'paginacion' => [
            'paginaActual' => $pagina,
            'totalPaginas' => $totalPaginas,
            'total' => $total,
            'porPagina' => $porPagina
        ]
    ]);
}

// Función para obtener un colaborador específico
function obtenerColaborador() {
    global $usuario;

    $documento = $_GET['documento'] ?? '';
    
    if (empty($documento)) {
        enviarRespuesta(false, "Documento requerido", null, 400);
    }

    $usuario->numDocumento = $documento;
    
    if ($usuario->leerUno()) {
        // No incluir la contraseña en la respuesta
        $datosColaborador = [
            'numDocumento' => $usuario->numDocumento,
            'tipoDocumento' => $usuario->tipoDocumento,
            'nombres' => $usuario->nombres,
            'apellidos' => $usuario->apellidos,
            'numTelefono' => $usuario->numTelefono,
            'correo' => $usuario->correo,
            'sexo' => $usuario->sexo,
            'fechaNacimiento' => $usuario->fechaNacimiento,
            'foto' => $usuario->foto,
            'solicitarContraseña' => $usuario->solicitarContraseña,
            'sesionCaducada' => $usuario->sesionCaducada,
            'roles' => $usuario->roles
        ];
        
        enviarRespuesta(true, "Colaborador encontrado", $datosColaborador);
    } else {
        enviarRespuesta(false, "Colaborador no encontrado", null, 404);
    }
}

// Función para actualizar colaborador
function actualizarColaborador() {
    global $usuario;

    // Obtener datos
    $documentoOriginal = $_POST['documento_original'] ?? '';
    $usuario->numDocumento = $documentoOriginal; // Para la actualización usamos el documento original
    $usuario->tipoDocumento = $_POST['tipoDocumento'] ?? '';
    $usuario->nombres = $_POST['nombres'] ?? '';
    $usuario->apellidos = $_POST['apellidos'] ?? '';
    $usuario->numTelefono = $_POST['numTelefono'] ?? '';
    $usuario->correo = $_POST['correo'] ?? '';
    $usuario->sexo = $_POST['sexo'] ?? '';
    $usuario->fechaNacimiento = $_POST['fechaNacimiento'] ?? '';
    $usuario->roles = $_POST['roles'] ?? '';
    $usuario->password = $_POST['password'] ?? '';
    $usuario->solicitarContraseña = $_POST['solicitarContraseña'] ?? '0';

    if (empty($documentoOriginal)) {
        enviarRespuesta(false, "Documento original requerido", null, 400);
    }

    // Validar datos básicos
    $errores = $usuario->validar();

    // Si se está cambiando el número de documento, verificar que no exista
    $nuevoDocumento = $_POST['numDocumento'] ?? '';
    if ($nuevoDocumento !== $documentoOriginal) {
        if ($usuario->existeDocumento($nuevoDocumento)) {
            $errores[] = "Ya existe un colaborador con este número de documento";
        }
    }

    // Verificar correo (excluyendo el colaborador actual)
    if ($usuario->existeCorreo($usuario->correo, $documentoOriginal)) {
        $errores[] = "Ya existe un colaborador con este correo electrónico";
    }

    if (!empty($errores)) {
        enviarRespuesta(false, implode(', ', $errores), null, 422);
    }