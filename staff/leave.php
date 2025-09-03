<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

$staff_id = $_SESSION['user_id'];
$msg = '';

// Submit leave request
if (isset($_POST['apply_leave'])) {
  $pdo->prepare("INSERT INTO leave_requests (staff_id,start_date,end_date,reason) VALUES (?,?,?,?)")
      ->execute([$staff_id,$_POST['start'],$_POST['end'],$_POST['reason']]);
  $msg = "✅ Leave request submitted!";
}

$requests = $pdo->prepare("SELECT * FROM leave_requests WHERE staff_id=? ORDER BY created_at DESC");
$requests->execute([$staff_id]);
$requests = $requests->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Leave Requests</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Staff</div>
  <nav class="nav">
    <a href="attendance.php">Attendance</a>
    <a href="leave.php">Leave</a>
    <a href="../admin/logout.php">Logout</a>
  </nav>
</header>
<div class="container" style="padding:22px">
  <h2>Leave Requests</h2>
  <?php if($msg): ?><div class="card" style="background:#1a4220"><?= $msg ?></div><?php endif; ?>
  <div class="card">
    <form method="post">
      <label>Start Date<input type="date" name="start" required></label>
      <label>End Date<input type="date" name="end" required></label>
      <label>Reason<textarea name="reason" required></textarea></label>
      <p><button class="btn" name="apply_leave">Apply</button></p>
    </form>
  </div>
  <h3>My Requests</h3>
  <table class="table">
    <tr><th>From</th><th>To</th><th>Status</th></tr>
    <?php foreach($requests as $r): ?>
      <tr>
        <td><?= $r['start_date'] ?></td>
        <td><?= $r['end_date'] ?></td>
        <td><?= ucfirst($r['status']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
</body></html>
