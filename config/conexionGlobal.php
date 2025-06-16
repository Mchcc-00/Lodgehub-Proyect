<?php

conexionDB();



function conexionDB(){
    try {
        $dsn = "mysql:host=localhost;dbname=Lodgehub";
        $db = new PDO($dsn,'root','');
        return $db;
    } catch (PDOException $PDOe) {
        echo $PDOe->getMessage(). "<br>";
        echo "No se ha podido realizar la conexion <br>";
        exit();
    }
}
