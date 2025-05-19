<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: index.php?page=login');
    exit;
}

function generateToken()
{
    if (!isset($_SESSION['csrf_token']) || time() > $_SESSION['csrf_token_expire']) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        $_SESSION['csrf_token_expire'] = time() + 1800; // 30 minutes
    }
    return $_SESSION['csrf_token'];
}

function checkToken($token)
{
    return isset($_SESSION['csrf_token']) &&
        hash_equals($_SESSION['csrf_token'], $token) &&
        time() <= $_SESSION['csrf_token_expire'];
}
