<?php
session_start();
include 'include/session.php';
include 'include/header.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$totalUsers = $conn->query("SELECT COUNT(*) FROM users")->fetch_array()[0];
$totalRequests = $conn->query("SELECT COUNT(*) FROM contact_requests WHERE status != 'processed'")->fetch_array()[0];
$totalVolunteers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'volunteer'")->fetch_array()[0];
$totalPendingVolunteers = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'volunteer' AND status = 'pending'")->fetch_array()[0];
$totalPendingMerchants = $conn->query("SELECT COUNT(*) FROM users WHERE role = 'merchant' AND status = 'pending'")->fetch_array()[0];

$collectionsQuery = "
    SELECT 
        cr.id AS collection_id, 
        cr.collection_date, 
        cr.status AS collection_status, 
        u.name AS merchant_name 
    FROM collection_requests cr
    JOIN users u ON cr.merchant_id = u.id
    WHERE cr.collection_date >= CURDATE() 
    AND cr.status != 'completed' 
    ORDER BY cr.collection_date ASC 
    LIMIT 5
";
$collectionsResult = $conn->query($collectionsQuery);

$deliveriesQuery = "
    SELECT 
        d.id AS delivery_id, 
        d.delivery_date, 
        d.status AS delivery_status, 
        b.name AS beneficiary_name, 
        cr.collection_date, 
        cr.merchant_address, 
        sl.name AS storage_location, 
        sl.address AS storage_address, 
        sl.city AS storage_city, 
        sl.country AS storage_country 
    FROM deliveries d
    JOIN collection_requests cr ON d.collection_request_id = cr.id
    JOIN beneficiaries b ON d.beneficiary_id = b.id
    JOIN storage_locations sl ON d.storage_id = sl.id
    WHERE d.delivery_date >= CURDATE() 
    AND d.status != 'completed' 
    ORDER BY d.delivery_date ASC 
    LIMIT 5
";
$deliveriesResult = $conn->query($deliveriesQuery);

$conn->close();
?>

<div class="container my-5">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <h4 class="alert-heading">Bienvenue, Administrateur !</h4>
        <p>Utilisez le tableau de bord pour surveiller les activités récentes et gérer les utilisateurs et les demandes.</p>
        <hr>
        <p class="mb-0">N'hésitez pas à consulter les pages disponibles via la barre de navigation pour accéder aux fonctionnalités administratives.</p>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <h1 class="mb-4">Tableau de bord</h1>
    
    <div class="row">
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-primary text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Utilisateurs Totals</h5>
                    <p class="card-text"><?php echo number_format($totalUsers); ?> utilisateurs</p>
                    <a href="manage_users.php" class="btn btn-light">Gérer les utilisateurs</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-success text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Demandes de contact</h5>
                    <p class="card-text"><?php echo number_format($totalRequests); ?> demandes</p>
                    <a href="manage_requests.php" class="btn btn-light">Gérer les demandes</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4 col-md-6 mb-4">
            <div class="card bg-info text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Bénévoles Totals</h5>
                    <p class="card-text"><?php echo number_format($totalVolunteers); ?> bénévoles</p>
                    <a href="manage_users.php?role=volunteer" class="btn btn-light">Gérer les bénévoles</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Bénévoles en attente</h5>
                    <p class="card-text"><?php echo number_format($totalPendingVolunteers); ?> bénévoles en attente</p>
                    <a href="manage_users.php?role=volunteer&status=pending" class="btn btn-light">Gérer les bénévoles en attente</a>
                </div>
            </div>
        </div>
        
        <div class="col-lg-6 col-md-12 mb-4">
            <div class="card bg-warning text-white shadow-sm">
                <div class="card-body">
                    <h5 class="card-title">Commerçants en attente</h5>
                    <p class="card-text"><?php echo number_format($totalPendingMerchants); ?> commerçants en attente</p>
                    <a href="manage_users.php?role=merchant&status=pending" class="btn btn-light">Gérer les commerçants en attente</a>
                </div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header" style="background-color: #28a745; color: white;">
            Prochaines Collectes
        </div>
        <div class="card-body">
            <?php if ($collectionsResult->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date de Collecte</th>
                            <th>Statut</th>
                            <th>Commerçant</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($collection = $collectionsResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($collection['collection_id']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($collection['collection_date']))); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($collection['collection_status'])); ?></td>
                                <td><?php echo htmlspecialchars($collection['merchant_name']); ?></td>
                                <td><a href="collection_details.php?id=<?php echo htmlspecialchars($collection['collection_id']); ?>" class="btn btn-info btn-sm">Détails</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune collecte prévue.</p>
            <?php endif; ?>
            <a href="manage_collections.php" class="btn btn-success">Voir toutes les collectes en cours</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header" style="background-color: #28a745; color: white;">
            Prochaines Livraisons
        </div>
        <div class="card-body">
            <?php if ($deliveriesResult->num_rows > 0): ?>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Date de Livraison</th>
                            <th>Statut</th>
                            <th>Bénéficiaire</th>
                            <th>Date de Collecte</th>
                            <th>Adresse du Commerçant</th>
                            <th>Lieu de Stockage</th>
                            <th>Adresse</th>
                            <th>Ville</th>
                            <th>Pays</th>
                            <th>Détails</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($delivery = $deliveriesResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($delivery['delivery_id']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($delivery['delivery_date']))); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($delivery['delivery_status'])); ?></td>
                                <td><?php echo htmlspecialchars($delivery['beneficiary_name']); ?></td>
                                <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($delivery['collection_date']))); ?></td>
                                <td><?php echo htmlspecialchars($delivery['merchant_address']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['storage_location']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['storage_address']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['storage_city']); ?></td>
                                <td><?php echo htmlspecialchars($delivery['storage_country']); ?></td>
                                <td><a href="delivery_details.php?id=<?php echo htmlspecialchars($delivery['delivery_id']); ?>" class="btn btn-info btn-sm">Détails</a></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Aucune livraison prévue.</p>
            <?php endif; ?>
            <a href="manage_deliveries.php" class="btn btn-success">Voir toutes les livraisons en cours</a>
        </div>
    </div>

</div>

<?php include 'include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
