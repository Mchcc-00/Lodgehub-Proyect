<?php
class Router
{
    // Un array para almacenar todas las rutas definidas
    public $routes = [];

    /**
     * Añade una nueva ruta al mapa de rutas.
     * Llamado desde index.php para cada ruta.
     */
    public function add($uri, $controller, $method, $request_type)
    {
        $this->routes[] = [
            'uri' => $uri,
            'controller' => $controller,
            'method' => $method,
            'request_type' => $request_type
        ];
    }

    /**
     * Busca una coincidencia para la URI y el método de petición actual,
     * y ejecuta el controlador correspondiente.
     */
    public function dispatch($uri, $request_type) {
        // Recorre todas las rutas guardadas
        foreach ($this->routes as $route) {
            // Si la URI y el tipo de petición coinciden con la ruta actual del bucle...
            if ($route['uri'] === $uri && $route['request_type'] === $request_type) {
                
                // Crea una instancia del controlador definido en la ruta
                $controller = new $route['controller']();

                // Obtiene el nombre del método a llamar
                $method = $route['method'];

                // Llama al método del controlador
                $controller->$method();

                // Salimos del método dispatch, ya que encontramos y ejecutamos la ruta
                return;
            }
        }

        // Si el bucle termina y no se encontró ninguna ruta, muestra un error 404
        $this->abort(404);
    }

    /**
     * Muestra una página de error simple.
     */
    protected function abort($code = 404) {
        http_response_code($code);
        // En una app real, cargarías una vista de error más bonita
        echo "Error {$code}: Página no encontrada.";
        die();
    }
}
