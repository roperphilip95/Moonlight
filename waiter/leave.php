<?php
session_start();
require_once __DIR__ . '/../lib/config.php';
if(!isset($_SESSION['waiter_id'])){ header("Location: index.php"); exit; }
$pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

if($_SERVER['REQUEST_METHOD']==='POST'){
  $st=$pdo->prepare("INSERT INTO leaves(user_id,type,from_date,to_date,reason,status) VALUES (?,?,?,?,?,'pending')");
  $st->execute([$_SESSION['waiter_id'], $_POST['type'], $_POST['from_date'], $_POST['to_date'], trim($_POST['reason']) ]);
  $ok=true;
}
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><link rel="stylesheet" href="/assets/style.css"><title>Leave Request</title></head>
<body class="container">
  <div class="card" style="max-width:560px;margin:20px auto">
    <h2>Apply for Leave</h2>
    <?php if(!empty($ok)): ?><div class="flash">Submitted.</div><?php endif; ?>
    <form method="post">
      <label>Type
        <select name="type">
          <option>Sick</option><option>Casual</option><option>Vacation</option>
        </select>
      </label>
      <div class="row">
        <label>From <input type="date" name="from_date" required></label>
        <label>To <input type="date" name="to_date" required></label>
      </div>
      <label>Reason<textarea name="reason" rows="4"></textarea></label>
      <p><button class="btn">Submit</button></p>
    </form>
    <p><a href="/waiter/sales.php" class="btn">Back to POS</a></p>
  </div>
</body>
</html>
