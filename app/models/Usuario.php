<?php

class Usuario
{
    // Variable para guardar la conexión a la BD
    private $db;

    // El constructor recibe la conexión a la BD cuando se crea un objeto Usuario
    public function __construct($database_connection)
    {
        $this->db = $database_connection;
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     * Recibe un array con los datos validados del controlador.
     * @param array $datos Array con los datos del usuario
     * @return bool Devuelve true si tuvo éxito, false si falló.
     */
    public function crear(array $datos)
    {
        $sql = "INSERT INTO tp_usuarios
                (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, sexo, fechaNacimiento, password, foto, solicitarContraseña, tokenPassword, sesionCaducada, roles)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, '0', NULL, 'Activo', ?)";

        try {
            $stmt = $this->db->prepare($sql);
            
            // El execute devuelve true o false directamente
            return $stmt->execute([
                $datos['numDocumento'],
                $datos['tipoDocumento'],
                $datos['nombres'],
                $datos['apellidos'],
                $datos['numTelefono'],
                $datos['correo'],
                $datos['sexo'],
                $datos['fechaNacimiento'],
                $datos['password'], // El controlador le pasa el hash 
                $datos['foto'], // Puede ser null si no se subió imagen
                $datos['roles']
            ]);
        } catch (PDOException $e) {
            // Log del error para debugging
            error_log("Error al crear usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si ya existe un usuario con el mismo documento o correo
     * @param string $numDocumento
     * @param string $correo
     * @return bool
     */
    public function existeUsuario($numDocumento, $correo)
    {
        $sql = "SELECT COUNT(*) FROM tp_usuarios WHERE numDocumento = ? OR correo = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numDocumento, $correo]);
            $count = $stmt->fetchColumn();
            
            return $count > 0;
        } catch (PDOException $e) {
            error_log("Error al verificar usuario existente: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un usuario por su número de documento
     * @param string $numDocumento
     * @return array|false
     */
    public function obtenerPorId($numDocumento)
    {
        $sql = "SELECT * FROM tp_usuarios WHERE numDocumento = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numDocumento]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // fetch() para un solo resultado
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene un usuario por correo electrónico (útil para login)
     * @param string $correo
     * @return array|false
     */
    public function obtenerPorCorreo($correo)
    {
        $sql = "SELECT * FROM tp_usuarios WHERE correo = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$correo]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuario por correo: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza los datos de un usuario existente.
     * @param string $id Número de documento del usuario
     * @param array $datos Datos a actualizar
     * @return bool
     */
    public function actualizar($id, array $datos)
    {
        // Solo actualizar campos que pueden ser modificados
        $sql = "UPDATE tp_usuarios SET 
                numTelefono = ?,
                correo = ?";

        $parametros = [
            $datos['numTelefono'],
            $datos['correo']
        ];

        // Si se proporciona una nueva contraseña, incluirla en la actualización
        if (!empty($datos['password'])) {
            $sql .= ", password = ?";
            $parametros[] = password_hash($datos['password'], PASSWORD_DEFAULT);
        }

        // Si se proporciona una nueva foto, incluirla en la actualización
        if (!empty($datos['foto'])) {
            $sql .= ", foto = ?";
            $parametros[] = $datos['foto'];
        }

        $sql .= " WHERE numDocumento = ?";
        $parametros[] = $id;

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($parametros);
        } catch (PDOException $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario por su número de documento.
     * @param string $numDocumento
     * @return bool Devuelve true si tuvo éxito, false si falló.
     */
    public function eliminar($numDocumento)
    {
        $sql = "DELETE FROM tp_usuarios WHERE numDocumento = ?";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$numDocumento]);
        } catch (PDOException $e) {
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios de la base de datos
     * @return array
     */
    public function obtenerTodos()
    {
        // Consulta SQL para obtener todos los usuarios
        $sql = "SELECT 
                    numDocumento,
                    tipoDocumento,
                    nombres,
                    apellidos,
                    numTelefono,
                    correo,
                    sexo,
                    fechaNacimiento,
                    foto,
                    sesionCaducada,
                    roles,
                    DATE_FORMAT(fechaNacimiento, '%d/%m/%Y') as fechaNacimientoFormatted
                FROM tp_usuarios 
                ORDER BY nombres ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            // fetchAll devuelve todos los resultados en un array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener todos los usuarios: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Obtiene usuarios por rol específico
     * @param string $rol
     * @return array
     */
    public function obtenerPorRol($rol)
    {
        $sql = "SELECT * FROM tp_usuarios WHERE roles = ? ORDER BY nombres ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$rol]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al obtener usuarios por rol: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Cambia el estado de la sesión de un usuario
     * @param string $numDocumento
     * @param string $estado 'Activo' o 'Inactivo'
     * @return bool
     */
    public function cambiarEstadoSesion($numDocumento, $estado)
    {
        $sql = "UPDATE tp_usuarios SET sesionCaducada = ? WHERE numDocumento = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$estado, $numDocumento]);
        } catch (PDOException $e) {
            error_log("Error al cambiar estado de sesión: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Establece o actualiza el token de recuperación de contraseña
     * @param string $correo
     * @param string $token
     * @return bool
     */
    public function establecerTokenRecuperacion($correo, $token)
    {
        $sql = "UPDATE tp_usuarios SET tokenPassword = ?, solicitarContraseña = '1' WHERE correo = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$token, $correo]);
        } catch (PDOException $e) {
            error_log("Error al establecer token de recuperación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verifica si un token de recuperación es válido
     * @param string $token
     * @return array|false
     */
    public function verificarTokenRecuperacion($token)
    {
        $sql = "SELECT * FROM tp_usuarios WHERE tokenPassword = ? AND solicitarContraseña = '1'";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$token]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error al verificar token de recuperación: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza la contraseña y limpia el token de recuperación
     * @param string $token
     * @param string $nuevaPassword
     * @return bool
     */
    public function actualizarPasswordConToken($token, $nuevaPassword)
    {
        $sql = "UPDATE tp_usuarios SET 
                password = ?, 
                tokenPassword = NULL, 
                solicitarContraseña = '0' 
                WHERE tokenPassword = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                password_hash($nuevaPassword, PASSWORD_DEFAULT),
                $token
            ]);
        } catch (PDOException $e) {
            error_log("Error al actualizar contraseña con token: " . $e->getMessage());
            return false;
        }
    }
}
