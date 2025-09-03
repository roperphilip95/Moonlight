<?php
session_start();
require_once __DIR__ . '/../lib/config.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email']);
  $pass  = $_POST['password'];
  $st = $pdo->prepare("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=? LIMIT 1");
  $st->execute([$email]);
  $u = $st->fetch();
  if ($u && password_verify($pass, $u['password_hash'])) {
    $_SESSION['user_id'] = $u['id'];
    $_SESSION['role'] = $u['role_name'];
    if ($u['role_name'] === 'admin' || $u['role_name'] === 'manager' || $u['role_name'] === 'waiter' || $u['role_name'] === 'finance' || $u['role_name'] === 'hr') {
      header("Location: ../admin/index.php"); exit;
    } else {
      header("Location: profile.php"); exit;
    }
  } else {
    $err = "❌ Invalid email or password.";
  }
}
?>
<!doctype html>
<html><head>
<meta charset="utf-8"><title>Login — Moonlight</title>
<link rel="stylesheet" href="../assets/style.css">
</head><body>
<div class="form-container">
  <h2>Login</h2>
  <?php if($err): ?><div class="card" style="background:#421a1a"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <label>Email<input type="email" name="email" required></label>
    <label>Password<input type="password" name="password" required></label>
    <p><button class="btn">Login</button></p>
  </form>
  <p class="muted">No account? <a href="register.php">Register</a></p>
</div>
</body></html>
