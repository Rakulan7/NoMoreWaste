<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';

$query = "
    SELECT 
        d.id AS delivery_id, 
        d.delivery_date, 
        d.status AS delivery_status,
        cr.collection_date,
        b.name AS beneficiary_name,
        sl.name AS storage_name,
        u.name AS volunteer_name
    FROM deliveries d
    LEFT JOIN collection_requests cr ON d.collection_request_id = cr.id
    LEFT JOIN beneficiaries b ON d.beneficiary_id = b.id
    LEFT JOIN storage_locations sl ON d.storage_id = sl.id
    LEFT JOIN users u ON d.volunteer_id = u.id
    WHERE d.status = ?
";

$stmt = $conn->prepare($query);

if (!$stmt) {
    die('Échec de la préparation de la requête : ' . htmlspecialchars($conn->error));
}

$stmt->bind_param("s", $status_filter);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    die('Échec de l’exécution de la requête : ' . htmlspecialchars($stmt->error));
}
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Gérer les Livraisons</h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php
                echo htmlspecialchars($_SESSION['error_message'] ?? '', ENT_QUOTES, 'UTF-8');
                unset($_SESSION['error_message']);
            ?>
        </div>
    <?php elseif (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php
                echo htmlspecialchars($_SESSION['success_message'] ?? '', ENT_QUOTES, 'UTF-8');
                unset($_SESSION['success_message']);
            ?>
        </div>
    <?php endif; ?>

    <div class="mb-3">
        <a href="manage_deliveries.php?status=pending" class="btn btn-primary">En Attente</a>
        <a href="manage_deliveries.php?status=in-progress" class="btn btn-primary">En Cours</a>
        <a href="manage_deliveries.php?status=completed" class="btn btn-primary">Complètes</a>
    </div>

    <div class="mb-3">
        <a href="create_delivery.php" class="btn btn-success">Créer une Livraison</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date de Livraison</th>
                    <th>Statut</th>
                    <th>Date de Collecte</th>
                    <th>Nom du Bénéficiaire</th>
                    <th>Nom du Lieu de Stockage</th>
                    <th>Nom du Bénévole</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['delivery_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['delivery_date'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['delivery_status'] ?? ''), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['collection_date'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['beneficiary_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['storage_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['volunteer_name'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="delivery_details.php?id=<?php echo htmlspecialchars($row['delivery_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-info btn-sm">Voir Détails</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune livraison trouvée.</p>
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
