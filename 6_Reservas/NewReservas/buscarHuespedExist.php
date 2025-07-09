<?php
require_once '../../config/conexionGlobal.php';

if (isset($_GET['buscarHuesped'])) {
    $documentoHuesped = trim($_GET['buscarHuesped']);
    $db = conexionDB();
    $sql = "SELECT h.numDocumento, h.numTelefono, h.correo, h.nombres, h.apellidos, td.descripcion as tipoDocumento, s.descripcion as sexo, ec.descripcion as estadoCivil FROM tp_huespedes AS h

    INNER JOIN td_tipoDocumento AS td ON h.tipoDocumento = td.id
    INNER JOIN td_sexo AS s ON h.sexo = s.id   
    INNER JOIN td_estadoCivil AS ec ON h.estadoCivil = ec.id
    
    WHERE numDocumento = (:numDocumento)";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':numDocumento', $documentoHuesped, PDO::PARAM_STR);
    $stmt->execute();
    $huesped = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($huesped) {
        // Muestra la información del huésped y el botón para generar reserva
        ?>
        <h3>Información del huésped</h3>
            <div>
                <p><strong> Nombres: </strong><?php echo htmlspecialchars($huesped['nombres']);?></p>
                <p><strong> Apellidos: </strong><?php echo htmlspecialchars($huesped['apellidos']);?></p>
                <p><strong> Tipo Documento: </strong><?php echo htmlspecialchars($huesped['tipoDocumento']);?></p>
                <p><strong> Documento: </strong><?php echo htmlspecialchars($huesped['numDocumento']);?></p>
                <p><strong> sexo: </strong><?php echo htmlspecialchars($huesped['sexo']);?></p>
                <p><strong> Estado Civil: </strong><?php echo htmlspecialchars($huesped['estadoCivil']);?></p>
                <p><strong> Contacto: </strong><?php echo htmlspecialchars($huesped['numTelefono']);?></p>
                <p><strong> Correo: </strong><?php echo htmlspecialchars($huesped['correo']);?></p>
            </div>
        <form id="formReserva" action="generarReserva.php" method="POST">
            <input type="hidden" name="documentoHuesped" value="<?php echo htmlspecialchars($documentoHuesped); ?>">
            <button type="submit" id="btnGenerarReserva" style="cursor:pointer;">Generar Reserva</button>
        </form>
    <?php
    } else {
        echo "No se encontró ningún huésped con ese documento.";
    }
}
?>