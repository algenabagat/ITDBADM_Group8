<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['branch_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$branch_id = intval($input['branch_id']);
$conn = getDBConnection($host, $user, $password, $database, $port);

$stmt = $conn->prepare('DELETE FROM branches WHERE branch_id = ?');
$stmt->bind_param('i', $branch_id);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
?>