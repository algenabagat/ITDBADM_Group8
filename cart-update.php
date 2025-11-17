<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if ($cart_id <= 0 || $quantity <= 0) {
    header("Location: cart.php?error=" . urlencode("Invalid cart update."));
    exit;
}

$conn = getDBConnection($host, $user, $password, $database, $port);
if (!$conn) {
    header("Location: cart.php?error=" . urlencode("Database error."));
    exit;
}

$stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
$stmt->bind_param("ii", $quantity, $cart_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: cart.php?success=" . urlencode("Cart updated."));
exit;
