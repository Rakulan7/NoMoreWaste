<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $requestId = $_POST['request_id'];
    $adminId = $_SESSION['id_admin'];
    $response = $_POST['response'];
    $status = $_POST['status'];
    $processedAt = date('Y-m-d H:i:s');

    $stmt = $conn->prepare("UPDATE contact_requests SET status = ?, response = ?, admin_id = ?, processed_at = ? WHERE id = ?");
    $stmt->bind_param("ssisi", $status, $response, $adminId, $processedAt, $requestId);
    $stmt->execute();
    $stmt->close();
}

$sortColumn = 'submitted_at';
$sortOrder = 'DESC';

if (isset($_GET['sort']) && isset($_GET['order'])) {
    $sortColumn = $_GET['sort'];
    $sortOrder = $_GET['order'] === 'ASC' ? 'ASC' : 'DESC';
}

$allowedSortColumns = ['id', 'name', 'email', 'phone', 'message', 'status', 'admin_name', 'response', 'submitted_at'];
if (!in_array($sortColumn, $allowedSortColumns)) {
    $sortColumn = 'submitted_at';
}

$query = "SELECT cr.*, u.name AS admin_name 
          FROM contact_requests cr 
          LEFT JOIN users u ON cr.admin_id = u.id 
          WHERE cr.status = 'pending' OR cr.status = 'processed' 
          ORDER BY cr.$sortColumn $sortOrder";

$result = $conn->query($query);

$conn->close();
?>

<?php include('include/header.php') ?>

<div class="container my-5">
    <h1 class="mb-4">Gérer les Demandes de Contact</h1>

    <form method="get" action="manage_requests.php" class="mb-4">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="sort">Trier par</label>
                <select class="form-control" id="sort" name="sort">
                    <option value="submitted_at" <?php echo $sortColumn == 'submitted_at' ? 'selected' : ''; ?>>Date de Soumission</option>
                    <option value="name" <?php echo $sortColumn == 'name' ? 'selected' : ''; ?>>Nom</option>
                    <option value="email" <?php echo $sortColumn == 'email' ? 'selected' : ''; ?>>Email</option>
                    <option value="status" <?php echo $sortColumn == 'status' ? 'selected' : ''; ?>>Statut</option>
                </select>
            </div>
            <div class="form-group col-md-6">
                <label for="order">Ordre</label>
                <select class="form-control" id="order" name="order">
                    <option value="ASC" <?php echo $sortOrder == 'ASC' ? 'selected' : ''; ?>>Ascendant</option>
                    <option value="DESC" <?php echo $sortOrder == 'DESC' ? 'selected' : ''; ?>>Descendant</option>
                </select>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">Trier</button>
    </form>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th><a href="?sort=id&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">ID</a></th>
                    <th><a href="?sort=name&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Nom</a></th>
                    <th><a href="?sort=email&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Email</a></th>
                    <th><a href="?sort=phone&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Téléphone</a></th>
                    <th><a href="?sort=message&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Message</a></th>
                    <th><a href="?sort=status&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Statut</a></th>
                    <th><a href="?sort=admin_name&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Admin</a></th>
                    <th><a href="?sort=response&order=<?php echo $sortOrder === 'ASC' ? 'DESC' : 'ASC'; ?>">Réponse</a></th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['message'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['status'] ?? '')); ?></td>
                        <td><?php echo htmlspecialchars($row['admin_name'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row['response'] ?? ''); ?></td>
                        <td>
                            <?php if ($row['status'] == 'pending'): ?>
                                <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#processModal<?php echo $row['id']; ?>">Traiter</button>
                            <?php else: ?>
                                <button type="button" class="btn btn-success btn-sm" disabled>Traité</button>
                            <?php endif; ?>
                        </td>
                    </tr>

                    <div class="modal fade" id="processModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="processModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="processModalLabel<?php echo $row['id']; ?>">Traiter la Demande #<?php echo $row['id']; ?></h5>
                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                                <form method="post" action="manage_requests.php">
                                    <div class="modal-body">
                                        <input type="hidden" name="request_id" value="<?php echo htmlspecialchars($row['id'] ?? ''); ?>">
                                        <div class="form-group">
                                            <label for="response">Réponse</label>
                                            <textarea class="form-control" id="response" name="response" rows="4"></textarea>
                                        </div>
                                        <div class="form-group">
                                            <label for="status">Statut</label>
                                            <select class="form-control" id="status" name="status">
                                                <option value="processed">Traité</option>
                                                <option value="pending">En attente</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                                        <button type="submit" class="btn btn-primary">Soumettre</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucune demande de contact en attente.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php') ?>