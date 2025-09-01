<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['waiter_id'])){ header("Location: index.php"); exit; }
$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

// Clock in/out
if(isset($_POST['clock_in'])){
  $pdo->prepare("INSERT INTO attendance(user_id,clock_in) VALUES (?,NOW())")->execute([$_SESSION['waiter_id']]);
}
if(isset($_POST['clock_out'])){
  $pdo->prepare("UPDATE attendance SET clock_out=NOW() WHERE user_id=? AND clock_out IS NULL ORDER BY id DESC LIMIT 1")->execute([$_SESSION['waiter_id']]);
}

$st=$pdo->prepare("SELECT * FROM attendance WHERE user_id=? ORDER BY id DESC LIMIT 20");
$st->execute([$_SESSION['waiter_id']]); $rows=$st->fetchAll();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><link rel="stylesheet" href="/assets/style.css"><title>Attendance</title></head>
<body class="container">
  <div class="card" style="max-width:520px;margin:20px auto">
    <h2>Attendance</h2>
    <form method="post" style="display:inline"><button class="btn" name="clock_in">Clock In</button></form>
    <form method="post" style="display:inline"><button class="btn" name="clock_out">Clock Out</button></form>
  </div>
  <div class="card">
    <h3>Recent</h3>
    <table class="table">
      <tr><th>In</th><th>Out</th></tr>
      <?php foreach($rows as $r): ?>
        <tr><td><?= htmlspecialchars($r['clock_in']) ?></td><td><?= htmlspecialchars($r['clock_out'] ?: '—') ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>
</body>
</html>
