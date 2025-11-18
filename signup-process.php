<?php
require_once 'config.php';
$conn = getDBConnection($servername, $username, $password, $database, $port);

$firstName = trim($_POST['firstName'] ?? '');
$lastName  = trim($_POST['lastName'] ?? '');
$email     = trim($_POST['email'] ?? '');
$number    = trim($_POST['number'] ?? '');
$password  = $_POST['password'] ?? '';
$confirmPassword = $_POST['confirmPassword'] ?? '';

if ($firstName === '' || $lastName === '' || $email === '' || $password === '' || $confirmPassword === '') {
    header("Location: signup.php?error=" . urlencode('Please fill all required fields'));
    exit();
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header("Location: signup.php?error=" . urlencode('Invalid email address'));
    exit();
}

if ($password !== $confirmPassword) {
    header("Location: signup.php?error=" . urlencode('Passwords do not match'));
    exit();
}

$phone = preg_replace('/[^\d\+\-\s]/', '', $number);

$checkStmt = $conn->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
if (!$checkStmt) {
    header("Location: signup.php?error=" . urlencode('Server error'));
    exit();
}
$checkStmt->bind_param('s', $email);
$checkStmt->execute();
$checkStmt->store_result();
if ($checkStmt->num_rows > 0) {
    $checkStmt->close();
    $conn->close();
    header("Location: signup.php?error=" . urlencode('Email is already registered'));
    exit();
}
$checkStmt->close();

$hashedPassword = password_hash($password, PASSWORD_BCRYPT);

$insertUser = $conn->prepare("INSERT INTO users (first_name, last_name, email, password_hash, role_id, phone) VALUES (?, ?, ?, ?, 3, ?)");
if (!$insertUser) {
    header("Location: signup.php?error=" . urlencode('Server error'));
    exit();
}
$insertUser->bind_param("sssss", $firstName, $lastName, $email, $hashedPassword, $phone);
$ok = $insertUser->execute();
$insertUser->close();
$conn->close();

if ($ok) {
    header("Location: login.php?success=" . urlencode('Account created successfully. Please log in.'));
    exit();
} else {
    header("Location: signup.php?error=" . urlencode('Failed to create account'));
    exit();
}
?>
