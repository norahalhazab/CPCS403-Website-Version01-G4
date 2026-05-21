<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

$slot_id = intval($_GET["id"] ?? 0);

if ($slot_id > 0) {
    $stmt = $conn->prepare("DELETE FROM activity_time_slots WHERE slot_id = ?");
    $stmt->bind_param("i", $slot_id);
    $stmt->execute();
}

header("Location: dashboard.php");
exit;
?>
