<?php
session_start();
include 'include/session.php';
include 'include/database.php';
include 'include/header.php';

$database = new Database();
$conn = $database->getConnection();

$id_merchant = $_SESSION['id_merchant'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_email'])) {
        $new_email = $_POST['email'];
        
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param("si", $new_email, $id_merchant);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Cet e-mail est déjà utilisé par un autre compte.";
        } else {
            $stmt = $conn->prepare("UPDATE users SET email = ? WHERE id = ?");
            $stmt->bind_param("si", $new_email, $id_merchant);
            if ($stmt->execute()) {
                $success = "L'adresse e-mail a été mise à jour avec succès.";
            } else {
                $error = "Erreur lors de la mise à jour de l'adresse e-mail.";
            }
        }
        $stmt->close();
    } elseif (isset($_POST['update_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        if ($new_password !== $confirm_password) {
            $error = "Les nouveaux mots de passe ne correspondent pas.";
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $id_merchant);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();

            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashed_password, $id_merchant);
                if ($stmt->execute()) {
                    $success = "Le mot de passe a été mis à jour avec succès.";
                } else {
                    $error = "Erreur lors de la mise à jour du mot de passe.";
                }
            } else {
                $error = "Le mot de passe actuel est incorrect.";
            }
            $stmt->close();
        }
    }
}

$stmt = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt->bind_param("i", $id_merchant);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$current_email = htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8');

$conn->close();
?>

<body>
    <div class="container">
        <h1 class="my-4">Mon Profil</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card mb-4">
            <div class="card-header">Modifier l'adresse e-mail</div>
            <div class="card-body">
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="email">Nouvelle adresse e-mail</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo $current_email; ?>" required>
                    </div>
                    <button type="submit" name="update_email" class="btn btn-primary">Mettre à jour l'e-mail</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">Modifier le mot de passe</div>
            <div class="card-body">
                <form action="profile.php" method="POST">
                    <div class="form-group">
                        <label for="current_password">Mot de passe actuel</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label for="new_password">Nouveau mot de passe</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm_password">Confirmer le nouveau mot de passe</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-primary">Mettre à jour le mot de passe</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <?php include 'include/footer.php'; ?>
</body>
</html>
