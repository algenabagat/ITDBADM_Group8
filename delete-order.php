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

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['order_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit();
}

$order_id = intval($input['order_id']);

$conn->begin_transaction();
try {
    // Restore stock for each item
    $items_q = $conn->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
    $items_q->bind_param("i", $order_id);
    $items_q->execute();
    $res = $items_q->get_result();
    while ($row = $res->fetch_assoc()) {
        $pid = intval($row['product_id']);
        $qty = intval($row['quantity']);
        $upd = $conn->prepare("UPDATE products SET stock = stock + ? WHERE product_id = ?");
        $upd->bind_param("ii", $qty, $pid);
        if (!$upd->execute()) throw new Exception('Failed to restore stock');
    }

    // Delete payments first to avoid FK issues
    $delPayments = $conn->prepare("DELETE FROM payments WHERE order_id = ?");
    $delPayments->bind_param("i", $order_id);
    $delPayments->execute();

    // Delete items
    $delItems = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
    $delItems->bind_param("i", $order_id);
    $delItems->execute();

    // Delete order
    $delOrder = $conn->prepare("DELETE FROM orders WHERE order_id = ?");
    $delOrder->bind_param("i", $order_id);
    if (!$delOrder->execute()) throw new Exception('Failed to delete order');

    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
