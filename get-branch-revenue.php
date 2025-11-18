<?php
session_start();
require_once 'config.php';

// Check session and authorization (same logic as get-monthly-revenue.php)
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['branch_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing branch_id parameter']);
    exit();
}

$branch_id = intval($data['branch_id']);

try {
    $conn = getDBConnection($host, $user, $password, $database, $port);
    
    // Query branch revenue from the 'revenue' table
    $query = "SELECT COALESCE(total_revenue, 0) as revenue FROM revenue WHERE branch_id = ?";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param('i', $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    // If a row is found, use its revenue; otherwise, default to 0
    $revenue = $row['revenue'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'revenue' => $revenue,
        'branch_id' => $branch_id
    ]);
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>