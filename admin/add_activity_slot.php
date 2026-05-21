<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

$activity_id = intval($_POST["activity_id"] ?? 0);
$slot_time = $_POST["slot_time"] ?? "";
$max_people = intval($_POST["max_people"] ?? 0);

if ($activity_id > 0 && $slot_time !== "" && $max_people > 0) {
    $slot_time_db = strlen($slot_time) === 5 ? $slot_time . ":00" : $slot_time;

    $stmt = $conn->prepare("
        INSERT INTO activity_time_slots (activity_id, slot_time, max_people, is_active)
        VALUES (?, ?, ?, 1)
        ON DUPLICATE KEY UPDATE max_people = VALUES(max_people), is_active = 1
    ");
    $stmt->bind_param("isi", $activity_id, $slot_time_db, $max_people);
    $stmt->execute();
}

header("Location: dashboard.php");
exit;
?>
