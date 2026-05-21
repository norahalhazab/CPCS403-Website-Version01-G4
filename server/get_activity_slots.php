<?php
require_once "../config/db.php";

$activity_id = intval($_GET["activity_id"] ?? 0);

if ($activity_id <= 0) {
    header("Content-Type: application/json");
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT slot_time, max_people
    FROM activity_time_slots
    WHERE activity_id = ?
      AND is_active = 1
    ORDER BY slot_time
");
$stmt->bind_param("i", $activity_id);
$stmt->execute();
$result = $stmt->get_result();

$slots = [];
while ($row = $result->fetch_assoc()) {
    $slots[] = [
        "time" => substr($row["slot_time"], 0, 5),
        "max_people" => intval($row["max_people"])
    ];
}

header("Content-Type: application/json");
echo json_encode($slots);
?>
