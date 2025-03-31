<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Prevent admin users from accessing the cart
if (isAdmin()) {
    header('Location: index.php');
    exit();
}

$conn = getDBConnection();

// Handle cart updates
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if (!isLoggedIn()) {
            $_SESSION['redirect_after_login'] = 'cart.php';
            header('Location: login.php');
            exit();
        }
        
        switch ($_POST['action']) {
            case 'update':
                if (isset($_POST['product_id']) && isset($_POST['quantity'])) {
                    $productId = (int)$_POST['product_id'];
                    $quantity = (int)$_POST['quantity'];
                    if ($quantity > 0) {
                        $_SESSION['cart'][$productId] = $quantity;
                    } else {
                        unset($_SESSION['cart'][$productId]);
                    }
                }
                break;
                
            case 'remove':
                if (isset($_POST['product_id'])) {
                    $productId = (int)$_POST['product_id'];
                    unset($_SESSION['cart'][$productId]);
                }
                break;
                
            case 'checkout':
                if (isLoggedIn()) {
                    $items = getCartItems($conn);
                    if (!empty($items)) {
                        $orderId = createOrder($conn, $_SESSION['user_id'], $items);
                        if ($orderId) {
                            $_SESSION['cart'] = [];
                            header('Location: order-confirmation.php?id=' . $orderId);
                            exit();
                        }
                    }
                } else {
                    header('Location: login.php');
                    exit();
                }
                break;
            
            case 'add':
                $product_id = intval($_POST['product_id'] ?? 0);
                $quantity = intval($_POST['quantity'] ?? 1);
                
                if ($product_id > 0 && $quantity > 0) {
                    // Check if product exists and has enough stock
                    $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
                    $stmt->execute([$product_id]);
                    $product = $stmt->fetch(PDO::FETCH_ASSOC);
                    
                    if ($product && $product['stock'] >= $quantity) {
                        if (!isset($_SESSION['cart'])) {
                            $_SESSION['cart'] = [];
                        }
                        
                        if (isset($_SESSION['cart'][$product_id])) {
                            $_SESSION['cart'][$product_id] += $quantity;
                        } else {
                            $_SESSION['cart'][$product_id] = $quantity;
                        }
                        
                        $success = 'Product added to cart successfully.';
                    } else {
                        $error = 'Product not available or insufficient stock.';
                    }
                }
                break;
        }
        header('Location: cart.php');
        exit();
    }
}

// Get cart items
$cartItems = getCartItems($conn);
$cartTotal = getCartTotal($conn);

closeDBConnection($conn);

$theme = getCurrentTheme();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Your shopping cart - Bags Bags Bags">
    <meta name="keywords" content="shopping cart, bags, checkout">
    <title>Shopping Cart - Bags Bags Bags</title>
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
                    <li class="nav-item">
                        <a class="nav-link active" href="cart.php">Cart (<span id="cart-count"><?php echo count($cartItems); ?></span>)</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <h1 class="mb-4">Shopping Cart</h1>
        
        <?php if (empty($cartItems)): ?>
            <div class="alert alert-info">
                <p class="mb-0">Your cart is empty.</p>
                <a href="products.php" class="btn btn-primary mt-3">Continue Shopping</a>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-body">
                            <?php foreach ($cartItems as $item): ?>
                                <div class="row mb-4 pb-3 border-bottom">
                                    <div class="col-md-3">
                                        <img src="<?php echo htmlspecialchars($item['image_url']); ?>" 
                                             class="img-fluid rounded" 
                                             alt="<?php echo htmlspecialchars($item['name']); ?>">
                                    </div>
                                    <div class="col-md-9">
                                        <h5 class="card-title"><?php echo htmlspecialchars($item['name']); ?></h5>
                                        <p class="text-primary fw-bold">$<?php echo number_format($item['price'], 2); ?></p>
                                        
                                        <div class="row align-items-center">
                                            <div class="col-md-6">
                                                <form method="POST" class="d-flex align-items-center">
                                                    <input type="hidden" name="action" value="update">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <label for="quantity-<?php echo $item['id']; ?>" class="me-2">Quantity:</label>
                                                    <input type="number" 
                                                           id="quantity-<?php echo $item['id']; ?>" 
                                                           name="quantity" 
                                                           value="<?php echo $item['quantity']; ?>" 
                                                           min="1" 
                                                           max="<?php echo $item['stock']; ?>"
                                                           class="form-control form-control-sm w-auto"
                                                           onchange="this.form.submit()">
                                                </form>
                                            </div>
                                            <div class="col-md-6">
                                                <form method="POST" class="d-flex justify-content-end">
                                                    <input type="hidden" name="action" value="remove">
                                                    <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Order Summary</h5>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Subtotal:</span>
                                <span>$<?php echo number_format($cartTotal, 2); ?></span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Shipping:</span>
                                <span>Free</span>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between mb-4">
                                <strong>Total:</strong>
                                <strong>$<?php echo number_format($cartTotal, 2); ?></strong>
                            </div>
                            
                            <form method="POST">
                                <input type="hidden" name="action" value="checkout">
                                <button type="submit" class="btn btn-primary w-100">Order</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-light py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h3>About Us</h3>
                    <p>All things Bags..</p>
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