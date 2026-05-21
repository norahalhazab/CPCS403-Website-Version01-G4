<?php
include "../config/db.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST["user_name"];
    $email = $_POST["user_email"];
    $preference = $_POST["preference"];
    $rating = $_POST["rating"];
    $comments = $_POST["comments"];
    $services = isset($_POST["services"]) ? implode(", ", $_POST["services"]) : "";

    $stmt = $conn->prepare(
        "INSERT INTO feedback (name, email, preference, rating, services, comments)
         VALUES (?, ?, ?, ?, ?, ?)"
    );

    $stmt->bind_param("ssssss", $name, $email, $preference, $rating, $services, $comments);

    if ($stmt->execute()) {
        echo "Feedback submitted successfully!";
    } else {
        echo "Error saving feedback.";
    }
}
?>