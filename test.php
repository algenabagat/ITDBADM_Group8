<?php 
/* Database Configuration */
$host = "10.2.0.9"; // forwarded localhost
$port = 22009;        // local forwarded port
$user = "student1";
$password = "Dlsu1234!";
$database = "watch_db";



function getDBConnection() {
    global $host, $user, $password, $database, $port;
    
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
    
    try {
        $conn = new mysqli($host, $user, $password, $database, $port);
        
        if ($conn->connect_error) {
            throw new Exception("Connection failed: " . $conn->connect_error);
        }
        
        return $conn;
    } catch (Exception $e) {
        error_log("Database Error: " . $e->getMessage());
        return false;
    }
}

// Test connection
try {
    $test_conn = getDBConnection();
    if ($test_conn) {
        echo "✅ Database connected successfully!";
        $test_conn->close();
    } else {
        echo "❌ Database connection failed.";
    }
} catch (mysqli_sql_exception $e) {
    echo "MySQL Error: " . $e->getMessage();
} catch (Exception $e) {
    echo "General Error: " . $e->getMessage();
}
?>