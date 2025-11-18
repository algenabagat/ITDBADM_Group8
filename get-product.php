<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection($servername, $username, $password, $database, $port);
$user_id = $_SESSION['user_id'];

// Verify user is admin or staff
$role_q = "SELECT r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = ?";
$st = $conn->prepare($role_q);
$st->bind_param("i", $user_id);
$st->execute();
$res = $st->get_result();
$rd = $res->fetch_assoc();
if (!$rd || ($rd['role_name'] != 'Admin' && $rd['role_name'] != 'Staff')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'product_id required']);
    exit();
}

$product_id = intval($_GET['product_id']);
$q = "SELECT * FROM products WHERE product_id = ?";
$st = $conn->prepare($q);
$st->bind_param("i", $product_id);
$st->execute();
$result = $st->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

echo json_encode(['success' => true, 'product' => $product]);

$conn->close();
?>
