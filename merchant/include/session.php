<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['id_merchant']) || $_SESSION['role'] !== 'merchant') {
    $_SESSION['error_message'] = "Vous devez être connecté en tant que commerçant pour accéder à cette page.";
    $login_path = isset($path) ? $path : 'login.php';
    header("Location: ". $login_path);
    exit();
}