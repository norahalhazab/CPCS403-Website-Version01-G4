<?php
session_start();
include "../config/db.php";

$email = trim($_POST["email"]);
$password = $_POST["password"];

$stmt = $conn->prepare("SELECT id, name, email, password, role FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    if (password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];

        echo $user["role"];
    } else {
        echo "Wrong password.";
    }
} else {
    echo "Email not found.";
}
?>