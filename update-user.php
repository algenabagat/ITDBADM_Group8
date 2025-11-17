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
if (!$payload) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit();
}

$required = ['user_id','first_name','last_name','email','role_id'];
foreach ($required as $r) {
    if (!isset($payload[$r])) {
        echo json_encode(['success' => false, 'message' => 'Missing field: ' . $r]);
        exit();
    }
}

$user_id = intval($payload['user_id']);
$first = trim($payload['first_name']);
$last = trim($payload['last_name']);
$email = trim($payload['email']);
$phone = isset($payload['phone']) ? trim($payload['phone']) : null;
$role_id = intval($payload['role_id']);

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email']);
    exit();
}

// Update user
$update = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ?, role_id = ? WHERE user_id = ?";
$st = $conn->prepare($update);
$st->bind_param("ssssii", $first, $last, $email, $phone, $role_id, $user_id);

if ($st->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>