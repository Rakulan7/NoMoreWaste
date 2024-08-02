<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : ''; ?>NoMoreWaste Admin</title>
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
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="/">NoMoreWaste Admin</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Dashboard</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="usersDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Utilisateurs
                    </a>
                    <div class="dropdown-menu" aria-labelledby="usersDropdown">
                        <a class="dropdown-item" href="manage_users.php?role=admin">Admins</a>
                        <a class="dropdown-item" href="manage_users.php?role=merchant">Marchants</a>
                        <a class="dropdown-item" href="manage_users.php?role=volunteer">Bénévoles</a>
                        <a class="dropdown-item" href="manage_users.php?role=beneficiary">Bénéficiaires</a>
                    </div>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="activitiesDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Activités
                    </a>
                    <div class="dropdown-menu" aria-labelledby="activitiesDropdown">
                        <a class="dropdown-item" href="manage_collections.php">Gérer les Collectes</a>
                        <a class="dropdown-item" href="manage_deliveries.php">Gérer les Distributions</a>
                        <a class="dropdown-item" href="manage_storage_locations.php">Gérer les Entrepôts</a>
                    </div>
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>