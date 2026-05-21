<?php
/*
 * CPCS403 – Red Sea Escapes
 * File: api/delete-booking.php
 * Purpose: Admin deletes a booking row — called by fetch() — no page reload
 * Method:  POST (JSON body with id)
 * Returns: JSON {success, message}
 */

header('Content-Type: application/json');
session_start();
require_once __DIR__ . '/../config/db.php';

// Block anyone who is not an admin
if (empty($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Admin access only.']);
    exit;
}

$body = json_decode(file_get_contents('php://input'), true);
$id   = filter_var($body['id'] ?? 0, FILTER_VALIDATE_INT);

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Invalid ID.']);
    exit;
}

$pdo  = getDB();
$stmt = $pdo->prepare("DELETE FROM activity_bookings WHERE id = :id");
$stmt->execute([':id' => $id]);

if ($stmt->rowCount() > 0) {
    echo json_encode(['success' => true, 'message' => 'Booking deleted.']);
} else {
    echo json_encode(['success' => false, 'message' => 'Booking not found.']);
}