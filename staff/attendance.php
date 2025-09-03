<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

$staff_id = $_SESSION['user_id'];
$msg = '';

// Clock-in
if (isset($_POST['check_in'])) {
  $pdo->prepare("INSERT INTO attendance (staff_id, check_in) VALUES (?,NOW())")->execute([$staff_id]);
  $msg = "✅ Checked in!";
}

// Clock-out
if (isset($_POST['check_out'])) {
  $pdo->prepare("UPDATE attendance SET check_out=NOW() WHERE staff_id=? AND check_out IS NULL ORDER BY id DESC LIMIT 1")
      ->execute([$staff_id]);
  $msg = "✅ Checked out!";
}

$records = $pdo->prepare("SELECT * FROM attendance WHERE staff_id=? ORDER BY check_in DESC");
$records->execute([$staff_id]);
$records = $records->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Attendance</title>
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
  <h2>Attendance</h2>
  <?php if($msg): ?><div class="card" style="background:#1a4220"><?= $msg ?></div><?php endif; ?>
  <form method="post">
    <button class="btn" name="check_in">Clock In</button>
    <button class="btn" name="check_out">Clock Out</button>
  </form>
  <h3>History</h3>
  <table class="table">
    <tr><th>Check In</th><th>Check Out</th></tr>
    <?php foreach($records as $r): ?>
      <tr>
        <td><?= $r['check_in'] ?></td>
        <td><?= $r['check_out'] ?? '-' ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
</body></html>
