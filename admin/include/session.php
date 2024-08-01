<?php

if (!isset($_SESSION['id_admin'])) {
    $login_path = isset($path) ? $path : 'login.php';
    header("Location: ". $login_path);
    exit();
}