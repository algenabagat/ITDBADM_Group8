<?php
$servername = "127.0.0.1";
$username = "student1";
$password = "Dlsu1234!";
$database = "watch_db";
$port = 3309;
$conn = new mysqli($servername, $username, $password, $database, $port);

if ($conn->connect_error) {
   die("Connection failed: " . $conn->connect_error);
} else {
   echo "Database connected successfully!";
}
?>