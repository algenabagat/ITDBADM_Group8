<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user_id = $_SESSION['user_id'];
$conn = getDBConnection($servername, $username, $password, $database, $port);

// Basic role check (allow Admin and Staff)
$roleStmt = $conn->prepare("SELECT r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = ?");
$roleStmt->bind_param('i', $user_id);
$roleStmt->execute();
$res = $roleStmt->get_result();
$u = $res->fetch_assoc();
if (!$u || ($u['role_name'] != 'Admin' && $u['role_name'] != 'Staff')) {
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$order_id = isset($input['order_id']) ? intval($input['order_id']) : null;
$amount = isset($input['amount']) ? floatval($input['amount']) : null;
$payment_method_id = isset($input['payment_method_id']) ? intval($input['payment_method_id']) : null;
$currency_id = isset($input['currency_id']) ? intval($input['currency_id']) : null;
$status = isset($input['status']) ? $input['status'] : 'Pending';

if (!$order_id || !$amount || !$payment_method_id) {
    echo json_encode(['success' => false, 'message' => 'order_id, amount and payment_method_id are required']);
    exit;
}

// Verify order exists
$stmt = $conn->prepare('SELECT order_id FROM orders WHERE order_id = ?');
$stmt->bind_param('i', $order_id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit;
}

// Insert payment
$ins = $conn->prepare('INSERT INTO payments (order_id, amount, payment_method_id, currency_id, status) VALUES (?, ?, ?, ?, ?)');
$ins->bind_param('idiss', $order_id, $amount, $payment_method_id, $currency_id, $status);
if ($ins->execute()) {
    echo json_encode(['success' => true, 'payment_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

?>