<?php
session_start();
require_once "../lib/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "hr") {
    header("Location: ../public/login.php");
    exit;
}

$message = "";

// Mark attendance
if (isset($_POST["mark_attendance"])) {
    $userId = $_POST["user_id"];
    $status = $_POST["status"];
    $today = date("Y-m-d");

    $stmt = $conn->prepare("INSERT INTO attendance (user_id, date, status) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $userId, $today, $status);
    if ($stmt->execute()) {
        $message = "✅ Attendance marked for user!";
    }
}

// Approve/Decline Leave
if (isset($_POST["update_leave"])) {
    $leaveId = $_POST["leave_id"];
    $status = $_POST["status"];
    $stmt = $conn->prepare("UPDATE leave_requests SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $leaveId);
    if ($stmt->execute()) {
        $message = "✅ Leave request updated!";
    }
}

// Fetch staff
$staff = $conn->query("SELECT id, name, role FROM users WHERE role IN ('manager','waiter','finance')");

// Fetch leave requests
$leaves = $conn->query("SELECT l.*, u.name FROM leave_requests l JOIN users u ON l.user_id = u.id ORDER BY l.created_at DESC");

// Fetch attendance records
$attendance = $conn->query("SELECT a.*, u.name FROM attendance a JOIN users u ON a.user_id = u.id ORDER BY a.date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>HR Dashboard - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight HR</div>
    <nav class="nav">
      <a href="hr.php" class="active">Dashboard</a>
      <a href="../public/logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>HR Management</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <h3>Mark Attendance</h3>
    <form method="post">
      <select name="user_id" required>
        <option value="">-- Select Staff --</option>
        <?php while ($s = $staff->fetch_assoc()): ?>
          <option value="<?= $s["id"] ?>"><?= $s["name"] ?> (<?= $s["role"] ?>)</option>
        <?php endwhile; ?>
      </select>
      <select name="status" required>
        <option value="present">Present</option>
        <option value="absent">Absent</option>
        <option value="late">Late</option>
      </select>
      <button type="submit" name="mark_attendance" class="btn">Mark</button>
    </form>

    <h3>Attendance Records</h3>
    <table class="order-table">
      <tr><th>Name</th><th>Date</th><th>Status</th></tr>
      <?php while ($a = $attendance->fetch_assoc()): ?>
        <tr>
          <td><?= $a["name"] ?></td>
          <td><?= $a["date"] ?></td>
          <td><?= ucfirst($a["status"]) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>

    <h3>Leave Requests</h3>
    <table class="order-table">
      <tr><th>Name</th><th>Dates</th><th>Reason</th><th>Status</th><th>Action</th></tr>
      <?php while ($l = $leaves->fetch_assoc()): ?>
        <tr>
          <td><?= $l["name"] ?></td>
          <td><?= $l["start_date"] ?> → <?= $l["end_date"] ?></td>
          <td><?= $l["reason"] ?></td>
          <td><?= ucfirst($l["status"]) ?></td>
          <td>
            <?php if ($l["status"] == "pending"): ?>
              <form method="post" style="display:inline;">
                <input type="hidden" name="leave_id" value="<?= $l["id"] ?>">
                <select name="status" required>
                  <option value="approved">Approve</option>
                  <option value="declined">Decline</option>
                </select>
                <button type="submit" name="update_leave" class="btn">Update</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>