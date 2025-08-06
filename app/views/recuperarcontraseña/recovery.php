<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use PHPMailer\PHPMailer\SMTP;

require '../../../PHPMailer/Exception.php';
require '../../../PHPMailer/PHPMailer.php';
require '../../../PHPMailer/SMTP.php';



require_once ('../../../config/conexionGlobal.php');
$db = conexionDB();


$correo=$_POST['correo'];
session_start();
$_SESSION['correo']=$correo;



$conexion=mysqli_connect("localhost","root","","lodgehub");

$consulta="SELECT*FROM tp_empleados where correo= '$correo' and sesionCaducada = 1";
$resultado=mysqli_query($conexion,$consulta);
$row = mysqli_fetch_assoc($resultado);

$filas=mysqli_num_rows($resultado);

if($filas-- > 0){

$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'lodgehub3@gmail.com';
    $mail->Password   = 'myid grtr grcu fjab';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Permitir certificados autofirmados (solo para pruebas locales)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ];

    $mail->setFrom('lodgehub3@gmail.com', 'LODGEHUB');
    $mail->addAddress($correo);

    $mail->isHTML(true);
    $mail->Subject = 'Recuperar contrasena';
$id = isset($row['id']) ? $row['id'] : '';
$mail->Body ='Hola buen dia, este es un mensaje de recuperacion de contrasena, por favor ingrese al siguiente enlace para continuar con el proceso de recuperacion de su contrasena: <a href="http://localhost/lodgehub/app/views/recuperarcontraseña/Contraseña.php?id='.$id.'">Recuperar Contrasena</a>';
    $mail->AltBody = 'Este es el mensaje de recuperación de contraseña en texto plano.';

    $mail->send();
    header("location: ../login/login.php?mensaje=Correo enviado correctamente");
} catch (Exception $e) {
    header("location: ../login/login.php?mensaje=Error al enviar el correo: {$mail->ErrorInfo}");
}

}else {
    header("location: ../login/login.php?mensaje=El correo no existe o no es valido");
}

mysqli_free_result($resultado);
mysqli_close($conexion);

?>