<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection($servername, $username, $password, $database, $port);
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

if (!isset($_GET['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'user_id required']);
    exit();
}

$user_id = intval($_GET['user_id']);
$q = "SELECT user_id, role_id, first_name, last_name, email, phone FROM users WHERE user_id = ?";
$st = $conn->prepare($q);
$st->bind_param("i", $user_id);
$st->execute();
$result = $st->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'User not found']);
    exit();
}

echo json_encode(['success' => true, 'user' => $user]);

$conn->close();
?>