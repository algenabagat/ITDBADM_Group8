<?php 
/* Change my_username/my_password of ur database here */
$host = "localhost";
$user = "root";
$password = "DLSU1234!";
$database = "watch_db";
$port = 3306;

function getDBConnection($host, $user, $password, $database, $port) {
    $conn = new mysqli($host, $user, $password, $database, $port);
    
    if ($conn->connect_error) {
        return false;
    }
    return $conn;
}

// Test connection directly in config
$test_conn = new mysqli($host, $user, $password, $database, $port);
if ($test_conn->connect_error) {
    die("Connection failed: " . $test_conn->connect_error);
}
echo "✅ Connected successfully to MySQL database!";
$test_conn->close();
?>