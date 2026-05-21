<?php
session_start();
require_once "config/db.php";

if (!isset($_SESSION["user_id"])) {
    header("Location: index.html#account");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");

    if ($full_name === "") {
        $message = "Name is required.";
    } else {
        $stmt = $conn->prepare("UPDATE users SET full_name = ?, phone = ? WHERE user_id = ?");
        $stmt->bind_param("ssi", $full_name, $phone, $user_id);
        $stmt->execute();

        $_SESSION["full_name"] = $full_name;
        $message = "Profile updated successfully.";
    }

    if (isset($_FILES["profile_photo"]) && $_FILES["profile_photo"]["error"] === UPLOAD_ERR_OK) {
        $allowed_extensions = ["jpg", "jpeg", "png"];
        $max_size = 2 * 1024 * 1024;

        $original_name = $_FILES["profile_photo"]["name"];
        $file_size = $_FILES["profile_photo"]["size"];
        $tmp_name = $_FILES["profile_photo"]["tmp_name"];
        $extension = strtolower(pathinfo($original_name, PATHINFO_EXTENSION));

        if (!in_array($extension, $allowed_extensions)) {
            $message = "Only JPG, JPEG, and PNG files are allowed.";
        } elseif ($file_size > $max_size) {
            $message = "File size must be less than 2MB.";
        } else {
            $upload_dir = __DIR__ . "/uploads/profiles/";

            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            $new_name = "profile_" . $user_id . "_" . time() . "." . $extension;
            $server_path = $upload_dir . $new_name;
            $database_path = "uploads/profiles/" . $new_name;

            if (move_uploaded_file($tmp_name, $server_path)) {
                $stmt = $conn->prepare("UPDATE users SET profile_photo = ? WHERE user_id = ?");
                $stmt->bind_param("si", $database_path, $user_id);
                $stmt->execute();

                $stmt = $conn->prepare("
                    INSERT INTO uploads (user_id, file_name, file_path, file_type, file_size)
                    VALUES (?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("isssi", $user_id, $new_name, $database_path, $extension, $file_size);
                $stmt->execute();

                $message = "Profile photo uploaded successfully.";
            } else {
                $message = "Upload failed. Please try again.";
            }
        }
    }
}

$stmt = $conn->prepare("SELECT full_name, email, phone, role, profile_photo FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();

$stmt = $conn->prepare("
    SELECT b.*, r.room_name, a.activity_name
    FROM bookings b
    LEFT JOIN rooms r ON b.room_id = r.room_id
    LEFT JOIN activities a ON b.activity_id = a.activity_id
    WHERE b.user_id = ?
    ORDER BY b.created_at DESC
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$bookings = $stmt->get_result();
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>My Profile • Red Sea Escapes</title>
  <link rel="stylesheet" href="global/main.css">
  <style>
    .profile-grid{display:grid;grid-template-columns:360px 1fr;gap:22px}
    .profile-img{width:150px;height:150px;border-radius:50%;object-fit:cover;border:4px solid #8adce6;margin:0 auto 16px}
    .profile-placeholder{width:150px;height:150px;border-radius:50%;background:#e0f7fa;display:grid;place-items:center;margin:0 auto 16px;font-size:42px;font-weight:800}
    .profile-name{text-align:center;margin:0}
    .profile-email{text-align:center;margin-top:8px;color:#5b667a}
    .profile-table{width:100%;border-collapse:collapse}
    .profile-table th,.profile-table td{padding:14px;border-bottom:1px solid #e5e7eb;text-align:left}
    .profile-table th{background:#f8fafc}
    .msg{padding:14px;border-radius:16px;background:#e0f2fe;color:#075985;font-weight:800;margin-bottom:18px;text-align:center}
    .danger{background:#fee2e2!important;color:#991b1b!important;border-color:#fecaca!important}
    @media(max-width:900px){.profile-grid{grid-template-columns:1fr}}
  </style>
</head>

<body>

<div class="navWrap">
  <div class="container nav">
    <a class="brand" href="index.html"><i class="dot"></i><span>Red Sea Escapes</span></a>
    <nav class="navLinks">
      <a href="index.html">Home</a>
      <a href="pages/services.html">Services</a>
      <a class="active" href="profile.php">My Profile</a>
      <a href="server/logout.php">Logout</a>
    </nav>
  </div>
</div>

<header class="hero heroSimple" style="min-height:40vh;">
  <div class="hero__bg" style="background-image:url('images/hero.jpg');"></div>
  <div class="hero__overlay"></div>
  <div class="hero__content">
    <div class="container hero__inner center">
      <h1 class="h1">My Profile</h1>
      <p class="hero__sub">View your account information, upload a profile photo, and check your bookings.</p>
    </div>
  </div>
</header>

<main class="section">
  <div class="container">

    <?php if($message): ?>
      <div class="msg"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <div class="profile-grid">

      <section class="panel">
        <?php if(!empty($user["profile_photo"])): ?>
          <img class="profile-img" src="<?= htmlspecialchars($user["profile_photo"]) ?>" alt="Profile Photo">
        <?php else: ?>
          <div class="profile-placeholder"><?= strtoupper(substr($user["full_name"], 0, 1)) ?></div>
        <?php endif; ?>

        <h2 class="profile-name"><?= htmlspecialchars($user["full_name"]) ?></h2>
        <p class="profile-email"><?= htmlspecialchars($user["email"]) ?></p>

        <form method="POST" enctype="multipart/form-data">
          <div class="formgrid" style="grid-template-columns:1fr;">
            <label>
              Full Name
              <input type="text" name="full_name" value="<?= htmlspecialchars($user["full_name"]) ?>" required>
            </label>

            <label>
              Phone
              <input type="text" name="phone" value="<?= htmlspecialchars($user["phone"] ?? "") ?>" placeholder="05xxxxxxxx">
            </label>

            <label>
              Profile Photo
              <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png">
            </label>
          </div>

          <div class="notice">Allowed: JPG, JPEG, PNG only. Max size: 2MB.</div>

          <div class="hero-actions" style="justify-content:center;margin-top:16px;">
            <button class="btn brand" type="submit">Save Profile</button>
          </div>
        </form>
      </section>

      <section class="panel">
        <h2>My Bookings</h2>

        <?php if($bookings->num_rows === 0): ?>
          <p class="lead">You do not have any bookings yet.</p>
        <?php else: ?>
          <table class="profile-table">
            <tr>
              <th>Booking</th>
              <th>Date</th>
              <th>People</th>
              <th>Total</th>
              <th>Status</th>
              <th>Action</th>
            </tr>

            <?php while($b = $bookings->fetch_assoc()): ?>
              <tr>
                <td>
                  <?= $b["booking_type"] === "room" ? htmlspecialchars($b["room_name"]) : htmlspecialchars($b["activity_name"]) ?>
                  <br><small><?= htmlspecialchars($b["booking_type"]) ?></small>
                </td>

                <td><?= htmlspecialchars($b["start_date"]) ?> to <?= htmlspecialchars($b["end_date"]) ?></td>

                <td>
                  <?php if($b["booking_type"] === "room"): ?>
                    Adults: <?= htmlspecialchars($b["adults"]) ?> |
                    Children: <?= htmlspecialchars($b["children"]) ?>
                  <?php else: ?>
                    Participants: <?= htmlspecialchars($b["participants"]) ?>
                  <?php endif; ?>
                </td>

                <td><?= htmlspecialchars($b["total_price"]) ?> SAR</td>

                <td><?= htmlspecialchars($b["status"]) ?></td>

                <td>
                  <a class="btn danger"
                     href="server/cancel_booking.php?id=<?= $b["booking_id"] ?>"
                     onclick="return confirm('Cancel this booking?')">
                     Cancel
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          </table>
        <?php endif; ?>
      </section>

    </div>
  </div>
</main>

</body>
</html>