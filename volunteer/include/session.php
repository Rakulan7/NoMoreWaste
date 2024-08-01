<?php

if (!isset($_SESSION['id_volunteer'])) {
    $login_path = isset($path) ? $path : 'login.php';
    header("Location: ". $login_path);
    exit();
}