<?php
//Mostrar Errores y Iniciar Sesión
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

define('BASE_URL', '/Lodge/public');

//Cargar Clases Necesarias
require_once '../app/Core/Router.php';
require_once '../app/Controllers/UsuarioController.php';

$basePath = '/Lodge/public'; // La subcarpeta donde vive el proyecto
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($uri, $basePath) === 0) {
    $uri = substr($uri, strlen($basePath));
}

if (empty($uri)) {
    $uri = '/';
}

$request_method = $_SERVER['REQUEST_METHOD'];
$router = new Router();

// Definir el Mapa de Rutas de la página
// AQUÍ VAN TODAS LAS RUTAS JUNTAS, ANTES DE LLAMAR A DISPATCH
$router->add('/usuarios/lista', 'UsuarioController', 'mostrarLista', 'GET');
$router->add('/usuarios/crear', 'UsuarioController', 'mostrarFormularioCreacion', 'GET');
$router->add('/usuarios/guardar', 'UsuarioController', 'guardar', 'POST');
$router->add('/usuarios/editar', 'UsuarioController', 'mostrarFormularioEdicion', 'GET');
$router->add('/usuarios/actualizar', 'UsuarioController', 'actualizar', 'POST');
$router->add('/usuarios/eliminar', 'UsuarioController', 'eliminar', 'GET');

echo "<b>URI que se intenta buscar:</b>";
var_dump($uri);

echo "<br><br><b>Rutas disponibles en el mapa:</b>";
var_dump($router->routes);

die();
//Poner a Trabajar al Router
$router->dispatch($uri, $request_method);