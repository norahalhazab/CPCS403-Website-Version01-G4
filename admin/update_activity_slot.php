<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

$slot_id = intval($_POST["slot_id"] ?? 0);
$max_people = intval($_POST["max_people"] ?? 0);

if ($slot_id > 0 && $max_people >= 0) {
    $stmt = $conn->prepare("UPDATE activity_time_slots SET max_people = ? WHERE slot_id = ?");
    $stmt->bind_param("ii", $max_people, $slot_id);
    $stmt->execute();
}

header("Location: dashboard.php");
exit;
?>
