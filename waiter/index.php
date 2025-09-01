<?php
session_start();
require_once __DIR__ . '/../lib/config.php';

function db(){
  static $pdo=null;
  if(!$pdo){
    $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
      PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
    ]);
  }
  return $pdo;
}

// quick + dirty waiter login (email+password) using users table with role Waiter
$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
  $st=db()->prepare("SELECT * FROM users WHERE email=?");
  $st->execute([$_POST['email']]);
  $u=$st->fetch();
  if($u && password_verify($_POST['password'],$u['password_hash']) ){
    // ensure role is Waiter (id via roles table)
    $r=db()->prepare("SELECT name FROM roles WHERE id=?"); $r->execute([$u['role_id']]); $role=$r->fetchColumn();
    if($role!=='Waiter'){ $error='Not a waiter account.'; }
    else { $_SESSION['waiter_id']=$u['id']; header("Location: sales.php"); exit; }
  } else { $error='Invalid credentials.'; }
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<link rel="stylesheet" href="/assets/style.css"><title>Waiter Login</title></head>
<body class="container">
  <div class="card" style="max-width:420px;margin:40px auto">
    <h2>Waiter Login</h2>
    <?php if($error): ?><div class="flash" style="background:#ffecec;border-color:#ffbdbd;color:#6a0000"><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <form method="post">
      <label>Email<input type="email" name="email" required></label>
      <label>Password<input type="password" name="password" required></label>
      <p><button class="btn">Login</button></p>
    </form>
    <p class="muted"><a href="/admin/login.php">Admin/Manager Login</a></p>
  </div>
</body>
</html>
