<?php
session_start();

include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['step']) && $_POST['step'] == '1') {
        $_SESSION['signup_data_volunteer'] = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'],
            'address' => $_POST['address'],
            'city' => $_POST['city'],
            'country' => $_POST['country']
        ];

        header("Location: signup.php?step=2");
        exit();
    } elseif (isset($_POST['step']) && $_POST['step'] == '2') {
        $name = $_SESSION['signup_data_volunteer']['name'];
        $email = $_SESSION['signup_data_volunteer']['email'];
        $phone = $_SESSION['signup_data_volunteer']['phone'];
        $address = $_SESSION['signup_data_volunteer']['address'];
        $city = $_SESSION['signup_data_volunteer']['city'];
        $country = $_SESSION['signup_data_volunteer']['country'];

        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $error = "Cette adresse e-mail est déjà utilisée.";
        } elseif ($password !== $confirm_password) {
            $error = "Les mots de passe ne correspondent pas.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $role = 'volunteer';
            $join_date = date('Y-m-d');
            $status = 'pending';

            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone, role, join_date, address, city, country, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssssss", $name, $email, $hashed_password, $phone, $role, $join_date, $address, $city, $country, $status);

            if ($stmt->execute()) {
                $_SESSION['success'] = "Votre inscription a été réussie. Veuillez attendre l'approbation de votre compte.";
                header("Location: login.php");
                exit();
            } else {
                $error = "Une erreur s'est produite lors de l'inscription. Veuillez réessayer.";
            }

            $stmt->close();
            unset($_SESSION['signup_data_volunteer']);
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - Bénévole NoMoreWaste</title>
    <link rel="icon" type="image/x-icon" href="/img/banner/favicon.ico">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f4f7f6;
            margin: 0;
        }

        .signup-container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .signup-container img {
            width: 120px;
            margin-bottom: 1rem;
        }

        .signup-container h2 {
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            color: #343a40;
        }

        .signup-container .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .signup-container .btn {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
        }

        .alert {
            margin-top: 1rem;
        }

        .signup-container p {
            margin-top: 1rem;
        }

        .signup-container a {
            color: #007bff;
            text-decoration: none;
        }

        .signup-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="signup-container">
        <img src="/img/banner/logo_transparent.png" alt="NoMoreWaste Logo">
        <h2>Inscription bénévole</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if (!isset($_GET['step']) || $_GET['step'] == '1'): ?>
            <form action="signup.php" method="POST">
                <input type="hidden" name="step" value="1">
                <div class="form-group">
                    <label for="name">Nom complet</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Adresse e-mail</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Numéro de téléphone</label>
                    <input type="text" class="form-control" id="phone" name="phone">
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
                <button type="submit" class="btn btn-success">Continuer</button>
            </form>
        <?php elseif ($_GET['step'] == '2'): ?>
            <form action="signup.php" method="POST">
                <input type="hidden" name="step" value="2">
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn btn-success">S'inscrire</button>
            </form>
        <?php endif; ?>

        <p class="mt-3">Déjà inscrit ? <a href="login.php">Se connecter</a></p>
    </div>

    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            var password = document.getElementById('password') ? document.getElementById('password').value : '';
            var confirm_password = document.getElementById('confirm_password') ? document.getElementById('confirm_password').value : '';

            if (password && confirm_password && password !== confirm_password) {
                e.preventDefault();
                alert("Les mots de passe ne correspondent pas.");
            }
        });
    </script>

</body>
</html>
