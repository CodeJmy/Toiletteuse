<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: index.php?page=login');
    exit;
}
?>




