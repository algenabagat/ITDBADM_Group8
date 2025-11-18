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
$conn = getDBConnection($servername, $username, $password, $database, $port);

$stmt = $conn->prepare('DELETE FROM payments WHERE payment_id = ?');
$stmt->bind_param('i', $payment_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
?>