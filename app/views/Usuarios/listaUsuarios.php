<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuarios</title>
    <link rel="stylesheet" href="../../public/assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <header class="top-bar">

            <div class="logo-placeholder">
                <img src="../../public/img/LogoClaroLH.png" alt="LogoClaroLH" width="80px" height="auto">
            </div>

        </header>

        <div class="content-area"> <!-- Sidebar -->
            <aside class="sidebar left-sidebar">
                <nav>
                    <ul>
                        <li><a href="#">HABITACIONES</a></li>
                        <li><a href="#">RESERVAS</a></li>
                        <li><a href="listaUsuarios.php">USUARIOS</a></li>
                    </ul>
                </nav>
                <div class="sidebar-bottom-icons">
                    <i class="fas fa-headphones"></i>
                    <i class="fas fa-configuration"></i>
                </div>
            </aside>

            <!-- CRUD Content -->
            <main class="form-content-container">
                <!-- Lista de usuarios -->
                <div class="user-header">
                    <h2 class="form-title">Lista de Usuarios</h2>
                    <a href="../../views/Usuarios/crearUsuario.php" class="btn-add" title="Agregar">
                        <i class="fas fa-plus" width></i>
                    </a>
                </div>
                <table class="user-table">
                    <thead class="user-table-header">
                        <tr>
                            <th>Tipo de documento</th>
                            <th>Número de documento</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Sexo</th>
                            <th>Teléfono</th>
                            <th>Contacto personal</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody class="user-table-body">
                        <!-- Aquí se llenará dinámicamente con PHP -->
                        <?php
                        require_once '../../config/conexionGlobal.php';
                        $db=conexionDB();
                        if (!$db) {
                            die("Error al conectar a la base de datos.");
                        }
                        
                        $sql = "select
                                    d.descripcion AS tipo_documento,
                                    e.numDocumento,
                                    e.nombres,
                                    e.apellidos,
                                    s.descripcion AS sexo,
                                    e.numTelefono,
                                    e.telEmergencia,
                                    e.correo,
                                    r.descripcion AS rol
                                from tp_empleados e
                                inner join td_tipodocumento d on e.tipoDocumento = d.id
                                inner join td_sexo s on e.sexo = s.id
                                inner join td_roles r on e.roles = r.id
                                ";
                        $result = $db->prepare($sql);
                        $result->execute();
                        if (!$result) {
                            die("Error al ejecutar la consulta: " . implode(", ", $db->errorInfo()));
                        }
                        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                            echo "<tr>
                                    <td>{$row['tipo_documento']}</td>
                                    <td>{$row['numDocumento']}</td>
                                    <td>{$row['nombres']}</td>
                                    <td>{$row['apellidos']}</td>
                                    <td>{$row['sexo']}</td>
                                    <td>{$row['numTelefono']}</td>
                                    <td>{$row['telEmergencia']}</td>
                                    <td>{$row['correo']}</td>
                                    <td>{$row['rol']}</td>
                                    <td>
                                        <a href='../../views/usuarios/editarUsuario.php?numDocumento={$row['numDocumento']}' class='btn-edit'>Editar</a>
                                        <form action='eliminarUsuario.php' method='post' style='display:inline;'>
                                            <input type='hidden' name='numDocumento' value='{$row['numDocumento']}'>
                                            <button type='submit' class='btn-delete'>Eliminar</button>
                                        </form>
                                    </td>
                                </tr>";
                        }
                        if ($result->rowCount() == 0) {
                            echo "<tr><td colspan='10'>No hay usuarios registrados.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
                <footer class="form-footer">
                    lodgehubgroup © 2025
                </footer>

            </main>
        </div>
    </div>
    </div>
    <script src="../../public/js/formScript.js"></script>
</body>



</html>