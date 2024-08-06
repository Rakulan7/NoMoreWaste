<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$beneficiary_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($beneficiary_id > 0) {
    $database = new Database();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("DELETE FROM beneficiaries WHERE id = ?");
    $stmt->bind_param("i", $beneficiary_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Bénéficiaire supprimé avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la suppression du bénéficiaire.";
    }

    $stmt->close();
    $conn->close();
}

header("Location: manage_beneficiaries.php");
exit;
