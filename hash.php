<?php
include 'config.php';

$conn = getDBConnection($host, $user, $password, $database, $port);
$sql = "SELECT user_id, password_hash FROM users";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    $user_id = $row['user_id'];
    $plain_password = $row['password_hash'];

    if (preg_match('/^\$2[ayb]\$/', $plain_password)) {
        continue;
    }

    // Hash the plain text password
    $hashed = password_hash($plain_password, PASSWORD_BCRYPT);

    // Update DB
    $update = $conn->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
    $update->bind_param("si", $hashed, $user_id);
    $update->execute();
}

echo "All plain text passwords have been hashed successfully.";
