<?php
require_once __DIR__ . '/../models/hotelModel.php';

class HotelController {
    private $hotelModel;

    public function __construct() {
        $this->hotelModel = new HotelModel();
    }

    // Crear un nuevo hotel
    public function crearHotel() {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validación de entrada
            $validacion = $this->validarDatosHotel($input);
            if (!$validacion['valido']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validacion['errores']
                ]);
                return;
            }

            // Asegurarse de que la sesión esté iniciada
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // VALIDACIÓN CRÍTICA: El administrador debe ser el usuario logueado
            $usuarioLogueado = $_SESSION['user']['numDocumento'] ?? null;
            if ($input['numDocumento'] !== $usuarioLogueado) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Solo puedes registrar hoteles con tu propio número de documento como administrador'
                ]);
                return;
            }

            // Verificar si el NIT ya existe
            if ($this->hotelModel->nitExiste($input['nit'])) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El NIT ya está registrado para otro hotel'
                ]);
                return;
            }

            // Procesar la imagen si se subió
            $rutaFoto = $this->procesarImagenHotel($_FILES['foto'] ?? null);

            // Preparar datos para insertar
            $datosHotel = [
                'nit' => trim($input['nit']),
                'nombre' => trim($input['nombre']),
                'direccion' => isset($input['direccion']) ? trim($input['direccion']) : null,
                'telefono' => isset($input['telefono']) ? trim($input['telefono']) : null,
                'correo' => isset($input['correo']) ? trim($input['correo']) : null,
                'foto' => $rutaFoto, // Usar la ruta de la imagen procesada
                'descripcion' => isset($input['descripcion']) ? trim($input['descripcion']) : null,
                'numDocumentoAdmin' => $input['numDocumento']
            ];

            $resultado = $this->hotelModel->crearHotel($datosHotel);
            
            if ($resultado['success']) {
                http_response_code(201);

                // SOLUCIÓN: Actualizar la sesión del usuario para reflejar el nuevo hotel
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                // Actualizar la sesión con toda la información del nuevo hotel
                $_SESSION['hotel_id'] = $resultado['id'];
                $_SESSION['hotel_nombre'] = $datosHotel['nombre'];
                $_SESSION['tipo_admin'] = 'hotel'; // Cambiar de 'super' a 'hotel'
                
                // SOLUCIÓN ADICIONAL: Actualizar el rol en la sesión para consistencia inmediata.
                if (isset($_SESSION['user']['roles']) && $_SESSION['user']['roles'] === 'Administrador') {
                    $_SESSION['user']['roles'] = 'Colaborador';
                }

                // Poblar el array $_SESSION['hotel'] para que la homepage lo lea de inmediato
                $_SESSION['hotel'] = [
                    'id' => $resultado['id'],
                    'nombre' => $datosHotel['nombre'],
                    'nit' => $datosHotel['nit'],
                    'direccion' => $datosHotel['direccion'],
                    'telefono' => $datosHotel['telefono'],
                    'correo' => $datosHotel['correo'],
                    'foto' => $datosHotel['foto'],
                    'descripcion' => $datosHotel['descripcion']
                ];

                // Añadir el nuevo hotel a la lista de hoteles asignados en la sesión
                $_SESSION['hoteles_asignados'][] = [
                    'id' => $resultado['id'], 'nombre' => $datosHotel['nombre']
                ];
            } else {
                http_response_code(400);
            }
            
            echo json_encode($resultado);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    // Obtener hoteles
    public function obtenerHoteles() {
        try {
            $usuarioLogueado = $_SESSION['user'] ?? null;
            
            // Si es administrador, puede ver todos los hoteles
            if ($usuarioLogueado['roles'] === 'Administrador') {
                $resultado = $this->hotelModel->obtenerHoteles();
            } else {
                // Si no es administrador, solo puede ver sus propios hoteles
                $resultado = $this->hotelModel->obtenerHotelesPorAdmin($usuarioLogueado['numDocumento']);
            }
            
            if ($resultado['success']) {
                http_response_code(200);
            } else {
                http_response_code(500);
            }
            
            echo json_encode($resultado);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener hoteles: ' . $e->getMessage()
            ]);
        }
    }

    // Actualizar hotel
    public function actualizarHotel($hotelId = null) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Obtener ID del hotel (de parámetro o del body)
            $id = $hotelId ?? $input['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID del hotel requerido'
                ]);
                return;
            }

            // Obtener el hotel actual
            $hotelActual = $this->hotelModel->obtenerHotelPorId($id);
            if (!$hotelActual['success']) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Hotel no encontrado'
                ]);
                return;
            }

            // VALIDACIÓN: Solo el propietario del hotel o un administrador puede editarlo
            $usuarioLogueado = $_SESSION['user'] ?? null;
            $esAdministrador = $usuarioLogueado['roles'] === 'Administrador';
            $esPropietario = $hotelActual['data']['numDocumentoAdmin'] === $usuarioLogueado['numDocumento'];

            if (!$esAdministrador && !$esPropietario) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'No tienes permisos para editar este hotel'
                ]);
                return;
            }

            // Validación de datos
            $validacion = $this->validarDatosHotel($input, $id);
            if (!$validacion['valido']) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'Datos inválidos',
                    'errors' => $validacion['errores']
                ]);
                return;
            }

            // VALIDACIÓN: El administrador debe ser el usuario logueado (excepto si es admin global)
            if (!$esAdministrador && $input['numDocumento'] !== $usuarioLogueado['numDocumento']) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'Solo puedes asignar tu propio número de documento como administrador'
                ]);
                return;
            }

            // Verificar NIT duplicado
            if ($this->hotelModel->nitExiste($input['nit'], $id)) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'El NIT ya está registrado para otro hotel'
                ]);
                return;
            }

            // SOLUCIÓN: Combinar los datos existentes con los nuevos datos del formulario.
            // Esto evita que los campos opcionales se borren si vienen vacíos en la petición.
            $datosHotel = array_merge($hotelActual['data'], $input);

            // Limpiar y asegurar que los datos finales sean correctos
            $datosHotel['nit'] = trim($datosHotel['nit']);
            $datosHotel['nombre'] = trim($datosHotel['nombre']);

            $resultado = $this->hotelModel->actualizarHotel($id, $datosHotel);
            
            if ($resultado['success']) {
                http_response_code(200);
            } else {
                http_response_code(400);
            }
            
            echo json_encode($resultado);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    // Eliminar hotel
    public function eliminarHotel($hotelId = null) {
        try {
            $input = json_decode(file_get_contents('php://input'), true);
            
            // Obtener ID del hotel (de parámetro o del body)
            $id = $hotelId ?? $input['id'] ?? null;
            
            if (!$id) {
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'message' => 'ID del hotel requerido'
                ]);
                return;
            }

            // Obtener el hotel actual
            $hotelActual = $this->hotelModel->obtenerHotelPorId($id);
            if (!$hotelActual['success']) {
                http_response_code(404);
                echo json_encode([
                    'success' => false,
                    'message' => 'Hotel no encontrado'
                ]);
                return;
            }

            // VALIDACIÓN: Solo el propietario del hotel o un administrador pueden eliminarlo
            $usuarioLogueado = $_SESSION['user'] ?? null;
            $esAdministrador = $usuarioLogueado['roles'] === 'Administrador';
            $esPropietario = $hotelActual['data']['numDocumentoAdmin'] === $usuarioLogueado['numDocumento'];

            if (!$esAdministrador && !$esPropietario) {
                http_response_code(403);
                echo json_encode([
                    'success' => false,
                    'message' => 'No tienes permisos para eliminar este hotel'
                ]);
                return;
            }

            $resultado = $this->hotelModel->eliminarHotel($id);
            
            if ($resultado['success']) {
                http_response_code(200);
            } else {
                http_response_code(400);
            }
            
            echo json_encode($resultado);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error interno del servidor: ' . $e->getMessage()
            ]);
        }
    }

    // Obtener hotel específico (para edición)
    public function obtenerHotel($id) {
        try {
            $resultado = $this->hotelModel->obtenerHotelPorId($id);
            
            if ($resultado['success']) {
                // Verificar permisos
                $usuarioLogueado = $_SESSION['user'] ?? null;
                $esAdministrador = $usuarioLogueado['roles'] === 'Administrador';
                $esPropietario = $resultado['data']['numDocumentoAdmin'] === $usuarioLogueado['numDocumento'];

                if (!$esAdministrador && !$esPropietario) {
                    http_response_code(403);
                    echo json_encode([
                        'success' => false,
                        'message' => 'No tienes permisos para ver este hotel'
                    ]);
                    return;
                }

                http_response_code(200);
            } else {
                http_response_code(404);
            }
            
            echo json_encode($resultado);

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener el hotel: ' . $e->getMessage()
            ]);
        }
    }

    // Validar datos del hotel
    private function validarDatosHotel($datos, $hotelId = null) {
        $errores = [];

        // NIT - obligatorio
        if (!isset($datos['nit']) || empty(trim($datos['nit']))) {
            $errores['nit'] = 'El NIT es obligatorio';
        } elseif (strlen(trim($datos['nit'])) > 20) {
            $errores['nit'] = 'El NIT no puede exceder 20 caracteres';
        } elseif (!preg_match('/^[0-9\-]+$/', trim($datos['nit']))) {
            $errores['nit'] = 'El NIT solo puede contener números y guiones';
        }

        // Nombre - obligatorio
        if (!isset($datos['nombre']) || empty(trim($datos['nombre']))) {
            $errores['nombre'] = 'El nombre del hotel es obligatorio';
        } elseif (strlen(trim($datos['nombre'])) > 100) {
            $errores['nombre'] = 'El nombre no puede exceder 100 caracteres';
        }

        // Documento del administrador - obligatorio
        if (!isset($datos['numDocumento']) || empty(trim($datos['numDocumento']))) {
            $errores['numDocumento'] = 'El documento del administrador es obligatorio';
        } elseif (!$this->hotelModel->administradorExiste($datos['numDocumento'])) {
            $errores['numDocumento'] = 'El administrador especificado no existe en el sistema';
        }

        // Teléfono - opcional pero con formato
        if (isset($datos['telefono']) && !empty(trim($datos['telefono']))) {
            if (strlen(trim($datos['telefono'])) > 15) {
                $errores['telefono'] = 'El teléfono no puede exceder 15 caracteres';
            }
        }

        // Correo - opcional pero con formato válido
        if (isset($datos['correo']) && !empty(trim($datos['correo']))) {
            if (!filter_var(trim($datos['correo']), FILTER_VALIDATE_EMAIL)) {
                $errores['correo'] = 'El formato del correo electrónico no es válido';
            } elseif (strlen(trim($datos['correo'])) > 255) {
                $errores['correo'] = 'El correo no puede exceder 255 caracteres';
            }
        }

        // URL de foto - opcional pero con formato válido
        if (isset($datos['foto']) && !empty(trim($datos['foto']))) {
            if (!filter_var(trim($datos['foto']), FILTER_VALIDATE_URL)) {
                $errores['foto'] = 'La URL de la foto no es válida';
            }
        }

        // Dirección - opcional pero con límite de caracteres
        if (isset($datos['direccion']) && strlen(trim($datos['direccion'])) > 200) {
            $errores['direccion'] = 'La dirección no puede exceder 200 caracteres';
        }

        // Descripción - opcional pero con límite de caracteres
        if (isset($datos['descripcion']) && strlen(trim($datos['descripcion'])) > 1000) {
            $errores['descripcion'] = 'La descripción no puede exceder 1000 caracteres';
        }

        return [
            'valido' => empty($errores),
            'errores' => $errores
        ];
    }

    /**
     * Procesa la imagen subida para un hotel y la guarda en el servidor.
     * @param array|null $file El array de la imagen de $_FILES.
     * @return string|null La ruta relativa de la imagen guardada o null si no se subió/hubo error.
     */
    private function procesarImagenHotel($file) {
        if (!$file || $file['error'] !== UPLOAD_ERR_OK) {
            return null; // No se subió ninguna imagen o hubo un error inicial
        }

        // Verificar el tamaño del archivo (máximo 5MB)
        if ($file['size'] > 5 * 1024 * 1024) {
            // Podrías registrar este error si quieres
            return null;
        }

        // Verificar el tipo de archivo usando finfo para más seguridad
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipoMime = $finfo->file($file['tmp_name']);

        if (!in_array($tipoMime, $tiposPermitidos)) {
            return null;
        }

        // Generar un nombre único para la imagen para evitar colisiones
        $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'hotel_' . uniqid() . '_' . time() . '.' . $extension;

        // Definir la ruta de destino. Usamos rutas relativas desde la raíz del proyecto.
        $directorioDestino = '/public/uploads/hoteles/';
        $rutaCompletaServidor = $_SERVER['DOCUMENT_ROOT'] . $directorioDestino;
        $rutaArchivoServidor = $rutaCompletaServidor . $nombreArchivo;

        // Crear el directorio si no existe
        if (!is_dir($rutaCompletaServidor)) {
            if (!mkdir($rutaCompletaServidor, 0775, true)) {
                // No se pudo crear el directorio, registrar error y retornar null
                error_log("Error: No se pudo crear el directorio de subida en " . $rutaCompletaServidor);
                return null;
            }
        }

        // Mover el archivo al destino final
        if (move_uploaded_file($file['tmp_name'], $rutaArchivoServidor)) {
            // Retornar la ruta relativa que se usará en el src de la etiqueta <img>
            return $directorioDestino . $nombreArchivo;
        }

        return null;
    }
}