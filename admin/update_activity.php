<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

$activity_id = intval($_POST["activity_id"] ?? 0);
$total_slots = intval($_POST["total_slots"] ?? 0);

$stmt = $conn->prepare("UPDATE activities SET total_slots = ? WHERE activity_id = ?");
$stmt->bind_param("ii", $total_slots, $activity_id);
$stmt->execute();

header("Location: dashboard.php");
exit;