<?php
session_start();
include 'include/session.php';
include 'include/database.php';

if (!isset($_GET['id'])) {
    header("Location: manage_users.php");
    exit();
}

$userId = $_GET['id'];

$database = new Database();
$conn = $database->getConnection();

$query = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: manage_users.php?role=admin");
exit();
?>
