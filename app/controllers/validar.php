<?php


// require_once '../../config/conexionGlobal.php';
// $db = conexionDB();


$correo=$_POST['correo'];
$password=$_POST['password'];
session_start();
$_SESSION['correo']=$correo;



$conexion=mysqli_connect("localhost","root","","lodgehub");

$consulta="SELECT*FROM tp_empleados where correo= '$correo' and password= '$password'";
$resultado=mysqli_query($conexion,$consulta);

$filas=mysqli_num_rows($resultado);

if($filas){
    header("location: ../homepage/homepage.php");

}else {
    ?>
    <?php
    include("../views/login/login.php");
    ?>

        <h6 class= "bad">ERROR EN EL REGISTRO</h6>

    <?php
}
mysqli_free_result($resultado);
mysqli_close($conexion);
