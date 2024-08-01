<!-- header.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : ''; ?>NoMoreWaste</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            padding-top: 56px;
        }

        .hero {
            background: url('../img/banner.jpg') center center no-repeat;
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
            background-color: #1c1c1c;
            color: white;
            padding: 2rem 0;
        }

        .footer .text-uppercase {
            text-transform: uppercase;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .footer p {
            margin-bottom: 1rem;
            color: #bbb;
        }

        .footer a {
            color: #ddd;
            text-decoration: none;
        }

        .footer a:hover {
            color: #fff;
            text-decoration: none;
        }

        .footer ul {
            padding-left: 0;
            list-style: none;
        }

        .footer ul li {
            margin-bottom: 0.5rem;
        }

        .footer .bg-secondary {
            background-color: #111 !important;
        }

        .footer .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .footer-logo {
            width: 100px; /* Adjust the size as needed */
            margin-bottom: 1rem;
        }

        @media (max-width: 768px) {
            .footer .container {
                flex-direction: column;
                text-align: center;
            }
            .footer .row {
                margin-bottom: 2rem;
            }
        }
    </style>


</head>
<body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
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
