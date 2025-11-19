<?php
session_start();
require_once 'config.php';

$conn = getDBConnection($servername, $username, $password, $database, $port);

echo "<h1>Direct Test</h1>";

// Test the exact query
$result = $conn->query("SELECT SUM(amount) as revenue FROM payments WHERE status='Completed'");
$row = $result->fetch_assoc();

echo "<p>Completed Payments Total: ₱" . number_format($row['revenue'] ?? 0, 2) . "</p>";

echo "<p>Expected: ₱917,000.00</p>";
echo "<p>Got: ₱" . number_format($row['revenue'] ?? 0, 2) . "</p>";

if (($row['revenue'] ?? 0) == 917000) {
    echo "<p style='color: green;'> SUCCESS - Query is working!</p>";
} else {
    echo "<p style='color: red;'> FAILED - Still wrong result</p>";
}
?>