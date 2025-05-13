<?php
// filepath: c:\xampp\htdocs\Lodgehub\database.php

// Configuración de la base de datos
$host = 'localhost'; // Servidor de la base de datos
$user = 'root';      // Usuario de la base de datos (por defecto en XAMPP es 'root')
$password = '';      // Contraseña del usuario (por defecto en XAMPP es vacío)
$dbname = 'lodgehub'; // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($host, $user, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión a la base de datos: " . $conn->connect_error);
}
if (!isset($conn)) {
    die("Error: No se pudo establecer la conexión a la base de datos.");
}
// Establecer el conjunto de caracteres a UTF-8
$conn->set_charset("utf8");
