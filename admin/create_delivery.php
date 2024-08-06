<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$beneficiaries_query = "SELECT id, name FROM beneficiaries";
$beneficiaries_result = $conn->query($beneficiaries_query);

$volunteers_query = "SELECT id, name FROM users WHERE role = 'volunteer'";
$volunteers_result = $conn->query($volunteers_query);

$completed_collections_query = "
    SELECT 
        cr.id AS collection_id, 
        cr.collection_date, 
        cr.collection_time, 
        COUNT(p.id) AS product_count 
    FROM collection_requests cr
    LEFT JOIN products p ON cr.id = p.collection_request_id
    WHERE cr.status = 'completed'
    AND cr.id NOT IN (SELECT DISTINCT collection_request_id FROM deliveries)
    GROUP BY cr.id
";
$completed_collections_result = $conn->query($completed_collections_query);

if ($completed_collections_result === false) {
    die('Échec de l’exécution de la requête : ' . htmlspecialchars($conn->error));
}
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Créer une Livraison</h1>

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

    <form action="store_delivery.php" method="post">
        <div class="form-group">
            <label for="collection_id">Collecte</label>
            <select class="form-control" id="collection_id" name="collection_id" required>
                <option value="" disabled selected>Sélectionnez une collecte</option>
                <?php while ($row = $completed_collections_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['collection_id'], ENT_QUOTES, 'UTF-8'); ?>">
                        ID: <?php echo htmlspecialchars($row['collection_id'], ENT_QUOTES, 'UTF-8'); ?> - Date: <?php echo htmlspecialchars(date('d/m/Y', strtotime($row['collection_date'])), ENT_QUOTES, 'UTF-8'); ?> - Nombre de Produits: <?php echo htmlspecialchars($row['product_count'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="beneficiary_id">Bénéficiaire</label>
            <select class="form-control" id="beneficiary_id" name="beneficiary_id" required>
                <?php while ($row = $beneficiaries_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="volunteer_id">Bénévole</label>
            <select class="form-control" id="volunteer_id" name="volunteer_id" required>
                <?php while ($row = $volunteers_result->fetch_assoc()): ?>
                    <option value="<?php echo htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo htmlspecialchars($row['name'], ENT_QUOTES, 'UTF-8'); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="delivery_date">Date de Livraison</label>
            <input type="date" class="form-control" id="delivery_date" name="delivery_date" required>
        </div>
        <button type="submit" class="btn btn-success">Créer Livraison</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php'); ?>
<?php $conn->close(); ?>
