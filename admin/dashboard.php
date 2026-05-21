<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.html");
    exit;
}

include "../config/db.php";
?>

<h1>Admin Dashboard</h1>
<p>Only admins can access this page.</p>

<a href="../server/logout.php">Logout</a>

<h2>Add Item</h2>
<form method="POST">
  <input type="text" name="title" placeholder="Title" required>
  <input type="text" name="category" placeholder="Category" required>
  <textarea name="description" placeholder="Description"></textarea>
  <button type="submit" name="add">Add</button>
</form>

<?php
if (isset($_POST["add"])) {
    $title = $_POST["title"];
    $category = $_POST["category"];
    $description = $_POST["description"];

    $stmt = $conn->prepare("INSERT INTO items (title, category, description) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $title, $category, $description);
    $stmt->execute();

    echo "<p>Item added successfully.</p>";
}

$result = $conn->query("SELECT * FROM items");
?>

<h2>Manage Items</h2>

<?php while ($row = $result->fetch_assoc()) { ?>
  <div style="border:1px solid #ccc; padding:10px; margin:10px;">
    <h3><?php echo $row["title"]; ?></h3>
    <p><?php echo $row["category"]; ?></p>
    <p><?php echo $row["description"]; ?></p>
    <a href="delete_item.php?id=<?php echo $row["id"]; ?>">Delete</a>
  </div>
<?php } ?>