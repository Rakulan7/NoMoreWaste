<?php
session_start();
include 'include/session.php';
include 'include/database.php';

if (!isset($_GET['id']) || !isset($_GET['status'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id'];
$status = $_GET['status'];

// Connexion à la base de données
$database = new Database();
$conn = $database->getConnection();

if ($status == 'block') {
    $status = 'blocked';
} elseif ($status == 'approve') {
    $status = 'approved';
} else {
    header("Location: manage_users.php");
    exit();
}

$query = "UPDATE users SET status = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $status, $userId);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: manage_users.php?role=admin");
exit();
?>
