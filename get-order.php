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

if (!isset($_GET['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'order_id required']);
    exit();
}

$order_id = intval($_GET['order_id']);

$q = "SELECT o.*, u.first_name, u.last_name, b.branch_name
      FROM orders o
      JOIN users u ON o.user_id = u.user_id
      LEFT JOIN branches b ON o.branch_id = b.branch_id
      WHERE o.order_id = ?";
$st = $conn->prepare($q);
$st->bind_param("i", $order_id);
$st->execute();
$result = $st->get_result();
$order = $result->fetch_assoc();

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Order not found']);
    exit();
}

$items_q = "SELECT oi.*, p.product_name FROM order_items oi JOIN products p ON oi.product_id = p.product_id WHERE oi.order_id = ?";
$st = $conn->prepare($items_q);
$st->bind_param("i", $order_id);
$st->execute();
$items_res = $st->get_result();
$items = [];
while ($row = $items_res->fetch_assoc()) {
    $items[] = $row;
}

$order['customer_name'] = $order['first_name'] . ' ' . $order['last_name'];
$order['items'] = $items;

echo json_encode(['success' => true, 'order' => $order]);

$conn->close();
?>
