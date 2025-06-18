<?php

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