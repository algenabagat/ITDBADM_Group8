<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has admin/staff access
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection($servername, $username, $password, $database, $port);
$user_id = $_SESSION['user_id'];

// Verify user is admin or staff
$role_query = "SELECT r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = ?";
$stmt = $conn->prepare($role_query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

if (!$user_data || ($user_data['role_name'] != 'Admin' && $user_data['role_name'] != 'Staff')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get product stock information
if (!isset($_GET['product_id'])) {
    echo json_encode(['success' => false, 'message' => 'Product ID not provided']);
    exit();
}

$product_id = intval($_GET['product_id']);
$product_query = "SELECT product_id, product_name, stock FROM products WHERE product_id = ?";
$stmt = $conn->prepare($product_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

if (!$product) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

echo json_encode([
    'success' => true,
    'product_id' => $product['product_id'],
    'product_name' => $product['product_name'],
    'stock' => $product['stock']
]);

$conn->close();
?>
