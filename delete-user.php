<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection($host, $user, $password, $database, $port);
$requester = $_SESSION['user_id'];

// Verify permission
$role_q = "SELECT r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = ?";
$st = $conn->prepare($role_q);
$st->bind_param("i", $requester);
$st->execute();
$res = $st->get_result();
$rd = $res->fetch_assoc();
if (!$rd || ($rd['role_name'] != 'Admin' && $rd['role_name'] != 'Staff')) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!isset($payload['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'user_id required']);
    exit();
}

$user_id = intval($payload['user_id']);

// Prevent deleting self
if ($user_id === $requester) {
    echo json_encode(['success' => false, 'message' => 'Cannot delete yourself']);
    exit();
}

// Delete user (hard delete). If you prefer soft-delete, update a flag instead.
$del = "DELETE FROM users WHERE user_id = ?";
$st = $conn->prepare($del);
$st->bind_param("i", $user_id);
if ($st->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>