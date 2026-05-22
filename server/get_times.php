<?php
require_once "../config/db.php";

header("Content-Type: application/json");

// استعلام لجلب الأوقات بدون تكرار وترتيبها
$sql = "SELECT DISTINCT slot_time FROM activity_time_slots WHERE is_active = 1 ORDER BY slot_time";
$result = $conn->query($sql);

$times = [];
while ($row = $result->fetch_assoc()) {
    // نأخذ أول 5 خانات فقط (مثلاً 09:00 بدلاً من 09:00:00)
    $times[] = substr($row["slot_time"], 0, 5); 
}

echo json_encode($times);
?>