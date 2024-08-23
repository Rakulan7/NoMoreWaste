<?php

include 'include/database.php';
include 'include/header.php';
include 'include/session.php';

$database = new Database();
$conn = $database->getConnection();

$statuses = ['pending', 'assigned', 'completed'];

$collections = [];
foreach ($statuses as $status) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS count FROM collection_requests WHERE merchant_id = ? AND status = ?");
    $stmt->bind_param("is", $_SESSION['id_merchant'], $status);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $collections[$status] = $data['count'];
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord - NMW Commerce</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card-title {
            font-size: 1.25rem;
        }
        .card-body {
            font-size: 1.1rem;
        }
    </style>
</head>
<body>
    <?php include 'include/header.php'; ?>

    <div class="container my-5">
        <h1 class="mb-4">Tableau de Bord</h1>
        
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title">Collectes Créées</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($collections['pending']); ?> collectes</p>
                        <a href="merchant_collections.php?status=created" class="btn btn-primary">Voir les Collectes</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-warning text-white">
                        <h5 class="card-title">Collectes en Cours</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($collections['assigned']); ?> collectes</p>
                        <a href="merchant_collections.php?status=in-progress" class="btn btn-warning">Voir les Collectes</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-header bg-success text-white">
                        <h5 class="card-title">Collectes Complètes</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text"><?php echo htmlspecialchars($collections['completed']); ?> collectes</p>
                        <a href="merchant_collections.php?status=completed" class="btn btn-success">Voir les Collectes</a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php include 'include/footer.php'; ?>
</body>
</html>
