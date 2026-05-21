<?php
session_start();
header("Content-Type: application/json");
require_once "../config/db.php";

$name = trim($_POST["user_name"] ?? "");
$email = trim($_POST["user_email"] ?? "");
$preference = trim($_POST["preference"] ?? "");
$rating = trim($_POST["rating"] ?? "");
$comments = trim($_POST["comments"] ?? "");
$services = isset($_POST["services"]) ? implode(", ", $_POST["services"]) : "";
$user_id = $_SESSION["user_id"] ?? null;

if ($name === "" || $email === "" || $rating === "") {
    echo json_encode(["success" => false, "message" => "Name, email, and rating are required."]);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email address."]);
    exit;
}

$stmt = $conn->prepare("
INSERT INTO feedback
(user_id, user_name, user_email, preference, rating, services, comments)
VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param("issssss", $user_id, $name, $email, $preference, $rating, $services, $comments);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Feedback submitted successfully."]);
} else {
    echo json_encode(["success" => false, "message" => "Feedback failed."]);
}
?>