<?php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
if (!$input || !isset($input['branch_id'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid payload']);
    exit;
}

$branch_id = intval($input['branch_id']);
$name = isset($input['branch_name']) ? trim($input['branch_name']) : null;
$location = isset($input['location']) ? trim($input['location']) : null;

$conn = getDBConnection($servername, $username, $password, $database, $port);

$fields = [];
$params = [];
$types = '';
if ($name !== null) { $fields[] = 'branch_name = ?'; $params[] = $name; $types .= 's'; }
if ($location !== null) { $fields[] = 'location = ?'; $params[] = $location; $types .= 's'; }

if (empty($fields)) {
    echo json_encode(['success' => false, 'message' => 'Nothing to update']);
    exit;
}

$sql = 'UPDATE branches SET ' . implode(', ', $fields) . ' WHERE branch_id = ?';
$params[] = $branch_id; $types .= 'i';

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}
?>