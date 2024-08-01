<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoMoreWaste Admin</title>
    <link rel="icon" type="image/x-icon" href="/img/banner/favicon.ico">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px; /* Pour l'espacement sous la navbar */
        }

        .navbar {
            background-color: #28a745; /* Vert clair pour la navbar */
        }

        .navbar-brand, .nav-link {
            color: #ffffff; /* Texte blanc pour contraster avec la couleur de fond */
        }

        .navbar-brand:hover, .nav-link:hover {
            color: #e2e6ea; /* Couleur plus claire pour le survol */
        }

        .header-logo {
            width: 100px; /* Ajustez la taille du logo si nécessaire */
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="index.php">NoMoreWaste Admin</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="index.php">Dashboard</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_users.php">Utilisateurs</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="manage_requests.php">Demandes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Déconnexion</a>
                </li>
            </ul>
        </div>
    </nav>
