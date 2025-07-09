<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulario PQRS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../../../public/assets/css/stylesPQRS.css">
</head>

<body>

    <section class="table-card">
        <h3>Consulta de PQRS</h3>

        <div class="consulta-filtros">
            <label for="filtroPQRS">Filtrar:</label>
            <select class="filtro-pqrs" id="filtroPQRS">
                <option value="todos">Todos</option>
                <option value="abierto">Abiertos</option>
                <option value="solucionado">Solucionados</option>
                <option value="alta">Urgencia Alta</option>
                <option value="media">Urgencia Media</option>
                <option value="baja">Urgencia Baja</option>
            </select>
        </div>

        <div class="tabla-responsive">
            <table class="tabla-pqrs">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Fecha registro</th>
                        <th>Nivel de urgencia</th>
                        <th>Categoría</th>
                        <th>Tipo</th>
                        <th>Solicitante</th>
                        <th>Registra</th>
                        <th>Estado</th>
                    </tr>
                </thead>
                <tbody id="tablaCuerpo">
                    <?php
                    // --- 1. CONFIGURACIÓN DE LA BASE DE DATOS ---
                    $host = 'localhost';
                    $dbname = 'formulario_pqrs'; // El nombre de tu base de datos
                    $user = 'root';              // Tu usuario de MySQL (usualmente 'root')
                    $password = '';              // Tu contraseña de MySQL (vacía por defecto en XAMPP)

                    try {
                        // --- 2. CONEXIÓN A LA BASE DE DATOS USANDO PDO ---
                        $db = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
                        // Configurar PDO para que lance excepciones en caso de error
                        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                        // --- 3. PREPARAR Y EJECUTAR LA CONSULTA SQL ---
                        // Se seleccionan las columnas necesarias.
                        // Se usa CONCAT para unir nombre y apellido en una sola columna "solicitante".
                        // Se añade una columna estática para "Estado", ya que no está en tu tabla.
                        $sql = "SELECT 
                    id,
                    fecha,
                    urgencia,
                    categoria,
                    tipo_pqrs,
                    CONCAT(nombre, ' ', apellido) AS solicitante,
                    empleado AS registra,
                    'Pendiente' AS estado -- Columna estática para el estado
                FROM pqrs
                ORDER BY fecha DESC"; // Ordenar por fecha, los más nuevos primero

                        $stmt = $db->query($sql);
                        $pqrs_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        // --- 4. GENERAR LAS FILAS DE LA TABLA ---
                        if (count($pqrs_list) > 0) {
                            foreach ($pqrs_list as $pqrs) {
                                echo "<tr>";
                                // Usamos htmlspecialchars() para evitar ataques XSS
                                echo "<td>" . htmlspecialchars($pqrs['id']) . "</td>";
                                echo "<td>" . htmlspecialchars($pqrs['fecha']) . "</td>";
                                echo "<td>" . htmlspecialchars($pqrs['urgencia']) . "</td>";
                                echo "<td>" . htmlspecialchars($pqrs['categoria']) . "</td>";
                                echo "<td>" . htmlspecialchars($pqrs['tipo_pqrs']) . "</td>";
                                echo "<td>" . htmlspecialchars($pqrs['solicitante']) . "</td>";
                                echo "<td>" . htmlspecialchars($pqrs['registra']) . "</td>";
                                echo "<td>" . htmlspecialchars($pqrs['estado']) . "</td>";
                                echo "</tr>";
                            }
                        } else {
                            // Mensaje si no hay registros
                            echo "<tr><td colspan='8' style='text-align: center;'>No hay registros para mostrar.</td></tr>";
                        }
                    } catch (PDOException $e) {
                        // Manejo de errores de conexión o consulta
                        echo "<tr><td colspan='8' style='text-align: center; color: red;'>Error al conectar con la base de datos: " . $e->getMessage() . "</td></tr>";
                    }

                    // Cerrar la conexión
                    $db = null;
                    ?>
                </tbody>
            </table>
        </div>

        <div class="empty-state" id="estadoVacio">
            <i class="fa-solid fa-circle-info"></i>
            No hay registros de PQRS disponibles.
        </div>

        <div class="acciones-tabla">
            <button type="button" id="btnEditar" class="btn-editar">Editar PQRS</button>
        </div>
    </section>

</body>

</html>