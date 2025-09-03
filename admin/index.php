<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin — Dashboard</title><link rel="stylesheet" href="/assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Admin</div>
  <nav class="nav"><a href="index.php">Dashboard</a><a href="logout.php">Logout</a></nav>
</header>
<div class="container" style="padding:22px">
  <div class="card">
    <h2>Welcome</h2>
    <p class="muted">You are logged in as <?= e($_SESSION['role'] ?? 'user') ?>. Next: we will add menu management, orders, reservations management and dashboards.</p>
    <p><a class="btn" href="/public/index.php">View Site</a></p>
  </div>
</div>
</body></html>
