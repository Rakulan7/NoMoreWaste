<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$collection_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Vérifier si l'identifiant de la collecte est valide
if ($collection_id <= 0) {
    $_SESSION['error_message'] = "ID de collecte invalide.";
    header("Location: manage_collections.php");
    exit();
}

// Requête pour obtenir les détails de la collecte
$query = "SELECT c.id, u.name AS merchant_name, c.collection_date, c.status,
                 sl.name AS storage_name, sl.address AS storage_address,
                 v.name AS volunteer_name, v.id AS volunteer_id
          FROM collections c
          LEFT JOIN users u ON c.merchant_id = u.id
          LEFT JOIN storage_locations sl ON sl.id = (SELECT storage_location_id FROM collection_requests WHERE collection_date = c.collection_date AND merchant_id = c.merchant_id LIMIT 1)
          LEFT JOIN volunteers v ON v.id = (SELECT volunteer_id FROM collection_requests WHERE collection_date = c.collection_date AND merchant_id = c.merchant_id LIMIT 1)
          WHERE c.id = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $collection_id);
$stmt->execute();
$result = $stmt->get_result();
$collection = $result->fetch_assoc();

// Traitement du formulaire pour mettre à jour le statut
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = isset($_POST['status']) ? $_POST['status'] : $collection['status'];

    // Assurez-vous que le statut est valide avant de mettre à jour
    $valid_statuses = ['pending', 'scheduled', 'completed', 'canceled'];
    if (!in_array($new_status, $valid_statuses)) {
        $_SESSION['error_message'] = "Statut invalide.";
        header("Location: collection_details.php?id=" . $collection_id);
        exit();
    }

    $update_query = "UPDATE collections SET status = ? WHERE id = ?";
    $update_stmt = $conn->prepare($update_query);
    if ($update_stmt) {
        $update_stmt->bind_param("si", $new_status, $collection_id);
        $update_stmt->execute();
        if ($update_stmt->affected_rows > 0) {
            // Redirection pour éviter le rechargement du formulaire
            $_SESSION['success_message'] = "Statut mis à jour avec succès.";
            header("Location: collection_details.php?id=" . $collection_id);
            exit();
        } else {
            $_SESSION['error_message'] = "Erreur lors de la mise à jour du statut.";
        }
    } else {
        $_SESSION['error_message'] = "Erreur de préparation de la requête.";
    }
    $update_stmt->close();
}
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Détails de la Collecte</h1>

    <!-- Affichage du message d'erreur ou de succès -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php
                echo htmlspecialchars($_SESSION['error_message'] ?? ''); // Utiliser une valeur par défaut pour éviter les erreurs
                unset($_SESSION['error_message']);
            ?>
        </div>
    <?php elseif (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php
                echo htmlspecialchars($_SESSION['success_message'] ?? ''); // Utiliser une valeur par défaut pour éviter les erreurs
                unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <?php if ($collection): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom du Marchand</th>
                    <th>Date de Collecte</th>
                    <th>Statut</th>
                    <th>Nom du Lieu de Stockage</th>
                    <th>Adresse du Lieu de Stockage</th>
                    <th>Bénévole</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td><?php echo htmlspecialchars($collection['id'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars($collection['merchant_name'] ?? ''); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($collection['collection_date'] ?? ''))); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($collection['status'] ?? '')); ?></td>
                    <td><?php echo htmlspecialchars($collection['storage_name'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($collection['storage_address'] ?? 'N/A'); ?></td>
                    <td>
                        <?php if (isset($collection['volunteer_name']) && isset($collection['volunteer_id'])): ?>
                            <a href="volunteer_details.php?id=<?php echo htmlspecialchars($collection['volunteer_id']); ?>">
                                <?php echo htmlspecialchars($collection['volunteer_name']); ?>
                            </a>
                        <?php else: ?>
                            N/A
                        <?php endif; ?>
                    </td>
                </tr>
            </tbody>
        </table>

        <h2 class="mt-4">Modifier le Statut</h2>
        <form method="post" action="">
            <div class="form-group">
                <label for="status">Statut</label>
                <select class="form-control" id="status" name="status" required>
                    <option value="pending" <?php echo ($collection['status'] === 'pending') ? 'selected' : ''; ?>>En Attente</option>
                    <option value="scheduled" <?php echo ($collection['status'] === 'scheduled') ? 'selected' : ''; ?>>Planifiées</option>
                    <option value="completed" <?php echo ($collection['status'] === 'completed') ? 'selected' : ''; ?>>Complètes</option>
                    <option value="canceled" <?php echo ($collection['status'] === 'canceled') ? 'selected' : ''; ?>>Annulées</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Mettre à Jour</button>
        </form>
    <?php else: ?>
        <p>Aucune collecte trouvée.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php 
include('include/footer.php');
$stmt->close();
$conn->close();
?>
