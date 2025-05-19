<?php
include_once('includes/db.php');
$id = $_GET['id'];

$pdo->prepare('DELETE FROM paiements WHERE id_paiement = ?')->execute([$id]);
header('Location: index.php?page=paiements');
exit;
?>
