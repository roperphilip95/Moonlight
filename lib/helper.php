
<?php
require_once __DIR__ . '/config.php';
function audit($action, $details = null) {
  $uid = $_SESSION['user_id'] ?? null;
  $ip  = $_SERVER['REMOTE_ADDR'] ?? null;
  $stmt = $GLOBALS['conn']->prepare("INSERT INTO audit_logs (user_id, action, details, ip_address) VALUES (?,?,?,?)");
  $stmt->bind_param("isss", $uid, $action, $details, $ip);
  $stmt->execute();
}

function price_today($item_id) {
  $today = date('Y-m-d');
  $stmt = $GLOBALS['conn']->prepare("
    SELECT price FROM item_prices 
    WHERE item_id = ? AND effective_date <= ? 
    ORDER BY effective_date DESC LIMIT 1
  ");
  $stmt->bind_param("is", $item_id, $today);
  $stmt->execute();
  $res = $stmt->get_result()->fetch_assoc();
  return $res ? (float)$res['price'] : null;
}
function pdo_conn() {
  return new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
}

function require_login() {
  session_start();
  if (!isset($_SESSION['uid'])) {
    header("Location: login.php"); exit;
  }
}

function require_role($roles = []) {
  if (empty($roles)) return; // no restriction
  if (!isset($_SESSION['role_id'])) { header("Location: login.php"); exit; }
  // Map role IDs to names (seeded by installer)
  $map = [1=>'Admin',2=>'Manager',3=>'Waiter',4=>'Finance',5=>'HR'];
  $userRole = $map[$_SESSION['role_id']] ?? 'Guest';
  if (!in_array($userRole, $roles, true)) {
    http_response_code(403); die("Access denied");
  }
}

function log_action($pdo, $user_id, $action, $details='') {
  $st = $pdo->prepare("INSERT INTO audit_logs (user_id, action, details) VALUES (?,?,?)");
  $st->execute([$user_id, $action, $details]);
}

function safe($v){ return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }
