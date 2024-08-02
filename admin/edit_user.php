<?php
session_start();
include 'include/session.php';
include 'include/database.php';

// Connexion à la base de données
$database = new Database();
$conn = $database->getConnection();

$userId = $_GET['id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Collecter les données du formulaire
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $status = $_POST['status'];
    $rejection_reason = $_POST['rejection_reason'];
    $role = $_POST['role'];

    // Mise à jour des informations de l'utilisateur
    $query = "UPDATE users SET name = ?, email = ?, phone = ?, status = ?, rejection_reason = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssi", $name, $email, $phone, $status, $rejection_reason, $userId);
    $stmt->execute();
    $stmt->close();

    header("Location: manage_users.php?role=" . urlencode($role));
    exit();
}

// Récupérer les informations de l'utilisateur pour l'édition
$query = "SELECT * FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}

// Définir les options de statut
$statusOptions = [
    'approved' => 'Validé',
    'blocked' => 'Bloqué',
    'pending' => 'En Attente'
];
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Modifier l'Utilisateur #<?php echo htmlspecialchars($user['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></h1>

    <form method="post" action="edit_user.php?id=<?php echo htmlspecialchars($user['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="role" value="<?php echo htmlspecialchars($user['role'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        
        <div class="form-group">
            <label for="name">Nom</label>
            <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
            <label for="phone">Téléphone</label>
            <input type="text" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="status">Statut</label>
            <select class="form-control" id="status" name="status">
                <?php foreach ($statusOptions as $key => $value): ?>
                    <option value="<?php echo htmlspecialchars($key, ENT_QUOTES, 'UTF-8'); ?>" <?php echo $user['status'] == $key ? 'selected' : ''; ?>><?php echo htmlspecialchars($value, ENT_QUOTES, 'UTF-8'); ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="rejection_reason">Raison du Blocage</label>
            <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="3"><?php echo htmlspecialchars($user['rejection_reason'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Sauvegarder</button>
    </form>
</div>


<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php'); ?>
