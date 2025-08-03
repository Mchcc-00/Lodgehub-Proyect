<?php

require_once '../../config/conexionGlobal.php';

$db = conexionDB();

if (isset($_POST['idReserva'])) {
    $idReserva = $_POST['idReserva'];

    try {
        // 3. Preparar la consulta SQL para obtener los datos de la reserva
        $sql = "SELECT * FROM tp_reservas WHERE id = :idReserva";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':idReserva', $idReserva, PDO::PARAM_INT);
        $stmt->execute();

        $reserva = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($reserva) {
            $costoReserva = $reserva["costo"];
            $fechaInicioReserva = $reserva["fechainicio"];
            $fechaFinReserva = $reserva["fechaFin"];
            $canAdultosReserva = $reserva["cantidadAdultos"];
            $canNinosReserva = $reserva["cantidadNinos"];
            $canDiscapacidadReserva = $reserva["cantidadDiscapacitados"];
            $habitacionReserva = $reserva["numeroHabitacion"];
            $infoAdicionalReserva = $reserva["informacionAdicional"];
            $estadoReserva = $reserva["estado"];
        } else {
            echo "<p>Error: Reserva no encontrada.</p>";
        }
    } catch (PDOException $e) {
        echo "Error de conexión o consulta: " . $e->getMessage();
    }
} else {
    // Si no se recibió idReserva, es un acceso inválido a la página
    echo "<p>Acceso inválido. No se ha proporcionado un ID de reserva.</p>";
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Reserva</title>
    <link rel="stylesheet" href="//cdn.datatables.net/2.3.2/css/dataTables.dataTables.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-LN+7fdVzj6u52u30Kp6M/trliBMCMKTyK833zpbD+pXdCLuTusPj697FH4R/5mcr" crossorigin="anonymous">
    <link rel="stylesheet" href="../styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg bg-body-tertiary">
        <div class="container-fluid">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item dropdown">
                        <a id="lodgebub-dropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            LODGEHUB
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="../../app/views/homepage/homepage.php">Home</a></li>
                            <li><a class="dropdown-item active" href="mainReservas.php">Reservas</a></li>
                            <li><a class="dropdown-item" href="../../../HABITACIONES/views/dashboard.php">Habitaciones</a></li>
                            <li><a class="dropdown-item" href="../../../MANTENIMIENTO/views/dashboard.php">Mantenimiento</a></li>
                            <li><a class="dropdown-item" href="../../../PQRS/views/dashboard.php">PQRS</a></li>
                        </ul>
                    </li>

                </ul>
                <form class="d-flex" role="perfil">

                    <a href="../../app/views/homepage/cerrarSesion.php" class="btn btn-danger">Cerrar sesión</a>
                </form>
            </div>
        </div>
    </nav>

    <style>
        .container-fluid {
            background: #437bafff;
            padding: 20px;
        }

        #lodgebub-dropdown {
            color: #ffffffff;
            margin: 0;
            padding: 0;
            list-style: none;
            font-weight: bold;
            text-transform: uppercase;
        }
    </style>

    <div class="contenedorReservas">
        <h2>Modificar Reserva</h2>
        <form class="" id="FormModificacionReserva" action="proceUpdateReserva.php" method="POST">
            <input type="hidden" name="idReserva" value="<?php echo htmlspecialchars($idReserva); ?>">
            <div class="FormEditReserva">
                <h3>INFORMACIÓN DE HOSPEDAJE</h3>
                <fieldset id="campoFechaEdit">
                    <label for="fechaInicioModif">Fecha inicio<input id="fechaInicioModif" type="date" name="fechaInicioModif" value="<?php echo htmlspecialchars($fechaInicioReserva ?? ''); ?>" required></label>
                    <label for="fechaFinModif">Fecha salida<input id="fechaFinModif" type="date" name="fechaFinModif" value="<?php echo htmlspecialchars($fechaFinReserva ?? ''); ?>" required></label>
                </fieldset>
                <fieldset id="campoHabEdit">
                    <legend>Habitación</legend>
                    <label for="numHabitacionModif">Nº<input id="numHabitacionModif" type="text" name="numHabitacionModif" value="<?php echo htmlspecialchars($habitacionReserva ?? ''); ?>" maxlength="3" placeholder="Numero de habitacion" required></label>
                </fieldset>
                <fieldset id="campoCanPersonasEdit">
                    <legend>Número de personas</legend>
                    <label for="numAdultosModif">Adultos<input id="numAdultosModif" type="number" name="numAdultosModif" value="<?php echo htmlspecialchars($canAdultosReserva ?? ''); ?>" min="1" max="10"></label>
                    <label for="numNinosModif">Niños<input id="numNinosModif" type="number" name="numNinosModif" value="<?php echo htmlspecialchars($canNinosReserva ?? ''); ?>" min="1" max="10"></label>
                    <label for="numDiscapacitadosModif">Discapacitados<input id="numDiscapacitadosModif" type="number" name="numDiscapacitadosModif" value="<?php echo htmlspecialchars($canDiscapacidadReserva ?? ''); ?>" min="1" max="10"></label>
                </fieldset>
                <fieldset id="campoInfoAdicionalEdit">
                    <legend>Información adicional</legend>
                    <textarea name="infoAdicionalReservaModif" id="infoAdicionalModif" rows="7" value="<?php echo htmlspecialchars($$infoAdicionalReserva ?? ''); ?>" placeholder="Información necesaria a tener en cuenta o sugerencias"></textarea>
                </fieldset>
                <fieldset id="campoEstadoReservaEdit">
                    <label for="estadoReservaModif">
                        Estado de la reserva
                        <select name="estadoReservaModif" id="estadoReservaModif" value="<?php echo htmlspecialchars($estadoReserva ?? ''); ?>" required>
                            <option value="" disabled selected>Seleccione</option>
                            <option value="1">Activo</option>
                            <option value="5">Pendiente</option>
                            <option value="4">Finalizado</option>
                            <option value="6">Cancelado</option>
                        </select>
                    </label>
                    <label for="totalPagoModif">Total a pagar<input id="totalPagoModif" type="text" name="totalPagoModif" value="<?php echo htmlspecialchars($costoReserva ?? ''); ?>" required></label>
                </fieldset>
                <div id="btnFormModifReserva">
                    <button type="button" id="btnCancelarModifReserva">Cancelar</button>
                    <button type="submit" id="btnModificarReserva">Modificar</button>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.7.1/jquery.min.js" integrity="sha512-v2CJ7UaYy4JwqLDIrZUI/4hqeoQieOmAZNXBeQyjo21dadnwR+8ZaIJVT8EE2iyI61OV8e6M8PP2/4hpQINQ/g==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="//cdn.datatables.net/2.3.2/js/dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ndDqU0Gzau9qJ1lfW4pNLlhNTkCfHzAVBReH9diLvGRem5+R9g2FzA8ZGN954O5Q"
        crossorigin="anonymous"></script>
    <script src="../scripts.js"></script>
</body>

</html>