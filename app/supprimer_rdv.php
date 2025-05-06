<?php
include_once('includes/db.php');
$id = $_GET['id'];

$pdo->prepare('DELETE FROM rdv WHERE id_rdv = ?')->execute([$id]);
header('Location: rdv.php');
exit;
?>
