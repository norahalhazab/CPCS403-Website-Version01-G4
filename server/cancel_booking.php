<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: ../index.html");
    exit;
}

$user_id = $_SESSION["user_id"];

$booking_id = intval($_GET["id"] ?? 0);

$stmt = $conn->prepare("
    DELETE FROM bookings
    WHERE booking_id = ?
    AND user_id = ?
");

$stmt->bind_param("ii", $booking_id, $user_id);
$stmt->execute();

header("Location: ../profile.php");
exit;
?>