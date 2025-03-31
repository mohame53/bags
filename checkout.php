<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];
    $total_amount = 0;
    $items = [];
    
    // Calculate total and prepare items
    foreach ($_SESSION['cart'] as $product_id => $quantity) {
        $stmt = $conn->prepare("SELECT price FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($product) {
            $total_amount += $product['price'] * $quantity;
            $items[] = [
                'product_id' => $product_id,
                'quantity' => $quantity
            ];
        }
    }
    
    // Check stock availability
    $stock_check = checkStockAvailability($conn, $items);
    if ($stock_check !== true) {
        $error = $stock_check;
    } else {
        try {
            $conn->beginTransaction();
            
            // Create order
            $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, status) VALUES (?, ?, 'pending')");
            $stmt->execute([$user_id, $total_amount]);
            $order_id = $conn->lastInsertId();
            
            // Add order items and update stock
            foreach ($items as $item) {
                $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
                $stmt->execute([$order_id, $item['product_id'], $item['quantity']]);
                
                // Update product stock
                $stmt = $conn->prepare("UPDATE products SET stock = stock - ? WHERE id = ?");
                $stmt->execute([$item['quantity'], $item['product_id']]);
            }
            
            $conn->commit();
            $_SESSION['cart'] = [];
            header('Location: order_confirmation.php?order_id=' . $order_id);
            exit();
        } catch (PDOException $e) {
            $conn->rollBack();
            $error = 'An error occurred while processing your order.';
        }
    }
} 