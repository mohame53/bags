<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get database connection
$conn = getDBConnection();

// Get current theme from database
$theme = getCurrentTheme($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn more about Bags Bags Bags - Your trusted source for quality bags">
    <meta name="keywords" content="about us, bags, company, quality, customer service">
    <title>About Us - Bags Bags Bags</title>
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/themes.css">
    <script src="js/main.js" defer></script>
</head>
<body class="theme-<?php echo $theme; ?>">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Bags Bags Bags</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="wiki.php">Wiki</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="profile.php">Profile</a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="admin_settings.php">Admin Settings</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="register.php">Register</a>
                        </li>
                    <?php endif; ?>
                    <?php if (!isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="cart.php">Cart (<span id="cart-count">0</span>)</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-8">
                        <h1 class="display-4 mb-3">About Bags Bags Bags</h1>
                        <p class="lead">All things Bags.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Content Section -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 mx-auto">
                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h2 class="card-title mb-4">Our Story</h2>
                                <p class="card-text">Founded in 2025, Bags Bags Bags emerged from a simple idea: to provide high-quality, stylish, and functional bags for every occasion. We believe that a good bag is more than just an accessory - it's an essential part of your daily life.</p>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h2 class="card-title mb-4">Our Mission</h2>
                                <p class="card-text">We strive to offer our customers the perfect blend of style, functionality, and durability in every bag we sell. Our commitment to quality and customer satisfaction drives everything we do.</p>
                            </div>
                        </div>

                        <div class="card shadow-sm mb-4">
                            <div class="card-body">
                                <h2 class="card-title mb-4">Our Values</h2>
                                <ul class="list-unstyled">
                                    <li class="mb-3">
                                        <strong>Quality First:</strong> We never compromise on the quality of our products
                                    </li>
                                    <li class="mb-3">
                                        <strong>Customer Focus:</strong> Your satisfaction is our top priority
                                    </li>
                                    <li class="mb-3">
                                        <strong>Sustainability:</strong> We're committed to environmentally responsible practices
                                    </li>
                                    <li class="mb-3">
                                        <strong>Innovation:</strong> We continuously improve our products and services
                                    </li>
                                </ul>
                            </div>
                        </div>

                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h2 class="card-title mb-4">Our Team</h2>
                                <p class="card-text">We're a passionate team of bag enthusiasts dedicated to bringing you the best products and shopping experience. From our designers to our customer service representatives, every member of our team is committed to excellence.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3>About Us</h3>
                    <p>All things Bags.</p>
                </div>
                <div class="col-md-6">
                    <h3>Quick Links</h3>
                    <ul class="list-unstyled">
                        <li><a href="about.php" class="text-light text-decoration-none">About</a></li>
                        <li><a href="wiki.php" class="text-light text-decoration-none">Wiki</a></li>
                    </ul>
                </div>
            </div>
            <div class="text-center mt-4">
                <p class="mb-0">&copy; 2025 Bags Bags Bags. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 