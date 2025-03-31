<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

$theme = getCurrentTheme();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Learn how to place and track orders at Bags Bags Bags">
    <meta name="keywords" content="order placement, track orders, shopping cart, checkout, bags">
    <title>How to Place and Track Orders - Bags Bags Bags</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <script src="../js/main.js" defer></script>
</head>
<body class="<?php echo $theme; ?>">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container-fluid">
            <a class="navbar-brand" href="../index.php">Bags Bags Bags</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../products.php">Products</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../about.php">About</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="../wiki.php">Wiki</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../profile.php">Profile</a>
                        </li>
                        <?php if (isAdmin()): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="../admin_settings.php">Admin Settings</a>
                            </li>
                        <?php endif; ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="../register.php">Register</a>
                        </li>
                    <?php endif; ?>
                    <?php if (!isAdmin()): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="../cart.php">Cart (<span id="cart-count">0</span>)</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="../wiki.php">Help Center</a></li>
                <li class="breadcrumb-item active" aria-current="page">How to Place and Track Orders</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title mb-4">How to Place and Track Orders</h1>
                        
                        <div class="guide-content">
                            <h2 class="h4 mb-3">Placing an Order</h2>
                            <p>Follow these steps to place your order:</p>
                            
                            <ol class="mb-4">
                                <li>Browse our collection of bags in the Products section</li>
                                <li>Click "Add to Cart" on any items you want to purchase</li>
                                <li>View your cart by clicking the cart icon in the navigation bar</li>
                                <li>Adjust quantities or remove items as needed</li>
                                <li>Click "Order" when ready</li>
                            </ol>

                            <h2 class="h4 mb-3">Tracking Your Order</h2>
                            <p>To track your order status:</p>
                            <ul class="mb-4">
                                <li>Log in to your account</li>
                                <li>Go to your Profile page</li>
                                <li>Scroll down to the "Order History" section</li>
                                <li>View the status of each order:
                                    <ul>
                                        <li>Pending: Order received, awaiting processing</li>
                                        <li>Processing: Order is being prepared</li>
                                        <li>Shipped: Order has been sent</li>
                                    </ul>
                                </li>
                            </ul>

                            <h2 class="h4 mb-3">Order Details</h2>
                            <p>For each order, you can view:</p>
                            <ul class="mb-4">
                                <li>Order ID</li>
                                <li>Date of purchase</li>
                                <li>Items purchased</li>
                                <li>Total amount</li>
                                <li>Current status</li>
                            </ul>

                            <div class="alert alert-info">
                                <h3 class="h5 mb-2">Need Help?</h3>
                                <p class="mb-0">If you have any questions about your order, please contact our customer support team.</p>
                            </div>
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