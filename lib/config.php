<?php
// Placeholder values for GitHub. On your server, run /install/ to generate the real values.
if(!defined('DB_HOST')) define('DB_HOST', 'localhost');
if(!defined('DB_NAME')) define('DB_NAME', 'moonlight');
if(!defined('DB_USER')) define('DB_USER', 'root');
if(!defined('DB_PASS')) define('DB_PASS', '');
if(!defined('BASE_URL')) define('BASE_URL', ''); // e.g. '' or '/subfolder'

try {
  // Only attempt DB if actually present (prevents errors when browsing on GitHub).
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch (Exception $e) {
  // Silently ignore in GitHub environment; real server will be configured by installer.
}
define('PAYSTACK_PUBLIC_KEY', 'pk_test_xxxxxxxxxxxxxxxxxxxxx');
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxxxxxxxxxxxxxxxxxx');



