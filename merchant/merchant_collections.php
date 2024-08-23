<?php
session_start();
include 'include/session.php';
include 'include/database.php';
include 'include/header.php';

$database = new Database();
$conn = $database->getConnection();

$id_merchant = $_SESSION['id_merchant'];

$status_map = [
    'created' => 'pending',
    'in-progress' => 'assigned',
    'completed' => 'completed',
    'canceled' => 'canceled'
];

if (empty($_GET['status'])){
    header('location: merchant_collections.php?status=created');
    exit;
}

$status_filter = isset($_GET['status']) && isset($status_map[$_GET['status']]) ? $status_map[$_GET['status']] : 'pending';

$error = '';
$success = '';

$query = "SELECT cr.*, sl.address as storage_address 
          FROM collection_requests cr
          LEFT JOIN storage_locations sl ON cr.storage_location_id = sl.id
          WHERE cr.merchant_id = ? AND cr.status = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("is", $id_merchant, $status_filter);
$stmt->execute();
$collection_result = $stmt->get_result();
?>

<body>
    <div class="container">
        <?php if (isset($_SESSION['error_message'])): ?>
            <div class="my-4 alert alert-danger">
                <?php
                    echo htmlspecialchars($_SESSION['error_message'] ?? '');
                    unset($_SESSION['error_message']);
                ?>
            </div>
        <?php elseif (isset($_SESSION['success_message'])): ?>
            <div class="my-4 alert alert-success">
                <?php
                    echo htmlspecialchars($_SESSION['success_message'] ?? '');
                    unset($_SESSION['success_message']);
                ?>
            </div>
        <?php endif; ?>
        <h1 class="my-4">Gestion des Collectes</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="mb-4">
            <a href="merchant_collections.php?status=created" class="btn btn-primary <?php echo $_GET['status'] === 'created' ? 'btn-active' : ''; ?>">Collectes Créées</a>
            <a href="merchant_collections.php?status=in-progress" class="btn btn-primary <?php echo $_GET['status'] === 'in-progress' ? 'btn-active' : ''; ?>">Collectes en Cours</a>
            <a href="merchant_collections.php?status=completed" class="btn btn-primary <?php echo $_GET['status'] === 'completed' ? 'btn-active' : ''; ?>">Collectes Terminées</a>
            <a href="merchant_collections.php?status=canceled" class="btn btn-primary <?php echo $_GET['status'] === 'canceled' ? 'btn-active' : ''; ?>">Collectes Annulée</a>
            <a href="create_collection.php" class="btn btn-success">Créer une Collecte</a>
        </div>

        <div class="card">
            <div class="card-header">Mes Collectes (<?php echo ucfirst($_GET['status']); ?>)</div>
            <div class="card-body">
                <?php if ($collection_result->num_rows > 0): ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Date de Collecte</th>
                                <th>Lieu de Stockage</th>
                                <th>Statut</th>
                                <th>PDF</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $collection_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($row['collection_date']))); ?></td>
                                    <td><?php echo htmlspecialchars($row['storage_address'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(ucfirst($row['status'])); ?></td>
                                    <td>
                                        <?php
                                        $pdf_filename = '../pdf/collection_' . $row['id'] . '.pdf';
                                        if (file_exists($pdf_filename)) {
                                            echo '<a href="' . htmlspecialchars($pdf_filename, ENT_QUOTES, 'UTF-8') . '" target="_blank" class="btn btn-secondary btn-sm">Voir PDF</a>';
                                        } else {
                                            echo 'N/A';
                                        }
                                        ?>
                                    </td>
                                    <td>
                                        <a href="collection_details.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-info btn-sm">Détails</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Aucune collecte trouvée.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php') ?>
