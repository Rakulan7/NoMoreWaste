<?php
session_start();
include 'include/session.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

// Traitement des demandes POST pour ajouter, modifier ou supprimer un lieu de stockage
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_storage_location'])) {
        // Ajouter un lieu de stockage
        $name = $_POST['name'] ?? '';
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $country = $_POST['country'] ?? '';
        $contact_phone = $_POST['contact_phone'] ?? '';
        $contact_email = $_POST['contact_email'] ?? '';

        if ($name && $address && $city && $country) {
            $query = "INSERT INTO storage_locations (name, address, city, country, contact_phone, contact_email) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssss", $name, $address, $city, $country, $contact_phone, $contact_email);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Lieu de stockage ajouté avec succès.';
            } else {
                $_SESSION['error_message'] = 'Échec de l\'ajout du lieu de stockage.';
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'Tous les champs obligatoires doivent être remplis.';
        }
    } elseif (isset($_POST['update_storage_location'])) {
        // Modifier un lieu de stockage
        $id = $_POST['id'] ?? 0;
        $name = $_POST['name'] ?? '';
        $address = $_POST['address'] ?? '';
        $city = $_POST['city'] ?? '';
        $country = $_POST['country'] ?? '';
        $contact_phone = $_POST['contact_phone'] ?? '';
        $contact_email = $_POST['contact_email'] ?? '';

        if ($id && $name && $address && $city && $country) {
            $query = "UPDATE storage_locations SET name = ?, address = ?, city = ?, country = ?, contact_phone = ?, contact_email = ? WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssssssi", $name, $address, $city, $country, $contact_phone, $contact_email, $id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Lieu de stockage mis à jour avec succès.';
            } else {
                $_SESSION['error_message'] = 'Échec de la mise à jour du lieu de stockage.';
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'Tous les champs obligatoires doivent être remplis.';
        }
    } elseif (isset($_POST['delete_storage_location'])) {
        // Supprimer un lieu de stockage
        $id = $_POST['id'] ?? 0;

        if ($id) {
            $query = "DELETE FROM storage_locations WHERE id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = 'Lieu de stockage supprimé avec succès.';
            } else {
                $_SESSION['error_message'] = 'Échec de la suppression du lieu de stockage.';
            }
            $stmt->close();
        } else {
            $_SESSION['error_message'] = 'ID de lieu de stockage invalide.';
        }
    }

    header('Location: manage_storage_locations.php');
    exit();
}

// Récupération des lieux de stockage existants
$query = "SELECT * FROM storage_locations";
$storage_locations_result = $conn->query($query);
?>

<?php include('include/header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Gestion des Lieux de Stockage</h1>

    <!-- Affichage du message d'erreur ou de succès -->
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

    <!-- Bouton pour ouvrir le modal d'ajout -->
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addStorageModal">
        Ajouter un Nouveau Lieu de Stockage
    </button>

    <!-- Liste des lieux de stockage existants avec options pour modifier et supprimer -->
    <h2 class="mt-5">Lieux de Stockage Existants</h2>
    <?php if ($storage_locations_result->num_rows > 0): ?>
        <table class="table table-bordered mt-3">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Adresse</th>
                    <th>Ville</th>
                    <th>Pays</th>
                    <th>Téléphone de Contact</th>
                    <th>Email de Contact</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($location = $storage_locations_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($location['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($location['name'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($location['address'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($location['city'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($location['country'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($location['contact_phone'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td><?php echo htmlspecialchars($location['contact_email'], ENT_QUOTES, 'UTF-8'); ?></td>
                        <td>
                            <!-- Boutons Modifier et Supprimer -->
                            <button type="button" class="btn btn-warning btn-sm" data-toggle="modal" data-target="#editModal<?php echo $location['id']; ?>">Modifier</button>
                            <form action="manage_storage_locations.php" method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($location['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                <button type="submit" name="delete_storage_location" class="btn btn-danger btn-sm">Supprimer</button>
                            </form>

                            <!-- Modal Modifier -->
                            <div class="modal fade" id="editModal<?php echo $location['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="editModalLabel">Modifier Lieu de Stockage</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                <span aria-hidden="true">&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="manage_storage_locations.php" method="post">
                                                <input type="hidden" name="update_storage_location">
                                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($location['id'], ENT_QUOTES, 'UTF-8'); ?>">
                                                <div class="form-group">
                                                    <label for="edit_name<?php echo $location['id']; ?>">Nom</label>
                                                    <input type="text" class="form-control" id="edit_name<?php echo $location['id']; ?>" name="name" value="<?php echo htmlspecialchars($location['name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_address<?php echo $location['id']; ?>">Adresse</label>
                                                    <input type="text" class="form-control" id="edit_address<?php echo $location['id']; ?>" name="address" value="<?php echo htmlspecialchars($location['address'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_city<?php echo $location['id']; ?>">Ville</label>
                                                    <input type="text" class="form-control" id="edit_city<?php echo $location['id']; ?>" name="city" value="<?php echo htmlspecialchars($location['city'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_country<?php echo $location['id']; ?>">Pays</label>
                                                    <input type="text" class="form-control" id="edit_country<?php echo $location['id']; ?>" name="country" value="<?php echo htmlspecialchars($location['country'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_contact_phone<?php echo $location['id']; ?>">Téléphone de Contact</label>
                                                    <input type="text" class="form-control" id="edit_contact_phone<?php echo $location['id']; ?>" name="contact_phone" value="<?php echo htmlspecialchars($location['contact_phone'], ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <div class="form-group">
                                                    <label for="edit_contact_email<?php echo $location['id']; ?>">Email de Contact</label>
                                                    <input type="email" class="form-control" id="edit_contact_email<?php echo $location['id']; ?>" name="contact_email" value="<?php echo htmlspecialchars($location['contact_email'], ENT_QUOTES, 'UTF-8'); ?>">
                                                </div>
                                                <button type="submit" class="btn btn-primary">Mettre à Jour</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Aucun lieu de stockage trouvé.</p>
    <?php endif; ?>

    <!-- Modal Ajouter -->
    <div class="modal fade" id="addStorageModal" tabindex="-1" role="dialog" aria-labelledby="addStorageModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addStorageModalLabel">Ajouter Nouveau Lieu de Stockage</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="manage_storage_locations.php" method="post">
                        <input type="hidden" name="add_storage_location">
                        <div class="form-group">
                            <label for="name">Nom</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="address">Adresse</label>
                            <input type="text" class="form-control" id="address" name="address" required>
                        </div>
                        <div class="form-group">
                            <label for="city">Ville</label>
                            <input type="text" class="form-control" id="city" name="city" required>
                        </div>
                        <div class="form-group">
                            <label for="country">Pays</label>
                            <input type="text" class="form-control" id="country" name="country" required>
                        </div>
                        <div class="form-group">
                            <label for="contact_phone">Téléphone de Contact</label>
                            <input type="text" class="form-control" id="contact_phone" name="contact_phone">
                        </div>
                        <div class="form-group">
                            <label for="contact_email">Email de Contact</label>
                            <input type="email" class="form-control" id="contact_email" name="contact_email">
                        </div>
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<?php 
include('include/footer.php');
$conn->close();
?>
