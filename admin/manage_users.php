<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

$role = isset($_GET['role']) ? $_GET['role'] : 'admin';

$allowedRoles = ['admin', 'merchant', 'volunteer'];
if (!in_array($role, $allowedRoles)) {
    $role = 'admin';
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'id';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

$allowedSorts = ['id', 'name', 'email', 'phone', 'status'];
if (!in_array($sort, $allowedSorts)) {
    $sort = 'id';
}

$order = strtoupper($order) === 'DESC' ? 'DESC' : 'ASC';

$query = "SELECT * FROM users WHERE role = ? AND (name LIKE ? OR email LIKE ?) ORDER BY $sort $order";
$stmt = $conn->prepare($query);
$searchParam = "%$search%";
$stmt->bind_param("sss", $role, $searchParam, $searchParam);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Gérer les Utilisateurs</h1>

    <div class="mb-4">
        <a href="manage_users.php?role=admin&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>&order=<?php echo htmlspecialchars($order, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary <?php echo $role == 'admin' ? 'active' : ''; ?>">Admins</a>
        <a href="manage_users.php?role=merchant&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>&order=<?php echo htmlspecialchars($order, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary <?php echo $role == 'merchant' ? 'active' : ''; ?>">Marchands</a>
        <a href="manage_users.php?role=volunteer&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>&order=<?php echo htmlspecialchars($order, ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-primary <?php echo $role == 'volunteer' ? 'active' : ''; ?>">Bénévoles</a>
        <a href="manage_beneficiaries.php" class="btn btn-primary">Bénéficiaires</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="add_user.php" class="btn btn-success">Ajouter un Utilisateur</a>
        <?php endif; ?>
    </div>

    <div class="mb-4">
        <form method="get" action="manage_users.php">
            <input type="hidden" name="role" value="<?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort, ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="order" value="<?php echo htmlspecialchars($order, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-group">
                <input type="text" class="form-control" name="search" value="<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>" placeholder="Rechercher par nom ou email...">
            </div>
            <button type="submit" class="btn btn-primary">Rechercher</button>
        </form>
    </div>

    <div class="mb-4">
        <a href="manage_users.php?role=<?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=id&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn btn-secondary">ID <?php echo $sort == 'id' ? ($order === 'ASC' ? '▲' : '▼') : ''; ?></a>
        <a href="manage_users.php?role=<?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=name&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn btn-secondary">Nom <?php echo $sort == 'name' ? ($order === 'ASC' ? '▲' : '▼') : ''; ?></a>
        <a href="manage_users.php?role=<?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=email&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn btn-secondary">Email <?php echo $sort == 'email' ? ($order === 'ASC' ? '▲' : '▼') : ''; ?></a>
        <a href="manage_users.php?role=<?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=phone&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn btn-secondary">Téléphone <?php echo $sort == 'phone' ? ($order === 'ASC' ? '▲' : '▼') : ''; ?></a>
        <a href="manage_users.php?role=<?php echo htmlspecialchars($role, ENT_QUOTES, 'UTF-8'); ?>&search=<?php echo htmlspecialchars($search, ENT_QUOTES, 'UTF-8'); ?>&sort=status&order=<?php echo $order === 'ASC' ? 'DESC' : 'ASC'; ?>" class="btn btn-secondary">Statut <?php echo $sort == 'status' ? ($order === 'ASC' ? '▲' : '▼') : ''; ?></a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['name'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($row['phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($row['status']) ?? '', ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-warning btn-sm">Modifier</a>
                            <?php if ($row['role'] != 'admin'): ?>
                                <a href="delete_user.php?id=<?php echo htmlspecialchars($row['id'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" class="btn btn-danger btn-sm" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">Supprimer</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun utilisateur trouvé.</p>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php include('include/footer.php'); ?>
