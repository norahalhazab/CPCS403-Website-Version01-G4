<?php
require_once "../config/db.php";

header("Content-Type: application/json");

// Get filter values from the URL
$date = $_GET["date"] ?? date("Y-m-d");
$type = $_GET["type"] ?? "all";
$age = $_GET["age"] ?? "all";
$time = $_GET["time"] ?? "all"; // Get the time filter value
$query = trim($_GET["query"] ?? ""); 

$sql = "
SELECT 
  a.activity_id,
  a.activity_name,
  a.category,
  a.min_age,
  a.image_path,
  ats.slot_time,
  ats.max_people,
  COALESCE(SUM(b.participants), 0) AS booked_people
FROM activity_time_slots ats
JOIN activities a ON ats.activity_id = a.activity_id
LEFT JOIN bookings b 
  ON b.activity_id = ats.activity_id
  AND b.time_slot = ats.slot_time
  AND b.start_date = ?
  AND b.booking_type = 'activity'
  AND b.status = 'confirmed'
WHERE ats.is_active = 1
AND a.is_active = 1
";

$params = [$date];
$types = "s";

// Filter by activity category
if ($type !== "all") {
    $sql .= " AND a.category = ?";
    $params[] = $type;
    $types .= "s";
}

// Filter by minimum age
if ($age !== "all") {
    $sql .= " AND a.min_age = ?";
    $params[] = (int)$age;
    $types .= "i";
}

// Filter by time slot
if ($time !== "all") {
    $sql .= " AND ats.slot_time LIKE ?";
    $params[] = $time . "%"; // e.g., "09:00%" matches "09:00:00" in the database
    $types .= "s";
}

// Filter by activity name (partial match using LIKE)
if ($query !== "") {
    $sql .= " AND a.activity_name LIKE ?";
    $params[] = "%" . $query . "%";
    $types .= "s";
}

$sql .= "
GROUP BY ats.slot_id
ORDER BY a.category, a.activity_name, ats.slot_time
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$result = $stmt->get_result();
$data = [];

while ($row = $result->fetch_assoc()) {
    $remaining = (int)$row["max_people"] - (int)$row["booked_people"];

    if ($remaining <= 0) {
        $status = "Full";
        $disabled = true;
    } elseif ($remaining <= 3) {
        $status = "Limited";
        $disabled = false;
    } else {
        $status = "Available";
        $disabled = false;
    }

    $data[] = [
        "activity_id" => $row["activity_id"],
        "activity_name" => $row["activity_name"],
        "category" => $row["category"],
        "min_age" => $row["min_age"],
        "image_path" => $row["image_path"],
        "slot_time" => substr($row["slot_time"], 0, 5),
        "max_people" => (int)$row["max_people"],
        "booked_people" => (int)$row["booked_people"],
        "remaining" => max(0, $remaining),
        "status" => $status,
        "disabled" => $disabled
    ];
}

echo json_encode($data);
?>