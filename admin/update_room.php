<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

$room_id = intval($_POST["room_id"] ?? 0);
$total_rooms = intval($_POST["total_rooms"] ?? 0);

$stmt = $conn->prepare("UPDATE rooms SET total_rooms = ? WHERE room_id = ?");
$stmt->bind_param("ii", $total_rooms, $room_id);
$stmt->execute();

header("Location: dashboard.php");
exit;