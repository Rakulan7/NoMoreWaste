<!-- header.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : ''; ?>NoMoreWaste</title>
    <link rel="icon" type="image/x-icon" href="/img/banner/favicon.ico"> <!-- Ajoutez cette ligne -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
        }

        .hero {
            background: url('/img/banner/banner.jpg') center center no-repeat;
            background-size: cover;
            color: black;
            height: 70vh;
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .highlight {
            background-color: rgba(211, 211, 211, 0.7);
            padding: 0 5px;
        }

        .hero h1 {
            font-size: 3.5rem;
            font-weight: bold;
        }

        .services {
            padding: 4rem 0;
        }

        .testimonial {
            background: #f8f9fa;
            padding: 4rem 0;
        }

        .footer {
            background-color: #d4edda; /* Light green background */
            color: #343a40; /* Dark text color for contrast */
        }

        .footer-logo {
            width: 120px; /* Adjust the size of the logo */
            margin-bottom: 1rem;
        }

        .footer h5 {
            font-size: 1.25rem;
            margin-bottom: 1rem;
        }

        .footer p {
            margin-bottom: 1rem;
        }

        .footer a {
            color: #343a40;
            text-decoration: none;
        }

        .footer a:hover {
            color: #0056b3; /* Darker shade on hover */
            text-decoration: underline;
        }

        .footer .text-uppercase {
            text-transform: uppercase;
        }

        .footer .list-unstyled {
            padding-left: 0;
        }

        .footer .list-unstyled li {
            margin-bottom: 0.5rem;
        }

        .footer .bg-success {
            background-color: #28a745 !important; /* Green background for footer bottom section */
        }

        .navbar {
            background-color: #28a745; /* Light green background for the navbar */
        }

        .navbar-brand, .nav-link {
            color: #343a40; /* Dark text color for contrast */
        }

        .navbar-brand:hover, .nav-link:hover {
            color: #0056b3; /* Darker shade on hover */
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#">NoMoreWaste</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarResponsive">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="#">Accueil</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">À propos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
