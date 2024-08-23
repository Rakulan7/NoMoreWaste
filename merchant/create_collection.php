<?php
session_start();
require '../vendor/autoload.php';
include 'include/database.php';
include 'include/header.php';

$env = parse_ini_file('../.env');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$database = new Database();
$conn = $database->getConnection();

$merchant_id = $_SESSION['id_merchant'];
$query = "SELECT address, email FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $merchant_id);
$stmt->execute();
$result = $stmt->get_result();
$merchant = $result->fetch_assoc();
$default_address = $merchant['address'];
$merchant_email = $merchant['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $collection_date = $_POST['collection_date'];
    $collection_time = isset($_POST['collection_time']) ? $_POST['collection_time'] : null;
    $merchant_address = isset($_POST['merchant_address']) ? $_POST['merchant_address'] : $default_address;

    if ($collection_time && $merchant_address) {
        $query = "INSERT INTO collection_requests (merchant_id, request_date, collection_date, collection_time, status, merchant_address) 
                  VALUES (?, NOW(), ?, ?, 'pending', ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("isss", $merchant_id, $collection_date, $collection_time, $merchant_address);

        if ($stmt->execute()) {
            $collection_id = $stmt->insert_id;

            foreach ($_POST['products'] as $product) {
                $name = $product['name'];
                $barcode = $product['barcode'];
                $expiry_date = $product['expiry_date'];
                $quantity = $product['quantity'];

                $query = "INSERT INTO products (name, barcode, expiry_date, quantity, collection_request_id) 
                          VALUES (?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssii", $name, $barcode, $expiry_date, $quantity, $collection_id);
                $stmt->execute();
            }

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
                $mail->addAddress($merchant_email);

                $mail->isHTML(true);
                $mail->Subject = "Confirmation de création de collecte";
                $mail->Body    = "
                    <p>Bonjour,</p>
                    <p>Votre collecte a été créée avec succès.</p>
                    <p><strong>Détails de la collecte:</strong><br>
                    Date: $collection_date<br>
                    Heure: $collection_time<br>
                    Lieu: $merchant_address</p>
                    <p>Merci de votre contribution!</p>";

                $mail->AltBody = "Bonjour,\n\nVotre collecte a été créée avec succès.\n\nDétails de la collecte:\nDate: $collection_date\nHeure: $collection_time\nLieu: $merchant_address\n\nMerci de votre contribution!";

                $mail->send();
                $_SESSION['success_message'] = "Collecte créée avec succès. Un email de confirmation vous a été envoyé.";
            } catch (Exception $e) {
                $_SESSION['success_message'] = "Collecte créée avec succès. Cependant, l'envoi de l'email de confirmation a échoué.";
            }

            header("Location: merchant_collections.php?status=created");
            exit();
        } else {
            $error = "Erreur lors de la création de la collecte.";
        }
    } else {
        $error = "Heure de collecte ou lieu de stockage manquant.";
    }
}

$default_address = isset($merchant['address']) ? htmlspecialchars($merchant['address']) : '';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer une Collecte - NoMoreWaste</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .product-group {
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h1>Créer une Nouvelle Collecte</h1>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <form action="create_collection.php" method="POST">
        <div class="form-group">
            <label for="collection_date">Date de Collecte</label>
            <input type="date" class="form-control" id="collection_date" name="collection_date" required>
        </div>
        <div class="form-group">
            <label for="collection_time">Heure de Collecte</label>
            <input type="time" class="form-control" id="collection_time" name="collection_time" required>
        </div>
        <div class="form-group">
            <label for="merchant_address">Lieu de Stockage (Lieu de récupération)</label>
            <input type="text" class="form-control" id="merchant_address" name="merchant_address"
                   value="<?php echo $default_address; ?>" placeholder="Entrez un autre lieu si nécessaire">
        </div>

        <h3>Ajouter des Produits</h3>
        <div id="products-container">
            <div class="product-group">
                <div class="form-group">
                    <label for="product_name">Nom du Produit</label>
                    <input type="text" class="form-control" name="products[0][name]" required>
                </div>
                <div class="form-group">
                    <label for="barcode">Code-barres</label>
                    <input type="text" class="form-control" name="products[0][barcode]">
                </div>
                <div class="form-group">
                    <label for="expiry_date">Date d'Expiration</label>
                    <input type="date" class="form-control" name="products[0][expiry_date]" required>
                </div>
                <div class="form-group">
                    <label for="quantity">Quantité</label>
                    <input type="number" class="form-control" name="products[0][quantity]" required>
                </div>
            </div>
        </div>
        <button type="button" class="btn btn-secondary" id="add-product-btn">Ajouter un Produit</button>

        <button type="submit" class="btn btn-success mt-4">Créer la Collecte</button>
    </form>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script>
    $(document).ready(function() {
        let productIndex = 1;

        $('#add-product-btn').click(function() {
            let productGroup = `
                <div class="product-group">
                    <hr>
                    <div class="form-group">
                        <label for="product_name">Nom du Produit</label>
                        <input type="text" class="form-control" name="products[${productIndex}][name]" required>
                    </div>
                    <div class="form-group">
                        <label for="barcode">Code-barres</label>
                        <input type="text" class="form-control" name="products[${productIndex}][barcode]">
                    </div>
                    <div class="form-group">
                        <label for="expiry_date">Date d'Expiration</label>
                        <input type="date" class="form-control" name="products[${productIndex}][expiry_date]" required>
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantité</label>
                        <input type="number" class="form-control" name="products[${productIndex}][quantity]" required>
                    </div>
                </div>`;
            $('#products-container').append(productGroup);
            productIndex++;
        });
    });
</script>
</body>
</html>

<?php
include 'include/footer.php';
$conn->close();
?>
