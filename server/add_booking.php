<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    echo "Please login first.";
    exit;
}

$user_id = $_SESSION["user_id"];

$room_id = intval($_POST["room_id"] ?? 0);

$check_in = $_POST["check_in"] ?? "";
$check_out = $_POST["check_out"] ?? "";

$adults = intval($_POST["adults"] ?? 1);
$children = intval($_POST["children"] ?? 0);

if ($room_id <= 0) {
    echo "Invalid room.";
    exit;
}

if ($check_in === "" || $check_out === "") {
    echo "Please select dates.";
    exit;
}

if ($check_in >= $check_out) {
    echo "Check-out must be after check-in.";
    exit;
}

if ($adults <= 0) {
    echo "At least one adult is required.";
    exit;
}

$stmt = $conn->prepare("
    SELECT total_rooms, price_per_night, max_adults, max_children
    FROM rooms
    WHERE room_id = ?
");

$stmt->bind_param("i", $room_id);
$stmt->execute();

$room = $stmt->get_result()->fetch_assoc();

if (!$room) {
    echo "Room not found.";
    exit;
}

if ($adults > $room["max_adults"]) {
    echo "Maximum adults allowed: " . $room["max_adults"];
    exit;
}

if ($children > $room["max_children"]) {
    echo "Maximum children allowed: " . $room["max_children"];
    exit;
}

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM bookings
    WHERE room_id = ?
    AND booking_type = 'room'
    AND status = 'confirmed'
    AND (
        start_date <= ?
        AND end_date >= ?
    )
");

$stmt->bind_param("iss", $room_id, $check_out, $check_in);
$stmt->execute();

$existing = $stmt->get_result()->fetch_assoc();

if ($existing["total"] >= $room["total_rooms"]) {
    echo "This room is fully booked for the selected dates.";
    exit;
}

$days = (strtotime($check_out) - strtotime($check_in)) / (60 * 60 * 24);

$total_price = $days * $room["price_per_night"];

$status = "confirmed";

$stmt = $conn->prepare("
    INSERT INTO bookings (
        user_id,
        room_id,
        booking_type,
        start_date,
        end_date,
        adults,
        children,
        total_price,
        status
    )
    VALUES (?, ?, 'room', ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iissiids",
    $user_id,
    $room_id,
    $check_in,
    $check_out,
    $adults,
    $children,
    $total_price,
    $status
);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Booking failed.";
}
?>