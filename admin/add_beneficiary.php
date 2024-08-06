<?php
session_start();
include 'include/session.php';
include 'include/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $database = new Database();
    $conn = $database->getConnection();

    $name = $_POST['name'];
    $contact_person = $_POST['contact_person'];
    $contact_email = $_POST['contact_email'];
    $contact_phone = $_POST['contact_phone'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $country = $_POST['country'];
    $registration_date = $_POST['registration_date'];
    $service_type = $_POST['service_type'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO beneficiaries (name, contact_person, contact_email, contact_phone, address, city, country, registration_date, service_type, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssssss", $name, $contact_person, $contact_email, $contact_phone, $address, $city, $country, $registration_date, $service_type, $notes);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Bénéficiaire ajouté avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de l'ajout du bénéficiaire.";
    }

    $stmt->close();
    $conn->close();
    header("Location: manage_beneficiaries.php");
    exit;
} else {
    header("Location: manage_beneficiaries.php");
    exit;
}
