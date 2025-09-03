<?php
// placeholder config for preview. Installer will overwrite this file on server.

if(!defined('DB_HOST')) define('DB_HOST','localhost');
if(!defined('DB_NAME')) define('DB_NAME','moonlight');
if(!defined('DB_USER')) define('DB_USER','root');
if(!defined('DB_PASS')) define('DB_PASS','');
if(!defined('BASE_URL')) define('BASE_URL','/');

try {
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
} catch(Exception $e) {
  $pdo = null; // front-end preview OK without DB
}

function e($s){ return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
