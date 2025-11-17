<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    // If not logged in, redirect to login
    header("Location: login.php");
    exit();
}

$conn = getDBConnection($host, $user, $password, $database, $port);

$product_id = $_POST['product_id'];
$user_id = $_SESSION['user_id'];
$quantity = 1;

// Check if item already in cart
$sql = "SELECT * FROM cart WHERE user_id = ? AND product_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $user_id, $product_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If product already exists → increase quantity
    $update = $conn->prepare("UPDATE cart SET quantity = quantity + 1 WHERE user_id = ? AND product_id = ?");
    $update->bind_param("ii", $user_id, $product_id);
    $update->execute();
} else {
    // Insert new cart row
    $insert = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
    $insert->bind_param("iii", $user_id, $product_id, $quantity);
    $insert->execute();
}

$conn->close();

// Redirect to cart page
header("Location: cart.php");
exit();
