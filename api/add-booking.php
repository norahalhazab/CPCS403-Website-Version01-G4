<?php
/*
 * CPCS403 – Red Sea Escapes
 * File: api/add-booking.php
 * Purpose: Saves an activity booking — called by fetch() — NO page reload
 * Method:  POST (JSON body)
 * Returns: JSON {success, message, booking_id}
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Read JSON body sent by JavaScript fetch()
$body = json_decode(file_get_contents('php://input'), true);
if (!$body) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit;
}

// Get and validate each field
$activityId   = filter_var($body['activity_id']  ?? 0,  FILTER_VALIDATE_INT);
$bookingDate  = $body['booking_date'] ?? '';
$timeSlot     = $body['time_slot']    ?? '';
$participants = filter_var($body['participants']  ?? 1,  FILTER_VALIDATE_INT);
$email        = filter_var($body['email']         ?? '', FILTER_VALIDATE_EMAIL);

if (!$activityId) {
    echo json_encode(['success' => false, 'message' => 'Please select an activity.']);
    exit;
}
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email.']);
    exit;
}
if (!$participants || $participants < 1 || $participants > 20) {
    echo json_encode(['success' => false, 'message' => 'Participants must be 1–20.']);
    exit;
}

// Validate date is today or future
$dateObj = DateTime::createFromFormat('Y-m-d', $bookingDate);
if (!$dateObj || $dateObj < new DateTime('today')) {
    echo json_encode(['success' => false, 'message' => 'Please pick a valid future date.']);
    exit;
}

// Validate time slot
$validSlots = ['09:00', '11:00', '13:00', '15:00', '17:00'];
if (!in_array($timeSlot, $validSlots, true)) {
    echo json_encode(['success' => false, 'message' => 'Invalid time slot.']);
    exit;
}

// Insert into database
$pdo  = getDB();
$stmt = $pdo->prepare(
    "INSERT INTO activity_bookings
        (activity_id, booking_date, time_slot, participants, email, status)
     VALUES
        (:act, :date, :slot, :ppl, :email, 'pending')"
);
$stmt->execute([
    ':act'   => $activityId,
    ':date'  => $bookingDate,
    ':slot'  => $timeSlot,
    ':ppl'   => $participants,
    ':email' => $email,
]);

$bookingId = $pdo->lastInsertId();

// Return success JSON to JavaScript
echo json_encode([
    'success'    => true,
    'message'    => 'Booking confirmed!',
    'booking_id' => (int) $bookingId,
    'date'       => $bookingDate,
    'time'       => $timeSlot,
]);