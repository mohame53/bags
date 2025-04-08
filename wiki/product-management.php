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
    <meta name="description" content="Guide for administrators on managing products at Bags Bags Bags">
    <meta name="keywords" content="product management, admin guide, inventory, products, bags">
    <title>Managing Products - Bags Bags Bags</title>
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
                    <li class="breadcrumb-item active" aria-current="page">Managing Products</li>
                </ol>
            </nav>
        </div>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Managing Products</h1>
                        
                        <div class="guide-content">
                            <h2 class="h4 mb-3">Accessing Product Management</h2>
                            <p>To manage products on the system:</p>
                            <ol class="mb-4">
                                <li>Log in to your administrator account</li>
                                <li>Navigate to the Admin Settings page</li>
                                <li>Click on the "Product Management" tab</li>
                            </ol>

                            <h2 class="h4 mb-3">Adding New Products</h2>
                            <p>To add a new product to the catalog:</p>
                            <ol class="mb-4">
                                <li>Fill out the product form with:
                                    <ul>
                                        <li>Product name</li>
                                        <li>Price</li>
                                        <li>Category</li>
                                        <li>Image URL</li>
                                        <li>Initial stock quantity</li>
                                    </ul>
                                </li>
                                <li>Click "Add Product" to save</li>
                            </ol>

                            <h2 class="h4 mb-3">Editing Existing Products</h2>
                            <p>To modify an existing product:</p>
                            <ol class="mb-4">
                                <li>Find the product in the products table</li>
                                <li>Click the "Edit" button next to the product</li>
                                <li>Update the desired fields</li>
                                <li>Click "Save Changes" to update</li>
                            </ol>

                            <h2 class="h4 mb-3">Update Product Inventory Quantity</h2>
                            <p>For a visual guide, refer to the video below:</p>
                            <video controls style="width: 100%; height: auto;">
                                <source src="../assets/videos/update_stock.mov" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>

                            <h2 class="h4 mb-3">Managing Product Inventory</h2>
                            <p>Important considerations for inventory management:</p>
                            <ul class="mb-4">
                                <li>Regularly update stock levels</li>
                                <li>Monitor low stock items</li>
                                <li>Consider seasonal trends when managing inventory</li>
                            </ul>



                            <h2 class="h4 mb-3">Important Notes</h2>
                            <ul class="mb-4">
                                <li>Always verify product information before adding or updating</li>
                                <li>Keep product images up to date</li>
                                <li>Regularly review prices to ensure competitiveness</li>
                                <li>Monitor product performance and adjust inventory accordingly</li>
                            </ul>

                            <div class="alert alert-info">
                                <h3 class="h5 mb-2">Best Practices</h3>
                                <p class="mb-0">Regular inventory checks and product updates help maintain a successful online store.</p>
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