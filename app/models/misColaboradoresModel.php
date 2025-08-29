<?php
class Usuario {
    private $conexion;
    private $tabla = "tp_usuarios";

    // Propiedades del usuario
    public $numDocumento;
    public $tipoDocumento;
    public $nombres;
    public $apellidos;
    public $numTelefono;
    public $correo;
    public $sexo;
    public $fechaNacimiento;
    public $password;
    public $foto;
    public $solicitarContraseña;
    public $tokenPassword;
    public $sesionCaducada;
    public $roles;

    public function __construct($db) {
        $this->conexion = $db;
    }

    // Crear un nuevo usuario/colaborador
    public function crear() {
        $query = "INSERT INTO " . $this->tabla . " 
                  (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, 
                   sexo, fechaNacimiento, password, foto, solicitarContraseña, roles) 
                  VALUES 
                  (:numDocumento, :tipoDocumento, :nombres, :apellidos, :numTelefono, :correo, 
                   :sexo, :fechaNacimiento, :password, :foto, :solicitarContraseña, :roles)";

        $stmt = $this->conexion->prepare($query);

        // Limpiar datos
        $this->numDocumento = htmlspecialchars(strip_tags($this->numDocumento));
        $this->tipoDocumento = htmlspecialchars(strip_tags($this->tipoDocumento));
        $this->nombres = htmlspecialchars(strip_tags($this->nombres));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->numTelefono = htmlspecialchars(strip_tags($this->numTelefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->fechaNacimiento = htmlspecialchars(strip_tags($this->fechaNacimiento));
        $this->foto = htmlspecialchars(strip_tags($this->foto));
        $this->roles = htmlspecialchars(strip_tags($this->roles));

        // Encriptar contraseña
        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);

        // Bind de parámetros
        $stmt->bindParam(':numDocumento', $this->numDocumento);
        $stmt->bindParam(':tipoDocumento', $this->tipoDocumento);
        $stmt->bindParam(':nombres', $this->nombres);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':numTelefono', $this->numTelefono);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':fechaNacimiento', $this->fechaNacimiento);
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':foto', $this->foto);
        $stmt->bindParam(':solicitarContraseña', $this->solicitarContraseña);
        $stmt->bindParam(':roles', $this->roles);

        return $stmt->execute();
    }

    // Leer todos los colaboradores con paginación y filtros
    public function leer($offset = 0, $limit = 10, $busqueda = '', $filtro = 'all', $valorFiltro = '') {
        $query = "SELECT numDocumento, tipoDocumento, nombres, apellidos, numTelefono, 
                         correo, sexo, fechaNacimiento, foto, solicitarContraseña, roles,
                         sesionCaducada
                  FROM " . $this->tabla . " 
                  WHERE 1=1";

        $params = array();

        // Aplicar búsqueda
        if (!empty($busqueda)) {
            $query .= " AND (numDocumento LIKE :busqueda 
                            OR nombres LIKE :busqueda 
                            OR apellidos LIKE :busqueda 
                            OR correo LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        // Aplicar filtros
        if ($filtro !== 'all' && !empty($valorFiltro)) {
            if ($filtro === 'roles') {
                $query .= " AND roles = :valorFiltro";
                $params[':valorFiltro'] = $valorFiltro;
            } elseif ($filtro === 'tipoDocumento') {
                $query .= " AND tipoDocumento = :valorFiltro";
                $params[':valorFiltro'] = $valorFiltro;
            } elseif ($filtro === 'sexo') {
                $query .= " AND sexo = :valorFiltro";
                $params[':valorFiltro'] = $valorFiltro;
            }
        }

        $query .= " ORDER BY nombres ASC LIMIT :offset, :limit";

        $stmt = $this->conexion->prepare($query);
        
        // Bind parámetros de búsqueda y filtro
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }
        
        // Bind parámetros de paginación
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);

        $stmt->execute();
        return $stmt;
    }

    // Contar total de colaboradores (para paginación)
    public function contarTotal($busqueda = '', $filtro = 'all', $valorFiltro = '') {
        $query = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE 1=1";
        $params = array();

        if (!empty($busqueda)) {
            $query .= " AND (numDocumento LIKE :busqueda 
                            OR nombres LIKE :busqueda 
                            OR apellidos LIKE :busqueda 
                            OR correo LIKE :busqueda)";
            $params[':busqueda'] = '%' . $busqueda . '%';
        }

        if ($filtro !== 'all' && !empty($valorFiltro)) {
            if ($filtro === 'roles') {
                $query .= " AND roles = :valorFiltro";
                $params[':valorFiltro'] = $valorFiltro;
            } elseif ($filtro === 'tipoDocumento') {
                $query .= " AND tipoDocumento = :valorFiltro";
                $params[':valorFiltro'] = $valorFiltro;
            } elseif ($filtro === 'sexo') {
                $query .= " AND sexo = :valorFiltro";
                $params[':valorFiltro'] = $valorFiltro;
            }
        }

        $stmt = $this->conexion->prepare($query);
        
        foreach ($params as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'];
    }

    // Leer un colaborador por documento
    public function leerUno() {
        $query = "SELECT * FROM " . $this->tabla . " WHERE numDocumento = :numDocumento LIMIT 0,1";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':numDocumento', $this->numDocumento);
        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $this->tipoDocumento = $row['tipoDocumento'];
            $this->nombres = $row['nombres'];
            $this->apellidos = $row['apellidos'];
            $this->numTelefono = $row['numTelefono'];
            $this->correo = $row['correo'];
            $this->sexo = $row['sexo'];
            $this->fechaNacimiento = $row['fechaNacimiento'];
            $this->foto = $row['foto'];
            $this->solicitarContraseña = $row['solicitarContraseña'];
            $this->tokenPassword = $row['tokenPassword'];
            $this->sesionCaducada = $row['sesionCaducada'];
            $this->roles = $row['roles'];
            return true;
        }
        return false;
    }

    // Actualizar colaborador
    public function actualizar() {
        $query = "UPDATE " . $this->tabla . " SET 
                  tipoDocumento = :tipoDocumento,
                  nombres = :nombres,
                  apellidos = :apellidos,
                  numTelefono = :numTelefono,
                  correo = :correo,
                  sexo = :sexo,
                  fechaNacimiento = :fechaNacimiento,
                  roles = :roles,
                  solicitarContraseña = :solicitarContraseña";

        // Si se proporciona una nueva contraseña, incluirla en la actualización
        if (!empty($this->password)) {
            $query .= ", password = :password";
        }

        // Si se proporciona una nueva foto, incluirla
        if (!empty($this->foto)) {
            $query .= ", foto = :foto";
        }

        $query .= " WHERE numDocumento = :numDocumento_original";

        $stmt = $this->conexion->prepare($query);

        // Limpiar datos
        $this->tipoDocumento = htmlspecialchars(strip_tags($this->tipoDocumento));
        $this->nombres = htmlspecialchars(strip_tags($this->nombres));
        $this->apellidos = htmlspecialchars(strip_tags($this->apellidos));
        $this->numTelefono = htmlspecialchars(strip_tags($this->numTelefono));
        $this->correo = htmlspecialchars(strip_tags($this->correo));
        $this->sexo = htmlspecialchars(strip_tags($this->sexo));
        $this->fechaNacimiento = htmlspecialchars(strip_tags($this->fechaNacimiento));
        $this->roles = htmlspecialchars(strip_tags($this->roles));

        // Bind de parámetros básicos
        $stmt->bindParam(':tipoDocumento', $this->tipoDocumento);
        $stmt->bindParam(':nombres', $this->nombres);
        $stmt->bindParam(':apellidos', $this->apellidos);
        $stmt->bindParam(':numTelefono', $this->numTelefono);
        $stmt->bindParam(':correo', $this->correo);
        $stmt->bindParam(':sexo', $this->sexo);
        $stmt->bindParam(':fechaNacimiento', $this->fechaNacimiento);
        $stmt->bindParam(':roles', $this->roles);
        $stmt->bindParam(':solicitarContraseña', $this->solicitarContraseña);
        $stmt->bindParam(':numDocumento_original', $this->numDocumento);

        // Bind condicional para contraseña
        if (!empty($this->password)) {
            $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
            $stmt->bindParam(':password', $passwordHash);
        }

        // Bind condicional para foto
        if (!empty($this->foto)) {
            $stmt->bindParam(':foto', $this->foto);
        }

        return $stmt->execute();
    }

    // Eliminar colaborador
    public function eliminar() {
        $query = "DELETE FROM " . $this->tabla . " WHERE numDocumento = :numDocumento";
        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':numDocumento', $this->numDocumento);
        return $stmt->execute();
    }

    // Verificar si existe un documento
    public function existeDocumento($documento, $documentoOriginal = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE numDocumento = :documento";
        
        // Si estamos editando, excluir el documento original
        if ($documentoOriginal !== null) {
            $query .= " AND numDocumento != :documentoOriginal";
        }

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':documento', $documento);
        
        if ($documentoOriginal !== null) {
            $stmt->bindParam(':documentoOriginal', $documentoOriginal);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }

    // Verificar si existe un correo
    public function existeCorreo($correo, $documentoOriginal = null) {
        $query = "SELECT COUNT(*) as total FROM " . $this->tabla . " WHERE correo = :correo";
        
        if ($documentoOriginal !== null) {
            $query .= " AND numDocumento != :documentoOriginal";
        }

        $stmt = $this->conexion->prepare($query);
        $stmt->bindParam(':correo', $correo);
        
        if ($documentoOriginal !== null) {
            $stmt->bindParam(':documentoOriginal', $documentoOriginal);
        }

        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row['total'] > 0;
    }

    // Cambiar contraseña específicamente
    public function cambiarPassword() {
        $query = "UPDATE " . $this->tabla . " SET 
                  password = :password,
                  solicitarContraseña = :solicitarContraseña
                  WHERE numDocumento = :numDocumento";

        $stmt = $this->conexion->prepare($query);

        $passwordHash = password_hash($this->password, PASSWORD_DEFAULT);
        
        $stmt->bindParam(':password', $passwordHash);
        $stmt->bindParam(':solicitarContraseña', $this->solicitarContraseña);
        $stmt->bindParam(':numDocumento', $this->numDocumento);

        return $stmt->execute();
    }

    // Validar datos del colaborador
    public function validar() {
        $errores = array();

        // Validar número de documento
        if (empty($this->numDocumento)) {
            $errores[] = "El número de documento es obligatorio";
        } elseif (strlen($this->numDocumento) > 15) {
            $errores[] = "El número de documento no puede tener más de 15 caracteres";
        }

        // Validar tipo de documento
        $tiposValidos = ['Cédula de Ciudadanía', 'Tarjeta de Identidad', 'Cedula de Extranjeria', 'Pasaporte', 'Registro Civil'];
        if (empty($this->tipoDocumento) || !in_array($this->tipoDocumento, $tiposValidos)) {
            $errores[] = "Debe seleccionar un tipo de documento válido";
        }

        // Validar nombres
        if (empty($this->nombres)) {
            $errores[] = "Los nombres son obligatorios";
        } elseif (strlen($this->nombres) > 50) {
            $errores[] = "Los nombres no pueden tener más de 50 caracteres";
        }

        // Validar apellidos
        if (empty($this->apellidos)) {
            $errores[] = "Los apellidos son obligatorios";
        } elseif (strlen($this->apellidos) > 50) {
            $errores[] = "Los apellidos no pueden tener más de 50 caracteres";
        }

        // Validar teléfono
        if (empty($this->numTelefono)) {
            $errores[] = "El teléfono es obligatorio";
        } elseif (strlen($this->numTelefono) > 15) {
            $errores[] = "El teléfono no puede tener más de 15 caracteres";
        }

        // Validar correo
        if (empty($this->correo)) {
            $errores[] = "El correo electrónico es obligatorio";
        } elseif (!filter_var($this->correo, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del correo electrónico no es válido";
        } elseif (strlen($this->correo) > 255) {
            $errores[] = "El correo electrónico es demasiado largo";
        }

        // Validar sexo
        $sexosValidos = ['Hombre', 'Mujer', 'Otro', 'Prefiero no decirlo'];
        if (empty($this->sexo) || !in_array($this->sexo, $sexosValidos)) {
            $errores[] = "Debe seleccionar un sexo válido";
        }

        // Validar fecha de nacimiento
        if (empty($this->fechaNacimiento)) {
            $errores[] = "La fecha de nacimiento es obligatoria";
        } else {
            $fecha = DateTime::createFromFormat('Y-m-d', $this->fechaNacimiento);
            if (!$fecha || $fecha->format('Y-m-d') !== $this->fechaNacimiento) {
                $errores[] = "La fecha de nacimiento no tiene un formato válido";
            } elseif ($fecha > new DateTime()) {
                $errores[] = "La fecha de nacimiento no puede ser futura";
            }
        }

        // Validar rol
        $rolesValidos = ['Administrador', 'Colaborador', 'Usuario'];
        if (empty($this->roles) || !in_array($this->roles, $rolesValidos)) {
            $errores[] = "Debe seleccionar un rol válido";
        }

        // Validar contraseña (solo en creación)
        if (!empty($this->password) && strlen($this->password) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }

        return $errores;
    }

    // Subir foto de perfil
    public function subirFoto($archivo) {
        $directorioDestino = "../../public/uploads/fotos/";
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif'];
        $tamañoMaximo = 2 * 1024 * 1024; // 2MB

        // Crear directorio si no existe
        if (!file_exists($directorioDestino)) {
            mkdir($directorioDestino, 0755, true);
        }

        // Validar archivo
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'Error al subir el archivo'];
        }

        if ($archivo['size'] > $tamañoMaximo) {
            return ['success' => false, 'message' => 'El archivo es demasiado grande (máx. 2MB)'];
        }

        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, $extensionesPermitidas)) {
            return ['success' => false, 'message' => 'Formato de archivo no permitido'];
        }

        // Generar nombre único
        $nombreArchivo = $this->numDocumento . '_' . time() . '.' . $extension;
        $rutaCompleta = $directorioDestino . $nombreArchivo;

        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return ['success' => true, 'filename' => $nombreArchivo];
        } else {
            return ['success' => false, 'message' => 'Error al guardar el archivo'];
        }
    }

    // Eliminar foto anterior
    public function eliminarFoto($nombreArchivo) {
        $rutaArchivo = "../../public/uploads/fotos/" . $nombreArchivo;
        if (file_exists($rutaArchivo)) {
            unlink($rutaArchivo);
        }
    }
}