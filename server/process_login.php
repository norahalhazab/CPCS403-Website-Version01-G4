<?php
session_start();

require_once __DIR__ . "/../config/db.php";

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

if ($email === "" || $password === "") {
    echo "Email and password are required.";
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Invalid email address.";
    exit;
}

$stmt = $conn->prepare("
    SELECT user_id, full_name, email, password_hash, role, is_active
    FROM users
    WHERE email = ?
    LIMIT 1
");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Invalid email or password.";
    exit;
}

$user = $result->fetch_assoc();

if ((int)$user["is_active"] !== 1) {
    echo "This account is disabled.";
    exit;
}

// Secure password verification against password_hash().
if (!password_verify($password, $user["password_hash"])) {
    echo "Invalid email or password.";
    exit;
}

// Prevent session fixation.
session_regenerate_id(true);

$_SESSION["user_id"] = $user["user_id"];
$_SESSION["full_name"] = $user["full_name"];
$_SESSION["email"] = $user["email"];
$_SESSION["role"] = $user["role"];

if ($user["role"] === "admin") {
    echo "admin";
} else {
    echo "user";
}
?>
