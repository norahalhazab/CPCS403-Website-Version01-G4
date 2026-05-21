<?php
session_start();
require_once "../config/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

$users = $conn->query("
    SELECT user_id, full_name, email, role, is_active, created_at
    FROM users
    ORDER BY created_at DESC
");

$rooms = $conn->query("
    SELECT rooms.*, resorts.resort_name
    FROM rooms
    JOIN resorts ON rooms.resort_id = resorts.resort_id
    ORDER BY resorts.resort_name, rooms.room_name
");

$activities = $conn->query("
    SELECT *
    FROM activities
    ORDER BY category, activity_name
");

$slots = $conn->query("
    SELECT
        ats.*,
        a.activity_name,
        a.category,
        a.min_age
    FROM activity_time_slots ats
    JOIN activities a ON ats.activity_id = a.activity_id
    ORDER BY a.activity_name, ats.slot_time
");

$bookings = $conn->query("
    SELECT
        b.*,
        u.full_name,
        u.email,
        r.room_name,
        rs.resort_name,
        a.activity_name
    FROM bookings b
    JOIN users u ON b.user_id = u.user_id
    LEFT JOIN rooms r ON b.room_id = r.room_id
    LEFT JOIN resorts rs ON r.resort_id = rs.resort_id
    LEFT JOIN activities a ON b.activity_id = a.activity_id
    ORDER BY b.created_at DESC
");

$totalUsers = $conn->query("SELECT COUNT(*) AS c FROM users")->fetch_assoc()["c"];
$totalRooms = $conn->query("SELECT COUNT(*) AS c FROM rooms")->fetch_assoc()["c"];
$totalActivities = $conn->query("SELECT COUNT(*) AS c FROM activities")->fetch_assoc()["c"];
$totalBookings = $conn->query("SELECT COUNT(*) AS c FROM bookings")->fetch_assoc()["c"];
?>

<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin Dashboard • Red Sea Escapes</title>

<link rel="stylesheet" href="../global/main.css">

<style>
body{
  background:#f6f8fb;
}

.admin-hero{
  min-height:42vh;
}

.admin-wrap{
  padding:56px 0;
}

.admin-grid{
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:14px;
  margin-bottom:24px;
}

.stat-card{
  background:#fff;
  border:1px solid rgba(15,23,42,.12);
  border-radius:22px;
  padding:20px;
  box-shadow:0 14px 36px rgba(2,6,23,.08);
}

.stat-card span{
  color:#5b667a;
  font-weight:700;
  font-size:13px;
}

.stat-card b{
  display:block;
  font-size:34px;
  margin-top:8px;
  letter-spacing:-.03em;
}

.admin-section{
  margin-bottom:28px;
}

.admin-section h2{
  margin-bottom:8px;
}

.table-wrap{
  overflow-x:auto;
}

.admin-table{
  width:100%;
  border-collapse:collapse;
  min-width:850px;
}

.admin-table th{
  background:#f8fafc;
  color:#0f172a;
  padding:16px;
  text-align:left;
  font-weight:800;
  border-bottom:1px solid #e2e8f0;
}

.admin-table td{
  padding:16px;
  border-bottom:1px solid #e2e8f0;
  vertical-align:middle;
}

.admin-table tr:hover{
  background:#f8fafc;
}

.admin-form-row{
  display:grid;
  grid-template-columns:1.2fr 1fr 1fr auto;
  gap:14px;
  align-items:end;
  margin-top:18px;
}

.admin-form-row label{
  display:flex;
  flex-direction:column;
  gap:8px;
  font-size:13px;
  font-weight:800;
  color:#334155;
}

.admin-form-row input,
.admin-form-row select,
.small-input{
  width:100%;
  padding:13px 14px;
  border-radius:16px;
  border:1px solid rgba(15,23,42,.14);
  background:#fff;
  font-size:14px;
  color:#0f172a;
}

.small-input{
  max-width:120px;
}

.inline-form{
  display:flex;
  gap:10px;
  align-items:center;
}

.badge{
  display:inline-flex;
  padding:8px 12px;
  border-radius:999px;
  background:rgba(121,201,208,.18);
  border:1px solid rgba(121,201,208,.45);
  color:#06343c;
  font-weight:800;
  font-size:12px;
}

.btn-danger{
  background:#fee2e2!important;
  color:#991b1b!important;
  border-color:#fecaca!important;
}

.btn-small{
  padding:10px 14px;
  font-size:12px;
}

@media(max-width:980px){
  .admin-grid{
    grid-template-columns:1fr 1fr;
  }

  .admin-form-row{
    grid-template-columns:1fr;
  }
}

@media(max-width:600px){
  .admin-grid{
    grid-template-columns:1fr;
  }
}
</style>
</head>

<body>

<div class="navWrap">
  <div class="container nav">
    <a class="brand" href="../index.html">
      <i class="dot"></i>
      <span>Red Sea Escapes</span>
    </a>

    <nav class="navLinks">
      <a href="../index.html">Home</a>
      <a class="active" href="./dashboard.php">Dashboard</a>
      <a href="../profile.php">Profile</a>
      <a href="../server/logout.php">Logout</a>
    </nav>
  </div>
</div>

<header class="hero heroSimple admin-hero">
  <div class="hero__bg" style="background-image:url('../images/hero.jpg');"></div>
  <div class="hero__overlay"></div>
  <div class="hero__content">
    <div class="container hero__inner center">
      <h1 class="h1">Admin Dashboard</h1>
      <p class="hero__sub">Manage users, resorts, rooms, activities, time slots, and bookings.</p>
    </div>
  </div>
</header>

<main class="admin-wrap">
<div class="container">

  <div class="admin-grid">
    <div class="stat-card">
      <span>Total Users</span>
      <b><?= $totalUsers ?></b>
    </div>

    <div class="stat-card">
      <span>Total Rooms</span>
      <b><?= $totalRooms ?></b>
    </div>

    <div class="stat-card">
      <span>Activities</span>
      <b><?= $totalActivities ?></b>
    </div>

    <div class="stat-card">
      <span>Bookings</span>
      <b><?= $totalBookings ?></b>
    </div>
  </div>

  <section class="admin-section panel">
    <h2>Add Activity Time Slot</h2>
    <p class="lead">
      Select an activity, choose a time, and set the maximum number of people allowed for that time.
      Users will choose the date while booking.
    </p>

    <form action="add_activity_slot.php" method="POST" class="admin-form-row">
      <label>
        Activity
        <select name="activity_id" required>
          <?php
          $activityList = $conn->query("SELECT activity_id, activity_name FROM activities ORDER BY activity_name");
          while($act = $activityList->fetch_assoc()):
          ?>
            <option value="<?= $act["activity_id"] ?>">
              <?= htmlspecialchars($act["activity_name"]) ?>
            </option>
          <?php endwhile; ?>
        </select>
      </label>

      <label>
        Time
        <input type="time" name="slot_time" required>
      </label>

      <label>
        Max People
        <input type="number" name="max_people" min="1" value="10" required>
      </label>

      <button class="btn brand" type="submit">Add / Update Slot</button>
    </form>
  </section>

  <section class="admin-section panel">
    <h2>Activity Time Slots</h2>
    <p class="lead">Edit or delete the available times for each activity.</p>

    <div class="table-wrap">
      <table class="admin-table">
        <tr>
          <th>Activity</th>
          <th>Category</th>
          <th>Min Age</th>
          <th>Time</th>
          <th>Max People</th>
          <th>Update</th>
          <th>Delete</th>
        </tr>

        <?php while($slot = $slots->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($slot["activity_name"]) ?></td>
          <td><span class="badge"><?= htmlspecialchars($slot["category"]) ?></span></td>
          <td><?= htmlspecialchars($slot["min_age"]) ?>+</td>
          <td><?= date("g:i A", strtotime($slot["slot_time"])) ?></td>

          <td>
            <form class="inline-form" action="update_activity_slot.php" method="POST">
              <input type="hidden" name="slot_id" value="<?= $slot["slot_id"] ?>">
              <input class="small-input" type="number" name="max_people" min="1" value="<?= htmlspecialchars($slot["max_people"]) ?>">
          </td>

          <td>
              <button class="btn brand btn-small" type="submit">Save</button>
            </form>
          </td>

          <td>
            <a class="btn btn-danger btn-small"
               href="delete_activity_slot.php?id=<?= $slot["slot_id"] ?>"
               onclick="return confirm('Delete this time slot?')">
              Delete
            </a>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </section>

  <section class="admin-section panel">
    <h2>Room Availability</h2>
    <p class="lead">Control how many rooms are available for each room type.</p>

    <div class="table-wrap">
      <table class="admin-table">
        <tr>
          <th>Resort</th>
          <th>Room</th>
          <th>Price / Night</th>
          <th>Max Adults</th>
          <th>Max Children</th>
          <th>Total Rooms</th>
          <th>Update</th>
        </tr>

        <?php while($room = $rooms->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($room["resort_name"]) ?></td>
          <td><?= htmlspecialchars($room["room_name"]) ?></td>
          <td><?= htmlspecialchars($room["price_per_night"]) ?> SAR</td>
          <td><?= htmlspecialchars($room["max_adults"]) ?></td>
          <td><?= htmlspecialchars($room["max_children"]) ?></td>

          <td>
            <form class="inline-form" action="update_room.php" method="POST">
              <input type="hidden" name="room_id" value="<?= $room["room_id"] ?>">
              <input class="small-input" type="number" name="total_rooms" min="0" value="<?= htmlspecialchars($room["total_rooms"]) ?>">
          </td>

          <td>
              <button class="btn brand btn-small" type="submit">Save</button>
            </form>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </section>

  <section class="admin-section panel">
    <h2>Activities</h2>
    <p class="lead">Current activity information and age restrictions.</p>

    <div class="table-wrap">
      <table class="admin-table">
        <tr>
          <th>Activity</th>
          <th>Category</th>
          <th>Min Age</th>
          <th>Price / Person</th>
          <th>Duration</th>
        </tr>

        <?php while($activity = $activities->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($activity["activity_name"]) ?></td>
          <td><span class="badge"><?= htmlspecialchars($activity["category"]) ?></span></td>
          <td><?= htmlspecialchars($activity["min_age"]) ?>+</td>
          <td><?= htmlspecialchars($activity["price_per_person"]) ?> SAR</td>
          <td><?= htmlspecialchars($activity["duration"]) ?></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </section>

  <section class="admin-section panel">
    <h2>Users</h2>
    <p class="lead">View registered users and delete regular users if needed.</p>

    <div class="table-wrap">
      <table class="admin-table">
        <tr>
          <th>Name</th>
          <th>Email</th>
          <th>Role</th>
          <th>Status</th>
          <th>Joined</th>
          <th>Action</th>
        </tr>

        <?php while($user = $users->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($user["full_name"]) ?></td>
          <td><?= htmlspecialchars($user["email"]) ?></td>
          <td><span class="badge"><?= htmlspecialchars($user["role"]) ?></span></td>
          <td><?= $user["is_active"] ? "Active" : "Disabled" ?></td>
          <td><?= htmlspecialchars($user["created_at"]) ?></td>
          <td>
            <?php if($user["role"] !== "admin"): ?>
              <a class="btn btn-danger btn-small"
                 href="delete_user.php?id=<?= $user["user_id"] ?>"
                 onclick="return confirm('Delete this user?')">
                Delete
              </a>
            <?php else: ?>
              <span class="mini">Protected</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </section>

  <section class="admin-section panel">
    <h2>All Bookings</h2>
    <p class="lead">View all room and activity bookings made by users.</p>

    <div class="table-wrap">
      <table class="admin-table">
        <tr>
          <th>User</th>
          <th>Email</th>
          <th>Type</th>
          <th>Booking</th>
          <th>Date</th>
          <th>Time</th>
          <th>People</th>
          <th>Total</th>
          <th>Status</th>
        </tr>

        <?php while($booking = $bookings->fetch_assoc()): ?>
        <tr>
          <td><?= htmlspecialchars($booking["full_name"]) ?></td>
          <td><?= htmlspecialchars($booking["email"]) ?></td>
          <td><span class="badge"><?= htmlspecialchars($booking["booking_type"]) ?></span></td>

          <td>
            <?php if($booking["booking_type"] === "room"): ?>
              <?= htmlspecialchars($booking["resort_name"] ?? "") ?> -
              <?= htmlspecialchars($booking["room_name"] ?? "") ?>
            <?php else: ?>
              <?= htmlspecialchars($booking["activity_name"] ?? "") ?>
            <?php endif; ?>
          </td>

          <td>
            <?= htmlspecialchars($booking["start_date"]) ?>
            <?php if($booking["booking_type"] === "room"): ?>
              to <?= htmlspecialchars($booking["end_date"]) ?>
            <?php endif; ?>
          </td>

          <td><?= htmlspecialchars($booking["time_slot"] ?? "-") ?></td>

          <td>
            <?php if($booking["booking_type"] === "room"): ?>
              Adults: <?= htmlspecialchars($booking["adults"]) ?> |
              Children: <?= htmlspecialchars($booking["children"]) ?>
            <?php else: ?>
              Participants: <?= htmlspecialchars($booking["participants"]) ?>
              <?php if(isset($booking["user_age"])): ?>
                <br><small>Youngest age: <?= htmlspecialchars($booking["user_age"]) ?></small>
              <?php endif; ?>
            <?php endif; ?>
          </td>

          <td><?= htmlspecialchars($booking["total_price"]) ?> SAR</td>
          <td><span class="badge"><?= htmlspecialchars($booking["status"]) ?></span></td>
        </tr>
        <?php endwhile; ?>
      </table>
    </div>
  </section>

</div>
</main>

</body>
</html>