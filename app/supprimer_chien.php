<?php
include_once('includes/db.php');
$id = $_GET['id'];

$pdo->prepare('DELETE FROM chiens WHERE id_chien = ?')->execute([$id]);
header('Location: chiens.php');
exit;
?>
