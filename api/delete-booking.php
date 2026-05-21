<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    echo json_encode(["success" => false, "message" => "Admin access only."]);
    exit;
}

$body = json_decode(file_get_contents("php://input"), true);
$booking_id = intval($body["booking_id"] ?? 0);

if ($booking_id <= 0) {
    echo json_encode(["success" => false, "message" => "Invalid booking ID."]);
    exit;
}

$stmt = $conn->prepare("DELETE FROM bookings WHERE booking_id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();

echo json_encode([
    "success" => true,
    "message" => "Booking deleted successfully."
]);
?>