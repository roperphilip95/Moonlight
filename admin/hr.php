<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

$msg = '';

// Approve/reject leave
if (isset($_GET['approve'])) {
  $pdo->prepare("UPDATE leave_requests SET status='approved' WHERE id=?")->execute([$_GET['approve']]);
  $msg = "✅ Leave approved";
}
if (isset($_GET['reject'])) {
  $pdo->prepare("UPDATE leave_requests SET status='rejected' WHERE id=?")->execute([$_GET['reject']]);
  $msg = "❌ Leave rejected";
}

$requests = $pdo->query("
  SELECT l.*, u.name 
  FROM leave_requests l 
  JOIN users u ON u.id=l.staff_id 
  ORDER BY l.created_at DESC
")->fetchAll();

$attendance = $pdo->query("
  SELECT a.*, u.name 
  FROM attendance a 
  JOIN users u ON u.id=a.staff_id 
  ORDER BY a.check_in DESC
")->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>HR Dashboard</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight HR</div>
  <nav class="nav">
    <a href="index.php">Dashboard</a>
    <a href="hr.php">HR</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<div class="container" style="padding:22px">
  <h2>HR Dashboard</h2>
  <?php if($msg): ?><div class="card" style="background:#1a4220"><?= $msg ?></div><?php endif; ?>

  <div class="card">
    <h3>Leave Requests</h3>
    <table class="table">
      <tr><th>Staff</th><th>From</th><th>To</th><th>Reason</th><th>Status</th><th>Action</th></tr>
      <?php foreach($requests as $r): ?>
        <tr>
          <td><?= e($r['name']) ?></td>
          <td><?= $r['start_date'] ?></td>
          <td><?= $r['end_date'] ?></td>
          <td><?= e($r['reason']) ?></td>
          <td><?= ucfirst($r['status']) ?></td>
          <td>
            <a href="?approve=<?= $r['id'] ?>" class="btn">Approve</a>
            <a href="?reject=<?= $r['id'] ?>" class="btn">Reject</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>

  <div class="card" style="margin-top:20px">
    <h3>Attendance Logs</h3>
    <table class="table">
      <tr><th>Staff</th><th>Check In</th><th>Check Out</th></tr>
      <?php foreach($attendance as $a): ?>
        <tr>
          <td><?= e($a['name']) ?></td>
          <td><?= $a['check_in'] ?></td>
          <td><?= $a['check_out'] ?? '-' ?></td>
        </tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
