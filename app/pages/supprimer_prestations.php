<?php
include_once('includes/db.php');
$id = $_GET['id'];

$pdo->prepare('DELETE FROM prestations WHERE id_prestation = ?')->execute([$id]);
header('Location: index.php?page=prestations');
exit;
?>