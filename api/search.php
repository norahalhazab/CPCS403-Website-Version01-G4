<?php
header("Content-Type: application/json");
require_once "../config/db.php";

$q = trim($_GET["q"] ?? "");
$category = trim($_GET["category"] ?? "all");

$sql = "
SELECT activity_id, activity_name, category, description, price_per_person, min_age, image_path
FROM activities
WHERE is_active = 1
";

$params = [];
$types = "";

if ($q !== "") {
    $sql .= " AND (activity_name LIKE ? OR description LIKE ?)";
    $like = "%" . $q . "%";
    $params[] = $like;
    $params[] = $like;
    $types .= "ss";
}

if ($category !== "all") {
    $sql .= " AND category = ?";
    $params[] = $category;
    $types .= "s";
}

$sql .= " ORDER BY activity_name LIMIT 10";

$stmt = $conn->prepare($sql);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode([
    "success" => true,
    "count" => count($data),
    "results" => $data
]);
?>