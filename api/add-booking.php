<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Please login first."]);
    exit;
}

$user_id = $_SESSION["user_id"];

$activity_id = intval($_POST["activity_id"] ?? 0);
$date = $_POST["activity_date"] ?? "";
$slot_time = $_POST["slot_time"] ?? "";
$participants = intval($_POST["participants"] ?? 1);
$user_age = intval($_POST["user_age"] ?? 0);

if ($activity_id <= 0  $date === ""  $slot_time === ""  $participants <= 0  $user_age <= 0) {
    echo json_encode(["success" => false, "message" => "Please complete all fields."]);
    exit;
}

$stmt = $conn->prepare("
SELECT activity_name, min_age, price_per_person
FROM activities
WHERE activity_id = ? AND is_active = 1
");
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    echo json_encode(["success" => false, "message" => "Activity not found."]);
    exit;
}

if ($user_age < $activity["min_age"]) {
    echo json_encode([
        "success" => false,
        "message" => "Sorry, this activity requires age " . $activity["min_age"] . "+."
    ]);
    exit;
}

$stmt = $conn->prepare("
SELECT max_people
FROM activity_time_slots
WHERE activity_id = ? AND slot_time = ? AND is_active = 1
");
$stmt->bind_param("is", $activity_id, $slot_time);
$stmt->execute();
$slot = $stmt->get_result()->fetch_assoc();

if (!$slot) {
    echo json_encode(["success" => false, "message" => "This time slot is not available."]);
    exit;
}

$stmt = $conn->prepare("
SELECT COALESCE(SUM(participants), 0) AS booked
FROM bookings
WHERE activity_id = ?
AND start_date = ?
AND time_slot = ?
AND booking_type = 'activity'
AND status = 'confirmed'
");
$stmt->bind_param("iss", $activity_id, $date, $slot_time);
$stmt->execute();
$booked = (int)$stmt->get_result()->fetch_assoc()["booked"];

$remaining = (int)$slot["max_people"] - $booked;

if ($participants > $remaining) {
    echo json_encode([
        "success" => false,
        "message" => "Only $remaining slot(s) left for this time."
    ]);
    exit;
}

$total_price = $participants * $activity["price_per_person"];
$status = "confirmed";

$stmt = $conn->prepare("
INSERT INTO bookings
(user_id, booking_type, activity_id, start_date, end_date, time_slot, participants, user_age, total_price, status)
VALUES (?, 'activity', ?, ?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "iisssiids",
    $user_id,
    $activity_id,
    $date,
    $date,
    $slot_time,
    $participants,
    $user_age,
    $total_price,
    $status
);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "Activity booked successfully.",
        "booking_id" => $stmt->insert_id
    ]);
} else {
    echo json_encode(["success" => false, "message" => "Booking failed."]);
}
?>