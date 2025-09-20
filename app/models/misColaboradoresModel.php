<?php
/**
 * Modelo Colaborador - ACTUALIZADO PARA FILTRAR ROLES
 * Maneja todas las operaciones CRUD para la tabla tp_usuarios
 * Modificado para excluir administradores de la lista
 */

require_once '../../config/conexionGlobal.php';

// SOLUCIÓN: Cambiar el nombre de la clase a "MisColaboradoresModel" para que sea consistente
// con el resto del proyecto y el controlador pueda encontrarla.
class MisColaboradoresModel {
    private $conexion;
    
    public function __construct() {
        global $conexion;
        
        if (!$conexion) {
            throw new Exception('Error de conexión con la base de datos');
        }
        
        $this->conexion = $conexion;
        
        // Configurar charset para caracteres especiales
        $this->conexion->set_charset("utf8");
    }
    
    /**
     * Verificar conexión a la base de datos
     */
    private function verificarConexion() {
        if (!$this->conexion || $this->conexion->connect_error) {
            throw new Exception('Conexión a la base de datos perdida');
        }
    }
    
    /**
     * Crear un nuevo colaborador
     */
    public function crear($datos) {
        // El controlador debe pasar el id_hotel del administrador logueado.
        $id_hotel_admin = $datos['id_hotel_admin'] ?? null;
        if (empty($id_hotel_admin)) {
            return ['success' => false, 'message' => 'No se pudo identificar el hotel del administrador. Inicie sesión de nuevo.'];
        }
        try {
            $this->verificarConexion();
            
            // Verificar si el documento ya existe
            if ($this->existeDocumento($datos['numDocumento'])) {
                return ['success' => false, 'message' => 'El número de documento ya está registrado'];
            }
            
            // Verificar si el correo ya existe
            if ($this->existeCorreo($datos['correo'])) {
                return ['success' => false, 'message' => 'El correo electrónico ya está registrado'];
            }
            
            // Validar edad mínima (18 años)
            if (!$this->validarEdadMinima($datos['fechaNacimiento'])) {
                return ['success' => false, 'message' => 'El colaborador debe ser mayor de 18 años'];
            }
            
            // Preparar la consulta con manejo de transacciones
            $this->conexion->begin_transaction();
            
            try {
                $sql = "INSERT INTO tp_usuarios (
                            numDocumento, tipoDocumento, nombres, apellidos, 
                            numTelefono, correo, sexo, fechaNacimiento, 
                            password, foto, solicitarContraseña, roles
                        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $this->conexion->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception('Error al preparar la consulta: ' . $this->conexion->error);
                }
                
                // Hash de la contraseña
                $passwordHash = password_hash($datos['password'], PASSWORD_DEFAULT);
                
                // Manejar la foto (si existe)
                $fotoPath = null;
                if (isset($datos['foto']) && $datos['foto']['error'] === UPLOAD_ERR_OK) {
                    $fotoPath = $this->guardarFoto($datos['foto'], $datos['numDocumento']);
                    if (!$fotoPath) {
                        throw new Exception('Error al guardar la foto');
                    }
                }
                
                $solicitarContraseña = isset($datos['solicitarContraseña']) && $datos['solicitarContraseña'] ? '1' : '0';
                
                $stmt->bind_param(
                    "ssssssssssss",
                    $datos['numDocumento'],
                    $datos['tipoDocumento'],
                    $datos['nombres'],
                    $datos['apellidos'],
                    $datos['numTelefono'],
                    $datos['correo'],
                    $datos['sexo'],
                    $datos['fechaNacimiento'],
                    $passwordHash,
                    $fotoPath,
                    $solicitarContraseña,
                    $datos['roles']
                );
                
                if (!$stmt->execute()) {
                    throw new Exception('Error al ejecutar la consulta: ' . $stmt->error);
                }
                
                // Verificar que el usuario fue creado antes de asignarlo al hotel
                if ($stmt->affected_rows === 0) {
                    throw new Exception('No se pudo crear el registro principal del usuario.');
                }

                // 2. Asignar el nuevo usuario al hotel del administrador en ti_personal
                $sqlPersonal = "INSERT INTO ti_personal (id_hotel, numDocumento, roles) VALUES (?, ?, ?)";
                $stmtPersonal = $this->conexion->prepare($sqlPersonal);

                if (!$stmtPersonal) {
                    throw new Exception('Error al preparar la consulta de personal: ' . $this->conexion->error);
                }

                // Usar el mismo rol que se seleccionó en el formulario
                $stmtPersonal->bind_param(
                    "iss",
                    $id_hotel_admin,
                    $datos['numDocumento'],
                    $datos['roles']
                );

                if (!$stmtPersonal->execute()) {
                    throw new Exception('Error al asignar el colaborador al hotel: ' . $stmtPersonal->error);
                }

                $this->conexion->commit();
                return ['success' => true, 'message' => 'Colaborador creado exitosamente'];
                
            } catch (Exception $e) {
                $this->conexion->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            $this->logError('Error en crear(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema: ' . $e->getMessage()];
        }
    }
    
    /**
     * Listar colaboradores y usuarios (excluyendo administradores)
     * MODIFICADO PARA FILTRAR ROLES
     */
    public function listar($filtros = []) {
        try {
            $this->verificarConexion();
            
            $condiciones = [];
            $params = [];
            $types = "";

            // BASE: Unir usuarios con personal para filtrar por hotel
            $sql = "SELECT u.numDocumento, u.tipoDocumento, u.nombres, u.apellidos, 
                           u.numTelefono, u.correo, u.sexo, u.fechaNacimiento, 
                           u.foto, u.roles, u.solicitarContraseña, p.id_hotel
                    FROM tp_usuarios u
                    INNER JOIN ti_personal p ON u.numDocumento = p.numDocumento
                    WHERE u.roles IN ('Colaborador', 'Usuario')";

            // Filtro por hotel del administrador
            if (!empty($filtros['id_hotel_admin'])) {
                $condiciones[] = "p.id_hotel = ?";
                $params[] = $filtros['id_hotel_admin'];
                $types .= "i";
            }
            
            
            // Aplicar filtros adicionales
            if (!empty($filtros['busqueda'])) {
                $busqueda = trim($filtros['busqueda']);
                if (strlen($busqueda) >= 2) {
                    $condiciones[] = "(numDocumento LIKE ? OR nombres LIKE ? OR apellidos LIKE ? OR correo LIKE ?)";
                    $busquedaLike = "%" . $busqueda . "%";
                    $params = array_merge($params, [$busquedaLike, $busquedaLike, $busquedaLike, $busquedaLike]);
                    $types .= "ssss";
                }
            }
            
            // Filtro por rol específico (solo Colaborador o Usuario)
            if (!empty($filtros['rol']) && $filtros['rol'] !== 'all') {
                $rolesPermitidos = ['Colaborador', 'Usuario'];
                if (in_array($filtros['rol'], $rolesPermitidos, true)) {
                    $condiciones[] = "roles = ?";
                    $params[] = $filtros['rol'];
                    $types .= "s";
                }
            }
            
            if (!empty($filtros['tipoDocumento']) && $filtros['tipoDocumento'] !== 'all') {
                $tiposValidos = ['Cédula de Ciudadanía', 'Tarjeta de Identidad', 'Cedula de Extranjeria', 'Pasaporte', 'Registro Civil'];
                if (in_array($filtros['tipoDocumento'], $tiposValidos, true)) {
                    $condiciones[] = "tipoDocumento = ?";
                    $params[] = $filtros['tipoDocumento'];
                    $types .= "s";
                }
            }
            
            if (!empty($filtros['sexo']) && $filtros['sexo'] !== 'all') {
                $sexosValidos = ['Hombre', 'Mujer', 'Otro', 'Prefiero no decirlo'];
                if (in_array($filtros['sexo'], $sexosValidos, true)) {
                    $condiciones[] = "sexo = ?";
                    $params[] = $filtros['sexo'];
                    $types .= "s";
                }
            }
            
            if (!empty($condiciones)) {
                $sql .= " AND " . implode(" AND ", $condiciones);
            }
            
            $sql .= " ORDER BY nombres ASC, apellidos ASC";
            
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Error al preparar la consulta de listado: ' . $this->conexion->error);
            }
            
            if (!empty($params)) {
                $stmt->bind_param($types, ...$params);
            }
            
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar consulta de listado: ' . $stmt->error);
            }
            
            $resultado = $stmt->get_result();
            
            $colaboradores = [];
            while ($row = $resultado->fetch_assoc()) {
                $colaboradores[] = $row;
            }
            
            return ['success' => true, 'data' => $colaboradores];
            
        } catch (Exception $e) {
            $this->logError('Error en listar(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al listar colaboradores: ' . $e->getMessage()];
        }
    }
    
    /**
     * Obtener un colaborador por documento
     */
    public function obtenerPorDocumento($numDocumento) {
        try {
            $this->verificarConexion();
            
            if (empty($numDocumento)) {
                return ['success' => false, 'message' => 'Número de documento requerido'];
            }
            
            $sql = "SELECT * FROM tp_usuarios WHERE numDocumento = ?";
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Error al preparar consulta: ' . $this->conexion->error);
            }
            
            $stmt->bind_param("s", $numDocumento);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al ejecutar consulta: ' . $stmt->error);
            }
            
            $resultado = $stmt->get_result();
            $colaborador = $resultado->fetch_assoc();
            
            if ($colaborador) {
                // No retornar información sensible
                unset($colaborador['password']);
                unset($colaborador['tokenPassword']);
                return ['success' => true, 'data' => $colaborador];
            } else {
                return ['success' => false, 'message' => 'Colaborador no encontrado'];
            }
            
        } catch (Exception $e) {
            $this->logError('Error en obtenerPorDocumento(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al obtener colaborador: ' . $e->getMessage()];
        }
    }
    
    /**
     * Actualizar un colaborador
     * MODIFICADO para prevenir cambio a rol Administrador
     */
    public function actualizar($numDocumentoOriginal, $datos) {
        try {
            $this->verificarConexion();
            
            // Verificar si el colaborador existe
            $colaboradorExistente = $this->obtenerPorDocumento($numDocumentoOriginal);
            if (!$colaboradorExistente['success']) {
                return ['success' => false, 'message' => 'Colaborador no encontrado'];
            }
            
            // VALIDAR QUE NO SE PUEDA CAMBIAR A ADMINISTRADOR
            if (isset($datos['roles']) && $datos['roles'] === 'Administrador') {
                return ['success' => false, 'message' => 'No tiene permisos para asignar el rol de Administrador'];
            }
            
            // Si se cambió el documento, verificar que el nuevo no exista
            if ($datos['numDocumento'] !== $numDocumentoOriginal && $this->existeDocumento($datos['numDocumento'])) {
                return ['success' => false, 'message' => 'El nuevo número de documento ya está registrado'];
            }
            
            // Si se cambió el correo, verificar que el nuevo no exista
            if ($datos['correo'] !== $colaboradorExistente['data']['correo'] && $this->existeCorreo($datos['correo'])) {
                return ['success' => false, 'message' => 'El nuevo correo ya está registrado'];
            }
            
            $this->conexion->begin_transaction();
            
            try {
                // Preparar campos para actualizar
                $campos = [
                    'numDocumento' => 's', 'tipoDocumento' => 's', 'nombres' => 's',
                    'apellidos' => 's', 'numTelefono' => 's', 'correo' => 's',
                    'sexo' => 's', 'fechaNacimiento' => 's', 'roles' => 's',
                    'solicitarContraseña' => 's'
                ];
                
                $updateFields = [];
                $params = [];
                $types = "";

                foreach ($campos as $campo => $tipo) {
                    if (isset($datos[$campo])) {
                        $updateFields[] = "$campo = ?";
                        if ($campo === 'solicitarContraseña') {
                            $params[] = ($datos[$campo] === '1' || $datos[$campo] === true) ? '1' : '0';
                        } else {
                            $params[] = $datos[$campo];
                        }
                        $types .= $tipo;
                    }
                }
                
                // Si se proporciona nueva contraseña
                if (!empty($datos['password'])) {
                    $campos[] = 'password = ?';
                    $params[] = password_hash($datos['password'], PASSWORD_DEFAULT);
                    $types .= "s";
                }
                
                // Agregar documento original para WHERE
                $params[] = $numDocumentoOriginal;
                $types .= "s";
                
                $sql = "UPDATE tp_usuarios SET " . implode(", ", $updateFields) . " WHERE numDocumento = ?";
                
                $stmt = $this->conexion->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception('Error al preparar consulta de actualización: ' . $this->conexion->error);
                }
                
                $stmt->bind_param($types, ...$params);
                
                if (!$stmt->execute()) {
                    throw new Exception('Error al ejecutar actualización: ' . $stmt->error);
                }
                
                $this->conexion->commit();
                return ['success' => true, 'message' => 'Colaborador actualizado exitosamente'];
                
            } catch (Exception $e) {
                $this->conexion->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            $this->logError('Error en actualizar(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema: ' . $e->getMessage()];
        }
    }
    
    /**
     * Eliminar un colaborador
     * MODIFICADO para prevenir eliminación de administradores
     */
    public function eliminar($numDocumento) {
        try {
            $this->verificarConexion();
            
            if (empty($numDocumento)) {
                return ['success' => false, 'message' => 'Número de documento requerido'];
            }
            
            // Verificar si el colaborador existe
            $colaborador = $this->obtenerPorDocumento($numDocumento);
            if (!$colaborador['success']) {
                return ['success' => false, 'message' => 'Colaborador no encontrado'];
            }
            
            // VERIFICAR QUE NO SEA UN ADMINISTRADOR
            if ($colaborador['data']['roles'] === 'Administrador') {
                return ['success' => false, 'message' => 'No tiene permisos para eliminar administradores'];
            }
            
            $this->conexion->begin_transaction();
            
            try {
                // 1. Eliminar foto si existe
                if (!empty($colaborador['data']['foto'])) {
                    $this->eliminarFoto($colaborador['data']['foto']);
                }
                
                // 2. Eliminar la referencia en la tabla de personal del hotel
                $sqlPersonal = "DELETE FROM ti_personal WHERE numDocumento = ?";
                $stmtPersonal = $this->conexion->prepare($sqlPersonal);
                if (!$stmtPersonal) {
                    throw new Exception('Error al preparar la consulta para eliminar de personal: ' . $this->conexion->error);
                }
                $stmtPersonal->bind_param("s", $numDocumento);
                $stmtPersonal->execute(); // Se ejecuta para eliminar la dependencia
                $stmtPersonal->close();
                
                // 3. Eliminar el usuario de la tabla principal
                $sql = "DELETE FROM tp_usuarios WHERE numDocumento = ? AND roles IN ('Colaborador', 'Usuario')";
                $stmt = $this->conexion->prepare($sql);
                
                if (!$stmt) {
                    throw new Exception('Error al preparar consulta de eliminación: ' . $this->conexion->error);
                }
                
                $stmt->bind_param("s", $numDocumento);
                
                if (!$stmt->execute()) {
                    throw new Exception('Error al ejecutar eliminación: ' . $stmt->error);
                }
                
                if ($stmt->affected_rows > 0) {
                    $this->conexion->commit();
                    return ['success' => true, 'message' => 'Colaborador eliminado exitosamente'];
                } else {
                    $this->conexion->rollback();
                    return ['success' => false, 'message' => 'No se pudo eliminar el colaborador. Es posible que no exista o ya haya sido eliminado.'];
                }
                
            } catch (Exception $e) {
                $this->conexion->rollback();
                throw $e;
            }
            
        } catch (Exception $e) {
            $this->logError('Error en eliminar(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema: ' . $e->getMessage()];
        }
    }
    
    /**
     * Cambiar contraseña de un colaborador
     */
    public function cambiarPassword($numDocumento, $nuevaPassword, $solicitarCambio = false) {
        try {
            $this->verificarConexion();
            
            if (empty($numDocumento) || empty($nuevaPassword)) {
                return ['success' => false, 'message' => 'Documento y contraseña son requeridos'];
            }
            
            if (strlen($nuevaPassword) < 6) {
                return ['success' => false, 'message' => 'La contraseña debe tener al menos 6 caracteres'];
            }
            
            $passwordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
            $solicitarContraseña = $solicitarCambio ? '1' : '0';
            
            $sql = "UPDATE tp_usuarios SET password = ?, solicitarContraseña = ? WHERE numDocumento = ?";
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception('Error al preparar consulta: ' . $this->conexion->error);
            }
            
            $stmt->bind_param("sss", $passwordHash, $solicitarContraseña, $numDocumento);
            
            if (!$stmt->execute()) {
                throw new Exception('Error al cambiar contraseña: ' . $stmt->error);
            }
            
            if ($stmt->affected_rows > 0) {
                return ['success' => true, 'message' => 'Contraseña actualizada exitosamente'];
            } else {
                return ['success' => false, 'message' => 'No se encontró el colaborador'];
            }
            
        } catch (Exception $e) {
            $this->logError('Error en cambiarPassword(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error del sistema: ' . $e->getMessage()];
        }
    }
    
    /**
     * Verificar si existe un documento - MEJORADO
     */
    public function existeDocumento($numDocumento) {
        try {
            $this->verificarConexion();
            
            if (empty($numDocumento)) {
                return false;
            }
            
            $sql = "SELECT COUNT(*) as count FROM tp_usuarios WHERE numDocumento = ?";
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                $this->logError('Error al preparar consulta existeDocumento: ' . $this->conexion->error);
                return false;
            }
            
            $stmt->bind_param("s", $numDocumento);
            
            if (!$stmt->execute()) {
                $this->logError('Error al ejecutar existeDocumento: ' . $stmt->error);
                return false;
            }
            
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
            
            return $row['count'] > 0;
            
        } catch (Exception $e) {
            $this->logError('Error en existeDocumento(): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verificar si existe un correo - MEJORADO
     */
    public function existeCorreo($correo) {
        try {
            $this->verificarConexion();
            
            if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
                return false;
            }
            
            $sql = "SELECT COUNT(*) as count FROM tp_usuarios WHERE correo = ?";
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                $this->logError('Error al preparar consulta existeCorreo: ' . $this->conexion->error);
                return false;
            }
            
            $stmt->bind_param("s", $correo);
            
            if (!$stmt->execute()) {
                $this->logError('Error al ejecutar existeCorreo: ' . $stmt->error);
                return false;
            }
            
            $resultado = $stmt->get_result();
            $row = $resultado->fetch_assoc();
            
            return $row['count'] > 0;
            
        } catch (Exception $e) {
            $this->logError('Error en existeCorreo(): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Validar edad mínima (18 años)
     */
    private function validarEdadMinima($fechaNacimiento) {
        try {
            if (empty($fechaNacimiento)) {
                return false;
            }
            
            $hoy = new DateTime();
            $fechaNac = new DateTime($fechaNacimiento);
            $edad = $hoy->diff($fechaNac)->y;
            
            return $edad >= 18;
            
        } catch (Exception $e) {
            $this->logError('Error en validarEdadMinima(): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Guardar foto de perfil
     */
    private function guardarFoto($archivo, $documento) {
        try {
            // Validar el archivo
            $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($archivo['type'], $tiposPermitidos)) {
                $this->logError('Tipo de archivo no permitido: ' . $archivo['type']);
                return false;
            }
            
            // Validar tamaño (2MB máximo)
            if ($archivo['size'] > 2 * 1024 * 1024) {
                $this->logError('Archivo demasiado grande: ' . $archivo['size'] . ' bytes');
                return false;
            }
            
            // Crear directorio si no existe
            $directorioFotos = '../../public/assets/img/';
            if (!file_exists($directorioFotos)) {
                if (!mkdir($directorioFotos, 0755, true)) {
                    $this->logError('No se pudo crear directorio de fotos');
                    return false;
                }
            }
            
            // Generar nombre único y seguro
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $extension = strtolower($extension);
            $nombreArchivo = 'colaborador_' . preg_replace('/[^a-zA-Z0-9]/', '', $documento) . '_' . time() . '.' . $extension;
            $rutaCompleta = $directorioFotos . $nombreArchivo;
            
            // Mover archivo
            if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
                return 'assets/img/' . $nombreArchivo;
            } else {
                $this->logError('Error al mover archivo de foto');
                return false;
            }
            
        } catch (Exception $e) {
            $this->logError('Error en guardarFoto(): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Eliminar foto de perfil
     */
    private function eliminarFoto($rutaFoto) {
        try {
            if (empty($rutaFoto)) return;
            
            $rutaCompleta = '../../public/img/' . $rutaFoto;
            if (file_exists($rutaCompleta)) {
                unlink($rutaCompleta);
            }
        } catch (Exception $e) {
            $this->logError('Error al eliminar foto: ' . $e->getMessage());
        }
    }
    
    /**
     * Obtener estadísticas de colaboradores (sin administradores)
     * MODIFICADO PARA EXCLUIR ADMINISTRADORES
     */
    public function obtenerEstadisticas() {
        try {
            $this->verificarConexion();
            
            $sql = "SELECT 
                        COUNT(*) as total,
                        SUM(CASE WHEN roles = 'Colaborador' THEN 1 ELSE 0 END) as colaboradores,
                        SUM(CASE WHEN roles = 'Usuario' THEN 1 ELSE 0 END) as usuarios,
                        SUM(CASE WHEN solicitarContraseña = '1' THEN 1 ELSE 0 END) as pendientes_password
                    FROM tp_usuarios 
                    WHERE roles IN ('Colaborador', 'Usuario')";
            
            $resultado = $this->conexion->query($sql);
            
            if (!$resultado) {
                throw new Exception('Error al obtener estadísticas: ' . $this->conexion->error);
            }
            
            $estadisticas = $resultado->fetch_assoc();
            
            return ['success' => true, 'data' => $estadisticas];
            
        } catch (Exception $e) {
            $this->logError('Error en obtenerEstadisticas(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al obtener estadísticas: ' . $e->getMessage()];
        }
    }
    
    /**
     * Validar datos de entrada - MODIFICADO PARA RESTRICCIÓN DE ROLES
     */
    public function validarDatos($datos, $esActualizacion = false) {
        $errores = [];
        
        // Validar documento
        if (empty($datos['numDocumento'])) {
            $errores[] = 'El número de documento es requerido';
        } elseif (strlen($datos['numDocumento']) < 6 || strlen($datos['numDocumento']) > 15) {
            $errores[] = 'El documento debe tener entre 6 y 15 caracteres';
        }
        
        // Validar tipo de documento
        $tiposValidos = ['Cédula de Ciudadanía', 'Tarjeta de Identidad', 'Cedula de Extranjeria', 'Pasaporte', 'Registro Civil'];
        if (empty($datos['tipoDocumento']) || !in_array($datos['tipoDocumento'], $tiposValidos)) {
            $errores[] = 'Tipo de documento inválido';
        }
        
        // Validar nombres
        if (empty($datos['nombres'])) {
            $errores[] = 'Los nombres son requeridos';
        } elseif (strlen($datos['nombres']) > 50) {
            $errores[] = 'Los nombres no pueden exceder 50 caracteres';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['nombres'])) {
            $errores[] = 'Los nombres solo pueden contener letras y espacios';
        }
        
        // Validar apellidos
        if (empty($datos['apellidos'])) {
            $errores[] = 'Los apellidos son requeridos';
        } elseif (strlen($datos['apellidos']) > 50) {
            $errores[] = 'Los apellidos no pueden exceder 50 caracteres';
        } elseif (!preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/', $datos['apellidos'])) {
            $errores[] = 'Los apellidos solo pueden contener letras y espacios';
        }
        
        // Validar correo
        if (empty($datos['correo'])) {
            $errores[] = 'El correo electrónico es requerido';
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = 'Correo electrónico inválido';
        } elseif (strlen($datos['correo']) > 255) {
            $errores[] = 'El correo es demasiado largo';
        }
        
        // Validar teléfono
        if (empty($datos['numTelefono'])) {
            $errores[] = 'El número de teléfono es requerido';
        } elseif (!preg_match('/^[0-9+\-\s()]{7,15}$/', $datos['numTelefono'])) {
            $errores[] = 'Número de teléfono inválido';
        }
        
        // Validar sexo
        $sexosValidos = ['Hombre', 'Mujer', 'Otro', 'Prefiero no decirlo'];
        if (empty($datos['sexo']) || !in_array($datos['sexo'], $sexosValidos)) {
            $errores[] = 'Sexo inválido';
        }
        
        // Validar fecha de nacimiento
        if (empty($datos['fechaNacimiento'])) {
            $errores[] = 'La fecha de nacimiento es requerida';
        } else {
            try {
                $fechaNac = new DateTime($datos['fechaNacimiento']);
                $hoy = new DateTime();
                
                // Validar que no sea fecha futura
                if ($fechaNac > $hoy) {
                    $errores[] = 'La fecha de nacimiento no puede ser futura';
                }
                
                // Validar edad mínima
                if (!$this->validarEdadMinima($datos['fechaNacimiento'])) {
                    $errores[] = 'El colaborador debe ser mayor de 18 años';
                }
                
                // Validar edad máxima razonable (120 años)
                $edadMaxima = $hoy->diff($fechaNac)->y;
                if ($edadMaxima > 120) {
                    $errores[] = 'Fecha de nacimiento no válida';
                }
                
            } catch (Exception $e) {
                $errores[] = 'Formato de fecha de nacimiento inválido';
            }
        }
        
        // VALIDAR ROL - SOLO COLABORADOR Y USUARIO PERMITIDOS
        $rolesValidos = ['Colaborador', 'Usuario'];
        if (empty($datos['roles']) || !in_array($datos['roles'], $rolesValidos)) {
            $errores[] = 'Rol inválido. Solo se permiten roles de Colaborador o Usuario';
        }
        
        // Validar contraseña (solo para creación o si se proporciona)
        if (!$esActualizacion || !empty($datos['password'])) {
            if (empty($datos['password'])) {
                $errores[] = 'La contraseña es requerida';
            } elseif (strlen($datos['password']) < 6) {
                $errores[] = 'La contraseña debe tener al menos 6 caracteres';
            } elseif (strlen($datos['password']) > 255) {
                $errores[] = 'La contraseña es demasiado larga';
            }
        }
        
        return $errores;
    }
    
    /**
     * Verificar si un colaborador puede ser eliminado
     */
    public function puedeEliminar($numDocumento) {
        try {
            $this->verificarConexion();
            
            if (empty($numDocumento)) {
                return false;
            }
            
            // Verificar que no sea administrador
            $colaborador = $this->obtenerPorDocumento($numDocumento);
            if ($colaborador['success'] && $colaborador['data']['roles'] === 'Administrador') {
                return false;
            }
            
            // Aquí puedes agregar lógica adicional para verificar dependencias
            // Por ejemplo, verificar si tiene reservas, actividades, etc.
            
            return true; // Permitir eliminar colaboradores y usuarios
            
        } catch (Exception $e) {
            $this->logError('Error en puedeEliminar(): ' . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Función de logging de errores
     */
    private function logError($mensaje) {
        $fecha = date('Y-m-d H:i:s');
        $logMessage = "[$fecha] misColaboradoresModel.php Error: $mensaje" . PHP_EOL;
        
        // Intentar escribir al log de PHP
        error_log($logMessage);
        
        // También puedes escribir a un archivo específico si lo deseas
        $logFile = '../../logs/colaboradores_errors.log';
        $logDir = dirname($logFile);
        
        if (!file_exists($logDir)) {
            @mkdir($logDir, 0755, true);
        }
        
        @file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
    }
    
    /**
     * Contar colaboradores (sin administradores)
     */
    public function contar() {
        try {
            $this->verificarConexion();
            
            $sql = "SELECT COUNT(*) as total FROM tp_usuarios WHERE roles IN ('Colaborador', 'Usuario')";
            $resultado = $this->conexion->query($sql);
            
            if (!$resultado) {
                throw new Exception('Error al contar colaboradores: ' . $this->conexion->error);
            }
            
            $row = $resultado->fetch_assoc();
            
            return ['success' => true, 'total' => (int)$row['total']];
            
        } catch (Exception $e) {
            $this->logError('Error en contar(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al contar colaboradores: ' . $e->getMessage()];
        }
    }
    
    /**
     * Buscar colaboradores (alias para listar con búsqueda)
     */
    public function buscar($termino) {
        $filtros = ['busqueda' => $termino];
        return $this->listar($filtros);
    }
    
    /**
     * Obtener colaboradores por rol (solo Colaborador o Usuario)
     */
    public function obtenerPorRol($rol) {
        if (!in_array($rol, ['Colaborador', 'Usuario'])) {
            return ['success' => false, 'message' => 'Rol no permitido'];
        }
        
        $filtros = ['rol' => $rol];
        return $this->listar($filtros);
    }
    
    /**
     * Verificar integridad de la base de datos
     */
    public function verificarIntegridad() {
        try {
            $this->verificarConexion();
            
            // Verificar estructura de tabla
            $sql = "DESCRIBE tp_usuarios";
            $resultado = $this->conexion->query($sql);
            
            if (!$resultado) {
                return ['success' => false, 'message' => 'Error al verificar estructura de tabla'];
            }
            
            return ['success' => true, 'message' => 'Integridad verificada'];
            
        } catch (Exception $e) {
            $this->logError('Error en verificarIntegridad(): ' . $e->getMessage());
            return ['success' => false, 'message' => 'Error al verificar integridad'];
        }
    }
}
?>