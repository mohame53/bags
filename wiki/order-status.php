<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not logged in as admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

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
    <meta name="description" content="Guide for administrators on updating order statuses at Bags Bags Bags">
    <meta name="keywords" content="order status, admin guide, order management, orders, bags">
    <title>Updating Order Statuses - Bags Bags Bags</title>
    <link rel="icon" type="image/x-icon" href="../assets/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/themes.css">
    <script src="../js/main.js" defer></script>
</head>
<body class="theme-<?php echo $theme; ?>">
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
        <div class="hero">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="../wiki.php">Help Center</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Updating Order Statuses</li>
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Updating Order Statuses</h1>
                        
                        <div class="guide-content">
                            <h2 class="h4 mb-3">Accessing Order Management</h2>
                            <p>To manage order statuses:</p>
                            <ol class="mb-4">
                                <li>Log in to your administrator account</li>
                                <li>Navigate to the Admin Settings page</li>
                                <li>Click on the "Order Management" tab</li>
                            </ol>

                            <h2 class="h4 mb-3">Understanding Order Statuses</h2>
                            <p>Orders can have the following statuses:</p>
                            <ul class="mb-4">
                                <li><strong>Pending</strong> - Initial status when order is placed</li>
                                <li><strong>Processing</strong> - Order is being prepared</li>
                                <li><strong>Shipped</strong> - Order has been sent to customer</li>
                            </ul>

                            <h2 class="h4 mb-3">Updating Order Status</h2>
                            <p>To update an order's status:</p>
                            <ol class="mb-4">
                                <li>Find the order in the orders table</li>
                                <li>Click the "Mark as Processing" or "Mark as Shipped" button</li>
                            </ol>

                            <h2 class="h4 mb-3">Important Notes</h2>
                            <ul class="mb-4">
                                <li>Orders should be processed in a timely manner</li>
                                <li>Keep accurate records of status changes</li>
                                <li>Verify order details before updating status</li>
                            </ul>

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