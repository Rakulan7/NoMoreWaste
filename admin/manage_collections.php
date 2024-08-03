<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';

// Requête pour obtenir les collectes avec des détails croisés
$query = "SELECT c.id, u.name AS merchant_name, c.collection_date, c.status,
                 sl.name AS storage_name, sl.address AS storage_address
          FROM collections c
          LEFT JOIN users u ON c.merchant_id = u.id
          LEFT JOIN storage_locations sl ON sl.id = (SELECT storage_location_id FROM collection_requests WHERE collection_date = c.collection_date AND merchant_id = c.merchant_id LIMIT 1)
          WHERE c.status = ?";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $status_filter);
$stmt->execute();
$result = $stmt->get_result();
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Gérer les Collectes</h1>

    <!-- Affichage du message d'erreur ou de succès -->
    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php
                echo htmlspecialchars($_SESSION['error_message']);
                unset($_SESSION['error_message']);
            ?>
        </div>
    <?php elseif (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php
                echo htmlspecialchars($_SESSION['success_message']);
                unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>
    
    <!-- Boutons de filtrage avec couleur bleue -->
    <div class="mb-3">
        <a href="manage_collections.php?status=pending" class="btn btn-primary">En Attente</a>
        <a href="manage_collections.php?status=scheduled" class="btn btn-primary">Planifiées</a>
        <a href="manage_collections.php?status=completed" class="btn btn-primary">Complètes</a>
        <a href="manage_collections.php?status=canceled" class="btn btn-primary">Annulées</a>
    </div>
    
    <!-- Affichage des collectes -->
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom du Marchand</th>
                    <th>Date de Collecte</th>
                    <th>Statut</th>
                    <th>Nom du Lieu de Stockage</th>
                    <th>Adresse du Lieu de Stockage</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['merchant_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['collection_date']))); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['status']) ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['storage_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['storage_address'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="collection_details.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-info btn-sm">Voir Détails</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
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
