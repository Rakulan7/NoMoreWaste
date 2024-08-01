<?php
session_start();
$page_title = "Contact - ";
include('include/header.php'); 
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php
            if (isset($_SESSION['contact_status']) && isset($_SESSION['contact_message'])) {
                $status = $_SESSION['contact_status'];
                $message = $_SESSION['contact_message'];

                if ($status == 'success') {
                    echo '<div class="alert alert-success">' . $message . '</div>';
                } else if ($status == 'error') {
                    echo '<div class="alert alert-danger">' . $message . '</div>';
                }

                unset($_SESSION['contact_status']);
                unset($_SESSION['contact_message']);
            }
            ?>
            <div class="card shadow-sm">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Contactez-nous</h2>
                    <form action="process_contact.php" method="post">
                        <div class="form-group">
                            <label for="name">Nom</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">Numéro de téléphone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="form-group">
                            <label for="message">Message</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-success">Envoyer</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include('include/footer.php'); ?>
