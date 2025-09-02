<?php
session_start();
require_once "../lib/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] == "customer") {
    header("Location: login.php");
    exit;
}

$message = "";

// Apply for leave
if (isset($_POST["apply_leave"])) {
    $userId = $_SESSION["user_id"];
    $start = $_POST["start_date"];
    $end = $_POST["end_date"];
    $reason = $_POST["reason"];

    $stmt = $conn->prepare("INSERT INTO leave_requests (user_id, start_date, end_date, reason) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $userId, $start, $end, $reason);
    if ($stmt->execute()) {
        $message = "✅ Leave request submitted!";
    }
}

// Fetch user leaves
$userId = $_SESSION["user_id"];
$leaves = $conn->query("SELECT * FROM leave_requests WHERE user_id = $userId ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Leave Request - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight</div>
    <nav class="nav">
      <a href="leave.php" class="active">Leave</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Apply for Leave</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <form method="post">
      <input type="date" name="start_date" required>
      <input type="date" name="end_date" required>
      <textarea name="reason" placeholder="Reason for leave" required></textarea>
      <button type="submit" name="apply_leave" class="btn">Submit</button>
    </form>

    <h3>My Leave Requests</h3>
    <table class="order-table">
      <tr><th>Start</th><th>End</th><th>Reason</th><th>Status</th></tr>
      <?php while ($l = $leaves->fetch_assoc()): ?>
        <tr>
          <td><?= $l["start_date"] ?></td>
          <td><?= $l["end_date"] ?></td>
          <td><?= $l["reason"] ?></td>
          <td><?= ucfirst($l["status"]) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>