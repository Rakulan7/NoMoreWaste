<?php
$page_title = 'Voir les PDF - ';
include('include/header.php');

$directory = '../pdf/';

$files = array_diff(scandir($directory), array('.', '..'));

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
    <ul class="list-group">
        <?php foreach ($pdf_files as $file): ?>
            <li class="list-group-item">
                <a href="<?php echo $directory . $file; ?>" target="_blank"><?php echo $file; ?></a>
                <span class="badge badge-secondary float-right"><?php echo date('d-m-Y H:i:s', filemtime($directory . $file)); ?></span>
                <span class="badge badge-info float-right mr-2">
                    <?php
                        if (strpos($file, 'collection_') === 0) {
                            echo 'Collecte';
                        } elseif (strpos($file, 'delivery_') === 0) {
                            echo 'Livraison';
                        } else {
                            echo 'Autre';
                        }
                    ?>
                </span>
            </li>
        <?php endforeach; ?>
    </ul>
</div>

<script>
    function filterPdfs() {
        const filterType = document.getElementById('filter_type').value;
        const sortBy = document.getElementById('sort_by').value;
        const sortOrder = document.getElementById('sort_order').value;
        window.location.href = `view_pdfs.php?filter_type=${filterType}&sort_by=${sortBy}&sort_order=${sortOrder}`;
    }
</script>

<?php include('include/footer.php'); ?>
