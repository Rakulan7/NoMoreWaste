<?php
$page_title = 'Voir les PDF - ';
include('include/header.php');
include('include/database.php'); // Inclure le fichier contenant la classe Database

// Instancier la classe Database et obtenir la connexion
$database = new Database();
$conn = $database->getConnection();

$directory = '../pdf/';

// Récupération de la liste des fichiers PDF
$files = array_diff(scandir($directory), array('.', '..'));

// Filtrer uniquement les fichiers PDF
$pdf_files = array_filter($files, function ($file) use ($directory) {
    return is_file($directory . $file) && pathinfo($file, PATHINFO_EXTENSION) === 'pdf';
});

$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'name';
$sort_order = isset($_GET['sort_order']) ? $_GET['sort_order'] : 'asc';
$filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : 'all';

if ($filter_type !== 'all') {
    $pdf_files = array_filter($pdf_files, function ($file) use ($filter_type) {
        if ($filter_type === 'collecte') {
            return strpos($file, 'collection_') === 0;
        } elseif ($filter_type === 'livraison') {
            return strpos($file, 'delivery_') === 0;
        }
        return true;
    });
}

usort($pdf_files, function ($a, $b) use ($sort_by, $sort_order, $directory) {
    if ($sort_by === 'name') {
        return $sort_order === 'asc' ? strcmp($a, $b) : strcmp($b, $a);
    } elseif ($sort_by === 'date') {
        $time_a = filemtime($directory . $a);
        $time_b = filemtime($directory . $b);
        return $sort_order === 'asc' ? $time_a - $time_b : $time_b - $time_a;
    } elseif ($sort_by === 'type') {
        $type_a = strpos($a, 'collection_') === 0 ? 'collecte' : (strpos($a, 'delivery_') === 0 ? 'livraison' : 'autre');
        $type_b = strpos($b, 'collection_') === 0 ? 'collecte' : (strpos($b, 'delivery_') === 0 ? 'livraison' : 'autre');
        return $sort_order === 'asc' ? strcmp($type_a, $type_b) : strcmp($type_b, $type_a);
    }
    return 0;
});

// Fonction pour récupérer les détails d'une collecte ou d'une livraison
function getDetails($file, $conn) {
    if (strpos($file, 'collection_') === 0) {
        $id = intval(str_replace(['collection_', '.pdf'], '', $file));
        $query = "SELECT c.id, u.name AS volunteer_name, COUNT(p.id) AS product_count
                  FROM collection_requests c
                  LEFT JOIN users u ON c.merchant_id = u.id
                  LEFT JOIN products p ON p.collection_request_id = c.id
                  WHERE c.id = $id
                  GROUP BY c.id";
    } elseif (strpos($file, 'delivery_') === 0) {
        $id = intval(str_replace(['delivery_', '.pdf'], '', $file));
        $query = "SELECT d.id, u.name AS volunteer_name, COUNT(p.id) AS product_count
                  FROM deliveries d
                  LEFT JOIN users u ON d.volunteer_id = u.id
                  LEFT JOIN products p ON p.collection_request_id = d.collection_request_id
                  WHERE d.id = $id
                  GROUP BY d.id";
    } else {
        return null;
    }

    $result = $conn->query($query);

    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}
?>

<div class="container mt-5">
    <h1>Liste des fichiers PDF</h1>
    <div class="mb-3">
        <label for="filter_type">Filtrer par type :</label>
        <select id="filter_type" name="filter_type" class="form-control" onchange="filterPdfs()">
            <option value="all" <?php echo $filter_type === 'all' ? 'selected' : ''; ?>>Tous</option>
            <option value="collecte" <?php echo $filter_type === 'collecte' ? 'selected' : ''; ?>>Collecte</option>
            <option value="livraison" <?php echo $filter_type === 'livraison' ? 'selected' : ''; ?>>Livraison</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="sort_by">Trier par :</label>
        <select id="sort_by" name="sort_by" class="form-control" onchange="filterPdfs()">
            <option value="name" <?php echo $sort_by === 'name' ? 'selected' : ''; ?>>Nom</option>
            <option value="date" <?php echo $sort_by === 'date' ? 'selected' : ''; ?>>Date</option>
            <option value="type" <?php echo $sort_by === 'type' ? 'selected' : ''; ?>>Type</option>
        </select>
    </div>
    <div class="mb-3">
        <label for="sort_order">Ordre de tri :</label>
        <select id="sort_order" name="sort_order" class="form-control" onchange="filterPdfs()">
            <option value="asc" <?php echo $sort_order === 'asc' ? 'selected' : ''; ?>>Croissant</option>
            <option value="desc" <?php echo $sort_order === 'desc' ? 'selected' : ''; ?>>Décroissant</option>
        </select>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Nom du fichier</th>
                <th>Date de création</th>
                <th>Type</th>
                <th>Nombre de produits</th>
                <th>Bénévole responsable</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($pdf_files as $file): ?>
                <?php
                $details = getDetails($file, $conn);
                ?>
                <tr>
                    <td><a href="<?php echo $directory . $file; ?>" target="_blank"><?php echo $file; ?></a></td>
                    <td><?php echo date('d-m-Y H:i:s', filemtime($directory . $file)); ?></td>
                    <td>
                        <?php
                            if (strpos($file, 'collection_') === 0) {
                                echo 'Collecte';
                            } elseif (strpos($file, 'delivery_') === 0) {
                                echo 'Livraison';
                            } else {
                                echo 'Autre';
                            }
                        ?>
                    </td>
                    <td><?php echo $details ? $details['product_count'] : 'N/A'; ?></td>
                    <td><?php echo $details ? $details['volunteer_name'] : 'N/A'; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
    function filterPdfs() {
        const filterType = document.getElementById('filter_type').value;
        const sortBy = document.getElementById('sort_by').value;
        const sortOrder = document.getElementById('sort_order').value;
        window.location.href = `view_pdfs.php?filter_type=${filterType}&sort_by=${sortBy}&sort_order=${sortOrder}`;
    }
</script>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php'); ?>

<?php
$conn->close(); // Fermer la connexion
?>
