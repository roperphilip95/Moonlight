<?php
require_once __DIR__ . '/../lib/config.php'; // installer writes this
$site_name = "Moonlight VIP Lounge";
$phone_wa  = "+2348012345678"; // change later in Admin → Customizations (backend)
$base = defined('BASE_URL') ? rtrim(BASE_URL,'/') : '';
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title><?= htmlspecialchars($site_name) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="<?= $base ?>/assets/style.css">
  <script defer src="<?= $base ?>/assets/script.js"></script>
</head>
<body>
<header>
  <div class="nav container">
    <div class="logo">MOON<span>LIGHT</span></div>
    <nav>
      <a href="<?= $base ?>/public/index.php">Home</a>
      <a href="<?= $base ?>/public/menu.php">Menu</a>
      <a href="<?= $base ?>/public/blog.php">Blog</a>
      <a href="<?= $base ?>/public/live.php">Live</a>
      <a href="<?= $base ?>/public/contact.php" class="btn">Reserve</a>
    </nav>
  </div>
</header>
<main class="container">
