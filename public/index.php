<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Moonlight VIP Lounge</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <!-- HEADER -->
  <header class="site-header">
    <div class="logo">🌙 Moonlight</div>
    <nav class="nav">
      <a href="index.php">Home</a>
      <a href="menu.php">Menu</a>
      <a href="blog.php">Events</a>
      <a href="live.php">Live Stream</a>
      <a href="orders.php">Order Online</a>
      <a href="register.php">Sign Up</a>
      <a href="login.php">Login</a>
    </nav>
  </header>

  <!-- HERO with VIDEO -->
  <section class="hero">
    <video autoplay muted loop class="hero-video">
      <source src="https://videos.pexels.com/video-files/2746957/2746957-uhd_3840_2160_30fps.mp4" type="video/mp4">
    </video>
    <div class="hero-overlay">
      <div class="hero-content">
        <h1>Welcome to Moonlight VIP Lounge</h1>
        <p>Luxury • Music • Drinks • Vibes</p>
        <a href="menu.php" class="btn neon-btn">Explore Menu</a>
        <a href="orders.php" class="btn neon-btn">Order Now</a>
      </div>
    </div>
  </section>

  <!-- ABOUT -->
  <section class="about">
    <h2>Why Choose Moonlight?</h2>
    <p>Step into a world of luxury and entertainment. Enjoy exclusive drinks, world-class DJs, and a nightlife experience like no other.</p>
  </section>

  <!-- FOOTER -->
  <footer class="site-footer">
    <p>© <?= date('Y') ?> Moonlight VIP Lounge. All rights reserved.</p>
  </footer>
</body>
</html>