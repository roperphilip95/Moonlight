<?php
@ini_set('display_errors',1); error_reporting(E_ALL);

// If already installed, stop here
$cfgPath = __DIR__ . '/../lib/config.php';
if (file_exists($cfgPath) && $_SERVER['REQUEST_METHOD'] !== 'POST') {
  echo "<p><strong>Already installed.</strong> To reinstall, delete <code>lib/config.php</code> on your server and reload.</p>";
}

function split_sql_statements($sql){
  // remove -- comments and /* */ blocks
  $sql = preg_replace('/--.*\n/', "\n", $sql);
  $sql = preg_replace('#/\*.*?\*/#s', '', $sql);
  $stmts = [];
  $buffer=''; $inString=false; $stringChar='';
  $len = strlen($sql);
  for($i=0;$i<$len;$i++){
    $ch=$sql[$i];
    if(($ch==="'" || $ch==='"')){
      if(!$inString){ $inString=true; $stringChar=$ch; }
      elseif($stringChar===$ch && $sql[$i-1] !== '\\\\'){ $inString=false; $stringChar=''; }
      $buffer.=$ch; continue;
    }
    if($ch===';' && !$inString){ $trim=trim($buffer); if($trim!=='') $stmts[]=$trim; $buffer=''; }
    else { $buffer.=$ch; }
  }
  $trim=trim($buffer); if($trim!=='') $stmts[]=$trim;
  return $stmts;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $dbhost = trim($_POST['dbhost'] ?? 'localhost');
  $dbname = trim($_POST['dbname'] ?? '');
  $dbuser = trim($_POST['dbuser'] ?? '');
  $dbpass = trim($_POST['dbpass'] ?? '');
  $base  = rtrim($_POST['baseurl'] ?? '/', '/').'/';
  $admin_email = trim($_POST['admin_email'] ?? '');
  $admin_pass  = trim($_POST['admin_pass'] ?? '');

  if(!$dbname || !$dbuser || !$admin_email || !$admin_pass){
    $err = "Please fill all required fields.";
  } else {
    try {
      $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
      ]);

      // Run schema
      $schema = file_get_contents(__DIR__ . '/../db.sql');
      foreach (split_sql_statements($schema) as $stmt) {
        if($stmt!=='') $pdo->exec($stmt);
      }

      // Seed roles
      $pdo->exec("INSERT IGNORE INTO roles (id,name) VALUES 
        (1,'Admin'),(2,'Manager'),(3,'Waiter'),(4,'Finance'),(5,'HR')");

      // Create admin user
      $hash = password_hash($admin_pass, PASSWORD_BCRYPT);
      $st = $pdo->prepare("INSERT INTO users (name,email,password_hash,role_id) VALUES (?,?,?,1)");
      $st->execute(['Administrator', $admin_email, $hash]);

      // Write config.php
      $cfg = "<?php\n".
        "define('DB_HOST','".addslashes($dbhost)."');\n".
        "define('DB_NAME','".addslashes($dbname)."');\n".
        "define('DB_USER','".addslashes($dbuser)."');\n".
        "define('DB_PASS','".addslashes($dbpass)."');\n".
        "define('BASE_URL','".addslashes($base)."');\n".
        "?>";
      file_put_contents($cfgPath, $cfg);

      echo "<h2>✅ Installation successful</h2>";
      echo "<p>Next, add the admin login files. After that you'll sign in at <code>".$base."admin/login.php</code>.</p>";
      exit;
    } catch (Exception $e) {
      $err = "Install error: " . htmlspecialchars($e->getMessage());
    }
  }
}
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8"><title>Moonlight PRO Installer</title>
  <style>
    body{font-family:system-ui,-apple-system,Segoe UI,Roboto,Ubuntu,Arial,sans-serif;margin:0;padding:24px;background:#fafafa}
    .card{max-width:720px;margin:auto;background:#fff;border:1px solid #eee;border-radius:12px;padding:18px}
    input{width:100%;padding:10px;margin:8px 0}
    button{padding:10px 14px;border:1px solid #111;border-radius:8px;background:#fff}
    .err{background:#ffecec;border:1px solid #ffbdbd;padding:10px;border-radius:8px}
  </style>
</head>
<body>
<div class="card">
  <h1>Moonlight PRO — Installer</h1>
  <?php if(!empty($err)): ?><p class="err"><?php echo $err; ?></p><?php endif; ?>
  <form method="post">
    <h3>Database</h3>
    <label>DB Host* <input name="dbhost" value="localhost" required></label>
    <label>DB Name* <input name="dbname" required></label>
    <label>DB User* <input name="dbuser" required></label>
    <label>DB Pass <input name="dbpass" type="password"></label>
    <h3>App</h3>
    <label>Base URL* <input name="baseurl" value="/" required></label>
    <h3>Admin User</h3>
    <label>Email* <input type="email" name="admin_email" required></label>
    <label>Password* <input type="password" name="admin_pass" required></label>
    <button>Install</button>
  </form>
</div>
</body>
</html>
