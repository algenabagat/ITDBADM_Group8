<?php 
/* Change my_username/my_password of ur database here */
$servername = "127.0.0.1";
$username = "student1";
$password = "Dlsu1234!";
$database = "watch_db";
$port = 3309;

function getDBConnection($servername, $username, $password, $database, $port) {
    $conn = new mysqli($servername, $username, $password, $database, $port);
    
    if ($conn->connect_error) {
        return false;
    }
    return $conn;
}

// Test connection directly in config
$test_conn = new mysqli($servername, $username, $password, $database, $port);
if ($test_conn->connect_error) {
    die("Connection failed: " . $test_conn->connect_error);
}
$test_conn->close();
?>