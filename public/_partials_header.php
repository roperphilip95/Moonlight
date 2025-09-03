<?php
require_once __DIR__ . '/../lib/config.php';
$site_name = $pdo ? ($pdo->query("SELECT setting_value FROM site_settings WHERE setting_key='site_name'")->fetchColumn() ?? 'Moonlight') : 'Moonlight';
$base = defined('BASE_URL') ? rtrim(BASE_URL,'/') : '';
?>
<header class="site-header container">
  <div class="logo"><?= e($site_name) ?></div>
  <nav class="nav">
    <a href="<?= $base ?>/public/index.php">Home</a>
    <a href="<?= $base ?>/public/menu.php">Menu</a>
    <a href="<?= $base ?>/public/blog.php">Events</a>
    <a href="<?= $base ?>/public/contact.php">Contact</a>
    <a href="<?= $base ?>/public/login.php" class="btn-login">Login</a>
  </nav>
</header>
