<?php
session_start();
require '../vendor/autoload.php';
include 'include/session.php';
include 'include/database.php';

$env = parse_ini_file('../.env');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

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

    $merchant_email = $conn->query("SELECT email FROM users WHERE id = ".$merchant_id."")->fetch_all(MYSQLI_ASSOC);
    $volunteer_name = $conn->query("SELECT name FROM users WHERE id = ".$volunteer_id."")->fetch_all(MYSQLI_ASSOC);

    if ($is_valid) {
        $query = "INSERT INTO collection_requests (merchant_id, collection_date, collection_time, storage_location_id, volunteer_id, status) VALUES (?, ?, ?, ?, ?, 'pending')";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssss", $merchant_id, $collection_date, $collection_time, $storage_location_id, $volunteer_id);
        /*
        if ($stmt->execute()) {
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = $env["HOST"];
                $mail->SMTPAuth   = true;
                $mail->Username   = $env["MAIL"];
                $mail->Password   = $env["PASSWORD"];
                $mail->Port       = 587;

                $mail->CharSet = 'UTF-8';
                $mail->Encoding = 'base64';

                $mail->setFrom('no-reply@nomorewaste.fr', 'no-reply@nomorewaste.fr');
                $mail->addAddress($merchant_email[0]["email"]);

                $mail->isHTML(true);
                $mail->Subject = "Confirmation de création de collecte";
                $mail->Body    = "
                    <p>Bonjour,</p>
                    <p>Votre collecte a été créée avec succès par l'admin id $merchant_id suite à votre demande.</p>
                    <p><strong>Détails de la collecte:</strong><br>
                    Date: $collection_date<br>
                    Heure: $collection_time<br>
                    Lieu de stockage: $storage_location_id<br>
                    Par : ".$volunteer_name[0]["name"]."</p>
                    <p>Merci de votre contribution!</p>";

                $mail->AltBody = "Bonjour,\n\nVotre collecte a été créée avec succès.\n\nDétails de la collecte:\nDate: $collection_date\nHeure: $collection_time\nLieu de stockage: $storage_location_id\n\nMerci de votre contribution!";

                $mail->send();
                $_SESSION['success_message'] = "Collecte créée avec succès. Un email de confirmation a été envoyé au marchant.";
            } catch (Exception $e) {
                $_SESSION['success_message'] = "Collecte créée avec succès. Cependant, l'envoi de l'email de confirmation a échoué.";
            }
            */
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
