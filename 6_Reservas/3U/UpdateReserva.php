<?php

require_once '../../config/conexionGlobal.php';

$db = conexionDB();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['idReserva'];
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modificar Reserva</title>
</head>

<body>
    <div class="container">
        <h2>Modificar Reserva</h2>
        <form class="" id="FormModificacionReserva" action="proceUpdateReserva.php" method="POST">
            <div class="FormEditReserva">
                <h3>INFORMACIÓN DE HOSPEDAJE</h3>
                <fieldset id="campoFechaEdit">
                    <label for="fechaInicioModif">Fecha inicio<input id="fechaInicioModif" type="date" name="fechaInicio" required></label>
                    <label for="fechaFinModif">Fecha salida<input id="fechaFinModif" type="date" name="fechaFin" required></label>
                </fieldset>
                <fieldset id="campoHabEdit">
                    <legend>Habitación</legend>
                    <label for="numHabitacionModif">Nº<input id="numHabitacionModif" type="text" name="numHabitacionReserva" maxlength="3" placeholder="Numero de habitacion" required></label>
                </fieldset>
                <fieldset id="campoCanPersonasEdit">
                    <legend>Número de personas</legend>
                    <label for="numAdultosModif">Adultos<input id="numAdultosModif" type="number" name="numAdultos" min="1" max="10"></label>
                    <label for="numNinosModif">Niños<input id="numNinosModif" type="number" name="numNinos" min="1" max="10"></label>
                    <label for="numDiscapacitadosModif">Discapacitados<input id="numDiscapacitadosModif" type="number" name="numDiscapacitados" min="1" max="10"></label>
                </fieldset>
                <fieldset id="campoInfoAdicionalEdit">
                    <legend>Información adicional</legend>
                    <textarea name="infoAdicionalReserva" id="infoAdicionalModif" rows="7" placeholder="Información necesaria a tener en cuenta o sugerencias"></textarea>
                </fieldset>
                <fieldset id="campoEstadoReservaEdit">
                    <label for="estadoReservaModif">
                        Estado de la reserva
                        <select name="estadoReserva" id="estadoReservaModif" required>
                            <option value="" disabled selected>Seleccione</option>
                            <option value="1">Activo</option>
                            <option value="5">Pendiente</option>
                            <option value="4">Finalizado</option>
                            <option value="6">Cancelado</option>
                        </select>
                    </label>
                    <label for="totalPagoModif">Total a pagar<input id="totalPagoModif" type="text" name="totalPago" required></label>
                </fieldset>
                <div id="btnFormModifReserva">
                    <button type="button" id="btnCancelarModifReserva">Cancelar</button>
                    <button type="submit" id="btnModificarReserva">Modificar</button>
                </div>
            </div>
        </form>
    </div>
</body>
    <script src="../scripts.js"></script>
</html>