<?php

include "../config/db.php";
include "send_email.php";

$name = trim($_POST["name"]);
$email = trim($_POST["email"]);
$password = $_POST["password"];

if ($name == "" || $email == "" || $password == "") {
    echo "Please fill all fields.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
    exit;
}

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// CHECK IF EMAIL ALREADY EXISTS
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
$check->bind_param("s", $email);
$check->execute();

$result = $check->get_result();

if ($result->num_rows > 0) {
    echo "This email is already registered.";
    exit;
}

// INSERT USER
$stmt = $conn->prepare("
    INSERT INTO users (name, email, password, role)
    VALUES (?, ?, ?, 'user')
");

$stmt->bind_param(
    "sss",
    $name,
    $email,
    $hashedPassword
);

if ($stmt->execute()) {

    $emailSent = sendWelcomeEmail($email, $name);

    if ($emailSent) {
        echo "Registration successful! Welcome email sent.";
    } else {
        echo "Registration successful, but welcome email could not be sent.";
    }

} else {

    echo "Registration failed.";

}
?>