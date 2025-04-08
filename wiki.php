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
    <meta name="description" content="Help guides and tutorials for Bags Bags Bags">
    <meta name="keywords" content="help, guide, tutorial, bags, user guide, admin guide">
    <title>Help Center - Bags Bags Bags</title>
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
                        <a class="nav-link" href="about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="wiki.php">Wiki</a>
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
        <div class="hero">
            <h1 class="text-center mb-5">Help Center</h1>
        </div>
        
        <div class="row">
            <!-- User Guides -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title mb-4">User Guides</h2>
                        
                        <div class="list-group">
                            <a href="wiki/register.php" class="list-group-item list-group-item-action">
                                <h3 class="h5 mb-2">How to Register as a New User</h3>
                                <p class="mb-0">Learn how to create your account and get started with shopping.</p>
                            </a>
                            
                            <a href="wiki/order.php" class="list-group-item list-group-item-action">
                                <h3 class="h5 mb-2">How to Place and Track Orders</h3>
                                <p class="mb-0">Step-by-step guide to placing orders and viewing your order history.</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Guides -->
            <div class="col-md-6 mb-4">
                <div class="card h-100">
                    <div class="card-body">
                        <h2 class="card-title mb-4">Administrator Guides</h2>
                        
                        <div class="list-group">
                            <a href="wiki/user-management.php" class="list-group-item list-group-item-action">
                                <h3 class="h5 mb-2">Managing Users</h3>
                                <p class="mb-0">Learn how to add, remove, and manage user accounts.</p>
                            </a>
                            
                            <a href="wiki/product-management.php" class="list-group-item list-group-item-action">
                                <h3 class="h5 mb-2">Managing Products</h3>
                                <p class="mb-0">Guide to adding, editing, and managing product inventory.</p>
                            </a>
                            
                            <a href="wiki/order-status.php" class="list-group-item list-group-item-action">
                                <h3 class="h5 mb-2">Updating Order Statuses</h3>
                                <p class="mb-0">How to track and update the status of customer orders.</p>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
