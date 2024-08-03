<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$collection_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$collection_id) {
    die('ID de collecte invalide.');
}

// Récupération des détails de la collecte, y compris l'adresse du marchand
$query = "
    SELECT 
        cr.id AS collection_id, 
        cr.collection_date, 
        cr.collection_time, 
        cr.status AS collection_status, 
        cr.storage_location_id,
        cr.merchant_address,
        sl.name AS storage_name,
        sl.address AS storage_address,
        d.volunteer_id,
        u.name AS volunteer_name
    FROM collection_requests cr
    LEFT JOIN storage_locations sl ON cr.storage_location_id = sl.id
    LEFT JOIN deliveries d ON cr.id = d.collection_request_id
    LEFT JOIN users u ON d.volunteer_id = u.id
    WHERE cr.id = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("i", $collection_id);
$stmt->execute();
$collection = $stmt->get_result()->fetch_assoc();

if (!$collection) {
    die('Aucune collecte trouvée.');
}

// Récupération des produits associés à la collecte
$product_query = "SELECT * FROM products WHERE collection_request_id = ?";
$product_stmt = $conn->prepare($product_query);
$product_stmt->bind_param("i", $collection_id);
$product_stmt->execute();
$products_result = $product_stmt->get_result();

// Récupération des lieux de stockage
$storage_query = "SELECT * FROM storage_locations";
$storage_result = $conn->query($storage_query);

// Récupération des bénévoles
$volunteer_query = "SELECT id, name FROM users WHERE role = 'volunteer'";
$volunteer_result = $conn->query($volunteer_query);
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Détails de la Collecte</h1>

    <!-- Affichage du message d'erreur ou de succès -->
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

    <form action="update_collection.php" method="post">
        <input type="hidden" name="collection_id" value="<?php echo htmlspecialchars($collection['collection_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">

        <div class="form-group">
            <label for="collection_date">Date de Collecte</label>
            <input type="date" class="form-control" id="collection_date" name="collection_date" value="<?php echo htmlspecialchars($collection['collection_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-group">
            <label for="collection_time">Heure de Collecte</label>
            <input type="time" class="form-control" id="collection_time" name="collection_time" value="<?php echo htmlspecialchars($collection['collection_time'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-group">
            <label for="merchant_address">Adresse du Marchand</label>
            <input type="text" class="form-control" id="merchant_address" name="merchant_address" value="<?php echo htmlspecialchars($collection['merchant_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>

        <div class="form-group">
            <label for="status">Statut</label>
            <select id="status" name="status" class="form-control">
                <option value="pending" <?php echo ($collection['collection_status'] == 'pending') ? 'selected' : ''; ?>>En Attente</option>
                <option value="assigned" <?php echo ($collection['collection_status'] == 'assigned') ? 'selected' : ''; ?>>Assignée</option>
                <option value="completed" <?php echo ($collection['collection_status'] == 'completed') ? 'selected' : ''; ?>>Complète</option>
                <option value="canceled" <?php echo ($collection['collection_status'] == 'canceled') ? 'selected' : ''; ?>>Annulée</option>
            </select>
        </div>

        <div class="form-group">
            <label for="storage_location">Lieu de Stockage</label>
            <select id="storage_location" name="storage_location_id" class="form-control">
                <option value="">Sélectionner un lieu de stockage</option>
                <?php while ($storage = $storage_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($storage['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($storage['id'] == $collection['storage_location_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($storage['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="form-group">
            <label for="volunteer">Bénévole</label>
            <select id="volunteer" name="volunteer_id" class="form-control">
                <option value="">Sélectionner un bénévole</option>
                <?php while ($volunteer = $volunteer_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($volunteer['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo ($volunteer['id'] == $collection['volunteer_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($volunteer['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <button type="submit" class="btn btn-primary">Mettre à Jour</button>
    </form>

    <!-- Affichage des produits associés à la collecte -->
    <h2 class="mt-5">Produits Donnés</h2>
    <?php if ($products_result->num_rows > 0): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>Nom du Produit</th>
                    <th>Code-barres</th>
                    <th>Date d'Expiration</th>
                    <th>Quantité</th>
                    <th>Date de Stockage</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['barcode'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['expiry_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['quantity'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['storage_date'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun produit trouvé pour cette collecte.</p>
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
