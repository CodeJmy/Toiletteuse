<?php
include_once('includes/db.php');
$id = $_GET['id'];

$pdo->prepare("UPDATE animal SET id_client = NULL WHERE id_client = ?")->execute([$id]);

$pdo->prepare("DELETE FROM clients WHERE id_client = ?")->execute([$id]);

header('Location: index.php?page=clients');
exit;
?>
