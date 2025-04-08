<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get database connection
$conn = getDBConnection();

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: /login.php');
    exit();
}

// Get current theme from database
$theme = getCurrentTheme($conn);

// Get user data
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user's orders if they are a regular user
$orders = [];
if (!isAdmin()) {
    $stmt = $conn->prepare("
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.name) SEPARATOR ', ') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.user_id = ?
        GROUP BY o.id
        ORDER BY o.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View your profile at Bags Bags Bags">
    <meta name="keywords" content="profile, account, bags, user">
    <title>Profile - Bags Bags Bags</title>
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
                        <a class="nav-link" href="wiki.php">Wiki</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <a class="nav-link active" href="profile.php">Profile</a>
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
            <h1 class="text-center mb-4">Profile</h1>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h1 class="card-title text-center mb-4">Profile Details</h1>
                        
                        <!-- User Details Section -->
                        <div class="mb-4">
                            <h3 class="mb-3">User Information</h3>
                            <div class="row mb-3">
                                <div class="col-sm-3 fw-bold">Username:</div>
                                <div class="col-sm-9"><?php echo htmlspecialchars($user['username']); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3 fw-bold">Email:</div>
                                <div class="col-sm-9"><?php echo htmlspecialchars($user['email']); ?></div>
                            </div>
                            <div class="row mb-3">
                                <div class="col-sm-3 fw-bold">Role:</div>
                                <div class="col-sm-9">
                                    <span class="badge <?php echo $_SESSION['role'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                        <?php echo $_SESSION['role'] === 'admin' ? 'Admin' : 'Regular User'; ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php if (!isAdmin()): ?>
                <!-- Orders Section -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h2 class="card-title mb-4">My Orders</h2>
                        
                        <?php if (empty($orders)): ?>
                            <p class="text-center">No orders found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($order['items']); ?></td>
                                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <span class="badge bg-<?php 
                                                        echo $order['status'] === 'shipped' ? 'success' : 
                                                            ($order['status'] === 'processing' ? 'warning' : 'secondary'); 
                                                    ?>">
                                                        <?php echo ucfirst($order['status']); ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3>About Us</h3>
                    <p>Your trusted source for quality bags since 2024.</p>
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
                <p class="mb-0">&copy; 2024 Bags Bags Bags. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 