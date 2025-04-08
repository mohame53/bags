<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

// Get database connection
$conn = getDBConnection();

// Get current theme from database
$theme = getCurrentTheme($conn);

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            // Check if the user exists
            $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $password === $user['password']) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                
                // Handle redirect after login
                $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
                exit();
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log("Database error: " . $e->getMessage());
            $error = 'An error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Login to Bags Bags Bags - Your account">
    <meta name="keywords" content="login, account, bags, user">
    <title>Login - Bags Bags Bags</title>
    <link rel="icon" type="image/x-icon" href="assets/images/favicon.ico">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/themes.css">
    <!-- Add this line to check if jQuery is needed -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
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
                        <a class="nav-link active" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Register</a>
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
                            <h1 class="card-title text-center mb-4">Login</h1>
                            
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

                            <form method="POST" action="login.php" id="loginForm" style="position: relative; z-index: 2;">
                                <div class="mb-3">
                                    <label for="username" class="form-label">Username</label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="username" 
                                           name="username" 
                                           required 
                                           autocomplete="username"
                                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="password" 
                                           class="form-control" 
                                           id="password" 
                                           name="password" 
                                           required 
                                           autocomplete="current-password">
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember">Remember me</label>
                                </div>

                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-primary">Login</button>
                                </div>
                            </form>

                            <div class="text-center mt-4">
                                <p class="mb-2">Don't have an account? <a href="register.php" class="text-decoration-none">Register here</a></p>
                                <p class="mb-0"><a href="index.php" class="text-decoration-none">Forgot your password? Too bad</a></p>
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

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('loginForm');
        if(form) {
            form.addEventListener('submit', function(e) {
                console.log('Form submission attempted');
            });
        }

        // Test input functionality
        const username = document.getElementById('username');
        const password = document.getElementById('password');
        if(username && password) {
            username.addEventListener('input', function() {
                console.log('Username input working');
            });
            password.addEventListener('input', function() {
                console.log('Password input working');
            });
        }
    });
    </script>
</body>
</html> 