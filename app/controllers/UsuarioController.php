<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../../config/conexionGlobal.php';

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

    /**
     * Procesa el registro desde el formulario PÚBLICO.
     * Esta función maneja los datos del formulario crearUsuarioLogin.php
     */
    public function registrarPublico()
    {
        // 1. VERIFICAR que la petición sea POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: crearUsuarioLogin.php?error=Acceso no permitido');
            exit;
        }

        // 2. RECIBIR y LIMPIAR los datos
        $datos = [];
        $datos['numDocumento'] = trim($_POST['numDocumento'] ?? '');
        $datos['tipoDocumento'] = $_POST['tipoDocumento'] ?? '';
        $datos['nombres'] = trim($_POST['nombres'] ?? '');
        $datos['apellidos'] = trim($_POST['apellidos'] ?? '');
        $datos['numTelefono'] = trim($_POST['numTelefono'] ?? '');
        $datos['correo'] = trim($_POST['correo'] ?? '');
        $datos['sexo'] = $_POST['sexo'] ?? '';
        $datos['fechaNacimiento'] = $_POST['fechaNacimiento'] ?? '';
        $datos['password'] = $_POST['password'] ?? '';
        $datos['confirmar_password'] = $_POST['confirmar_password'] ?? '';
        $datos['roles'] = $_POST['roles'] ?? '';

        // 3. VALIDAR los datos
        $errors = $this->validarDatos($datos);

        // Si hay errores, redirigir con mensaje de error
        if (!empty($errors)) {
            $errorMsg = implode(' ', $errors);
            header('Location: crearUsuarioLogin.php?error=' . urlencode($errorMsg));
            exit;
        }

        // 4. PROCESAR la imagen si se subió
        $datos['foto'] = $this->procesarImagen();

        // 5. HASHEAR la contraseña
        $datos['password'] = password_hash($datos['password'], PASSWORD_DEFAULT);

        // 6. VERIFICAR si el usuario ya existe
        if ($this->usuarioModel->existeUsuario($datos['numDocumento'], $datos['correo'])) {
            header('Location: crearUsuarioLogin.php?error=' . urlencode('El número de documento o correo ya está registrado.'));
            exit;
        }

        // 7. CREAR el usuario
        $exito = $this->usuarioModel->crear($datos);

        // 8. REDIRIGIR según el resultado
        if ($exito) {
            header('Location: login.php?mensaje=' . urlencode('Registro exitoso. ¡Ya puedes iniciar sesión!'));
        } else {
            header('Location: crearUsuarioLogin.php?error=' . urlencode('Error al crear el usuario. Intente nuevamente.'));
        }
        exit;
    }

    /**
     * Valida todos los datos del formulario
     */
    private function validarDatos($datos)
    {
        $errors = [];

        // Validar número de documento
        if (empty($datos['numDocumento'])) {
            $errors[] = "El número de documento es obligatorio.";
        } elseif (!preg_match('/^[0-9A-Z\-]+$/', $datos['numDocumento'])) {
            $errors[] = "El número de documento contiene caracteres no válidos.";
        } elseif (strlen($datos['numDocumento']) > 15) {
            $errors[] = "El número de documento no puede tener más de 15 caracteres.";
        }

        // Validar tipo de documento
        $tiposValidos = ['Cédula de Ciudadanía', 'Tarjeta de Identidad', 'Cedula de Extranjeria', 'Pasaporte', 'Registro Civil'];
        if (empty($datos['tipoDocumento']) || !in_array($datos['tipoDocumento'], $tiposValidos)) {
            $errors[] = "Debe seleccionar un tipo de documento válido.";
        }

        // Validar nombres
        if (empty($datos['nombres'])) {
            $errors[] = "Los nombres son obligatorios.";
        } elseif (strlen($datos['nombres']) > 50) {
            $errors[] = "Los nombres no pueden tener más de 50 caracteres.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $datos['nombres'])) {
            $errors[] = "Los nombres solo pueden contener letras y espacios.";
        }

        // Validar apellidos
        if (empty($datos['apellidos'])) {
            $errors[] = "Los apellidos son obligatorios.";
        } elseif (strlen($datos['apellidos']) > 50) {
            $errors[] = "Los apellidos no pueden tener más de 50 caracteres.";
        } elseif (!preg_match('/^[a-zA-ZÀ-ÿ\s]+$/', $datos['apellidos'])) {
            $errors[] = "Los apellidos solo pueden contener letras y espacios.";
        }

        // Validar teléfono
        if (empty($datos['numTelefono'])) {
            $errors[] = "El número de teléfono es obligatorio.";
        } elseif (!preg_match('/^[\+]?[0-9\s\-\(\)]+$/', $datos['numTelefono'])) {
            $errors[] = "El formato del teléfono no es válido.";
        } elseif (strlen($datos['numTelefono']) > 15) {
            $errors[] = "El teléfono no puede tener más de 15 caracteres.";
        }

        // Validar correo
        if (empty($datos['correo'])) {
            $errors[] = "El correo electrónico es obligatorio.";
        } elseif (!filter_var($datos['correo'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = "El formato del correo electrónico no es válido.";
        } elseif (strlen($datos['correo']) > 30) {
            $errors[] = "El correo no puede tener más de 30 caracteres.";
        }

        // Validar sexo
        $sexosValidos = ['Hombre', 'Mujer', 'Otro', 'Prefiero no decirlo'];
        if (empty($datos['sexo']) || !in_array($datos['sexo'], $sexosValidos)) {
            $errors[] = "Debe seleccionar un sexo válido.";
        }

        // Validar fecha de nacimiento
        if (empty($datos['fechaNacimiento'])) {
            $errors[] = "La fecha de nacimiento es obligatoria.";
        } else {
            $fecha = DateTime::createFromFormat('Y-m-d', $datos['fechaNacimiento']);
            if (!$fecha || $fecha->format('Y-m-d') !== $datos['fechaNacimiento']) {
                $errors[] = "El formato de la fecha de nacimiento no es válido.";
            } else {
                $hoy = new DateTime();
                $edad = $hoy->diff($fecha)->y;
                if ($edad < 13) {
                    $errors[] = "Debe ser mayor de 13 años para registrarse.";
                } elseif ($edad > 120) {
                    $errors[] = "La fecha de nacimiento no es válida.";
                }
            }
        }

        // Validar contraseña
        if (empty($datos['password'])) {
            $errors[] = "La contraseña es obligatoria.";
        } else {
            if (strlen($datos['password']) < 8) {
                $errors[] = "La contraseña debe tener al menos 8 caracteres.";
            }
            if (!preg_match('/[A-Z]/', $datos['password'])) {
                $errors[] = "La contraseña debe contener al menos una letra mayúscula.";
            }
            if (!preg_match('/[a-z]/', $datos['password'])) {
                $errors[] = "La contraseña debe contener al menos una letra minúscula.";
            }
            if (!preg_match('/[0-9]/', $datos['password'])) {
                $errors[] = "La contraseña debe contener al menos un número.";
            }
            if (!preg_match('/[\W_]/', $datos['password'])) {
                $errors[] = "La contraseña debe contener al menos un carácter especial.";
            }
        }

        // Validar confirmación de contraseña
        if ($datos['password'] !== $datos['confirmar_password']) {
            $errors[] = "Las contraseñas no coinciden.";
        }

        // Validar rol
        $rolesValidos = ['Administrador', 'Colaborador', 'Usuario'];
        if (empty($datos['roles']) || !in_array($datos['roles'], $rolesValidos)) {
            $errors[] = "Debe seleccionar un rol válido.";
        }

        return $errors;
    }

    /**
     * Procesa la imagen subida y la guarda en el servidor
     */
    private function procesarImagen()
    {
        if (!isset($_FILES['foto']) || $_FILES['foto']['error'] === UPLOAD_ERR_NO_FILE) {
            return null; // No se subió ninguna imagen
        }

        $archivo = $_FILES['foto'];

        // Verificar si hubo errores en la subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        // Verificar el tamaño del archivo (máximo 2MB)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            return null;
        }

        // Verificar el tipo de archivo
        $tiposPermitidos = ['image/jpeg', 'image/png', 'image/gif'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $tipoMime = $finfo->file($archivo['tmp_name']);

        if (!in_array($tipoMime, $tiposPermitidos)) {
            return null;
        }

        // Generar un nombre único para la imagen
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = uniqid() . '_' . time() . '.' . $extension;

        // Definir la ruta donde se guardará la imagen
        $rutaDestino = __DIR__ . '/../../public/uploads/usuarios/' . $nombreArchivo;

        // Crear el directorio si no existe
        $directorio = dirname($rutaDestino);
        if (!is_dir($directorio)) {
            mkdir($directorio, 0755, true);
        }

        // Mover el archivo al destino final
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return $nombreArchivo; // Retornar solo el nombre del archivo
        }

        return null;
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
        $this->render('usuarios/editar', $data);
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

        // Validar correo
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
