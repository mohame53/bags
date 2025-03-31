<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get database connection
$conn = getDBConnection();

// Redirect if not logged in or not admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: /index.php');
    exit();
}

$theme = getCurrentTheme();
$error = '';
$success = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_product':
            $name = sanitizeInput($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = sanitizeInput($_POST['category'] ?? '');
            $image_url = sanitizeInput($_POST['image_url'] ?? '');
            $stock = intval($_POST['stock'] ?? 0);
            
            if (empty($name) || $price <= 0 || empty($category)) {
                $error = 'Please fill in all required fields.';
            } else {
                try {
                    $stmt = $conn->prepare("INSERT INTO products (name, price, category, image_url, stock) VALUES (?, ?, ?, ?, ?)");
                    $stmt->execute([$name, $price, $category, $image_url, $stock]);
                    $success = 'Product added successfully.';
                } catch (PDOException $e) {
                    $error = 'An error occurred while adding the product.';
                }
            }
            break;
            
        case 'edit_product':
            $id = intval($_POST['product_id'] ?? 0);
            $name = sanitizeInput($_POST['name'] ?? '');
            $price = floatval($_POST['price'] ?? 0);
            $category = sanitizeInput($_POST['category'] ?? '');
            $image_url = sanitizeInput($_POST['image_url'] ?? '');
            $stock = intval($_POST['stock'] ?? 0);
            
            if (empty($name) || $price <= 0 || empty($category)) {
                $error = 'Please fill in all required fields.';
            } else {
                try {
                    $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, category = ?, image_url = ?, stock = ? WHERE id = ?");
                    $stmt->execute([$name, $price, $category, $image_url, $stock, $id]);
                    $success = 'Product updated successfully.';
                } catch (PDOException $e) {
                    $error = 'An error occurred while updating the product.';
                }
            }
            break;
            
        case 'delete_product':
            $id = intval($_POST['product_id'] ?? 0);
            try {
                $conn->beginTransaction();
                
                // Delete related product options first
                $stmt = $conn->prepare("DELETE FROM product_options WHERE product_id = ?");
                $stmt->execute([$id]);
                
                // Delete related order items
                $stmt = $conn->prepare("DELETE FROM order_items WHERE product_id = ?");
                $stmt->execute([$id]);
                
                // Finally delete the product
                $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
                $stmt->execute([$id]);
                
                $conn->commit();
                $success = 'Product deleted successfully.';
            } catch (PDOException $e) {
                $conn->rollBack();
                $error = 'An error occurred while deleting the product.';
            }
            break;
            
        case 'create_account':
            $username = sanitizeInput($_POST['username'] ?? '');
            $email = sanitizeInput($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = sanitizeInput($_POST['role'] ?? 'user');
            
            if (empty($username) || empty($email) || empty($password)) {
                $error = 'Please fill in all required fields.';
            } else {
                try {
                    // Check if username exists
                    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    if ($stmt->fetch()) {
                        $error = 'Username already exists.';
                    } else {
                        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$username, $email, $password, $role]);
                        $success = 'Account created successfully.';
                    }
                } catch (PDOException $e) {
                    $error = 'An error occurred while creating the account.';
                }
            }
            break;
            
        case 'delete_account':
            $id = intval($_POST['user_id'] ?? 0);
            if ($id === $_SESSION['user_id']) {
                $error = 'You cannot delete your own account.';
            } else {
                try {
                    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
                    $stmt->execute([$id]);
                    $success = 'Account deleted successfully.';
                } catch (PDOException $e) {
                    $error = 'An error occurred while deleting the account.';
                }
            }
            break;

        case 'update_order_status':
            $orderId = intval($_POST['order_id']);
            $newStatus = sanitizeInput($_POST['new_status']);
            
            if (in_array($newStatus, ['processing', 'shipped'])) {
                $order = getOrderById($conn, $orderId);
                if ($order) {
                    // Only allow status transitions: pending -> processing -> shipped
                    if (($newStatus === 'processing' && $order['status'] === 'pending') ||
                        ($newStatus === 'shipped' && $order['status'] === 'processing')) {
                        if (updateOrderStatus($conn, $orderId, $newStatus)) {
                            $success = 'Order status updated successfully.';
                        } else {
                            $error = 'Failed to update order status.';
                        }
                    } else {
                        $error = 'Invalid status transition. Orders must follow the sequence: pending -> processing -> shipped.';
                    }
                } else {
                    $error = 'Order not found.';
                }
            } else {
                $error = 'Invalid status.';
            }
            break;
    }
}

// Get all products
$products = $conn->query("SELECT * FROM products ORDER BY name")->fetchAll(PDO::FETCH_ASSOC);

// Get all users
$users = $conn->query("SELECT * FROM users ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);

// Get all categories
$categories = $conn->query("SELECT DISTINCT category FROM products ORDER BY category")->fetchAll(PDO::FETCH_COLUMN);

// Get all orders with user information
$stmt = $conn->prepare("
    SELECT o.*, u.username, u.email,
           GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.name) SEPARATOR ', ') as items
    FROM orders o
    JOIN users u ON o.user_id = u.id
    LEFT JOIN order_items oi ON o.id = oi.order_id
    LEFT JOIN products p ON oi.product_id = p.id
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin settings for Bags Bags Bags">
    <meta name="keywords" content="admin, settings, products, users">
    <title>Admin Settings - Bags Bags Bags</title>
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
                                <a class="nav-link active" href="admin_settings.php">Admin Settings</a>
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
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <h1 class="text-center mb-4">Admin Settings</h1>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Tabs Navigation -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab">
                    Product Management
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab">
                    User Management
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="orders-tab" data-bs-toggle="tab" data-bs-target="#orders" type="button" role="tab">
                    Order Management
                </button>
            </li>
        </ul>

        <!-- Tab Content -->
        <div class="tab-content" id="adminTabsContent">
            <!-- Products Tab -->
            <div class="tab-pane fade show active" id="products" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Add Product Form -->
                        <form method="POST" action="admin_settings.php" class="mb-4">
                            <input type="hidden" name="action" value="add_product">
                            <h3 class="h5 mb-3">Add New Product</h3>
                            
                            <div class="mb-3">
                                <label for="name" class="form-label">Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="category" class="form-label">Category</label>
                                <input type="text" class="form-control" id="category" name="category" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="image_url" class="form-label">Image URL</label>
                                <input type="url" class="form-control" id="image_url" name="image_url" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="stock" class="form-label">Initial Stock</label>
                                <input type="number" class="form-control" id="stock" name="stock" min="0" value="0" required>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Add Product</button>
                        </form>

                        <!-- Product List -->
                        <h3 class="h5 mb-3">Existing Products</h3>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Price</th>
                                        <th>Category</th>
                                        <th>Stock</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($products as $product): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($product['name']); ?></td>
                                            <td>$<?php echo number_format($product['price'], 2); ?></td>
                                            <td><?php echo htmlspecialchars($product['category']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php echo $product['stock'] > 0 ? 'success' : 'danger'; ?>">
                                                    <?php echo $product['stock']; ?> in stock
                                                </span>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $product['id']; ?>">
                                                    Edit
                                                </button>
                                            </td>
                                        </tr>
                                        
                                        <!-- Edit Product Modal -->
                                        <div class="modal fade" id="editProductModal<?php echo $product['id']; ?>" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Edit Product</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form method="POST" action="admin_settings.php">
                                                        <div class="modal-body">
                                                            <input type="hidden" name="action" value="edit_product">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                                                            
                                                            <div class="mb-3">
                                                                <label for="edit_name_<?php echo $product['id']; ?>" class="form-label">Name</label>
                                                                <input type="text" class="form-control" id="edit_name_<?php echo $product['id']; ?>" name="name" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="edit_price_<?php echo $product['id']; ?>" class="form-label">Price</label>
                                                                <input type="number" class="form-control" id="edit_price_<?php echo $product['id']; ?>" name="price" step="0.01" value="<?php echo $product['price']; ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="edit_category_<?php echo $product['id']; ?>" class="form-label">Category</label>
                                                                <input type="text" class="form-control" id="edit_category_<?php echo $product['id']; ?>" name="category" value="<?php echo htmlspecialchars($product['category']); ?>" required>
                                                            </div>
                                                            
                                                            <div class="mb-3">
                                                                <label for="edit_image_url_<?php echo $product['id']; ?>" class="form-label">Image URL</label>
                                                                <input type="url" class="form-control" id="edit_image_url_<?php echo $product['id']; ?>" name="image_url" value="<?php echo htmlspecialchars($product['image_url']); ?>" required>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="edit_stock_<?php echo $product['id']; ?>" class="form-label">Current Stock</label>
                                                                <input type="number" class="form-control" id="edit_stock_<?php echo $product['id']; ?>" name="stock" min="0" value="<?php echo $product['stock']; ?>" required>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                                                            <button type="submit" class="btn btn-primary">Save Changes</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Users Tab -->
            <div class="tab-pane fade" id="users" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <!-- Create Account Form -->
                        <form method="POST" action="admin_settings.php" class="mb-4">
                            <input type="hidden" name="action" value="create_account">
                            <h3 class="h5 mb-3">Create New Account</h3>
                            
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role">
                                    <option value="user">Regular User</option>
                                    <option value="admin">Admin</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">Create Account</button>
                        </form>

                        <!-- User List -->
                        <h3 class="h5 mb-3">Existing Users</h3>
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Username</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge <?php echo $user['role'] === 'admin' ? 'bg-danger' : 'bg-primary'; ?>">
                                                    <?php echo $user['role'] === 'admin' ? 'Admin' : 'Regular User'; ?>
                                                </span>
                                            </td>
                                            <td>
                                                <?php if ($user['id'] !== $_SESSION['user_id']): ?>
                                                    <form method="POST" action="admin_settings.php" class="d-inline">
                                                        <input type="hidden" name="action" value="delete_account">
                                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this account?')">
                                                            Delete
                                                        </button>
                                                    </form>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Orders Tab -->
            <div class="tab-pane fade" id="orders" role="tabpanel">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <?php if (empty($orders)): ?>
                            <p class="text-center">No orders found.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Order ID</th>
                                            <th>Customer</th>
                                            <th>Date</th>
                                            <th>Items</th>
                                            <th>Total</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($orders as $order): ?>
                                            <tr>
                                                <td>#<?php echo $order['id']; ?></td>
                                                <td>
                                                    <?php echo htmlspecialchars($order['username']); ?><br>
                                                    <small class="text-muted"><?php echo htmlspecialchars($order['email']); ?></small>
                                                </td>
                                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                                <td><?php echo htmlspecialchars($order['items']); ?></td>
                                                <td>$<?php echo number_format($order['total_amount'], 2); ?></td>
                                                <td>
                                                    <?php if ($order['status'] === 'pending'): ?>
                                                        <span class="badge bg-warning">Pending</span>
                                                    <?php elseif ($order['status'] === 'processing'): ?>
                                                        <span class="badge bg-info">Processing</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Shipped</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <?php if ($order['status'] === 'pending'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="update_order_status">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="new_status" value="processing">
                                                            <button type="submit" class="btn btn-warning btn-sm">Mark as Processing</button>
                                                        </form>
                                                    <?php elseif ($order['status'] === 'processing'): ?>
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="action" value="update_order_status">
                                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                            <input type="hidden" name="new_status" value="shipped">
                                                            <button type="submit" class="btn btn-success btn-sm">Mark as Shipped</button>
                                                        </form>
                                                    <?php else: ?>
                                                        <span class="badge bg-success">Shipped</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

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