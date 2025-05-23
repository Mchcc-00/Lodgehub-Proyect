<!DOCTYPE html>
<html lang="es" data-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD de Usuarios</title>
    <link rel="stylesheet" href="../assets/css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">
</head>

<body>
    <div class="main-container">
        <header class="top-bar">

            <div class="logo-placeholder">
                <img src="../assets/img/LogoClaroLH.png" alt="LogoClaroLH" width="80px" height="auto">
            </div>

        </header>

        <div class="content-area"> <!-- Sidebar -->
            <aside class="sidebar left-sidebar">
                <nav>
                    <ul>
                        <li><a href="#">HABITACIONES</a></li>
                        <li><a href="#">RESERVAS</a></li>
                        <li><a href="../crudUsuarios/crudUsuarios.php">USUARIOS</a></li>
                    </ul>
                </nav>
                <div class="sidebar-bottom-icons">
                    <i class="fas fa-headphones"></i>
                    <i class="fas fa-configuration"></i>
                </div>
            </aside>

            <!-- CRUD Content -->
            <main class="form-content-container">

                <a href="../crudUsuarios/crearUsuario.php" class="add-button" title="Agregar">Agregar Usuario</a>

                <!-- Lista de usuarios -->
                <h2>Lista de Usuarios</h2>
                <table class="user-table">
                    <thead class="table-header">
                        <tr>
                            <th>DNI</th>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Rol</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="table-body">
                        <!-- Aquí se llenará dinámicamente con PHP -->
                        <?php
                        require_once '../database.php';
                        $sql = "SELECT dni, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, rol FROM empleados";
                        $result = $conn->query($sql);

                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>
                                                <td>{$row['dni']}</td>
                                                <td>{$row['primer_nombre']}</td>
                                                <td>{$row['segundo_nombre']}</td>
                                                <td>{$row['primer_apellido']}</td>
                                                <td>{$row['segundo_apellido']}</td>
                                                <td>{$row['rol']}</td>
                                                <td>
                                                    <a href='../crudUsuarios/editarUsuario.php?dni={$row['dni']}' class='btn btn-edit'>Editar</a>
                                                    <form action='eliminarUsuario.php' method='post' style='display:inline;'>
                                                        <input type='hidden' name='dni' value='{$row['dni']}'>
                                                        <button href='../crudUsuarios/eliminarUsuario.php' type='submit' class='btn btn-delete'>Eliminar</button>
                                                    </form>
                                                </td>
                                            </tr>";
                            }
                        } else {
                            echo "<tr><td colspan='5'>No hay usuarios registrados.</td></tr>";
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
    <script src="../assets/js/formScript.js"></script>
</body>



</html>