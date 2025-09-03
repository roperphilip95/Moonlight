<?php
session_start();
require_once __DIR__ . '/../lib/config.php';

$err = '';
if($_SERVER['REQUEST_METHOD'] === 'POST'){
  $email = trim($_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  if($pdo && $email){
    $st = $pdo->prepare("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email=? LIMIT 1");
    $st->execute([$email]);
    $u = $st->fetch();
    if($u && password_verify($pass, $u['password_hash'])){
      $_SESSION['user_id'] = $u['id'];
      $_SESSION['role'] = $u['role_name'];
      header("Location: index.php"); exit;
    } else $err = "Invalid credentials.";
  } else $err = "Database not available. Run installer on server.";
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Admin Login</title><link rel="stylesheet" href="/assets/style.css"></head>
<body>
<div class="form-container">
  <h2>Admin Login</h2>
  <?php if($err): ?><div class="card" style="background:#4b1a1a;color:#fff"><?= e($err) ?></div><?php endif; ?>
  <form method="post">
    <label>Email<input name="email" type="email" required></label>
    <label>Password<input name="password" type="password" required></label>
    <p><button class="btn">Login</button></p>
  </form>
</div>
</body></html>
