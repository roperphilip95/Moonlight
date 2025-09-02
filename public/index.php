<?php
require_once "../lib/config.php";

// Fetch settings
$settings = [];
$result = $conn->query("SELECT * FROM site_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row["setting_key"]] = $row["setting_value"];
}

// Fetch gallery
$gallery = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 12");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= $settings['site_name'] ?? 'Moonlight Club' ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/style.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    /* Hero Slider */
    .hero {
      position: relative;
      overflow: hidden;
      height: 90vh;
    }
    .slides {
      display: flex;
      width: 300%;
      animation: slide 12s infinite;
    }
    .slides img {
      width: 100%;
      height: 90vh;
      object-fit: cover;
    }
    @keyframes slide {
      0% { transform: translateX(0); }
      33% { transform: translateX(-100%); }
      66% { transform: translateX(-200%); }
      100% { transform: translateX(0); }
    }

    /* Gallery */
    .gallery {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 15px;
      padding: 20px;
    }
    .gallery img {
      width: 100%;
      height: 200px;
      object-fit: cover;
      border-radius: 8px;
      transition: transform 0.3s;
    }
    .gallery img:hover {
      transform: scale(1.05);
    }

    /* Call-to-action */
    .cta {
      text-align: center;
      padding: 40px 20px;
    }
    .cta h2 {
      margin-bottom: 10px;
    }
    .btn-login {
      background: #222;
      color: #fff;
      padding: 12px 24px;
      text-decoration: none;
      border-radius: 6px;
      transition: 0.3s;
    }
    .btn-login:hover {
      background: #444;
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="site-header">
    <div class="logo">🌙 <?= $settings['site_name'] ?? 'Moonlight Club' ?></div>
    <nav class="nav">
      <a href="#gallery">Gallery</a>
      <a href="menu.php">Menu</a>
      <a href="blog.php">Blog</a>
      <a href="contact.php">Contact</a>
      <a href="login.php" class="btn-login"><i class="fa fa-sign-in-alt"></i> Login</a>
    </nav>
  </header>

  <!-- Hero Slider -->
  <?php $slider = $conn->query("SELECT * FROM slider ORDER BY created_at DESC LIMIT 5"); ?>
<section class="hero">
  <div class="slides">
    <?php while ($s = $slider->fetch_assoc()): ?>
      <img src="../<?= $s['image_url'] ?>" alt="<?= $s['caption'] ?>">
    <?php endwhile; ?>
  </div>
  <div class="hero-text">
    <h1><?= $settings['homepage_heading'] ?? 'Welcome to Moonlight' ?></h1>
    <p><?= $settings['homepage_subtitle'] ?? 'Experience nightlife like never before' ?></p>
  </div>
</section>
  <!-- Gallery -->
  <section id="gallery">
    <h2 style="text-align:center; margin-top:20px;">Our Gallery</h2>
    <div class="gallery">
      <?php while ($g = $gallery->fetch_assoc()): ?>
        <div>
          <img src="../<?= $g['image_url'] ?>" alt="<?= $g['caption'] ?>">
        </div>
      <?php endwhile; ?>
    </div>
  </section>

  <!-- Call to Action -->
  <section class="cta">
    <h2>Join Us at Moonlight</h2>
    <p>Reserve your table today and enjoy premium nightlife.</p>
    <a href="login.php" class="btn-login">Login / Register</a>
  </section>

  <!-- Footer -->
  <footer style="text-align:center; padding:20px;">
    <p>📍 <?= $settings['contact_phone'] ?? '+000000000' ?> | ✉️ <?= $settings['contact_email'] ?? 'info@moonlight.com' ?></p>
    <p>&copy; <?= date("Y") ?> Moonlight Club. All Rights Reserved.</p>
  </footer>
</body>
</html>