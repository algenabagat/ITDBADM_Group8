<?php
require_once 'config.php';

// Test the connection using the actual variables from config.php
$conn = getDBConnection($host, $user, $password, $database, $port);

if ($conn) {
    echo "✓ Database connection successful!<br>";
    echo "Connected to database: " . $database . "<br>";
    echo "Server: " . $host . ":" . $port;
    
    // Close connection
    $conn->close();
} else {
    echo "✗ Connection failed!";
}
?>