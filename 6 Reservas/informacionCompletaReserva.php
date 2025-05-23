<?php

$conexion = new mysqli("localhost","root","","reservas_pruebita");

if ($conexion->connect_error){
    die("Conexión fallida: ".$conexion->connect_error);
}


if (isset($_GET['res_id'])){
    $id = $_GET['res_id'];

    $stmt = $conn->prepare("SELECT r.res_id, r.res_hue_numDocumento , r.res_temhab_numHabitacion, h.hue_nombres, h.hue_apellidos, d.tipDoc_descripcion, s.sex_descripcion, ec.estCiv_descripcion, h.hue_numContacto, h.hue_correo, r.res_fecRegistro, r.res_fechaInicio, r.res_fechaFin, r.res_costoReserva, e.estRes_descripcion FROM tp_reservas as r
    
INNER JOIN td_estadoReserva AS e ON  r.res_estRes_estadoReserva = e.estRes_id
INNER JOIN tp_huespedes AS h ON  r.res_hue_numDocumento = h.hue_numDocumento
INNER JOIN td_sexo AS s ON  h.hue_sex_sexo = s.sex_id
INNER JOIN td_tipoDocumento AS d ON  h.hue_tipDoc_tipoDocumento = d.tipDoc_id
INNER JOIN td_estadoCivil AS ec ON  h.hue_estCiv_estadoCivil = ec.estCiv_id

ORDER BY res_id DESC");

    $stmt->bind_param("i",$id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    $resultado = $conexion->query($stmt);


if ($resultado->num_rows > 0) {
    while ($fila = $resultado->fetch_assoc()) {

    echo        "<div  class='informacionReservaContenido'>";
    echo        "<div>";
    echo            "<p><strong> Nombres: </strong>" . $fila['hue_nombres'] . "</p>";
    echo            "<p><strong> Apellidos: </strong>" . $fila['hue_apellidos'] . "</p>";
    echo            "<p><strong> Documento: " . $fila['tipDoc_descripcion'] . "</strong> " . $fila['res_hue_numDocumento'] . "</p>";
    echo            "<p><strong> Sexo: </strong>" . $fila['sex_descripcion'] . "</p>";
    echo        "</div>";
    echo       "<div>";
    echo            "<p><strong> Estado Civil: </strong>" . $fila['estCiv_descripcion'] . "</p>";
    echo            "<p><strong> Contacto: </strong>" . $fila['hue_numContacto'] . "</p>";
    echo            "<p><strong> Correo: </strong>" . $fila['hue_correo'] . "</p>";
    echo       "</div>";
    echo    "</div>";

    }
}

$stmt->close();
}

$conexion->close();
?>