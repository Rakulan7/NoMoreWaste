<?php
session_start();
include 'include/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['collection_id'])) {
        $collection_id = $_POST['collection_id'];

        $database = new Database();
        $conn = $database->getConnection();

        $query = "SELECT status FROM collection_requests WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $collection_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $collection = $result->fetch_assoc();

        if ($collection && $collection['status'] == 'pending') {
            $update_query = "UPDATE collection_requests SET status = 'canceled' WHERE id = ?";
            $stmt = $conn->prepare($update_query);
            $stmt->bind_param("i", $collection_id);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "La collecte a été annulée avec succès.";
            } else {
                $_SESSION['error_message'] = "Erreur lors de l'annulation de la collecte.";
            }
        } else {
            $_SESSION['error_message'] = "La collecte ne peut pas être annulée car elle n'est plus en attente.";
        }

        header("Location: merchant_collections.php");
        exit();
    }
}
