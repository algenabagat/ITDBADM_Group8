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
if (!$input || !isset($input['user_id']) || !isset($input['items']) || !is_array($input['items'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit();
}

$order_user_id = intval($input['user_id']);
$branch_id = isset($input['branch_id']) ? intval($input['branch_id']) : null;
$items = $input['items'];

if (count($items) === 0) {
    echo json_encode(['success' => false, 'message' => 'No items provided']);
    exit();
}

// Begin transaction
$conn->begin_transaction();
try {
    $total = 0.0;

    // Prepare statements
    $productSel = $conn->prepare("SELECT price, stock FROM products WHERE product_id = ? FOR UPDATE");
    $productUpd = $conn->prepare("UPDATE products SET stock = stock - ? WHERE product_id = ?");
    $insertOrder = $conn->prepare("INSERT INTO orders (user_id, branch_id, total_amount) VALUES (?, ?, ?)");
    $insertItem = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");

    // Validate stock and compute total
    foreach ($items as $it) {
        $pid = intval($it['product_id']);
        $qty = intval($it['quantity']);
        if ($qty <= 0) throw new Exception('Invalid quantity');

        $productSel->bind_param("i", $pid);
        $productSel->execute();
        $res = $productSel->get_result();
        $p = $res->fetch_assoc();
        if (!$p) throw new Exception('Product not found: ' . $pid);
        if (intval($p['stock']) < $qty) throw new Exception('Insufficient stock for product ' . $pid);

        $price = floatval($p['price']);
        $total += $price * $qty;
    }

    // Insert order
    $insertOrder->bind_param("iid", $order_user_id, $branch_id, $total);
    if (!$insertOrder->execute()) throw new Exception('Failed to create order');
    $order_id = $conn->insert_id;

    // Insert items and update stock
    foreach ($items as $it) {
        $pid = intval($it['product_id']);
        $qty = intval($it['quantity']);

        // get price (no FOR UPDATE now since earlier we locked rows)
        $pstmt = $conn->prepare("SELECT price FROM products WHERE product_id = ?");
        $pstmt->bind_param("i", $pid);
        $pstmt->execute();
        $r = $pstmt->get_result()->fetch_assoc();
        $price = floatval($r['price']);

        $insertItem->bind_param("iiid", $order_id, $pid, $qty, $price);
        if (!$insertItem->execute()) throw new Exception('Failed to insert order item');

        $productUpd->bind_param("ii", $qty, $pid);
        if (!$productUpd->execute()) throw new Exception('Failed to update stock');
    }

    $conn->commit();
    echo json_encode(['success' => true, 'order_id' => $order_id]);
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

$conn->close();
?>
