<?php
session_start();
require_once '../config/database.php';
require_once '../includes/functions.php';

// Redirect if not logged in as admin
if (!isLoggedIn() || !isAdmin()) {
    header('Location: ../login.php');
    exit();
}

$theme = getCurrentTheme();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Guide for administrators on managing users at Bags Bags Bags">
    <meta name="keywords" content="user management, admin guide, user accounts, administrator, bags">
    <title>Managing Users - Bags Bags Bags</title>
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
                <li class="breadcrumb-item active" aria-current="page">Managing Users</li>
            </ol>
        </nav>

        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        <h1 class="card-title mb-4">Managing Users</h1>
                        
                        <div class="guide-content">
                            <h2 class="h4 mb-3">Accessing User Management</h2>
                            <p>To manage users on the system:</p>
                            <ol class="mb-4">
                                <li>Log in to your administrator account</li>
                                <li>Navigate to the Admin Settings page</li>
                                <li>Click on the "User Management" tab</li>
                            </ol>

                            <h2 class="h4 mb-3">Adding New Users</h2>
                            <p>To create a new user account:</p>
                            <ol class="mb-4">
                                <li>Fill out the registration form with:
                                    <ul>
                                        <li>Username (must be unique)</li>
                                        <li>Email address (must be unique)</li>
                                        <li>Password</li>
                                        <li>Role (User or Admin)</li>
                                    </ul>
                                </li>
                                <li>Click "Create Account" to save</li>
                            </ol>

                            <h2 class="h4 mb-3">Managing Existing Users</h2>
                            <p>In the user management table, you can:</p>
                            <ul class="mb-4">
                                <li>View all registered users</li>
                                <li>See their roles and account status</li>
                                <li>Delete user accounts (except your own admin account)</li>
                            </ul>

                            <h2 class="h4 mb-3">Important Notes</h2>
                            <ul class="mb-4">
                                <li>Always verify user information before creating accounts</li>
                                <li>Be cautious when deleting accounts as this action cannot be undone</li>
                                <li>Regular users cannot access admin features</li>
                            </ul>

                            <div class="alert alert-warning">
                                <h3 class="h5 mb-2">Security Reminder</h3>
                                <p class="mb-0">User management operations are sensitive. Always verify user identities before granting admin privileges.</p>
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