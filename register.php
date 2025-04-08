<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get database connection
$conn = getDBConnection();

// Get current theme from database
$theme = getCurrentTheme($conn);

// Check if user is already logged in
if (isLoggedIn()) {
    header("Location: index.php");
    exit();
}

// Initialize variables
$username = $email = $password = $confirm_password = "";
$username_err = $email_err = $password_err = $confirm_password_err = "";

$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $email = sanitizeInput($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            // Check if username already exists
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already taken.';
            } else {
                // Check if email already exists
                $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = 'Email already registered.';
                } else {
                    // Insert new user without password encryption
                    $stmt = $conn->prepare("INSERT INTO users (username, email, password, role, status) VALUES (?, ?, ?, 'user', 'active')");
                    try {
                        if ($stmt->execute([$username, $email, $password])) {
                            $success = 'Registration successful! Please login.';
                        } else {
                            $error = 'Registration failed. Please try again.';
                            error_log("Registration insert failed: " . print_r($stmt->errorInfo(), true));
                        }
                    } catch (PDOException $e) {
                        error_log("Registration insert error: " . $e->getMessage());
                        error_log("Registration insert trace: " . $e->getTraceAsString());
                        $error = 'An error occurred during registration. Please try again.';
                    }
                }
            }
        } catch (PDOException $e) {
            error_log("Database connection error: " . $e->getMessage());
            error_log("Database connection trace: " . $e->getTraceAsString());
            $error = 'Database connection error. Please try again later.';
        } catch (Exception $e) {
            error_log("General error: " . $e->getMessage());
            error_log("General error trace: " . $e->getTraceAsString());
            $error = 'An unexpected error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Create a new account at Bags Bags Bags">
    <meta name="keywords" content="register, sign up, account, bags, shopping">
    <title>Register - Bags Bags Bags</title>
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
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Login</a>
                    </li>   
                    <li class="nav-item">
                        <a class="nav-link active" href="register.php">Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="cart.php">Cart (<span id="cart-count">0</span>)</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container py-5">
        <div class="hero">
            <div class="row justify-content-center">
                <div class="col-md-6 col-lg-5">
                    <div class="card shadow">
                        <div class="card-body p-4">
                            <h1 class="text-center mb-4">Register</h1>
                            
                            <?php if ($error): ?>
                                <div class="alert alert-danger"><?php echo $error; ?></div>
                            <?php endif; ?>
                            
                            <?php if ($success): ?>
                                <div class="alert alert-success"><?php echo $success; ?></div>
                            <?php endif; ?>
                            
                            <form method="POST">
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
                                    <input type="password" class="form-control" id="password" name="password" required minlength="6">
                                </div>
                                
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">Register</button>
                            </form>
                            
                            <div class="text-center mt-4">
                                <p class="mb-0">Already have an account? <a href="login.php" class="text-decoration-none">Login here</a></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
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
                        <li><a href="faq.php" class="text-light text-decoration-none">FAQ</a></li>
                        <li><a href="shipping.php" class="text-light text-decoration-none">Shipping</a></li>
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