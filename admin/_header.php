<?php
require_once __DIR__ . '/_auth.php';
require_login();
$me = current_user();
$base = defined('BASE_URL') ? rtrim(BASE_URL,'') : '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Moonlight Admin</title>
  <link rel="stylesheet" href="<?= $base ?>/assets/style.css">
  <style>
    .admin-nav{display:flex;gap:12px;align-items:center;background:#111;color:#fff;padding:12px 20px}
    .admin-nav a{color:#fff;text-decoration:none;border:1px solid #333;padding:6px 10px;border-radius:8px}
    .admin-nav a:hover{border-color:#555}
    .wrap{padding:20px}
    .flash{background:#e6ffec;border:1px solid #b7f5c5;padding:10px;border-radius:8px;margin:10px 0;color:#044d1a}
    form .row{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    @media(max-width:900px){ form .row{grid-template-columns:1fr} }
  </style>
</head>
<body>
  <div class="admin-nav">
    <strong>Moonlight Admin</strong>
    <a href="<?= $base ?>/admin/index.php">Dashboard</a>
    <a href="<?= $base ?>/admin/menu.php">Menu</a>
    <a href="<?= $base ?>/admin/finance.php">Finance</a>
    <a href="<?= $base ?>/admin/hr.php">HR</a>
    <a href="<?= $base ?>/admin/blog.php">Blog</a>
    <a href="<?= $base ?>/admin/livestream.php">Livestream</a>
    <a href="<?= $base ?>/admin/customizer.php">Customizer</a>
    <span style="margin-left:auto">Hi, <?= htmlspecialchars($me['name']) ?> (<?= htmlspecialchars($me['role_name']) ?>)</span>
    <a href="<?= $base ?>/admin/logout.php">Logout</a>
  </div>
  <div class="wrap container">
    <?php if($f=flash()): ?><div class="flash"><?= htmlspecialchars($f) ?></div><?php endif; ?>
