<?php
session_start();
require_once "../lib/config.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();

    if ($user && password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["name"] = $user["name"];

        if ($user["role"] == "customer") {
            header("Location: orders.php");
        } else {
            header("Location: ../admin/index.php");
        }
        exit;
    } else {
        $message = "❌ Invalid email or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Login - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <div class="form-container">
    <h2>Login</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>
    <form method="post">
      <input type="email" name="email" placeholder="Email Address" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit" class="btn neon-btn">Login</button>
    </form>
    <p>Don't have an account? <a href="register.php">Register</a></p>
  </div>
</body>
</html>