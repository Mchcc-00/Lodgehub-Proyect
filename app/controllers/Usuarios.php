<?php

require_once '../app/Models/Usuarios/CrudUsuarios/Usuarios.php';
require_once '../config/conexionGlobal.php';

class UsuarioController {
    
    private $db;
    private $usuarioModel;

    public function __construct() {
        $this->db = conexionDB();
        $this->usuarioModel = new Usuario($this->db);
    }

    // --- MÉTODOS PARA MOSTRAR VISTAS (generalmente peticiones GET) ---

    /**
     * Muestra el formulario para crear un nuevo usuario.
     */
    public function mostrarFormularioCreacion() {
        $this->render('usuarios/crear', ['titulo' => 'Crear Nuevo Usuario']);
    }

    /**
     * Muestra la lista de todos los usuarios.
     */
    public function mostrarLista() {
        // Lógica para obtener todos los usuarios del modelo
        // $usuarios = $this->usuarioModel->obtenerTodos();
        // $this->render('usuarios/lista', ['titulo' => 'Lista de Usuarios', 'usuarios' => $usuarios]);
    }


    // --- MÉTODOS PARA PROCESAR ACCIONES (generalmente peticiones POST o GET con ID) ---

    /**
     * Procesa la creación de un nuevo usuario.
     */
    public function guardar() {
        // ... (Lógica de validación, delegación al modelo y redirección) ...
    }

    /**
     * Procesa la actualización de un usuario existente.
     */
    public function actualizar() {
        // ... (Lógica de validación, delegación al modelo y redirección) ...
    }

    /**
     * Procesa la eliminación de un usuario.
     */
    public function eliminar() {
        // ... (Lógica de validación, delegación al modelo y redirección) ...
    }


    // --- MÉTODO DE AYUDA (HELPER) PARA RENDERIZAR VISTAS ---

    /**
     * Renderiza una vista dentro del layout principal.
     */
    protected function render($vista, $data = []) {
        extract($data);
        ob_start();
        require_once "../app/Views/{$vista}.php"; // Ajusta la ruta si es necesario
        $contenido = ob_get_clean();
        require_once '../app/Views/layouts/main.php'; // Ajusta la ruta si es necesario
    }
}