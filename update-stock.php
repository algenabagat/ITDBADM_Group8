<?php
session_start();
require_once 'config.php';

// Check if user is logged in and has admin/staff access
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection($host, $user, $password, $database, $port);
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

// Get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['product_id']) || !isset($data['stock'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required fields']);
    exit();
}

$product_id = intval($data['product_id']);
$stock = intval($data['stock']);

if ($stock < 0) {
    echo json_encode(['success' => false, 'message' => 'Stock quantity cannot be negative']);
    exit();
}

// Update the stock in the database
$update_query = "UPDATE products SET stock = ? WHERE product_id = ?";
$stmt = $conn->prepare($update_query);
$stmt->bind_param("ii", $stock, $product_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Stock updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update stock: ' . $conn->error]);
}

$conn->close();
?>
