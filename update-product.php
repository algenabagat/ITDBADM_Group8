<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection($host, $user, $password, $database, $port);
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

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit();
}

$required = ['product_id', 'product_name', 'price'];
foreach ($required as $r) {
    if (!isset($payload[$r]) || $payload[$r] === '') {
        echo json_encode(['success' => false, 'message' => 'Missing field: ' . $r]);
        exit();
    }
}

$product_id = intval($payload['product_id']);
$name = trim($payload['product_name']);
$price = floatval($payload['price']);
$brand_id = isset($payload['brand_id']) && $payload['brand_id'] ? intval($payload['brand_id']) : null;
$category_id = isset($payload['category_id']) && $payload['category_id'] ? intval($payload['category_id']) : null;
$stock = isset($payload['stock']) ? intval($payload['stock']) : 0;
$description = isset($payload['description']) ? trim($payload['description']) : null;
$is_available = isset($payload['is_available']) ? $payload['is_available'] : 'Yes';

if ($price < 0) {
    echo json_encode(['success' => false, 'message' => 'Price cannot be negative']);
    exit();
}

$update = "UPDATE products SET product_name = ?, price = ?, brand_id = ?, category_id = ?, stock = ?, description = ?, is_available = ? WHERE product_id = ?";
$st = $conn->prepare($update);
$st->bind_param("sdiiissi", $name, $price, $brand_id, $category_id, $stock, $description, $is_available, $product_id);

if ($st->execute()) {
    echo json_encode(['success' => true, 'message' => 'Product updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
