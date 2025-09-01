<?php
require_once __DIR__ . '/config.php';

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
