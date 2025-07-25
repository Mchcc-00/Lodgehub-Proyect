<?php

require_once '../config/conexionGlobal.php';

$db = conexionDB();

    $sql = "SELECT r.id, r.costo,r.fechainicio,r.fechaFin, r.cantidadAdultos, r.cantidadNinos, r.cantidadDiscapacitados,r.hue_numDocumento, em.nombres as empNombres, em.apellidos as empApellidos, r.numeroHabitacion, mp.descripcion as metodoPago,h.nombres, h.apellidos, d.descripcion as tipDoc, s.descripcion as sexo, ec.descripcion as estaCiv, h.numTelefono, h.correo, r.fechaRegistro, r.informacionAdicional,  e.descripcion as estadoReserva FROM tp_reservas as r
    
    INNER JOIN td_estado AS e ON  r.estado = e.id
    INNER JOIN tp_huespedes AS h ON  r.hue_numDocumento = h.numDocumento
    INNER JOIN td_sexo AS s ON  h.sexo = s.id
    INNER JOIN td_tipoDocumento AS d ON  h.tipoDocumento = d.id
    INNER JOIN td_estadoCivil AS ec ON  h.estadoCivil = ec.id
    INNER JOIN td_metodoPago AS mp ON r.metodoPago = mp.id
    INNER JOIN tp_empleados AS em ON r.emp_numDocumento = em.numDocumento

ORDER BY id DESC";

$resultado = $db->prepare($sql);
$resultado->execute();

    while ($fila = $resultado->fetch(PDO::FETCH_ASSOC)){
?>


                <div id="miModal<?php echo $fila['id']; ?>" class="modal">

                <div class="division1Informacion">
                
                    <h2>Reserva N° <?php echo $fila['id']; ?></h2>

                    <form action="eliminarReserva.php" method="POST" onsubmit="return confirm('¿Estás seguro de eliminar esta reserva?');">
                        <input type="hidden" name="idReserva" value="<?php echo $fila['id']; ?>">
                        <button type="submit" class="btnEliminar" style="cursor:pointer;">Eliminar Reserva</button>

                        <span class="cerrar" onclick="cerrarModal(<?php echo $fila['id']; ?>)">&times;</span>
                    </form>

                    

                </div>
                <div class="division2Informacion">
                    <h3>Datos del huesped</h3>
                        <div  class='informacionReservaContenido'>
                            <div>
                                <p><strong> Nombres: </strong><?php echo $fila['nombres'];?></p>
                                <p><strong> Apellidos: </strong><?php echo $fila['apellidos'];?></p>
                                <p><strong> Tipo Documento: </strong> <?php echo $fila['tipDoc'];?></p>
                                <p><strong> Documento: </strong> <?php echo $fila['hue_numDocumento'];?></p>
                            </div>
                            <div>
                                <p><strong> Sexo: </strong> <?php echo $fila['sexo'];?></p>
                                <p><strong> Estado Civil: </strong><?php echo $fila['estaCiv'];?></p>
                                <p><strong> Contacto: </strong><?php echo $fila['numTelefono'];?></p>
                                <p><strong> Correo: </strong><?php echo $fila['correo'];?></p>
                            </div>
                        </div>
                </div>
                <div class="division3Informacion">
                    <h3>Información hospedaje</h3>
                    <div class="informacionReservaContenido">
                        <div>
                            <p><strong> Fecha Inicio: </strong><?php echo $fila['fechainicio'];?></p>
                            <p><strong> Fecha Fin: </strong><?php echo $fila['fechaFin'];?></p>
                            <p><strong> Habitacion: </strong> <?php echo $fila['numeroHabitacion'];?></p>
                            <p><strong> Metodo de Pago: </strong> <?php echo $fila['metodoPago'];?></p>
                            <p><strong> Total: </strong>$<?php echo $fila['costo'];?></p>
                        </div>
                        <div>
                            <p><strong> Numero de personas </strong></p>
                            <div class="infoNumeroPersonas">
                                <div>
                                    <p>Adultos</p>
                                    <div class="contenedoresPersonas">
                                        <p><?php echo $fila['cantidadAdultos'];?></p>
                                    </div>
                                </div>
                                <div>
                                    <p>Menores</p>
                                    <div class="contenedoresPersonas">
                                        <p><?php echo $fila['cantidadNinos'];?></p>
                                    </div>
                                </div>
                                <div>
                                    <p>Discapacitados</p>
                                    <div class="contenedoresPersonas">
                                        <p><?php echo $fila['cantidadDiscapacitados'];?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="division4Informacion">

                        <div class="division4InformacionAdicional">
                            <h3>Notas extra</h3>
                            <div class="contenedorInfoAdicional">
                                <p><?php echo $fila['informacionAdicional'];?></p>
                            </div>
                            <p><strong> Empleado que registro: </strong><?php echo $fila['empNombres']. " " .$fila['empApellidos'];?></p>
                        </div>

                </div>
            </div>

            <?php
    }


?>