<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php?error=" . urlencode("Please log in first to add items to cart."));
    exit;
}

$user_id = (int)$_SESSION['user_id'];
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($product_id <= 0 || $quantity <= 0) {
    header("Location: shop.php?error=" . urlencode("Invalid product or quantity."));
    exit;
}

$conn = getDBConnection($servername, $username, $password, $database, $port);
if (!$conn) {
    header("Location: shop.php?error=" . urlencode("Database connection error."));
    exit;
}

// Check if product exists and has stock
$stmt = $conn->prepare("SELECT product_id, stock FROM products WHERE product_id = ? LIMIT 1");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$res = $stmt->get_result();
$product = $res->fetch_assoc();
$stmt->close();

if (!$product) {
    $conn->close();
    header("Location: shop.php?error=" . urlencode("Product not found."));
    exit;
}

if ((int)$product['stock'] <= 0) {
    $conn->close();
    header("Location: view-item.php?product_id={$product_id}&error=" . urlencode("Product is out of stock."));
    exit;
}

// Check if item already in cart
$stmt = $conn->prepare("SELECT cart_id, quantity FROM cart WHERE user_id = ? AND product_id = ? LIMIT 1");
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$res = $stmt->get_result();
$row = $res->fetch_assoc();
$stmt->close();

if ($row) {
    // Update quantity
    $newQty = (int)$row['quantity'] + $quantity;
    $update = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $update->bind_param("ii", $newQty, $row['cart_id']);
    $update->execute();
    $update->close();
} else {
    // Insert new cart entry
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
    $insert->close();
}

$conn->close();

header("Location: cart.php?success=" . urlencode("Item added to cart."));
exit;
