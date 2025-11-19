<?php
session_start();
require_once 'config.php';

// Check session
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['month']) || !isset($data['year'])) {
    echo json_encode(['success' => false, 'message' => 'Missing parameters']);
    exit();
}

$month = intval($data['month']);
$year = intval($data['year']);

// Validate month and year
if ($month < 1 || $month > 12 || $year < 2000 || $year > 2100) {
    echo json_encode(['success' => false, 'message' => 'Invalid month or year']);
    exit();
}

try {
    $conn = getDBConnection($servername, $username, $password, $database, $port);
    
    // Query monthly revenue
    $query = "SELECT COALESCE(SUM(p.amount), 0) as revenue 
            FROM orders o 
            JOIN payments p ON o.order_id = p.order_id 
            WHERE MONTH(o.order_date) = ? AND YEAR(o.order_date) = ? 
            AND p.status = 'Completed'";
    $stmt = $conn->prepare($query);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
        exit();
    }
    
    $stmt->bind_param('ii', $month, $year);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    $revenue = $row['revenue'] ?? 0;
    
    echo json_encode([
        'success' => true,
        'revenue' => $revenue,
        'month' => $month,
        'year' => $year
    ]);
    
    $stmt->close();
    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
