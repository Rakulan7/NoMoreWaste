<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : ''; ?>NoMoreWaste Bénévole</title>
    <link rel="icon" type="image/x-icon" href="/img/banner/favicon.ico">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
        }

        .navbar {
            background-color: #28a745;
        }

        .navbar-brand, .nav-link {
            color: #ffffff;
        }

        .navbar-brand:hover, .nav-link:hover {
            color: #e2e6ea;
        }

        .header-logo {
            width: 100px;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <a class="navbar-brand" href="/">NoMoreWaste Bénévole</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <a class="nav-link" href="/">Tableau de bord</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="collectionsDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Collectes
                    </a>
                    <div class="dropdown-menu" aria-labelledby="collectionsDropdown">
                        <a class="dropdown-item" href="volunteer_collections.php">À aller chercher</a>
                        <a class="dropdown-item" href="volunteer_deliveries.php">À distribuer</a>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="volunteer_services.php">Services</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="volunteer_profile.php">Profil</a>
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
</body>
</html>
