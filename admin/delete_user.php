<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

$user_id = intval($_GET["id"] ?? 0);

$stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role != 'admin'");
$stmt->bind_param("i", $user_id);
$stmt->execute();

header("Location: dashboard.php");
exit;