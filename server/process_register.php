<?php
session_start();

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/send_email.php";

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($name === "" || $email === "" || $password === "") {
    echo "All fields are required.";
    exit;
}

if (!preg_match("/^[A-Za-z ]{2,100}$/", $name)) {
    echo "Name must contain letters only and be at least 2 characters.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
    exit;
}

// Password restrictions for the project security requirement.
if (strlen($password) < 8) {
    echo "Password must be at least 8 characters.";
    exit;
}

if (!preg_match("/[A-Z]/", $password)) {
    echo "Password must include at least one uppercase letter.";
    exit;
}

if (!preg_match("/[a-z]/", $password)) {
    echo "Password must include at least one lowercase letter.";
    exit;
}

if (!preg_match("/[0-9]/", $password)) {
    echo "Password must include at least one number.";
    exit;
}

// Check duplicate email using prepared statement.
$stmt = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    echo "Email already exists.";
    exit;
}

// Secure password hashing. Never store plain-text passwords.
$password_hash = password_hash($password, PASSWORD_DEFAULT);
$role = "user";

$stmt = $conn->prepare("
    INSERT INTO users (full_name, email, password_hash, role, is_active)
    VALUES (?, ?, ?, ?, 1)
");
$stmt->bind_param("ssss", $name, $email, $password_hash, $role);

if (!$stmt->execute()) {
    echo "Registration failed.";
    exit;
}

// Start session after registration.
$_SESSION["user_id"] = $stmt->insert_id;
$_SESSION["full_name"] = $name;
$_SESSION["email"] = $email;
$_SESSION["role"] = "user";

// Email trigger with error handling.
$emailSent = sendWelcomeEmail($email, $name);

if ($emailSent) {
    echo "registered_email_sent";
} else {
    echo "registered_email_failed";
}
?>
