<?php
require_once "../config/db.php";

header("Content-Type: application/json");

// Fetch unique minimum ages from the database and sort them
$sql = "SELECT DISTINCT min_age FROM activities WHERE is_active = 1 ORDER BY min_age";
$result = $conn->query($sql);

$ages = [];
while ($row = $result->fetch_assoc()) {
    $ages[] = (int)$row["min_age"];
}

echo json_encode($ages);
?>