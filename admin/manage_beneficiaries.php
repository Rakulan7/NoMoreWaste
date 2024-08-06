<?php
session_start();
include 'include/session.php';
include 'include/header.php';
include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

// Récupérer tous les bénéficiaires
$beneficiariesQuery = "SELECT * FROM beneficiaries";
$beneficiariesResult = $conn->query($beneficiariesQuery);

$conn->close();
?>

<div class="container my-5">
    <h1 class="mb-4">Gérer les Bénéficiaires</h1>

    <!-- Affichage des messages de succès et d'erreur -->
    <?php if (isset($_SESSION['success_message'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['success_message']; unset($_SESSION['success_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error_message'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $_SESSION['error_message']; unset($_SESSION['error_message']); ?>
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    <?php endif; ?>

    <!-- Bouton pour ouvrir le modal d'ajout -->
    <button type="button" class="btn btn-primary mb-3" data-toggle="modal" data-target="#addBeneficiaryModal">
        Ajouter un Bénéficiaire
    </button>

    <!-- Table des bénéficiaires -->
    <table class="table table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Ville</th>
                <th>Pays</th>
                <th>Date d'inscription</th>
                <th>Type de service</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($beneficiary = $beneficiariesResult->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($beneficiary['id']); ?></td>
                    <td><?php echo htmlspecialchars($beneficiary['name']); ?></td>
                    <td><?php echo htmlspecialchars($beneficiary['contact_email']); ?></td>
                    <td><?php echo htmlspecialchars($beneficiary['city']); ?></td>
                    <td><?php echo htmlspecialchars($beneficiary['country']); ?></td>
                    <td><?php echo htmlspecialchars(date('d/m/Y', strtotime($beneficiary['registration_date']))); ?></td>
                    <td><?php echo htmlspecialchars(ucfirst($beneficiary['service_type'])); ?></td>
                    <td>
                        <button class="btn btn-info btn-sm detail-btn" data-toggle="modal" data-target="#detailModal" 
                                data-id="<?php echo htmlspecialchars($beneficiary['id']); ?>"
                                data-name="<?php echo htmlspecialchars($beneficiary['name']); ?>"
                                data-contact_person="<?php echo htmlspecialchars($beneficiary['contact_person']); ?>"
                                data-contact_email="<?php echo htmlspecialchars($beneficiary['contact_email']); ?>"
                                data-contact_phone="<?php echo htmlspecialchars($beneficiary['contact_phone']); ?>"
                                data-address="<?php echo htmlspecialchars($beneficiary['address']); ?>"
                                data-city="<?php echo htmlspecialchars($beneficiary['city']); ?>"
                                data-country="<?php echo htmlspecialchars($beneficiary['country']); ?>"
                                data-registration_date="<?php echo htmlspecialchars($beneficiary['registration_date']); ?>"
                                data-service_type="<?php echo htmlspecialchars($beneficiary['service_type']); ?>"
                                data-notes="<?php echo htmlspecialchars($beneficiary['notes']); ?>"
                        >
                            Détails
                        </button>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<!-- Modal pour ajouter un bénéficiaire -->
<div class="modal fade" id="addBeneficiaryModal" tabindex="-1" role="dialog" aria-labelledby="addBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="add_beneficiary.php" method="post">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBeneficiaryModalLabel">Ajouter un Bénéficiaire</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Nom</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_person">Personne de Contact</label>
                        <input type="text" class="form-control" id="contact_person" name="contact_person" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_email">Email de Contact</label>
                        <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_phone">Téléphone de Contact</label>
                        <input type="text" class="form-control" id="contact_phone" name="contact_phone">
                    </div>
                    <div class="form-group">
                        <label for="address">Adresse</label>
                        <input type="text" class="form-control" id="address" name="address">
                    </div>
                    <div class="form-group">
                        <label for="city">Ville</label>
                        <input type="text" class="form-control" id="city" name="city">
                    </div>
                    <div class="form-group">
                        <label for="country">Pays</label>
                        <input type="text" class="form-control" id="country" name="country">
                    </div>
                    <div class="form-group">
                        <label for="registration_date">Date d'inscription</label>
                        <input type="date" class="form-control" id="registration_date" name="registration_date" required>
                    </div>
                    <div class="form-group">
                        <label for="service_type">Type de Service</label>
                        <select class="form-control" id="service_type" name="service_type" required>
                            <option value="food">Nourriture</option>
                            <option value="shelter">Hébergement</option>
                            <option value="clothing">Vêtements</option>
                            <option value="other">Autre</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="notes">Notes</label>
                        <textarea class="form-control" id="notes" name="notes"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal pour afficher les détails du bénéficiaire -->
<div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailModalLabel">Détails du Bénéficiaire</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="detailName">Nom</label>
                    <input type="text" class="form-control" id="detailName" readonly>
                </div>
                <div class="form-group">
                    <label for="detailContactPerson">Personne de Contact</label>
                    <input type="text" class="form-control" id="detailContactPerson" readonly>
                </div>
                <div class="form-group">
                    <label for="detailContactEmail">Email de Contact</label>
                    <input type="email" class="form-control" id="detailContactEmail" readonly>
                </div>
                <div class="form-group">
                    <label for="detailContactPhone">Téléphone de Contact</label>
                    <input type="text" class="form-control" id="detailContactPhone" readonly>
                </div>
                <div class="form-group">
                    <label for="detailAddress">Adresse</label>
                    <input type="text" class="form-control" id="detailAddress" readonly>
                </div>
                <div class="form-group">
                    <label for="detailCity">Ville</label>
                    <input type="text" class="form-control" id="detailCity" readonly>
                </div>
                <div class="form-group">
                    <label for="detailCountry">Pays</label>
                    <input type="text" class="form-control" id="detailCountry" readonly>
                </div>
                <div class="form-group">
                    <label for="detailRegistrationDate">Date d'inscription</label>
                    <input type="date" class="form-control" id="detailRegistrationDate" readonly>
                </div>
                <div class="form-group">
                    <label for="detailServiceType">Type de Service</label>
                    <input type="text" class="form-control" id="detailServiceType" readonly>
                </div>
                <div class="form-group">
                    <label for="detailNotes">Notes</label>
                    <textarea class="form-control" id="detailNotes" readonly></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" id="editLink" class="btn btn-info">Modifier</a>
                <a href="#" id="deleteLink" class="btn btn-danger delete-btn">Supprimer</a>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fermer</button>
            </div>
        </div>
    </div>
</div>

<?php include 'include/footer.php'; ?>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    $(document).ready(function() {
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            var link = $(this).attr('href');
            if (confirm('Êtes-vous sûr de vouloir supprimer ce bénéficiaire ?')) {
                window.location.href = link;
            }
        });

        $('.detail-btn').on('click', function() {
            var modal = $('#detailModal');
            modal.find('#detailName').val($(this).data('name'));
            modal.find('#detailContactPerson').val($(this).data('contact_person'));
            modal.find('#detailContactEmail').val($(this).data('contact_email'));
            modal.find('#detailContactPhone').val($(this).data('contact_phone'));
            modal.find('#detailAddress').val($(this).data('address'));
            modal.find('#detailCity').val($(this).data('city'));
            modal.find('#detailCountry').val($(this).data('country'));
            modal.find('#detailRegistrationDate').val($(this).data('registration_date'));
            modal.find('#detailServiceType').val($(this).data('service_type'));
            modal.find('#detailNotes').val($(this).data('notes'));
            modal.find('#editLink').attr('href', 'edit_beneficiary.php?id=' + $(this).data('id'));
            modal.find('#deleteLink').attr('href', 'delete_beneficiary.php?id=' + $(this).data('id'));
        });
    });
</script>

</body>
</html>
