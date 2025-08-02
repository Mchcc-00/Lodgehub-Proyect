<?php
// config/config.php
// Database configuration
$host = 'localhost';    
$usuario = 'root';
$password = '';
$db = 'lodgehub';

$conexion = new mysqli($host, $usuario, $password, $db);

if ($conexion->connect_error) {
    echo "fallo la conexion a la base de datos " . $conexion->connect_error ;
}
?>