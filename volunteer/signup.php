<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription - NoMoreWaste</title>
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

        .register-container {
            max-width: 500px;
            width: 100%;
            padding: 2rem;
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            text-align: center;
        }

        .register-container img {
            width: 120px;
            margin-bottom: 1rem;
        }

        .register-container h2 {
            margin-bottom: 1.5rem;
            font-size: 1.75rem;
            color: #343a40;
        }

        .register-container .form-group {
            margin-bottom: 1rem;
        }

        .register-container .btn {
            width: 100%;
            padding: 0.75rem;
            font-size: 1rem;
        }

        .alert {
            margin-top: 1rem;
        }

        .register-container p {
            margin-top: 1rem;
        }

        .register-container a {
            color: #007bff;
            text-decoration: none;
        }

        .register-container a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="register-container">
        <img src="/img/banner/logo_transparent.png" alt="NoMoreWaste Logo">
        <h2>Inscription</h2>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="email">Adresse e-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
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

        <p class="mt-3">Déjà un compte ? <a href="login.php">Se connecter</a></p>
    </div>

</body>
</html>
