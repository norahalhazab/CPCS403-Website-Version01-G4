<?php
$conn = new mysqli("localhost", "root", "", "red_sea_escapes");

if ($conn->connect_error) {
	die("Database connection failed: " . $conn->connect_error);
}
?>
