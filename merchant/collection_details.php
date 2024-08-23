<?php
session_start();
include 'include/database.php';
include 'include/header.php';

if (empty($_GET['id'])) {
    $_SESSION['error_message'] = "L'ID de la collecte est manquant.";
    header('Location: merchant_collections.php?status=created');
    exit();
}

$collection_id = $_GET['id'];

$database = new Database();
$conn = $database->getConnection();

$query = "SELECT cr.*, sl.address as storage_address, u.name as volunteer_name, u.email as volunteer_email 
          FROM collection_requests cr
          LEFT JOIN storage_locations sl ON cr.storage_location_id = sl.id
          LEFT JOIN users u ON cr.volunteer_id = u.id
          WHERE cr.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $collection_id);
$stmt->execute();
$collection_result = $stmt->get_result();
$collection = $collection_result->fetch_assoc();

if (!$collection) {
    $_SESSION['error_message'] = "La collecte demandée n'a pas été trouvée.";
    header('Location: merchant_collections.php?status=created');
    exit();
}

$query = "SELECT * FROM products WHERE collection_request_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $collection_id);
$stmt->execute();
$products_result = $stmt->get_result();
?>

<div class="container my-5">
    <h2>Détails de la Collecte #<?php echo htmlspecialchars($collection['id']); ?></h2>
    <p><strong>Date de la Collecte :</strong> <?php echo htmlspecialchars(date('d/m/Y', strtotime($collection['collection_date']))); ?></p>
    <p><strong>Heure de la Collecte :</strong> <?php echo htmlspecialchars($collection['collection_time']); ?></p>
    <p><strong>Adresse de Stockage :</strong> <?php echo htmlspecialchars($collection['storage_address'] ?? 'N/A'); ?></p>
    <p><strong>Statut :</strong> <?php echo htmlspecialchars(ucfirst($collection['status'])); ?></p>

    <?php if ($collection['volunteer_name']): ?>
        <h4>Informations de Livraison</h4>
        <p><strong>Bénévole :</strong> <?php echo htmlspecialchars($collection['volunteer_name']); ?></p>
        <p><strong>Email du Bénévole :</strong> <?php echo htmlspecialchars($collection['volunteer_email']); ?></p>
    <?php endif; ?>

    <?php if ($collection['status'] == 'pending'): ?>
        <form action="cancel_collection.php" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir annuler cette collecte ?');">
            <input type="hidden" name="collection_id" value="<?php echo $collection['id']; ?>">
            <button type="submit" class="btn btn-danger">Annuler la Collecte</button>
        </form>
    <?php endif; ?>

    <h4>Produits</h4>
    <?php if ($products_result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom du Produit</th>
                    <th>Code-barres</th>
                    <th>Date d'Expiration</th>
                    <th>Quantité</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['barcode'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($product['expiry_date']))); ?></td>
                        <td><?php echo htmlspecialchars($product['quantity']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun produit associé à cette collecte.</p>
    <?php endif; ?>
</div>

<?php include('include/footer.php'); ?>
