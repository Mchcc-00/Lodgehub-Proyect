<?php

require_once '../../config/conexionGlobal.php';


datosTabla();

function datosTabla(){

$db = conexionDB();


    $sql = "SELECT r.id, r.numeroHabitacion, th.descripcion as tipoHab, h.nombres, h.apellidos, r.fechaRegistro, r.fechainicio, r.fechaFin, r.costo, e.descripcion FROM tp_reservas as r
    
    INNER JOIN tp_habitaciones AS Hab ON r.numeroHabitacion = Hab.numero
    INNER JOIN td_tipohabitacion AS th ON Hab.tipoHabitacion = th.id
    INNER JOIN td_estado AS e ON  r.estado = e.id
    INNER JOIN tp_huespedes AS h ON  r.hue_numDocumento = h.numDocumento

ORDER BY id DESC";

$resultado = $db->prepare($sql);
$resultado->execute();
?>
    <table class='tabla-reservas'>
        <thead>
            <tr>
                <th>ID</th>
                <th>Habitación</th>
                <th>Tipo Habitación</th>
                <th>Huesped</th>
                <th>Fecha Registro</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Costo</th>
                <th>Estado</th>
                <th>Información completa</th>
            </tr>
        </thead>

    <?php
        while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)){
    ?>

        <tbody>
            <tr>
                <td><?php echo $fila['id'];?></td>
                <td><?php echo $fila['numeroHabitacion'];?></td>
                <td><?php echo $fila['tipoHab'];?></td>
                <td><?php echo $fila['nombres'] . " " .$fila['apellidos'] ;?></td>
                <td><?php echo $fila['fechaRegistro'];?></td>
                <td><?php echo $fila['fechainicio'];?></td>
                <td><?php echo $fila['fechaFin'];?></td>
                <td><?php echo $fila['costo'];?></td>
                <td><?php echo $fila['descripcion'];?></td>
                <td><button id="verInformacionReserva" onclick="mostrarModal(<?php echo $fila['id']; ?>)" style="cursor:pointer;">Ver</button></td>
            </tr>
        </tbody>


<?php
    }}
?>
</table>