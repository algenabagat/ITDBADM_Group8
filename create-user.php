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

// Verify permission (only Admin can create users)
$role_q = "SELECT r.role_name FROM users u JOIN roles r ON u.role_id = r.role_id WHERE u.user_id = ?";
$st = $conn->prepare($role_q);
$st->bind_param("i", $requester);
$st->execute();
$res = $st->get_result();
$rd = $res->fetch_assoc();
if (!$rd || $rd['role_name'] != 'Admin') {
    echo json_encode(['success' => false, 'message' => 'Only admins can create users']);
    exit();
}

$payload = json_decode(file_get_contents('php://input'), true);
if (!$payload) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit();
}

$required = ['first_name', 'last_name', 'email', 'password', 'role_id'];
foreach ($required as $r) {
    if (!isset($payload[$r]) || trim($payload[$r]) === '') {
        echo json_encode(['success' => false, 'message' => 'Missing field: ' . $r]);
        exit();
    }
}

$first = trim($payload['first_name']);
$last = trim($payload['last_name']);
$email = trim($payload['email']);
$phone = isset($payload['phone']) ? trim($payload['phone']) : null;
$password = $payload['password'];
$role_id = intval($payload['role_id']);

// Basic validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

if (strlen($password) < 6) {
    echo json_encode(['success' => false, 'message' => 'Password must be at least 6 characters']);
    exit();
}

// Check if email already exists
$check_q = "SELECT user_id FROM users WHERE email = ?";
$st = $conn->prepare($check_q);
$st->bind_param("s", $email);
$st->execute();
$check_res = $st->get_result();
if ($check_res->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Email already in use']);
    exit();
}

// Hash password
$hashed = password_hash($password, PASSWORD_BCRYPT);

// Insert new user
$insert = "INSERT INTO users (first_name, last_name, email, phone, password_hash, role_id, created_at) VALUES (?, ?, ?, ?, ?, ?, CURDATE())";
$st = $conn->prepare($insert);
$st->bind_param("sssssi", $first, $last, $email, $phone, $hashed, $role_id);

if ($st->execute()) {
    echo json_encode(['success' => true, 'message' => 'User created successfully']);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
?>
