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

function require_login(){
  if(!isset($_SESSION['uid'])){ header("Location: /admin/login.php"); exit; }
}

function current_user(){
  if(!isset($_SESSION['uid'])) return null;
  $st = db()->prepare("SELECT u.*, r.name AS role_name FROM users u JOIN roles r ON r.id=u.role_id WHERE u.id=?");
  $st->execute([$_SESSION['uid']]);
  return $st->fetch();
}

function require_role($roles){
  $u = current_user();
  if(!$u) { header("Location: /admin/login.php"); exit; }
  $roles = is_array($roles)? $roles : [$roles];
  if(!in_array($u['role_name'], $roles)){
    http_response_code(403);
    echo "<h2>403 — Forbidden</h2><p>Required role: ".htmlspecialchars(implode(', ',$roles))."</p>";
    exit;
  }
}

function flash($msg=null){
  if($msg!==null){ $_SESSION['flash']=$msg; return; }
  if(isset($_SESSION['flash'])){ $m=$_SESSION['flash']; unset($_SESSION['flash']); return $m; }
  return '';
}
