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

// role check
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
$name = isset($input['branch_name']) ? trim($input['branch_name']) : '';
$location = isset($input['location']) ? trim($input['location']) : '';

if ($name === '') {
    echo json_encode(['success' => false, 'message' => 'branch_name required']);
    exit;
}

$stmt = $conn->prepare('INSERT INTO branches (branch_name, location) VALUES (?, ?)');
$stmt->bind_param('ss', $name, $location);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'branch_id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
?>