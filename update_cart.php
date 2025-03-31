<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (isset($data['action']) && isset($data['productId'])) {
        switch ($data['action']) {
            case 'add':
                if (isset($data['quantity'])) {
                    addToCart($data['productId'], $data['quantity']);
                    echo json_encode(['success' => true, 'message' => 'Item added to cart']);
                }
                break;
                
            case 'remove':
                removeFromCart($data['productId']);
                echo json_encode(['success' => true, 'message' => 'Item removed from cart']);
                break;
                
            case 'update':
                if (isset($data['quantity'])) {
                    if ($data['quantity'] > 0) {
                        $_SESSION['cart'][$data['productId']] = $data['quantity'];
                    } else {
                        removeFromCart($data['productId']);
                    }
                    echo json_encode(['success' => true, 'message' => 'Cart updated']);
                }
                break;
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
} 