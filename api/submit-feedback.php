<?php
/*
 * CPCS403 – Red Sea Escapes
 * File: api/submit-feedback.php
 * Purpose: Receives feedback form data via fetch() — saves to DB — returns JSON
 * Method:  POST (JSON body sent by JavaScript)
 * Returns: JSON {success, message}
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../config/db.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Method not allowed.']);
    exit;
}

// Read the JSON body that JavaScript sends
$body = json_decode(file_get_contents('php://input'), true);

if (!$body) {
    echo json_encode(['success' => false, 'message' => 'Invalid data.']);
    exit;
}

// Validate fields
$name     = trim($body['name']     ?? '');
$email    = trim($body['email']    ?? '');
$rating   = trim($body['rating']   ?? '');
$comments = trim($body['comments'] ?? '');

if (!$name || !$email || !$rating) {
    echo json_encode(['success' => false, 'message' => 'Name, email and rating are required.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address.']);
    exit;
}

// Save to database using a prepared statement (prevents SQL injection)
$pdo  = getDB();
$stmt = $pdo->prepare(
    "INSERT INTO feedback (name, email, rating, comments, created_at)
     VALUES (:name, :email, :rating, :comments, NOW())"
);
$stmt->execute([
    ':name'     => htmlspecialchars($name),
    ':email'    => $email,
    ':rating'   => $rating,
    ':comments' => htmlspecialchars($comments),
]);

// Return JSON success
echo json_encode([
    'success' => true,
    'message' => "Thank you $name! Your feedback was submitted.",
]);