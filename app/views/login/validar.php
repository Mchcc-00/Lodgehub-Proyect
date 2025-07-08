<?php


// require_once '../../config/conexionGlobal.php';
// $db = conexionDB();


$usuario=$_POST['correo'];
$contraseña=$_POST['password'];
session_start();
$_SESSION['correo']=$usuario;



$conexion=mysqli_connect("localhost","root","","lodgehub");

$consulta="SELECT*FROM tp_empleados where correo= '$usuario' and password= '$contraseña'";
$resultado=mysqli_query($conexion,$consulta);

$filas=mysqli_num_rows($resultado);

if($filas){
    header("location: homepage.php");

}else {
    ?>
    <?php
    include("login.php");
    ?>

        <h6 class= "bad">ERROR EN EL REGISTRO</h6>

    <?php
}
mysqli_free_result($resultado);
mysqli_close($conexion);
