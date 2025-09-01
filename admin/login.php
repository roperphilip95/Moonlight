<?php
session_start();
require_once __DIR__ . '/../lib/config.php';

try {
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Exception $e) {
  die("DB connection failed: " . htmlspecialchars($e->getMessage()));
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = trim($_POST['email'] ?? '');
  $pass = $_POST['password'] ?? '';

  $st = $pdo->prepare("SELECT * FROM users WHERE email=?");
  $st->execute([$email]);
  $u = $st->fetch();
  if ($u && password_verify($pass, $u['password_hash'])) {
    $_SESSION['uid'] = $u['id'];
    $_SESSION['role_id'] = $u['role_id'];
    header("Location: index.php");
    exit;
  } else {
    $error = "Invalid login.";
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Moonlight Admin Login</title>
  <style>
    body { font-family: system-ui; background:#222; color:#fff; display:flex; height:100vh; align-items:center; justify-content:center; }
    form { background:#333; padding:20px; border-radius:10px; width:300px; }
    input { width:100%; margin:8px 0; padding:10px; border:none; border-radius:6px; }
    button { width:100%; padding:10px; border:none; background:#06f; color:#fff; border-radius:6px; }
    .error { background:#900; padding:8px; border-radius:6px; margin-bottom:10px; }
  </style>
</head>
<body>
  <form method="post">
    <h2>Admin Login</h2>
    <?php if($error): ?><div class="error"><?= $error ?></div><?php endif; ?>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="password" placeholder="Password" required>
    <button>Login</button>
  </form>
</body>
</html>
