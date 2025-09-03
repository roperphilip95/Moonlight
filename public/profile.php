<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: login.php"); exit; }

$uid = $_SESSION['user_id'];
$st = $pdo->prepare("SELECT * FROM users WHERE id=? LIMIT 1");
$st->execute([$uid]);
$u = $st->fetch();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Profile — Moonlight</title>
<link rel="stylesheet" href="../assets/style.css">
</head><body>
<div class="container" style="padding:22px">
  <div class="card">
    <h2>Welcome, <?= e($u['name']) ?></h2>
    <p>Email: <?= e($u['email']) ?></p>
    <p>Phone: <?= e($u['phone']) ?></p>
    <p>Role: <?= e($_SESSION['role']) ?></p>
    <p><a href="logout.php" class="btn">Logout</a></p>
  </div>
</div>
</body></html>
