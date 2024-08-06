<?php
session_start();
include 'include/session.php';
include 'include/header.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$beneficiary_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    $stmt = $conn->prepare("UPDATE beneficiaries SET name = ?, contact_person = ?, contact_email = ?, contact_phone = ?, address = ?, city = ?, country = ?, registration_date = ?, service_type = ?, notes = ? WHERE id = ?");
    $stmt->bind_param("ssssssssssi", $name, $contact_person, $contact_email, $contact_phone, $address, $city, $country, $registration_date, $service_type, $notes, $beneficiary_id);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Bénéficiaire mis à jour avec succès.";
    } else {
        $_SESSION['error_message'] = "Erreur lors de la mise à jour du bénéficiaire.";
    }

    $stmt->close();
    $conn->close();
    header("Location: manage_beneficiaries.php");
    exit;
} else {
    $stmt = $conn->prepare("SELECT * FROM beneficiaries WHERE id = ?");
    $stmt->bind_param("i", $beneficiary_id);
    $stmt->execute();
    $beneficiary = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}
?>

<div class="container my-5">
    <h1 class="mb-4">Modifier le Bénéficiaire</h1>

    <form action="edit_beneficiary.php?id=<?php echo htmlspecialchars($beneficiary_id); ?>" method="post">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($beneficiary['name']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_person">Personne de Contact</label>
            <input type="text" class="form-control" id="contact_person" name="contact_person" value="<?php echo htmlspecialchars($beneficiary['contact_person']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_email">Email de Contact</label>
            <input type="email" class="form-control" id="contact_email" name="contact_email" value="<?php echo htmlspecialchars($beneficiary['contact_email']); ?>" required>
        </div>
        <div class="form-group">
            <label for="contact_phone">Téléphone de Contact</label>
            <input type="text" class="form-control" id="contact_phone" name="contact_phone" value="<?php echo htmlspecialchars($beneficiary['contact_phone']); ?>">
        </div>
        <div class="form-group">
            <label for="address">Adresse</label>
            <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($beneficiary['address']); ?>">
        </div>
        <div class="form-group">
            <label for="city">Ville</label>
            <input type="text" class="form-control" id="city" name="city" value="<?php echo htmlspecialchars($beneficiary['city']); ?>">
        </div>
        <div class="form-group">
            <label for="country">Pays</label>
            <input type="text" class="form-control" id="country" name="country" value="<?php echo htmlspecialchars($beneficiary['country']); ?>">
        </div>
        <div class="form-group">
            <label for="registration_date">Date d'inscription</label>
            <input type="date" class="form-control" id="registration_date" name="registration_date" value="<?php echo htmlspecialchars($beneficiary['registration_date']); ?>" required>
        </div>
        <div class="form-group">
            <label for="service_type">Type de Service</label>
            <select class="form-control" id="service_type" name="service_type" required>
                <option value="food" <?php echo ($beneficiary['service_type'] == 'food') ? 'selected' : ''; ?>>Nourriture</option>
                <option value="shelter" <?php echo ($beneficiary['service_type'] == 'shelter') ? 'selected' : ''; ?>>Hébergement</option>
                <option value="clothing" <?php echo ($beneficiary['service_type'] == 'clothing') ? 'selected' : ''; ?>>Vêtements</option>
                <option value="other" <?php echo ($beneficiary['service_type'] == 'other') ? 'selected' : ''; ?>>Autre</option>
            </select>
        </div>
        <div class="form-group">
            <label for="notes">Notes</label>
            <textarea class="form-control" id="notes" name="notes"><?php echo htmlspecialchars($beneficiary['notes']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Enregistrer les modifications</button>
        <a href="manage_beneficiaries.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
