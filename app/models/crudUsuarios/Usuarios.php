<?php

class Usuario {
    // Variable para guardar la conexión a la BD
    private $db;

    // El constructor recibe la conexión a la BD cuando se crea un objeto Usuario
    public function __construct($database_connection) {
        $this->db = $database_connection;
    }

    /**
     * Crea un nuevo usuario en la base de datos.
     * Recibe un array con los datos validados del controlador.
     * @return bool Devuelve true si tuvo éxito, false si falló.
     */
    public function guardar(array $datos) {
        $sql = "INSERT INTO tp_empleados
                (numDocumento, nombres, apellidos, direccion, fechaNacimiento, numTelefono, contactoPersonal, password, correo, rnt, nit, sexo, tipoDocumento, roles)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $this->db->prepare($sql);
            // El execute devuelve true o false directamente
            return $stmt->execute([
                $datos['numDocumento'],
                $datos['nombres'],
                $datos['apellidos'],
                $datos['direccion'],
                $datos['fecha_nacimiento'], // Asegúrate que el name en el form sea este
                $datos['numTelefono'],
                $datos['telEmergencia'], // Asegúrate que el name en el form sea este
                $datos['password_hash'], // El controlador le pasa el hash
                $datos['correo'],
                $datos['rnt'],
                $datos['nit'],
                $datos['sexo'],
                $datos['tipoDocumento'],
                $datos['roles']
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }


    //Actualiza un usuario existente.
    // @return bool Devuelve true si tuvo éxito, false si falló.
    //
    public function actualizar($id, array $datos) {
        $sql = "UPDATE tp_empleados SET direccion = ?, numTelefono = ?, correo = ?, roles = ? WHERE numDocumento = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $datos['direccion'], 
                $datos['numTelefono'], 
                $datos['correo'], 
                $datos['roles'], 
                $id
            ]);
        } catch (PDOException $e) {
            return false;
        }
    }


    //Elimina un usuario por su número de documento.
    //@return bool Devuelve true si tuvo éxito, false si falló.
    //
    public function eliminar($numDocumento) {
        $sql = "DELETE FROM tp_empleados WHERE numDocumento = ?";
        
        try {
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([$numDocumento]);
        } catch (PDOException $e) {
            return false;
        }
    }
    
    // Puedes añadir más métodos como:
    // public function obtenerTodos() { ... }
    // public function obtenerPorId($id) { ... }
}
?>