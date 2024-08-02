<?php
session_start();
include 'include/session.php';
include 'include/database.php';

// Vérifier que l'utilisateur est un admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: manage_users.php?role=admin");
    exit();
}

// Connexion à la base de données
$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $status = $_POST['status'];

    // Assurez-vous que les administrateurs sont automatiquement approuvés
    if ($role == 'admin') {
        $status = 'approved'; // Les admins sont automatiquement approuvés
    }

    $query = "INSERT INTO users (name, email, password, phone, role, join_date, status) VALUES (?, ?, ?, ?, ?, NOW(), ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssss", $name, $email, $password, $phone, $role, $status);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php?role=" . $role);
    exit();
}
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Ajouter un Utilisateur</h1>

    <form method="post" action="add_user.php">
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="form-group">
            <label for="password">Mot de passe</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="text" class="form-control" id="phone" name="phone">
        </div>
        <div class="form-group">
            <label for="role">Rôle</label>
            <select class="form-control" id="role" name="role">
                <option value="admin">Admin</option>
                <option value="merchant">Marchand</option>
                <option value="volunteer">Bénévole</option>
            </select>
        </div>
        <div class="form-group">
            <label for="status">Statut</label>
            <select class="form-control" id="status" name="status">
                <option value="approved">Approuvé</option>
                <option value="pending">En attente</option>
                <option value="blocked">Bloqué</option>
            </select>
        </div>
        <button type="submit" class="btn btn-success">Ajouter</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php'); ?>