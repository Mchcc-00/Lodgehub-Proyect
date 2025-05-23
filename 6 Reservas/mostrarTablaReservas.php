<?php

$conexion = new mysqli("localhost","root","","reservas_pruebita");

if ($conexion->connect_error){
    die("Conexión fallida: ".$conexion->connect_error);
}

$sql = "SELECT r.res_id, r.res_temhab_numHabitacion, h.hue_nombres, h.hue_apellidos,r.res_fecRegistro, r.res_fechaInicio, r.res_fechaFin, r.res_costoReserva, e.estRes_descripcion FROM tp_reservas as r
INNER JOIN td_estadoReserva AS e ON  r.res_estRes_estadoReserva = e.estRes_id
INNER JOIN tp_huespedes AS h ON  r.res_hue_numDocumento = h.hue_numDocumento

ORDER BY res_id DESC";
$resultado = $conexion->query($sql);
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
        </tr>

        <?php
if ($resultado->num_rows > 0){
    while ($fila = $resultado->fetch_assoc()){?>
                <tr>
                    <td><?php echo $fila['res_id'];?></td>
                    <td><?php echo $fila['res_temhab_numHabitacion'];?></td>
                    <td><?php echo $fila['hue_nombres'] . " " .$fila['hue_apellidos'] ;?></td>
                    <td><?php echo $fila['res_fecRegistro'];?></td>
                    <td><?php echo $fila['res_fechaInicio'];?></td>
                    <td><?php echo $fila['res_fechaFin'];?></td>
                    <td><?php echo $fila['res_costoReserva'];?></td>
                    <td><?php echo $fila['estRes_descripcion'];?></td>
                    <td><button onclick="mostrarModal()">Ver información</button></td>
                </tr>
                <?php
                }
} else{
    ?>
    <tr><td colspan='9'>No hay reservas registradas. </td></tr>
    <?php
    }
    ?>

</table>

<?php
$conexion->close();
?>