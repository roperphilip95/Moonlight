<?php // public/_partials_header.php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<header class="site-header container">
  <div class="logo">Moonlight</div>
  <input id="nav-toggle" type="checkbox" style="display:none">
  <label for="nav-toggle" class="btn" style="margin-left:auto;display:none" id="burger">Menu</label>
  <nav class="nav" id="main-nav">
    <a href="/public/index.php">Home</a>
    <a href="/public/menu.php">Menu</a>
    <a href="/public/blog.php">Events</a>
    <a href="/public/gallery.php">Gallery</a>
    <a href="/public/live.php">Live</a>
    <a href="/public/contact.php">Contact</a>
    <?php if (!empty($_SESSION['user_id'])): ?>
      <a class="btn" href="/public/profile.php">My Account</a>
    <?php else: ?>
      <a class="btn" href="/public/login.php">Login</a>
    <?php endif; ?>
  </nav>
</header>
<style>
@media (max-width:820px){
  #burger{display:block}
  .nav{display:none;position:absolute;right:12px;left:12px;top:64px;background:#0f0f1a;border:1px solid #1e1e2e;border-radius:12px;padding:10px}
  #nav-toggle:checked ~ #main-nav{display:grid;gap:6px}
  .site-header{position:relative}
}
</style>
