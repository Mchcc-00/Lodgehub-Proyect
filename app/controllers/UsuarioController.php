<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once '../config/conexionGlobal.php';

class UsuarioController
{
    private $db;
    private $usuarioModel;

    public function __construct()
    {
        $this->db = conexionDB();
        $this->usuarioModel = new Usuario($this->db);
    }

    //Muestra el formulario para crear un nuevo usuario.
    public function mostrarFormularioCreacion()
    {
        $this->render('usuarios/crear', ['titulo' => 'Crear Nuevo Usuario']);
    }

    //Muestra la lista de todos los usuarios.
    public function mostrarLista()
    {
        //Pide al modelo que obtenga todos los usuarios
        $listaDeUsuarios = $this->usuarioModel->obtenerTodos();

        $data = [
            'titulo' => 'Lista de Usuarios',
            'usuarios' => $listaDeUsuarios
        ];

        $this->render('usuarios/lista', $data);
    }

    // Proceso de la creación de un nuevo usuario.
    public function guardar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/usuarios/crear');
            exit;
        }

        $datos = $_POST;
        $errors = [];

        // Validaciones
        if (empty(trim($datos['primer_nombre']))) $errors['primer_nombre'] = "El primer nombre es obligatorio.";
        if (empty(trim($datos['primer_apellido']))) $errors['primer_apellido'] = "El primer apellido es obligatorio.";
        if (empty($datos['correo']) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) $errors['correo'] = "El correo no es válido.";
        if (empty($datos['password'])) {
            $errors['password'] = "La contraseña no puede estar vacía.";
        } else {
            // Expresión regular para la validación
            $regex = "/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[\W_]).{8,}$/";

            if (!preg_match($regex, $datos['password'])) {
                $errors['password'] = "La contraseña debe tener al menos 8 caracteres, una mayúscula, un número y un carácter especial.";
            } elseif ($datos['password'] !== $datos['confirmar_password']) {
                $errors['confirmar_password'] = "Las contraseñas no coinciden.";
            }
        }

        if (!empty($errors)) {
            // Si hay errores, volvemos a mostrar el formulario con los errores y los datos antiguos
            $this->render('usuarios/crear', [
                'titulo' => 'Corregir Datos de Usuario',
                'errors' => $errors,
                'old_input' => $datos
            ]);
            return; // Detenemos la ejecución
        }

        // Si no hay errores, preparamos los datos para el modelo
        $datos['nombres'] = trim(($datos['primer_nombre'] ?? '') . ' ' . ($datos['segundo_nombre'] ?? ''));
        $datos['apellidos'] = trim(($datos['primer_apellido'] ?? '') . ' ' . ($datos['segundo_apellido'] ?? ''));

        // Hasheo de la contraseña
        $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);


        $exito = $this->usuarioModel->crear($datos);

        if ($exito) {

            header('Location: ' . BASE_URL . '/usuarios/lista?mensaje=Usuario creado exitosamente');
        } else {

            header('Location: ' . BASE_URL . '/usuarios/crear?error=No se pudo crear el usuario. El DNI o correo ya podría existir.');
        }
        exit;
    }

    public function mostrarFormularioEdicion()
    {
        //Obtener el ID del usuario de la URL
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: ' . BASE_URL . '/usuarios/lista');
            exit;
        }

        //Pedir los datos de ese usuario al Model
        $usuario = $this->usuarioModel->obtenerPorId($id);

        if (!$usuario) {
            //Si no se encuentra el usuario, redirigir
            header('Location: ' . BASE_URL . '/usuarios/lista?error=Usuario no encontrado');
            exit;
        }

        //Renderizar la vista, pasándole los datos del usuario
        $data = [
            'titulo' => 'Editar Usuario',
            'usuario' => $usuario // La vista usará esta variable para rellenar el formulario
        ];
        $this->render('usuarios/editar', $data); // Usaremos una nueva vista 'editar.php'
    }

    //Procesar la actualización de un usuario existente.
    public function actualizar()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASE_URL . '/usuarios/lista');
            exit;
        }

        //Recibir y validar los datos
        $datos = $_POST;
        $id = $datos['numDocumento']; // El ID viene del campo oculto

        // ... (Aquí iría tu lógica de validación para los campos editables) ...
        if (empty($datos['correo']) || !filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            header('Location: ' . BASE_URL . '/usuarios/editar?id=' . $id . '&error=Correo no válido');
            exit;
        }

        //Delegar la actualización al Modelo
        $exito = $this->usuarioModel->actualizar($id, $datos);

        //Redirigir
        if ($exito) {
            header('Location: ' . BASE_URL . '/usuarios/lista?mensaje=Usuario actualizado correctamente');
        } else {
            header('Location: ' . BASE_URL . '/usuarios/editar?id=' . $id . '&error=No se pudo actualizar el usuario');
        }
        exit;
    }

    //Procesar la eliminación de un usuario.
    public function eliminar()
    {
        //OBTENER el ID del usuario desde la URL (petición GET)
        $id = $_GET['id'] ?? null;

        //VALIDAR que se haya proporcionado un ID
        if (!$id) {
            // Si no hay ID, redirige de vuelta a la lista con un error
            header('Location: ' . BASE_URL . '/usuarios/lista?error=ID de usuario no proporcionado.');
            exit;
        }

        // DELEGAR la eliminación al Modelo
        $exito = $this->usuarioModel->eliminar($id);

        //REDIRIGIR según el resultado
        if ($exito) {
            header('Location: ' . BASE_URL . '/usuarios/lista?mensaje=Usuario eliminado correctamente.');
        } else {
            header('Location: ' . BASE_URL . '/usuarios/lista?error=No se pudo eliminar el usuario.');
        }
        exit;
    }

    /**
     * Renderiza una vista dentro del layout principal.
     */
    protected function render($vista, $data = [])
    {
        extract($data);
        ob_start();
        require_once __DIR__ . "/../views/{$vista}.php";
        $contenido = ob_get_clean();
        require_once __DIR__ . '/../views/layouts/main.php';
    }
}
