<?php
// Muestra errores para depuración
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carga el controlador de usuarios
require_once '../app/Core/Router.php';
require_once '../app/Controllers/Usuarios.php';

// Rutina simple para decidir qué acción ejecutar
$controller = new UsuarioController();

$action = $_GET['action'] ?? 'mostrarFormularioCreacion';

if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo "Acción no encontrada.";
}