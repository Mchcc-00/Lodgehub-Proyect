<?php

class UsuarioController {
    
    // Método para mostrar el formulario de creación
    public function mostrarFormularioCreacion() {
        // Datos para la plantilla
        $data = [
            'title' => 'Crear Nuevo Usuario'
        ];
        
        // Llama a la función render para unir layout y vista
        $this->render('crear', $data);
    }
    
    // Método para guardar el nuevo usuario
    public function guardar() {
        // Aquí iría tu lógica para procesar el $_POST y guardarlo en la BD
        // que antes tenías en 'crudUsuarios/Usuarios.php'
        
        // Después de guardar, rediriges a otra página
        // header('Location: /usuarios/lista?mensaje=Usuario creado exitosamente');
        // exit();
    }
    
    /**
     * Renderiza una vista dentro del layout principal.
     */
    protected function render($vista, $data = []) {
        extract($data);
        
        ob_start();
        require_once __DIR__ . "/../views/Usuarios/{$vista}.php";
        $contenido = ob_get_clean();
        
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
?>