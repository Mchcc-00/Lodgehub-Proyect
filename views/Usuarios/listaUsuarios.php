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
                <h2 class="form-title">Lista de Usuarios</h2>
                <a href="../../views/Usuarios/crearUsuario.php" class="btn-add" title="Agregar">Agregar Usuario</a>
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
                        require_once '../../config/database.php';
                        $sql = "select d.descripcion as Tipo_Documento, 
                                e.numDocumento as Documento,
                                e.nombres as Nombres, 
                                e.apellidos as Apellidos,
                                s.descripcion as Sexo,
                                e.numTelefono as Telefono, 
                                e.contactoPersonal as Contacto_Personal, 
                                e.correo as Correo, 
                                r.descripcion as Rol, 
                                from tp_empleados e
                                inner join td_sexo s on e.sexo = s.id 
                                inner join td_tipodocumento d on e.tipoDocumento = d.id
                                inner join td_roles r on e.roles = r.id";
                        $result = $conn->query($sql);

                        if ($result === false) {
                            echo "<tr><td colspan='10'>Error en la consulta: " . $conn->error . "</td></tr>";
                        } elseif ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                        <td>{$row['tipoDocumento']}</td>
                                        <td>{$row['numDocumento']}</td>
                                        <td>{$row['nombres']}</td>
                                        <td>{$row['apellidos']}</td>
                                        <td>{$row['sexo']}</td>
                                        <td>{$row['numTelefono']}</td>
                                        <td>{$row['contactoPersonal']}</td>
                                        <td>{$row['correo']}</td>
                                        <td>{$row['roles']}</td>
                                        <td>
                                            <a href='../../views/usuarios/editarUsuario.php?numDocumento={$row['numDocumento']}' class='btn-edit'>Editar</a>
                                            <form action='eliminarUsuario.php' method='post' style='display:inline;'>
                                                <input type='hidden' name='numDocumento' value='{$row['numDocumento']}'>
                                                <button type='submit' class='btn-delete'>Eliminar</button>
                                            </form>
                                        </td>
                                    </tr>";
                            }
                        } else {
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