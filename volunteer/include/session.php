<?php

if (!isset($_SESSION['volunteer_id']) || $_SESSION['role'] !== 'volunteer') {
    $login_path = isset($path) ? $path : 'login.php';
    header("Location: ". $login_path);
    exit();
}