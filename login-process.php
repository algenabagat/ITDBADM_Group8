<?php
session_start();
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email']) || empty($_POST['password'])) {
    header('Location: login.php?error=' . urlencode('Missing credentials'));
    exit;
}

$email = trim($_POST['email']);
$inputPassword = $_POST['password'];

$conn = getDBConnection($servername, $username, $password, $database, $port);
if (!$conn) {
    header('Location: login.php?error=' . urlencode('DB connection error'));
    exit;
}

// fetch user by email
$stmt = $conn->prepare('SELECT user_id, password_hash, role_id, first_name FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows < 1) {
    $stmt->close();
    $conn->close();
    header('Location: login.php?error=' . urlencode('Email or password is incorrect'));
    exit;
}

$row = $result->fetch_assoc();
$storedHash = $row['password_hash'] ?? '';

if (password_verify($inputPassword, $storedHash)) {
    // successful login
    $_SESSION['user_id'] = (int)$row['user_id'];
    $_SESSION['first_name'] = $row['first_name'];
    $_SESSION['role_id'] = (int)$row['role_id'];

    $stmt->close();
    $conn->close();

    if ($_SESSION['role_id'] === 1 || $_SESSION['role_id'] === 2) {
        header('Location: admin-dashboard.php');
    } else {
        header('Location: index.php');
    }
    exit;
} else {
    // invalid password
    $stmt->close();
    $conn->close();
    header('Location: login.php?error=' . urlencode('Email or password is incorrect'));
    exit;
}
?>