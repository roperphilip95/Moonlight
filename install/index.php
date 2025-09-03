<?php
// simple installer - put in /install/
@ini_set('display_errors',1); error_reporting(E_ALL);
$cfgPath = __DIR__ . '/../lib/config.php';
function split_sql($sql){
  $sql = preg_replace('#/\*.*?\*/#s','',$sql);
  $sql = preg_replace('/--.*\n/','',$sql);
  $stmts = preg_split('/;[\r\n]+/',$sql);
  return array_filter(array_map('trim',$stmts));
}

$installed = file_exists($cfgPath) && filesize($cfgPath) > 0;
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$installed) {
  $dbhost = trim($_POST['dbhost'] ?? 'localhost');
  $dbname = trim($_POST['dbname'] ?? '');
  $dbuser = trim($_POST['dbuser'] ?? '');
  $dbpass = trim($_POST['dbpass'] ?? '');
  $base = rtrim(trim($_POST['baseurl'] ?? '/'), '/') . '/';
  $admin_name = trim($_POST['admin_name'] ?? 'Administrator');
  $admin_email = trim($_POST['admin_email'] ?? '');
  $admin_pass = trim($_POST['admin_pass'] ?? '');

  if(!$dbname || !$dbuser || !$admin_email || !$admin_pass){
    $err = "Please fill required fields.";
  } else {
    try {
      $pdo0 = new PDO("mysql:host=$dbhost;charset=utf8mb4",$dbuser,$dbpass,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
      ]);
      // create database if missing
      $pdo0->exec("CREATE DATABASE IF NOT EXISTS `{$dbname}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
      // connect to created DB
      $pdo = new PDO("mysql:host=$dbhost;dbname=$dbname;charset=utf8mb4",$dbuser,$dbpass,[
        PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION
      ]);
      // run schema
      $schema = file_get_contents(__DIR__ . '/../db.sql');
      foreach (split_sql($schema) as $s) { if(trim($s)) $pdo->exec($s); }

      // seed admin user and roles (roles already seeded in SQL but safe)
      $pdo->exec("INSERT IGNORE INTO roles (id,name) VALUES (1,'admin')");

      $hash = password_hash($admin_pass, PASSWORD_BCRYPT);
      $st = $pdo->prepare("SELECT id FROM users WHERE email=? LIMIT 1");
      $st->execute([$admin_email]);
      if(!$st->fetch()){
        // use role_id 1 (admin)
        $ins = $pdo->prepare("INSERT INTO users (name,email,phone,password_hash,role_id) VALUES (?,?,?,?,1)");
        $ins->execute([$admin_name,$admin_email,'', $hash]);
      }

      // write config
      $cfg = "<?php\n";
      $cfg .= "if(!defined('DB_HOST')) define('DB_HOST', '".addslashes($dbhost)."');\n";
      $cfg .= "if(!defined('DB_NAME')) define('DB_NAME', '".addslashes($dbname)."');\n";
      $cfg .= "if(!defined('DB_USER')) define('DB_USER', '".addslashes($dbuser)."');\n";
      $cfg .= "if(!defined('DB_PASS')) define('DB_PASS', '".addslashes($dbpass)."');\n";
      $cfg .= "if(!defined('BASE_URL')) define('BASE_URL', '".addslashes($base)."');\n";
      $cfg .= "try { \$pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]); } catch(Exception \$e) { \$pdo=null; }\n";
      $cfg .= "function e(\$s){ return htmlspecialchars(\$s ?? '', ENT_QUOTES, 'UTF-8'); }\n";
      $cfg .= "?>";
      @file_put_contents($cfgPath, $cfg);

      $installed = true;
    } catch (Exception $ex) {
      $err = "Install error: " . $ex->getMessage();
    }
  }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Installer — Moonlight</title><style>body{font-family:system-ui;padding:18px;background:#0b0b14;color:#fff} .card{background:#111;padding:18px;border-radius:10px;max-width:720px;margin:auto} input,button{padding:10px;margin:6px 0;width:100%} .err{background:#600;padding:8px;border-radius:6px}</style></head>
<body>
<div class="card">
  <h2>Installer — Moonlight PRO</h2>
  <?php if($installed): ?>
    <p style="background:#073;padding:10px;border-radius:6px">✅ Installed. Remove <code>install/</code> for security. Admin login: <code>/admin/login.php</code></p>
  <?php else: ?>
    <?php if($err): ?><div class="err"><?= htmlspecialchars($err) ?></div><?php endif; ?>
    <form method="post">
      <h3>Database</h3>
      <input name="dbhost" value="localhost" required placeholder="DB Host">
      <input name="dbname" placeholder="Database name (will be created)" required>
      <input name="dbuser" placeholder="DB User" required>
      <input name="dbpass" placeholder="DB Password">
      <h3>App</h3>
      <input name="baseurl" placeholder="Base URL (e.g. / or /site/)" value="/">
      <h3>Admin user</h3>
      <input name="admin_name" placeholder="Admin full name" value="Administrator" required>
      <input name="admin_email" placeholder="Admin email" required>
      <input name="admin_pass" type="password" placeholder="Admin password" required>
      <button>Install</button>
    </form>
  <?php endif; ?>
</div>
</body></html>
