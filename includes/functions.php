<?php
// Authentication functions
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: /login.php');
        exit();
    }
}

function requireAdmin() {
    if (!isAdmin()) {
        header('Location: /index.php');
        exit();
    }
}

// Database functions
function getProductById($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getProductsByCategory($conn, $category) {
    $stmt = $conn->prepare("SELECT * FROM products WHERE category = ? ORDER BY name ASC");
    $stmt->execute([$category]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllCategories($conn) {
    $stmt = $conn->query("SELECT DISTINCT category FROM products ORDER BY category");
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Cart functions
function addToCart($productId, $quantity = 1) {
    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] += $quantity;
    } else {
        $_SESSION['cart'][$productId] = $quantity;
    }
}

function removeFromCart($productId) {
    if (isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }
}

function getCartItems($conn) {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return [];
    }
    
    $items = [];
    foreach ($_SESSION['cart'] as $productId => $quantity) {
        $product = getProductById($conn, $productId);
        if ($product) {
            $product['quantity'] = $quantity;
            $items[] = $product;
        }
    }
    return $items;
}

function getCartTotal($conn) {
    $items = getCartItems($conn);
    $total = 0;
    foreach ($items as $item) {
        $total += $item['price'] * $item['quantity'];
    }
    return $total;
}

// Order functions
function createOrder($conn, $userId, $items) {
    try {
        $conn->beginTransaction();
        
        // Create order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount) VALUES (?, ?)");
        $total = getCartTotal($conn);
        $stmt->execute([$userId, $total]);
        $orderId = $conn->lastInsertId();
        
        // Add order items
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        foreach ($items as $item) {
            $stmt->execute([$orderId, $item['id'], $item['quantity'], $item['price']]);
            
            // Update product stock
            $updateStock = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
            $updateStock->execute([$item['quantity'], $item['id']]);
        }
        
        $conn->commit();
        return $orderId;
    } catch (Exception $e) {
        $conn->rollBack();
        return false;
    }
}

function getOrderById($conn, $orderId) {
    $stmt = $conn->prepare("
        SELECT o.*, 
               GROUP_CONCAT(CONCAT(oi.quantity, 'x ', p.name) SEPARATOR ', ') as items
        FROM orders o
        LEFT JOIN order_items oi ON o.id = oi.order_id
        LEFT JOIN products p ON oi.product_id = p.id
        WHERE o.id = ?
        GROUP BY o.id
    ");
    $stmt->execute([$orderId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function updateOrderStatus($conn, $orderId, $newStatus) {
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    return $stmt->execute([$newStatus, $orderId]);
}

// Theme functions
function getCurrentTheme() {
    return isset($_SESSION['theme']) ? $_SESSION['theme'] : 'theme-default';
}

// Utility functions
function sanitizeInput($input) {
    return htmlspecialchars(trim($input));
}

function formatPrice($price) {
    return number_format($price, 2);
}

function generateSlug($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text;
}

// Error handling
function setError($message) {
    $_SESSION['error'] = $message;
}

function getError() {
    if (isset($_SESSION['error'])) {
        $error = $_SESSION['error'];
        unset($_SESSION['error']);
        return $error;
    }
    return null;
}

function setSuccess($message) {
    $_SESSION['success'] = $message;
}

function getSuccess() {
    if (isset($_SESSION['success'])) {
        $success = $_SESSION['success'];
        unset($_SESSION['success']);
        return $success;
    }
    return null;
}

function getImagePath($image_url) {
    // If the URL starts with http or https, return as is
    if (strpos($image_url, 'http://') === 0 || strpos($image_url, 'https://') === 0) {
        return $image_url;
    }
    
    // If the URL starts with /, remove it
    if (strpos($image_url, '/') === 0) {
        $image_url = substr($image_url, 1);
    }
    
    // Return the path relative to the root
    return $image_url;
}

/**
 * Check if there's enough stock for an order
 * @param PDO $conn Database connection
 * @param array $items Array of items with product_id and quantity
 * @return bool|string True if enough stock, error message if not
 */
function checkStockAvailability($conn, $items) {
    foreach ($items as $item) {
        $stmt = $conn->prepare("SELECT stock FROM products WHERE id = ?");
        $stmt->execute([$item['product_id']]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$product || $product['stock'] < $item['quantity']) {
            return "Not enough stock for product ID: " . $item['product_id'];
        }
    }
    return true;
}
?> 