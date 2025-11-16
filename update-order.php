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

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['order_id']) || !isset($input['status'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit();
}

$order_id = intval($input['order_id']);
$status = $input['status'];
$allowed = ['Pending','Shipped','Delivered','Cancelled'];
if (!in_array($status, $allowed)) {
    echo json_encode(['success' => false, 'message' => 'Invalid status']);
    exit();
}

$q = "UPDATE orders SET status = ? WHERE order_id = ?";
$st = $conn->prepare($q);
$st->bind_param("si", $status, $order_id);
if ($st->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Update failed']);
}

$conn->close();
?>
