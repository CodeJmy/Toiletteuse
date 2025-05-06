<?php
include_once('includes/db.php');
$id = $_GET['id'];

$pdo->prepare("DELETE FROM clients WHERE id_client = ?")->execute([$id]);
header('Location: clients.php');
exit;
?>
