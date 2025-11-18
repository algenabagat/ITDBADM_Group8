<?php
session_start();
require_once 'config.php';

$conn = getDBConnection($host, $user, $password, $database, $port);

echo "<pre>";
echo "=== DATABASE DEBUG ===\n\n";

// Check orders
$orders = $conn->query("SELECT COUNT(*) as c FROM orders");
$order_count = $orders->fetch_assoc()['c'];
echo "Orders count: " . $order_count . "\n";

if ($order_count > 0) {
    $sample = $conn->query("SELECT o.order_id, CONCAT(u.first_name, ' ', u.last_name) as customer, o.total_amount, o.order_date, o.status FROM orders o JOIN users u ON o.user_id = u.user_id LIMIT 3");
    echo "Sample orders:\n";
    while ($row = $sample->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "No orders found\n";
}

echo "\n";

// Check products
$products = $conn->query("SELECT COUNT(*) as c FROM products");
$product_count = $products->fetch_assoc()['c'];
echo "Products count: " . $product_count . "\n";

if ($product_count > 0) {
    $sample = $conn->query("SELECT p.product_id, p.product_name, b.brand_name, p.stock, p.is_available, p.price FROM products p LEFT JOIN brands b ON p.brand_id = b.brand_id LIMIT 3");
    echo "Sample products:\n";
    while ($row = $sample->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "No products found\n";
}

echo "\n";

// Check users
$users = $conn->query("SELECT COUNT(*) as c FROM users");
$user_count = $users->fetch_assoc()['c'];
echo "Users count: " . $user_count . "\n";

if ($user_count > 0) {
    $sample = $conn->query("SELECT u.user_id, CONCAT(u.first_name, ' ', u.last_name) as name, r.role_name, COUNT(o.order_id) as order_count, COALESCE(SUM(o.total_amount), 0) as total_spent FROM users u LEFT JOIN roles r ON u.role_id = r.role_id LEFT JOIN orders o ON u.user_id = o.user_id GROUP BY u.user_id LIMIT 3");
    echo "Sample users:\n";
    while ($row = $sample->fetch_assoc()) {
        echo json_encode($row) . "\n";
    }
} else {
    echo "No users found\n";
}

$conn->close();
echo "\n=== END DEBUG ===\n";
echo "</pre>";
?>
