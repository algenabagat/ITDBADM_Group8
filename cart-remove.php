<?php
session_start();
require_once 'config.php';

if (empty($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$cart_id = isset($_GET['cart_id']) ? (int)$_GET['cart_id'] : 0;
if ($cart_id <= 0) {
    header("Location: cart.php?error=" . urlencode("Invalid cart item."));
    exit;
}

$conn = getDBConnection($servername, $username, $password, $database, $port);
if (!$conn) {
    header("Location: cart.php?error=" . urlencode("Database error."));
    exit;
}

$stmt = $conn->prepare("DELETE FROM cart WHERE cart_id = ?");
$stmt->bind_param("i", $cart_id);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: cart.php?success=" . urlencode("Item removed from cart."));
exit;
