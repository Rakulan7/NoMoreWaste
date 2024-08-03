<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collection_id = isset($_POST['collection_id']) ? (int)$_POST['collection_id'] : 0;
    $collection_date = isset($_POST['collection_date']) ? $_POST['collection_date'] : '';
    $collection_time = isset($_POST['collection_time']) ? $_POST['collection_time'] : '';
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    $storage_location_id = isset($_POST['storage_location_id']) ? (int)$_POST['storage_location_id'] : null;
    $volunteer_id = isset($_POST['volunteer_id']) ? (int)$_POST['volunteer_id'] : null;
    $merchant_address = isset($_POST['merchant_address']) ? $_POST['merchant_address'] : '';

    // Validation des données
    if (!$collection_id || !$status) {
        $_SESSION['error_message'] = 'Les données sont invalides.';
        header('Location: collection_details.php?id=' . $collection_id);
        exit();
    }

    // Récupérer l'adresse du marchand si elle est vide
    if (empty($merchant_address)) {
        // Récupérer l'adresse du marchand depuis la table users
        $merchant_query = "
            SELECT address, city, country 
            FROM users 
            WHERE id = (SELECT merchant_id FROM collection_requests WHERE id = ?)
        ";
        $stmt = $conn->prepare($merchant_query);
        $stmt->bind_param("i", $collection_id);
        $stmt->execute();
        $merchant = $stmt->get_result()->fetch_assoc();
        if ($merchant) {
            $merchant_address = $merchant['address'] . ', ' . $merchant['city'] . ', ' . $merchant['country'];
        } else {
            $merchant_address = 'Adresse non trouvée';
        }
        $stmt->close();
    }

    // Requête de mise à jour
    $query = "
        UPDATE collection_requests
        SET collection_date = ?, 
            collection_time = ?, 
            storage_location_id = ?, 
            volunteer_id = ?, 
            status = ?, 
            merchant_address = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($query);
    if ($stmt) {
        $stmt->bind_param("sssissi", $collection_date, $collection_time, $storage_location_id, $volunteer_id, $status, $merchant_address, $collection_id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = 'Les détails de la collecte ont été mis à jour.';
        } else {
            $_SESSION['error_message'] = 'Échec de la mise à jour des détails de la collecte.';
        }
        $stmt->close();
    } else {
        $_SESSION['error_message'] = 'Échec de la préparation de la requête.';
    }

    $conn->close();

    header('Location: collection_details.php?id=' . $collection_id);
    exit();
}
?>
