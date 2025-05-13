<?php
$usuario=$_POST['usuario'];
$contraseña=$_POST['contraseña'];
session_start();
$_SESSION['usuario']=$usuario;

$conexion=mysqli_connect("localhost","root","","login");

$consulta="SELECT*FROM usuarios where usuario= '$usuario' and contraseña= '$contraseña'";
$resultado=mysqli_query($conexion,$consulta);

$filas=mysqli_num_rows($resultado);

if($filas){
    header("location:home.php");

}else {
    ?>
    <?php
    include("index.php");
    ?>

        <h4 class= "bad">ERROR EN LA AUTENTIFICACIÓN</h4>

    <?php
}
mysqli_free_result($resultado);
mysqli_close($conexion);
