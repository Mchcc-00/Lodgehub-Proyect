<?php
/**
 * Archivo de conexión global corregido
 * Proporciona tanto PDO como MySQLi para compatibilidad
 */

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'u705727716_oodyze');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8');

/**
 * Conexión PDO
 */
function conexionDB(){
    try {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $db = new PDO($dsn, DB_USER, DB_PASS);
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $db->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        return $db;
    } catch (PDOException $e) {
        error_log("Error de conexión PDO: " . $e->getMessage());
        return null;
    }
}

/**
 * Conexión MySQLi (para compatibilidad con el modelo existente)
 */
function conexionMySQLi() {
    try {
        $conexion = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        
        if ($conexion->connect_error) {
            throw new Exception("Error de conexión: " . $conexion->connect_error);
        }
        
        $conexion->set_charset(DB_CHARSET);
        return $conexion;
    } catch (Exception $e) {
        error_log("Error de conexión MySQLi: " . $e->getMessage());
        return null;
    }
}

// Variable global para MySQLi (requerida por el modelo)
$conexion = conexionMySQLi();

// Verificar que la conexión se estableció correctamente
if (!$conexion) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}
?>