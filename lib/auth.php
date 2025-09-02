<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function require_role($roles = []) {
  if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header("Location: ../public/login.php"); exit;
  }
  if (!in_array($_SESSION['role'], (array)$roles)) {
    http_response_code(403);
    echo "<h2 style='color:#fff;background:#111;padding:20px;'>Forbidden</h2>";
    exit;
  }
}