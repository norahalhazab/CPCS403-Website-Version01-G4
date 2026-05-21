<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["user_id"])) {
    echo "Please login first.";
    exit;
}

$user_id = intval($_SESSION["user_id"]);
$activity_id = intval($_POST["activity_id"] ?? 0);
$activity_date = $_POST["activity_date"] ?? "";
$slot_time = $_POST["slot_time"] ?? "";
$participants = intval($_POST["participants"] ?? 1);
$user_age = intval($_POST["user_age"] ?? 0);

if ($activity_id <= 0 || empty($activity_date) || empty($slot_time)) {
    echo "Please complete all booking fields.";
    exit;
}

if ($participants < 1) {
    echo "Participants must be at least 1.";
    exit;
}

if ($user_age < 1) {
    echo "Please enter your age.";
    exit;
}

$today = date("Y-m-d");
if ($activity_date < $today) {
    echo "You cannot book a past date.";
    exit;
}

$slot_time_db = strlen($slot_time) === 5 ? $slot_time . ":00" : $slot_time;

$stmt = $conn->prepare("
    SELECT a.activity_name, a.min_age, a.price_per_person, s.slot_id, s.max_people
    FROM activities a
    JOIN activity_time_slots s ON a.activity_id = s.activity_id
    WHERE a.activity_id = ?
      AND s.slot_time = ?
      AND a.is_active = 1
      AND s.is_active = 1
");
$stmt->bind_param("is", $activity_id, $slot_time_db);
$stmt->execute();
$activity = $stmt->get_result()->fetch_assoc();

if (!$activity) {
    echo "This activity time slot is not available.";
    exit;
}

if ($user_age < intval($activity["min_age"])) {
    echo "Sorry, " . $activity["activity_name"] . " requires age " . intval($activity["min_age"]) . "+.";
    exit;
}

$stmt = $conn->prepare("
    SELECT COUNT(*) AS total
    FROM bookings
    WHERE user_id = ?
      AND booking_type = 'activity'
      AND activity_id = ?
      AND start_date = ?
      AND time_slot = ?
      AND status = 'confirmed'
");
$stmt->bind_param("iiss", $user_id, $activity_id, $activity_date, $slot_time_db);
$stmt->execute();
$user_existing = $stmt->get_result()->fetch_assoc();

if (intval($user_existing["total"]) > 0) {
    echo "You already booked this activity at the same date and time.";
    exit;
}

$stmt = $conn->prepare("
    SELECT COALESCE(SUM(participants), 0) AS booked_people
    FROM bookings
    WHERE booking_type = 'activity'
      AND activity_id = ?
      AND start_date = ?
      AND time_slot = ?
      AND status = 'confirmed'
");
$stmt->bind_param("iss", $activity_id, $activity_date, $slot_time_db);
$stmt->execute();
$booked_people = intval($stmt->get_result()->fetch_assoc()["booked_people"]);

$remaining = intval($activity["max_people"]) - $booked_people;

if ($remaining <= 0) {
    echo "This time slot is fully booked.";
    exit;
}

if ($participants > $remaining) {
    echo "Only " . $remaining . " participant(s) available for this date and time.";
    exit;
}

$total_price = $participants * floatval($activity["price_per_person"]);
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
    $activity_date,
    $activity_date,
    $slot_time_db,
    $participants,
    $user_age,
    $total_price,
    $status
);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "Activity booking failed.";
}
?>
