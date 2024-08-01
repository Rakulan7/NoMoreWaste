<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion admin - NoMoreWaste</title>
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
        <h2>Connexion admin</h2>

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
