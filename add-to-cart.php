<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
require_once 'config.php';
$conn = getDBConnection($servername, $username, $password, $database, $port);

$user_id = (int)($_SESSION['user_id'] ?? 0);
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;

if (!$user_id || !$product_id) {
    // handle error / redirect
    header('Location: shop.php'); exit;
}

// Call stored procedure and capture OUT params via user variables
$stmt = $conn->prepare("CALL AddToCart(?, ?, ?, @p_success, @p_msg)");
$stmt->bind_param('iii', $user_id, $product_id, $quantity);
$stmt->execute();
$stmt->close();

// read OUT variables
$res = $conn->query("SELECT @p_success AS success, @p_msg AS message");
$row = $res ? $res->fetch_assoc() : null;
$ok = !empty($row['success']);
$msg = $row['message'] ?? '';

// redirect back with basic feedback 
if ($ok) {
    header('Location: cart.php');
} else {
    $_SESSION['flash_error'] = $msg;
    header('Location: view-item.php?product_id=' . $product_id);
}
exit;
