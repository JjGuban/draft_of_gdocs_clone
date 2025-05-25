<?php
// DO NOT put session_start() here
$host = 'localhost';
$dbname = 'google_docs_db';
$username = 'root';
$password = '';

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
