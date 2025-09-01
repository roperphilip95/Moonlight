<?php
session_start();
require_once __DIR__ . '/../lib/config.php';

// redirect if not logged in
if (!isset($_SESSION['uid'])) {
  header("Location: login.php");
  exit;
}

try {
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ]);
} catch (Exception $e) {
  die("DB connection failed: " . htmlspecialchars($e->getMessage()));
}

// fetch user info
$st = $pdo->prepare("SELECT u.*, r.name AS role FROM users u JOIN roles r ON u.role_id=r.id WHERE u.id=?");
$st->execute([$_SESSION['uid']]);
$user = $st->fetch();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <title>Moonlight Admin</title>
  <style>
    body { font-family: system-ui; margin:0; background:#f5f5f5; }
    header { background:#111; color:#fff; padding:12px 20px; }
    nav { background:#333; color:#fff; padding:10px; }
    nav a { color:#fff; margin-right:15px; text-decoration:none; }
    main { padding:20px; }
    .card { background:#fff; padding:15px; border-radius:8px; margin-bottom:15px; }
  </style>
</head>
<body>
  <header>
    <h1>Moonlight Admin Dashboard</h1>
  </header>
  <nav>
    <a href="index.php">Dashboard</a>
    <a href="menu.php">Menu</a>
    <a href="finance.php">Finance</a>
    <a href="hr.php">HR</a>
    <a href="logout.php">Logout</a>
  </nav>
  <main>
    <div class="card">
      <h2>Welcome, <?= htmlspecialchars($user['name']) ?> (<?= $user['role'] ?>)</h2>
      <p>This is your admin dashboard. More modules will appear here.</p>
    </div>
  </main>
</body>
</html>
