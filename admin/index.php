<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit; }
$users = $pdo->query("SELECT u.id,u.name,u.email,r.name as role FROM users u JOIN roles r ON r.id=u.role_id ORDER BY u.id DESC LIMIT 10");
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin — Dashboard</title>
<link rel="stylesheet" href="../assets/style.css"></head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight Admin</div>
  <nav class="nav"><a href="index.php">Dashboard</a><a href="logout.php">Logout</a></nav>
</header>
<div class="container" style="padding:22px">
  <div class="card">
    <h2>Welcome</h2>
    <p>You are logged in as <b><?= e($_SESSION['role']) ?></b>.</p>
  </div>

  <div class="card" style="margin-top:20px">
    <h3>Recent Users</h3>
    <table class="table">
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th></tr>
      <?php foreach($users as $u): ?>
        <tr><td><?= $u['id'] ?></td><td><?= e($u['name']) ?></td><td><?= e($u['email']) ?></td><td><?= e($u['role']) ?></td></tr>
      <?php endforeach; ?>
    </table>
  </div>
</div>
</body></html>
