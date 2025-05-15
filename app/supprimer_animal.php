<?php
include_once('includes/db.php');
$id = $_GET['id'];

$pdo->prepare('DELETE FROM animal WHERE id_animal = ?')->execute([$id]);
header('Location: animal.php');
exit;
?>
