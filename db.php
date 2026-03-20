<?php
// db.php - Database connection
$host   = 'localhost';
$dbname = 'password_hashing';
$user   = 'root';
$pass   = '';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
