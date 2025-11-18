<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['payment_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$payment_id = intval($input['payment_id']);
$amount = isset($input['amount']) ? floatval($input['amount']) : null;
$payment_method_id = isset($input['payment_method_id']) ? intval($input['payment_method_id']) : null;
$currency_id = isset($input['currency_id']) ? intval($input['currency_id']) : null;
$status = isset($input['status']) ? $input['status'] : null;

$conn = getDBConnection($servername, $username, $password, $database, $port);

// Build dynamic update
$fields = [];
$params = [];
$types = '';
if ($amount !== null) { $fields[] = 'amount = ?'; $params[] = $amount; $types .= 'd'; }
if ($payment_method_id !== null) { $fields[] = 'payment_method_id = ?'; $params[] = $payment_method_id; $types .= 'i'; }
if ($currency_id !== null) { $fields[] = 'currency_id = ?'; $params[] = $currency_id; $types .= 'i'; }
if ($status !== null) { $fields[] = 'status = ?'; $params[] = $status; $types .= 's'; }

if (empty($fields)) {
    echo json_encode(['success' => false, 'message' => 'Nothing to update']);
    exit;
}

$sql = 'UPDATE payments SET ' . implode(', ', $fields) . ' WHERE payment_id = ?';
$params[] = $payment_id; $types .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
?>