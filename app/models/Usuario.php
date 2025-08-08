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

    
    //  Crea un nuevo usuario en la base de datos.
    //  Recibe un array con los datos validados del controlador.
    //  @return bool Devuelve true si tuvo éxito, false si falló.
    public function crear(array $datos)
    {
        $sql = "INSERT INTO tp_usuarios
                (numDocumento, tipoDocumento, nombres, apellidos, numTelefono, correo, sexo, fechaNacimiento, password, foto, tokenPassword, sesionCaducada, roles)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

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
                $datos['foto'],
                $datos['tokenPassword'],
                $datos['sesionCaducada'],
                $datos['roles']
            ]);
        } catch (PDOException $e) {
            die("Error al insertar en la base de datos: " . $e->getMessage());
        }
    }

    //Actualizar un usuario existente.
    // @return bool Devuelve true si tuvo éxito, false si falló.
    //
    public function obtenerPorId($numDocumento)
    {
        $sql = "SELECT * FROM tp_usuarios WHERE numDocumento = ?";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$numDocumento]);
            return $stmt->fetch(PDO::FETCH_ASSOC); // fetch() para un solo resultado
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
    
    // Actualizar los datos de un usuario existente.
    public function actualizar($id, array $datos)
    {
        // La consulta incluye todos los campos que queremos poder editar
        $sql = "UPDATE tp_usuarios SET 
                numTelefono = ?,
                correo = ?,
                password = ?,
                foto = ?, 
            
            WHERE numDocumento = ?";

        try {
            $stmt = $this->db->prepare($sql);

            // El array de execute debe tener los valores en el mismo orden que los '?'
            return $stmt->execute([
                $datos['numTelefono'],
                $datos['correo'],
                $datos['correo'],
                $datos['password'],
                $datos['foto'],
                $id // Aquí usamos el ID del usuario que queremos actualizar
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    //Eliminar un usuario por su número de documento.
    //@return bool Devuelve true si tuvo éxito, false si falló.
    public function eliminar($numDocumento)
    {
        $sql = "DELETE FROM tp_usuarios WHERE numDocumento = ?";

        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$numDocumento]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function obtenerTodos()
    {
        // La consulta SQL para obtener todos los usuarios
        $sql = "SELECT
                    d.descripcion AS tipo_documento,
                    e.numDocumento,
                    e.nombres,
                    e.apellidos,
                    s.descripcion AS sexo,
                    e.numTelefono,
                    e.telEmergencia,
                    e.correo,
                    r.descripcion AS rol
                FROM tp_empleados e
                INNER JOIN td_tipodocumento d ON e.tipoDocumento = d.id
                INNER JOIN td_sexo s ON e.sexo = s.id
                INNER JOIN td_roles r ON e.roles = r.id
                ORDER BY e.nombres ASC";
        
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            // fetchAll devuelve todos los resultados en un array
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            
            error_log($e->getMessage());
            return [];
        }
    }
}
