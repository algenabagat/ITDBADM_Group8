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

// Calculate quantity change (difference between new stock and current stock)
$current_stock_query = "SELECT stock FROM products WHERE product_id = ?";
$stmt = $conn->prepare($current_stock_query);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
$product_data = $result->fetch_assoc();

if (!$product_data) {
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit();
}

$current_stock = $product_data['stock'];
$quantity_change = $stock - $current_stock;

// Call stored procedure to update product stock
$procedure_query = "CALL UpdateProductStock(?, ?, @new_stock, @success)";
$stmt = $conn->prepare($procedure_query);
$stmt->bind_param("ii", $product_id, $quantity_change);

if ($stmt->execute()) {
    // Get the output parameters from the stored procedure
    $result = $conn->query("SELECT @new_stock as new_stock, @success as success");
    $output = $result->fetch_assoc();
    
    if ($output['success']) {
        echo json_encode([
            'success' => true, 
            'message' => 'Stock updated successfully',
            'new_stock' => $output['new_stock']
        ]);
    } else {
        echo json_encode([
            'success' => false, 
            'message' => 'Insufficient stock: Cannot set stock to negative value',
            'current_stock' => $current_stock
        ]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update stock: ' . $conn->error]);
}

$conn->close();
?>