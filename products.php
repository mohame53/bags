<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get database connection
$conn = getDBConnection();

// Get current theme from database
$theme = getCurrentTheme($conn);

// Get products from database
$stmt = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);
closeDBConnection($conn);

// Get category filter
$category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : null;

// Build query
$query = "SELECT * FROM products";
$params = [];

if ($category) {
    $query .= " WHERE category = ?";
    $params[] = $category;
}

// Always order by name ascending
$query .= " ORDER BY name ASC";

$stmt = $conn->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get categories for filter
$stmt = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
$categories = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Browse our collection of high-quality bags">
    <meta name="keywords" content="bags, backpacks, totes, messenger bags, crossbody bags, duffel bags">
    <title>Products - Bags Bags Bags</title>
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
                        <a class="nav-link active" href="products.php">Products</a>
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
        <div class="row mb-4">
            <div class="col-md-6">
                <h1>Our Products</h1>
            </div>
            <div class="col-md-6">
                <form method="GET" class="row g-3">
                    <div class="col-12">
                        <label for="category" class="form-label">Category:</label>
                        <select name="category" id="category" class="form-select" onchange="this.form.submit()">
                            <option value="">All Categories</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat; ?>" <?php echo $category === $cat ? 'selected' : ''; ?>>
                                    <?php echo ucfirst($cat); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </form>
            </div>
        </div>

        <?php if (empty($products)): ?>
            <div class="alert alert-info">
                No products found in this category.
            </div>
        <?php else: ?>
            <div class="row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-4">
                <?php foreach ($products as $product): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="<?php echo htmlspecialchars($product['image_url']); ?>" 
                                 class="card-img-top" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 style="height: 200px; object-fit: contain;">
                            <div class="card-body d-flex flex-column">
                                <h3 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h3>
                                <p class="card-text text-muted"><?php echo ucfirst($product['category']); ?></p>
                                <p class="card-text text-primary fw-bold">$<?php echo number_format($product['price'], 2); ?></p>
                                <p class="card-text">
                                    <span class="badge bg-<?php echo $product['stock'] > 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $product['stock']; ?> in stock
                                    </span>
                                </p>
                                <?php if ($product['stock'] > 0 && !isAdmin()): ?>
                                    <form method="POST" action="cart.php" class="d-inline">
                                        <input type="hidden" name="action" value="add">
                                        <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                        <div class="input-group mb-3">
                                            <input type="number" class="form-control" name="quantity" value="1" min="1" max="<?php echo $product['stock']; ?>" required>
                                            <button type="submit" class="btn btn-primary">Add to Cart</button>
                                        </div>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled><?php echo isAdmin() ? 'Admin View' : 'Out of Stock'; ?></button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
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