<?php
session_start();
include 'include/session.php';
include 'include/database.php';
include '../generate_delivery_pdf.php';

$database = new Database();
$conn = $database->getConnection();

$delivery_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$query = "
    SELECT 
        d.id AS delivery_id, 
        d.delivery_date, 
        d.status AS delivery_status,
        cr.collection_date,
        cr.collection_time,
        cr.merchant_address,
        b.name AS beneficiary_name,
        sl.name AS storage_name,
        u.name AS volunteer_name,
        d.collection_request_id,
        d.volunteer_id,
        d.beneficiary_id,
        d.storage_id
    FROM deliveries d
    LEFT JOIN collection_requests cr ON d.collection_request_id = cr.id
    LEFT JOIN beneficiaries b ON d.beneficiary_id = b.id
    LEFT JOIN storage_locations sl ON d.storage_id = sl.id
    LEFT JOIN users u ON d.volunteer_id = u.id
    WHERE d.id = ?
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $delivery_id);
$stmt->execute();
$result = $stmt->get_result();
$delivery = $result->fetch_assoc();

if (!$delivery) {
    die('Livraison non trouvée.');
}

$products_query = "
    SELECT name, quantity, expiry_date 
    FROM products 
    WHERE collection_request_id = ?
";
$products_stmt = $conn->prepare($products_query);
$products_stmt->bind_param("i", $delivery['collection_request_id']);
$products_stmt->execute();
$products_result = $products_stmt->get_result();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $update_query = "
        UPDATE deliveries
        SET delivery_date = ?, status = ?, volunteer_id = ?, beneficiary_id = ?, storage_id = ?
        WHERE id = ?
    ";
    $update_stmt = $conn->prepare($update_query);
    $delivery_date = $_POST['delivery_date'];
    $status = $_POST['status'];
    $volunteer_id = $_POST['volunteer_id'];
    $beneficiary_id = $_POST['beneficiary_id'];
    $storage_id = $_POST['storage_id'];
    $update_stmt->bind_param("ssiiii", $delivery_date, $status, $volunteer_id, $beneficiary_id, $storage_id, $delivery_id);
    if ($update_stmt->execute()) {
        $_SESSION['success_message'] = 'Livraison mise à jour avec succès.';
        
        if ($status === 'completed') {
            include 'generate_delivery_pdf.php';
            $filePath = generateDeliveryPDF($delivery_id, $conn, '../');
            if ($filePath) {
                $_SESSION['success_message'] .= ' PDF généré avec succès.';
            } else {
                $_SESSION['error_message'] = 'Échec de la génération du PDF.';
            }
        }
    } else {
        $_SESSION['error_message'] = 'Échec de la mise à jour de la livraison.';
    }
    $update_stmt->close();
    header("Location: delivery_details.php?id=$delivery_id");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete'])) {
    $delete_query = "DELETE FROM deliveries WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_query);
    $delete_stmt->bind_param("i", $delivery_id);
    if ($delete_stmt->execute()) {
        $_SESSION['success_message'] = 'Livraison supprimée avec succès.';
    } else {
        $_SESSION['error_message'] = 'Échec de la suppression de la livraison.';
    }
    $delete_stmt->close();
    header('Location: manage_deliveries.php');
    exit();
}

$beneficiaries_result = $conn->query("SELECT id, name FROM beneficiaries");
$volunteers_result = $conn->query("SELECT id, name FROM users WHERE role = 'volunteer'");
$storage_locations_result = $conn->query("SELECT id, name FROM storage_locations");
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Détails de la Livraison</h1>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($_SESSION['error_message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['error_message']); ?>
        </div>
    <?php elseif (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($_SESSION['success_message'], ENT_QUOTES, 'UTF-8'); unset($_SESSION['success_message']); ?>
        </div>
    <?php endif; ?>

    <table class="table table-bordered">
        <tr>
            <th>ID</th>
            <td><?php echo htmlspecialchars($delivery['delivery_id'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Date de Livraison</th>
            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($delivery['delivery_date'])), ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Statut</th>
            <td><?php echo htmlspecialchars(ucfirst($delivery['delivery_status']), ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Date de Collecte</th>
            <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($delivery['collection_date'])), ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Heure de Collecte</th>
            <td><?php echo htmlspecialchars($delivery['collection_time'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Adresse du Marchand</th>
            <td><?php echo htmlspecialchars($delivery['merchant_address'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Nom du Bénéficiaire</th>
            <td><?php echo htmlspecialchars($delivery['beneficiary_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
        <tr>
            <th>Nom du Bénévole</th>
            <td><?php echo htmlspecialchars($delivery['volunteer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
        </tr>
    </table>

    <h3>Produits Associés</h3>
    <?php if ($products_result->num_rows > 0): ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Quantité</th>
                    <th>Date d'Expiration</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($product = $products_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($product['quantity'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($product['expiry_date'])), ENT_QUOTES, 'UTF-8'); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun produit associé à cette collecte.</p>
    <?php endif; ?>

    <h3>Modifier la Livraison</h3>
    <form action="delivery_details.php?id=<?php echo htmlspecialchars($delivery_id, ENT_QUOTES, 'UTF-8'); ?>" method="post">
        <div class="form-group">
            <label for="delivery_date">Date de Livraison</label>
            <input type="date" class="form-control" id="delivery_date" name="delivery_date" value="<?php echo htmlspecialchars($delivery['delivery_date'], ENT_QUOTES, 'UTF-8'); ?>" required>
        </div>
        <div class="form-group">
            <label for="status">Statut</label>
            <select class="form-control" id="status" name="status" required>
                <option value="pending" <?php echo ($delivery['delivery_status'] === 'pending') ? 'selected' : ''; ?>>En Attente</option>
                <option value="in-progress" <?php echo ($delivery['delivery_status'] === 'in-progress') ? 'selected' : ''; ?>>En Cours</option>
                <option value="completed" <?php echo ($delivery['delivery_status'] === 'completed') ? 'selected' : ''; ?>>Complète</option>
            </select>
        </div>
        <div class="form-group">
            <label for="volunteer_id">Bénévole</label>
            <select class="form-control" id="volunteer_id" name="volunteer_id" required>
                <?php while ($row = $volunteers_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($delivery['volunteer_id']) && $row['id'] == $delivery['volunteer_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="beneficiary_id">Bénéficiaire</label>
            <select class="form-control" id="beneficiary_id" name="beneficiary_id" required>
                <?php while ($row = $beneficiaries_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($delivery['beneficiary_id']) && $row['id'] == $delivery['beneficiary_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="storage_id">Lieu de Stockage</label>
            <select class="form-control" id="storage_id" name="storage_id" required>
                <?php while ($row = $storage_locations_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>" <?php echo (isset($delivery['storage_id']) && $row['id'] == $delivery['storage_id']) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" name="update" class="btn btn-primary">Mettre à Jour</button>
        <button type="submit" name="delete" class="btn btn-danger" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette livraison ?');">Supprimer</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php'); ?>
<?php $conn->close(); ?>
