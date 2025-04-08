<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

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
    <meta name="description" content="How to register as a new user at Bags Bags Bags">
    <meta name="keywords" content="register, sign up, new user, account creation, bags">
    <title>How to Register - Bags Bags Bags</title>
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
                    <li class="breadcrumb-item active" aria-current="page">How to Register</li>
                </ol>
            </nav>

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body">
                            <h1 class="card-title mb-4">How to Register as a New User</h1>
                            
                            <div class="guide-content">
                                <h2 class="h4 mb-3">Getting Started</h2>
                                <p>To create an account on Bags Bags Bags, follow these simple steps:</p>
                                
                                <ol class="mb-4">
                                    <li>Click the "Register" link in the navigation bar at the top of the page.</li>
                                    <li>You'll be taken to the registration form where you'll need to provide:
                                        <ul>
                                            <li>Username (must be unique)</li>
                                            <li>Email address (must be unique)</li>
                                            <li>Password (choose a strong password)</li>
                                        </ul>
                                    </li>
                                    <li>Fill in all the required fields and click "Register"</li>
                                </ol>

                                <h2 class="h4 mb-3">After Registration</h2>
                                <p>Once you've successfully registered:</p>
                                <ul class="mb-4">
                                    <li>You'll be automatically logged in</li>
                                    <li>You can start browsing products and adding items to your cart</li>
                                    <li>You'll have access to your profile page where you can view your order history</li>
                                </ul>

                                <h2 class="h4 mb-3">Important Notes</h2>
                                <ul class="mb-4">
                                    <li>Make sure to use a valid email address as it will be used for order notifications</li>
                                    <li>Keep your password secure and don't share it with anyone</li>
                                </ul>

                                <div class="alert alert-info">
                                    <h3 class="h5 mb-2">Need Help?</h3>
                                    <p class="mb-0">If you encounter any issues during registration, please contact our support team.</p>
                                </div>
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