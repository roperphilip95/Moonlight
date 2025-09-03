<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])) { header("Location: ../public/login.php"); exit; }

$res = $pdo->query("SELECT * FROM reservations ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Reservations</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Admin</div>
  <nav class="nav">
    <a href="index.php">Dashboard</a>
    <a href="menu.php">Menu</a>
    <a href="blog.php">Blog</a>
    <a href="reservations.php">Reservations</a>
    <a href="logout.php">Logout</a>
  </nav>
</header>
<div class="container" style="padding:22px">
  <h2>Reservations</h2>
  <table class="table">
    <tr><th>Name</th><th>Guests</th><th>Date</th><th>Time</th><th>Status</th></tr>
    <?php foreach($res as $r): ?>
      <tr>
        <td><?= e($r['name']) ?> (<?= e($r['phone']) ?>)</td>
        <td><?= e($r['guests']) ?></td>
        <td><?= e($r['date']) ?></td>
        <td><?= e($r['time']) ?></td>
        <td><?= e($r['status']) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>
</body></html>
