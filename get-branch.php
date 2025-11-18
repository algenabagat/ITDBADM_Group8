<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

if (!isset($_GET['branch_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing branch_id']);
    exit;
}

$branch_id = intval($_GET['branch_id']);
$conn = getDBConnection($servername, $username, $password, $database, $port);

$stmt = $conn->prepare('SELECT * FROM branches WHERE branch_id = ?');
$stmt->bind_param('i', $branch_id);
$stmt->execute();
$res = $stmt->get_result();
$branch = $res->fetch_assoc();
if ($branch) {
    echo json_encode(['success' => true, 'branch' => $branch]);
} else {
    echo json_encode(['success' => false, 'message' => 'Not found']);
}
?>