<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NoMoreWaste</title>
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
            background: #343a40;
            color: white;
            padding: 2rem 0;
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
                        <a class="nav-link" href="#">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Contact</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="hero">
        <div class="container">
            <h1 class="highlight">NoMoreWaste</h1>
            <p class="highlight">Combating waste one step at a time</p>
            <a href="#" class="btn btn-primary btn-lg">Join Us</a>
        </div>
    </header>

    <!-- Services Section -->
    <section class="services text-center">
        <div class="container">
            <h2 class="mb-4">Our Services</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <img class="card-img-top" src="https://example.com/service1.jpg" alt="">
                        <div class="card-body">
                            <h4 class="card-title">Anti-Waste Tips</h4>
                            <p class="card-text">Learn how to reduce waste with our expert tips.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <img class="card-img-top" src="https://example.com/service2.jpg" alt="">
                        <div class="card-body">
                            <h4 class="card-title">Cooking Classes</h4>
                            <p class="card-text">Join our cooking classes and make the most of your food.</p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <img class="card-img-top" src="https://example.com/service3.jpg" alt="">
                        <div class="card-body">
                            <h4 class="card-title">Vehicle Sharing</h4>
                            <p class="card-text">Share your vehicle to help distribute food and reduce waste.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonial Section -->
    <section class="testimonial text-center">
        <div class="container">
            <h2 class="mb-4">What People Are Saying</h2>
            <div class="row">
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="card-text">"NoMoreWaste has changed the way we think about food. We're so grateful for their help."</p>
                            <footer class="blockquote-footer">Jane Doe, <cite title="Source Title">Merchant</cite></footer>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="card-text">"Thanks to NoMoreWaste, we have been able to reduce our food waste significantly."</p>
                            <footer class="blockquote-footer">John Smith, <cite title="Source Title">Volunteer</cite></footer>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <p class="card-text">"Their services have helped us in so many ways. We can't thank them enough."</p>
                            <footer class="blockquote-footer">Sarah Lee, <cite title="Source Title">Beneficiary</cite></footer>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer text-center">
        <div class="container">
            <p class="m-0">© 2024 NoMoreWaste. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
