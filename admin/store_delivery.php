<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collection_id = isset($_POST['collection_id']) ? (int)$_POST['collection_id'] : 0;
    $beneficiary_id = isset($_POST['beneficiary_id']) ? (int)$_POST['beneficiary_id'] : 0;
    $volunteer_id = isset($_POST['volunteer_id']) ? (int)$_POST['volunteer_id'] : 0;
    $delivery_date = isset($_POST['delivery_date']) ? $_POST['delivery_date'] : '';

    if (!$collection_id || !$beneficiary_id || !$volunteer_id || !$delivery_date) {
        $_SESSION['error_message'] = 'Veuillez remplir tous les champs requis.';
        header('Location: create_delivery.php');
        exit();
    }

    $query = "
        INSERT INTO deliveries (collection_request_id, beneficiary_id, delivery_date, volunteer_id, status)
        VALUES (?, ?, ?, ?, 'pending')
    ";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("iisi", $collection_id, $beneficiary_id, $delivery_date, $volunteer_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'La livraison a été créée avec succès.';
        } else {
            $_SESSION['error_message'] = 'Échec de la création de la livraison : ' . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = 'Échec de la préparation de la requête : ' . htmlspecialchars($conn->error);
    }

    $conn->close();

    header('Location: manage_deliveries.php');
    exit();
}
?>
