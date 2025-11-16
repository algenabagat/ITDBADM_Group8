<?php
session_start();
require_once 'config.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$conn = getDBConnection($host, $user, $password, $database, $port);

$q = "SELECT brand_id, brand_name FROM brands ORDER BY brand_name";
$result = $conn->query($q);
$brands = [];

while ($row = $result->fetch_assoc()) {
    $brands[] = $row;
}

echo json_encode(['success' => true, 'brands' => $brands]);

$conn->close();
?>
