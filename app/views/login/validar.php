<?php


require_once '../../config/conexionGlobal.php';

$db = conexionDB();


$usuario=$_POST['usuario'];
$contraseña=$_POST['contraseña'];
session_start();
$_SESSION['usuario']=$usuario;

include('../../config/db.php');

$conexion=mysqli_connect("localhost","root","","lodgehub");

$consulta="SELECT*FROM usuarios where usuario= '$usuario' and contraseña= '$contraseña'";
$resultado=mysqli_query($conexion,$consulta);

$filas=mysqli_num_rows($resultado);

if($filas){
    header("location: ../../views/homepage/homepage.php");

}else {
    ?>
    <?php
    include("login.php");
    ?>

        <h1 class= "bad">ERROR EN EL REGISTRO</h1>

    <?php
}
mysqli_free_result($resultado);
mysqli_close($conexion);
