<?php

require_once '../../config/conexionGlobal.php';


datosTabla();

function datosTabla(){

$db = conexionDB();


    $sql = "SELECT r.id, r.hue_numDocumento , r.numeroHabitacion, h.nombres, h.apellidos, d.descripcion as tipDoc, s.descripcion as sexo, ec.descripcion as estaCiv, h.numTelefono, h.correo, r.fechaRegistro, r.fechainicio, r.fechaFin, r.costo, e.descripcion FROM tp_reservas as r
    
    INNER JOIN td_estado AS e ON  r.estado = e.id
    INNER JOIN tp_huespedes AS h ON  r.hue_numDocumento = h.numDocumento
    INNER JOIN td_sexo AS s ON  h.sexo = s.id
    INNER JOIN td_tipoDocumento AS d ON  h.tipoDocumento = d.id
    INNER JOIN td_estadoCivil AS ec ON  h.estadoCivil = ec.id

ORDER BY id DESC";

$resultado = $db->prepare($sql);
$resultado->execute();
?>


<table class='tabla-reservas'>
        <tr>
            <th>ID</th>
            <th>Habitación</th>
            <th>Huesped</th>
            <th>Fecha Registro</th>
            <th>Fecha Inicio</th>
            <th>Fecha Fin</th>
            <th>Costo</th>
            <th>Estado</th>
            <th></th>
        </tr>

        <?php

    while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)){
        ?>
                <tr>
                    <td><?php echo $fila['id'];?></td>
                    <td><?php echo $fila['numeroHabitacion'];?></td>
                    <td><?php echo $fila['nombres'] . " " .$fila['apellidos'] ;?></td>
                    <td><?php echo $fila['fechaRegistro'];?></td>
                    <td><?php echo $fila['fechainicio'];?></td>
                    <td><?php echo $fila['fechaFin'];?></td>
                    <td><?php echo $fila['costo'];?></td>
                    <td><?php echo $fila['descripcion'];?></td>
                    <td><button id="verInformacionReserva" onclick="mostrarModal(<?php echo $fila['id']; ?>)" style="cursor:pointer;">Ver</button></td>
                </tr>


<?php
    }}
?>

</table>