<?php
require_once __DIR__ . '/../lib/config.php';

// Load slider images (fall back to a stock banner if empty)
$slides = $pdo->query("SELECT * FROM slider ORDER BY created_at DESC LIMIT 6")->fetchAll();

// Load 8 newest gallery images for the strip
$strip = $pdo->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 8")->fetchAll();

// Load 6 featured menu items (if you want to flag featured later, just add a column)
$menu = $pdo->query("SELECT id,name,price,image_url,description FROM menu_items ORDER BY created_at DESC LIMIT 6")->fetchAll();
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Moonlight — Nightlife Reimagined</title>
<meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="../assets/style.css">
<script defer src="../assets/app.js"></script>
</head>
<body>
<header class="site-header container">
  <div class="logo">Moonlight</div>
  <nav class="nav">
    <a href="index.php">Home</a>
    <a href="menu.php">Menu</a>
    <a href="blog.php">Events</a>
    <a href="gallery.php">Gallery</a>
    <a href="live.php">Live</a>
    <a href="contact.php">Contact</a>
    <a class="btn" href="login.php">Login</a>
  </nav>
</header>

<div class="container" style="margin-top:12px">
  <!-- HERO / SLIDER -->
  <section class="slider-wrap card" aria-label="Moonlight highlights">
    <div class="slides">
      <?php if ($slides): ?>
        <?php foreach($slides as $s): ?>
          <div class="slide">
            <img src="../<?= e($s['image_url']) ?>" alt="<?= e($s['caption'] ?: 'Moonlight') ?>">
            <div class="hero-overlay">
              <h1>Moonlight</h1>
              <?php if ($s['caption']): ?><p><?= e($s['caption']) ?></p><?php else: ?><p>Premium cocktails • Live DJs • VIP Lounge</p><?php endif; ?>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="slide">
          <img src="https://images.unsplash.com/photo-1514525253161-7a46d19cd819?q=80&w=1600&auto=format&fit=crop" alt="Moonlight">
          <div class="hero-overlay">
            <h1>Moonlight</h1>
            <p>Premium cocktails • Live DJs • VIP Lounge</p>
          </div>
        </div>
      <?php endif; ?>
    </div>
    <div class="slider-nav">
      <button class="slider-prev" aria-label="Previous">&#8249;</button>
      <button class="slider-next" aria-label="Next">&#8250;</button>
    </div>
    <div class="slider-dots" aria-label="Slider pagination"></div>
  </section>

  <!-- QUICK ACTIONS -->
  <section class="quick-cards">
    <div class="quick-card">
      <h3>Book a Table</h3>
      <p>Reserve VIP seating for your crew. Fast confirmation.</p>
      <a href="contact.php" class="btn">Reserve Now</a>
    </div>
    <div class="quick-card">
      <h3>See Tonight’s Menu</h3>
      <p>Signature cocktails and chef specials updated daily.</p>
      <a href="menu.php" class="btn">View Menu</a>
    </div>
    <div class="quick-card">
      <h3>Live Stream</h3>
      <p>Catch the vibe on YouTube / Facebook when we go live.</p>
      <a href="live.php" class="btn">Watch Live</a>
    </div>
  </section>

  <!-- FEATURED MENU -->
  <section class="card" style="margin-top:16px">
    <h2>Featured Drinks & Bites</h2>
    <?php if (!$menu): ?>
      <p class="muted">No items yet. Check back soon.</p>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:14px">
        <?php foreach($menu as $m): ?>
          <div class="card" style="padding:0">
            <?php if($m['image_url']): ?>
              <img src="../<?= e($m['image_url']) ?>" alt="<?= e($m['name']) ?>" style="width:100%;height:140px;object-fit:cover;border-top-left-radius:12px;border-top-right-radius:12px">
            <?php endif; ?>
            <div style="padding:12px">
              <h4 style="margin:0 0 6px"><?= e($m['name']) ?></h4>
              <p class="muted" style="min-height:42px"><?= e(mb_strimwidth($m['description'] ?? '', 0, 80, '…')) ?></p>
              <div style="display:flex;justify-content:space-between;align-items:center">
                <b>$<?= number_format($m['price'],2) ?></b>
                <a class="btn" href="menu.php">Order</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

  <!-- GALLERY STRIP -->
  <section class="card" style="margin-top:16px">
    <h2>Inside the Lounge</h2>
    <?php if (!$strip): ?>
      <p class="muted">Gallery coming soon.</p>
    <?php else: ?>
      <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(140px,1fr));gap:10px">
        <?php foreach($strip as $g): ?>
          <img 
            src="../<?= e($g['image_url']) ?>" 
            alt="<?= e($g['caption']) ?>" 
            style="width:100%;height:120px;object-fit:cover;border-radius:10px;cursor:pointer"
            data-lightbox="../<?= e($g['image_url']) ?>"
            data-caption="<?= e($g['caption']) ?>">
        <?php endforeach; ?>
      </div>
      <div style="text-align:center;margin-top:10px">
        <a href="gallery.php" class="btn">Open Gallery</a>
      </div>
    <?php endif; ?>
  </section>
</div>

<?php include __DIR__ . '/_partials_footer.php'; ?>
</body>
</html>
