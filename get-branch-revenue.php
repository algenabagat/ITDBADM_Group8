<?php
session_start();
require_once 'config.php';

// Check session and authorization 
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
    $conn = getDBConnection($servername, $username, $password, $database, $port);
    
    // Call stored procedure to get branch revenue
    $query = "CALL GetBranchRevenue(?)";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param('i', $branch_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        $revenue = $row['total_revenue'] ?? 0;
        $branch_name = $row['branch_name'] ?? 'Unknown Branch';
    } else {
        $revenue = 0;
        $branch_name = 'Branch Not Found';
    }
    
    echo json_encode([
        'success' => true,
        'revenue' => $revenue,
        'branch_id' => $branch_id,
        'branch_name' => $branch_name
    ]);
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>