<?php
// استلام البيانات من النموذج
$name = $_POST['user_name'];
$email = $_POST['user_email'];
$preference = $_POST['preference'];
$comments = $_POST['comments'];

// التأكد إذا تم اختيار التقييم والخدمات
$rating = isset($_POST['rating']) ? $_POST['rating'] : 'Not specified';
$services = isset($_POST['services']) ? implode(", ", $_POST['services']) : 'None';
?>

<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <title>Feedback Received</title>
  <link rel="stylesheet" href="../global/main.css" />
</head>
<body>

  <div class="navWrap">
    <div class="container nav">
      <a class="brand" href="../index.html">
        <i class="dot"></i>
        <span>Red Sea Escapes</span>
      </a>
    </div>
  </div>

  <main class="section">
    <div class="container">
      <div class="panel">
        <div class="section-head center-head">
          <div>
            <h2>Thank You, <?php echo $name; ?>!</h2>
            <p class="lead">Your feedback has been sent successfully.</p>
          </div>
        </div>

        <div class="intro-copy">
          <h3>Your Submitted Data:</h3>
          <ul>
            <li><p class="lead"><b>Email:</b> <?php echo $email; ?></p></li>
            <li><p class="lead"><b>Rating:</b> <?php echo $rating; ?></p></li>
            <li><p class="lead"><b>Services Interested In:</b> <?php echo $services; ?></p></li>
            <li><p class="lead"><b>Preference:</b> <?php echo $preference; ?></p></li>
            <li><p class="lead"><b>Comments:</b> <?php echo $comments; ?></p></li>
          </ul>

          <div class="hero-actions" style="margin-top:20px;">
            <a class="btn brand" href="../index.html">Back to Home</a>
          </div>
        </div>

      </div>
    </div>
  </main>

</body>
</html>