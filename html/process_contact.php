<?php
include('include/database.php');

session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $phone = htmlspecialchars($_POST['phone']);
    $message = htmlspecialchars($_POST['message']);

    if (!empty($name) && !empty($email) && !empty($message)) {
        $database = new Database();
        $conn = $database->getConnection();

        $stmt = $conn->prepare("INSERT INTO contact_requests (name, email, phone, message, status, submitted_at) VALUES (?, ?, ?, ?, 'pending', NOW())");
        $stmt->bind_param("ssss", $name, $email, $phone, $message);

        if ($stmt->execute()) {
            $_SESSION['contact_status'] = 'success';
            $_SESSION['contact_message'] = 'Merci de nous avoir contactés. Nous reviendrons vers vous bientôt.';
        } else {
            $_SESSION['contact_status'] = 'error';
            $_SESSION['contact_message'] = 'Erreur lors de l\'envoi de votre demande. Veuillez réessayer.';
        }

        $stmt->close();
        $conn->close();
    } else {
        $_SESSION['contact_status'] = 'error';
        $_SESSION['contact_message'] = 'Veuillez remplir tous les champs obligatoires.';
    }

    header("Location: contact.php");
    exit();
} else {
    header("Location: contact.php");
    exit();
}
