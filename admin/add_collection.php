<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$merchant_error = $date_error = $time_error = $storage_error = $volunteer_error = '';
$success_message = '';

$merchants = $conn->query("SELECT id, name FROM users WHERE role='merchant'")->fetch_all(MYSQLI_ASSOC);
$storage_locations = $conn->query("SELECT id, name, address FROM storage_locations")->fetch_all(MYSQLI_ASSOC);
$volunteers = $conn->query("SELECT id, name FROM users WHERE role='volunteer'")->fetch_all(MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $merchant_id = $_POST['merchant_id'] ?? '';
    $collection_date = $_POST['collection_date'] ?? '';
    $collection_time = $_POST['collection_time'] ?? '';
    $storage_location_id = $_POST['storage_location_id'] ?? '';
    $volunteer_id = $_POST['volunteer_id'] ?? '';

    $is_valid = true;

    if (empty($merchant_id)) {
        $merchant_error = "Veuillez sélectionner un marchand.";
        $is_valid = false;
    }

    if (empty($collection_date)) {
        $date_error = "Veuillez sélectionner une date de collecte.";
        $is_valid = false;
    }

    if (empty($collection_time)) {
        $time_error = "Veuillez sélectionner une heure de collecte.";
        $is_valid = false;
    }

    if (empty($storage_location_id)) {
        $storage_error = "Veuillez sélectionner un lieu de stockage.";
        $is_valid = false;
    }

    if (empty($volunteer_id)) {
        $volunteer_error = "Veuillez sélectionner un bénévole.";
        $is_valid = false;
    }

    if ($is_valid) {
        $query = "INSERT INTO collection_requests (merchant_id, collection_date, collection_time, storage_location_id, volunteer_id, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $merchant_id, $collection_date, $collection_time, $storage_location_id, $volunteer_id);
        
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "Collecte ajoutée avec succès.";
            header("Location: manage_collections.php");
            exit();
        } else {
            $_SESSION['error_message'] = "Erreur lors de l'ajout de la collecte : " . htmlspecialchars($stmt->error);
        }
        $stmt->close();
    }
}

$conn->close();
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Ajouter une Collecte</h1>

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

    <form method="post" action="add_collection.php">
        <div class="form-group">
            <label for="merchant_id">Marchand</label>
            <select id="merchant_id" name="merchant_id" class="form-control">
                <option value="">Sélectionner un marchand</option>
                <?php foreach ($merchants as $merchant): ?>
                    <option value="<?php echo htmlspecialchars($merchant['id']); ?>">
                        <?php echo htmlspecialchars($merchant['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($merchant_error): ?>
                <small class="form-text text-danger"><?php echo htmlspecialchars($merchant_error); ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="collection_date">Date de Collecte</label>
            <input type="date" id="collection_date" name="collection_date" class="form-control">
            <?php if ($date_error): ?>
                <small class="form-text text-danger"><?php echo htmlspecialchars($date_error); ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="collection_time">Heure de Collecte</label>
            <input type="time" id="collection_time" name="collection_time" class="form-control">
            <?php if ($time_error): ?>
                <small class="form-text text-danger"><?php echo htmlspecialchars($time_error); ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="storage_location_id">Lieu de Stockage</label>
            <select id="storage_location_id" name="storage_location_id" class="form-control">
                <option value="">Sélectionner un lieu de stockage</option>
                <?php foreach ($storage_locations as $storage): ?>
                    <option value="<?php echo htmlspecialchars($storage['id']); ?>">
                        <?php echo htmlspecialchars($storage['name']) . ' - ' . htmlspecialchars($storage['address']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($storage_error): ?>
                <small class="form-text text-danger"><?php echo htmlspecialchars($storage_error); ?></small>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label for="volunteer_id">Bénévole</label>
            <select id="volunteer_id" name="volunteer_id" class="form-control">
                <option value="">Sélectionner un bénévole</option>
                <?php foreach ($volunteers as $volunteer): ?>
                    <option value="<?php echo htmlspecialchars($volunteer['id']); ?>">
                        <?php echo htmlspecialchars($volunteer['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if ($volunteer_error): ?>
                <small class="form-text text-danger"><?php echo htmlspecialchars($volunteer_error); ?></small>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Ajouter la Collecte</button>
        <a href="manage_collections.php" class="btn btn-secondary">Annuler</a>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php'); ?>
