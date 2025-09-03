<?php
require_once __DIR__ . '/../lib/config.php';
$err=''; $msg='';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = trim($_POST['name']);
  $email= trim($_POST['email']);
  $phone= trim($_POST['phone']);
  $pass = $_POST['password'];
  if (!$name || !$email || !$phone || !$pass) {
    $err = "All fields required.";
  } else {
    $st = $pdo->prepare("SELECT id FROM users WHERE email=?");
    $st->execute([$email]);
    if ($st->fetch()) {
      $err = "Email already registered.";
    } else {
      $hash = password_hash($pass,PASSWORD_BCRYPT);
      $role_id = 6; // customer
      $ins = $pdo->prepare("INSERT INTO users (name,email,phone,password_hash,role_id) VALUES (?,?,?,?,?)");
      $ins->execute([$name,$email,$phone,$hash,$role_id]);
      $msg = "✅ Registered! You may now login.";
    }
  }
}
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Register — Moonlight</title>
<link rel="stylesheet" href="../assets/style.css">
</head><body>
<div class="form-container">
  <h2>Create Account</h2>
  <?php if($err): ?><div class="card" style="background:#421a1a"><?= e($err) ?></div><?php endif; ?>
  <?php if($msg): ?><div class="card" style="background:#1a4220"><?= e($msg) ?></div><?php endif; ?>
  <form method="post">
    <label>Name<input type="text" name="name" required></label>
    <label>Email<input type="email" name="email" required></label>
    <label>Phone<input type="text" name="phone" required></label>
    <label>Password<input type="password" name="password" required></label>
    <p><button class="btn">Register</button></p>
  </form>
  <p class="muted">Already have an account? <a href="login.php">Login</a></p>
</div>
</body></html>
