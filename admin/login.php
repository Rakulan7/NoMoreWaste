<?php
session_start();

include 'include/database.php';

$database = new Database();
$conn = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            if ($user['role'] === 'admin') {
                $_SESSION['id_admin'] = $user['id'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php");
                exit();
            } else {
                $error = "Vous devez être administrateur pour vous connecter.";
            }
        } else {
            sleep(5);
            $error = "Mot de passe incorrect.";
        }
    } else {
        sleep(5);
        $error = "Adresse e-mail non trouvée.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - NoMoreWaste</title>
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

        .login-container {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .login-container img {
            width: 120px;
            margin-bottom: 1rem;
        }

        .login-container h2 {
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            color: #343a40;
        }

        .login-container .form-group {
            margin-bottom: 1rem;
        }

        .login-container .btn {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
        }

        .alert {
            margin-top: 1rem;
        }

        .login-container p {
            margin-top: 1rem;
        }

        .login-container a {
            color: #007bff;
            text-decoration: none;
        }

        .login-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="/img/banner/logo_transparent.png" alt="NoMoreWaste Logo">
        <h2>Connexion</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-success">Se connecter</button>
        </form>
    </div>

</body>
</html>
