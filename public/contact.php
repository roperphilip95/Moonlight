<?php require_once __DIR__ . '/../lib/config.php'; ?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Contact — Moonlight</title>
<link rel="stylesheet" href="../assets/style.css">
<style>
#whatsapp-chat {
  position: fixed; bottom: 20px; right: 20px;
  background: #25d366; color: white;
  padding: 12px 18px; border-radius: 30px;
  font-weight: bold; cursor: pointer;
}
</style>
</head><body>
<header class="site-header container">
  <div class="logo">Moonlight</div>
  <nav class="nav">
    <a href="index.php">Home</a>
    <a href="menu.php">Menu</a>
    <a href="blog.php">Blog</a>
    <a href="contact.php">Contact</a>
    <a href="login.php">Login</a>
  </nav>
</header>

<div class="container" style="padding:22px">
  <h2>Contact Us</h2>
  <div class="card">
    <p><b>Address:</b> 123 Moonlight Avenue, City</p>
    <p><b>Phone:</b> +123456789</p>
    <p><b>Email:</b> info@moonlight.com</p>
    <p><b>Opening Hours:</b> Mon-Sun 6PM – 3AM</p>
    <iframe src="https://maps.google.com/maps?q=New%20York&t=&z=13&ie=UTF8&iwloc=&output=embed" 
            width="100%" height="250" style="border:0;" allowfullscreen></iframe>
  </div>

  <div class="card" style="margin-top:20px">
    <h3>Make a Reservation</h3>
    <form method="post" action="reserve.php">
      <label>Name<input type="text" name="name" required></label>
      <label>Phone<input type="text" name="phone" required></label>
      <label>Email<input type="email" name="email" required></label>
      <label>Guests<input type="number" name="guests" required></label>
      <label>Date<input type="date" name="date" required></label>
      <label>Time<input type="time" name="time" required></label>
      <label>Message<textarea name="message"></textarea></label>
      <p><button class="btn">Book Now</button></p>
    </form>
  </div>
</div>

<!-- WhatsApp Chat Button -->
<a id="whatsapp-chat" href="https://wa.me/1234567890?text=Hello%20Moonlight%20Club!" target="_blank">
  💬 Chat with Us
</a>
</body></html>
