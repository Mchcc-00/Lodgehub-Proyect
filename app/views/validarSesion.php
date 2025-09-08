<?php

session_start();

if (!isset($_SESSION['user']) || !is_array($_SESSION['user'])) {

    header("location: login.php");
}

?>