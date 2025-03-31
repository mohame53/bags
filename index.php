<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get current theme from session or default
$theme = isset($_SESSION['theme']) ? $_SESSION['theme'] : 'theme-default';

// Get products from database
$conn = getDBConnection();
$stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
closeDBConnection($conn);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Shop for high-quality bags including backpacks, totes, messenger bags, and more">
    <meta name="keywords" content="bags, backpacks, totes, messenger bags, crossbody bags, duffel bags">
    <title>Bags E-commerce - Your One-Stop Shop for Quality Bags</title>
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <script src="js/main.js" defer></script>
</head>
<body class="<?php echo $theme; ?>">
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
                        <a class="nav-link active" href="index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="about.php">About</a>
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

    <main class="container py-5">
        <!-- Hero Section -->
        <section class="text-center mb-5">
            <h1 class="display-4">Welcome to Bags Bags Bags</h1>
            <p class="lead">Discover our collection of high-quality bags for every occasion</p>
        </section>

        <!-- Featured Products -->
        <section class="mb-5">
            <h2 class="text-center mb-4">Featured Products</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="height: 200px; object-fit: contain;">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                                <p class="card-text text-primary fw-bold">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="card-text text-muted"><?php echo ucfirst($product['category']); ?></p>
                                <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#productModal<?php echo $product['id']; ?>">
                                    View Details
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <!-- Product Details Modals -->
        <?php foreach ($products as $product): ?>
            <div class="modal fade" id="productModal<?php echo $product['id']; ?>" tabindex="-1" aria-labelledby="productModalLabel<?php echo $product['id']; ?>" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="productModalLabel<?php echo $product['id']; ?>"><?php echo htmlspecialchars($product['name']); ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                         class="img-fluid" 
                                         alt="<?php echo htmlspecialchars($product['name']); ?>">
                                </div>
                                <div class="col-md-6">
                                    <h4 class="text-primary mb-3">$<?php echo number_format($product['price'], 2); ?></h4>
                                    <p><strong>Category:</strong> <?php echo ucfirst($product['category']); ?></p>
                                    <p><strong>Stock:</strong> <?php echo $product['stock']; ?> units</p>
                                    <p><strong>Description:</strong> A beautiful <?php echo strtolower($product['category']); ?> that combines style and functionality.</p>
                                    <?php if (isAdmin()): ?>
                                        <button type="button" class="btn btn-primary mt-3" disabled>Add to Cart (Admin)</button>
                                    <?php else: ?>
                                        <a href="products.php?id=<?php echo $product['id']; ?>" class="btn btn-primary mt-3">Add to Cart</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Categories -->
        <section>
            <h2 class="text-center mb-4">Shop by Category</h2>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <div class="col">
                    <a href="products.php?category=backpack" class="card h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <h3 class="card-title">Backpacks</h3>
                            <p class="card-text">Perfect for school and travel</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="products.php?category=tote" class="card h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <h3 class="card-title">Tote Bags</h3>
                            <p class="card-text">Stylish and practical</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="products.php?category=messenger" class="card h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <h3 class="card-title">Messenger Bags</h3>
                            <p class="card-text">Professional and versatile</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="products.php?category=crossbody" class="card h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <h3 class="card-title">Crossbody Bags</h3>
                            <p class="card-text">Comfortable and trendy</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="products.php?category=handbag" class="card h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <h3 class="card-title">Handbags</h3>
                            <p class="card-text">Elegant and fashionable</p>
                        </div>
                    </a>
                </div>
                <div class="col">
                    <a href="products.php?category=suitcase" class="card h-100 text-decoration-none">
                        <div class="card-body text-center">
                            <h3 class="card-title">Suitcases</h3>
                            <p class="card-text">Durable travel companions</p>
                        </div>
                    </a>
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
                    <p>All things bags.</p>
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