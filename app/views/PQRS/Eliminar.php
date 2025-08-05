<?php
require_once('../../../config/conexionGlobal.php');
$pdo = conexionDB();

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("DELETE FROM pqrs WHERE id = ?");
    $stmt->execute([$id]);
}

header("Location: crud.php");
exit;
?>
