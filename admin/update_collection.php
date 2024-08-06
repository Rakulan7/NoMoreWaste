<?php
session_start();
include 'include/session.php';
include 'include/database.php';
include '../generate_pdf.php';

$database = new Database();
$conn = $database->getConnection();

$collection_id = $_POST['collection_id'] ?? '';
$collection_date = $_POST['collection_date'] ?? '';
$collection_time = $_POST['collection_time'] ?? '';
$merchant_address = $_POST['merchant_address'] ?? '';
$status = $_POST['status'] ?? '';
$storage_location_id = $_POST['storage_location_id'] ?? '';
$volunteer_id = $_POST['volunteer_id'] ?? '';

$is_valid = true;
$error_message = '';

if (empty($collection_id) || empty($collection_date) || empty($collection_time) || empty($status) || empty($storage_location_id)) {
    $is_valid = false;
    $error_message = "Tous les champs obligatoires doivent être remplis.";
}

if ($is_valid) {
    $query = "
        UPDATE collection_requests 
        SET collection_date = ?, 
            collection_time = ?, 
            merchant_address = ?, 
            status = ?, 
            storage_location_id = ?, 
            volunteer_id = ?
        WHERE id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssiii", $collection_date, $collection_time, $merchant_address, $status, $storage_location_id, $volunteer_id, $collection_id);

    if ($stmt->execute()) {
        if ($status === 'completed') {
            $pdfPath = generatePDF($collection_id, $conn, '../');
            if ($pdfPath) {
                $_SESSION['success_message'] = "Collecte mise à jour et PDF généré avec succès.";
            } else {
                $_SESSION['error_message'] = "Collecte mise à jour mais échec de la génération du PDF.";
            }
        } else {
            $_SESSION['success_message'] = "Collecte mise à jour avec succès.";
        }
    } else {
        $_SESSION['error_message'] = "Erreur lors de la mise à jour de la collecte : " . htmlspecialchars($stmt->error);
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = $error_message;
}

header("Location: collection_details.php?id=" . $collection_id);
exit();
?>
