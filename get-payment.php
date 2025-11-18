<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['payment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing payment_id']);
    exit;
}

$payment_id = intval($_GET['payment_id']);
$conn = getDBConnection($servername, $username, $password, $database, $port);

$stmt = $conn->prepare("SELECT p.*, pm.method_name FROM payments p LEFT JOIN payment_methods pm ON p.payment_method_id = pm.payment_method_id WHERE p.payment_id = ?");
$stmt->bind_param('i', $payment_id);
$stmt->execute();
$res = $stmt->get_result();
$payment = $res->fetch_assoc();
if ($payment) {
    echo json_encode(['success' => true, 'payment' => $payment]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not found']);
}
?>