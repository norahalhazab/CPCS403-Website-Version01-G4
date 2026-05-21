<?php
// Database connection for Red Sea Escapes.
// This file creates $conn, which is used by the PHP backend files.

$host = "localhost";
$user = "root";
$password = "";
$database = "red_sea_escapes";

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");
?>
