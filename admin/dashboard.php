<?php
session_start();
require_once "../lib/config.php";

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../public/login.php");
    exit;
}

$message = "";

// Update settings
if (isset($_POST["update_settings"])) {
    foreach ($_POST as $key => $value) {
        if ($key == "update_settings") continue;
        $stmt = $conn->prepare("INSERT INTO site_settings (setting_key, setting_value) VALUES (?, ?) 
                                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->bind_param("ss", $key, $value);
        $stmt->execute();
    }
    $message = "✅ Settings updated!";
}

// Create user
if (isset($_POST["create_user"])) {
    $name = $_POST["name"];
    $email = $_POST["email"];
    $phone = $_POST["phone"];
    $role = $_POST["role"];
    $password = password_hash("password123", PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $password, $role);
    if ($stmt->execute()) {
        $message = "✅ User created with default password (password123).";
    }
}

// Fetch settings
$settings = [];
$result = $conn->query("SELECT * FROM site_settings");
while ($row = $result->fetch_assoc()) {
    $settings[$row["setting_key"]] = $row["setting_value"];
}

// Fetch users
$users = $conn->query("SELECT id, name, email, phone, role FROM users ORDER BY role");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard - Moonlight</title>
  <link rel="stylesheet" href="../assets/style.css">
</head>
<body>
  <header class="site-header">
    <div class="logo">🌙 Moonlight Admin</div>
    <nav class="nav">
      <a href="dashboard.php" class="active">Dashboard</a>
      <a href="../public/logout.php">Logout</a>
    </nav>
  </header>

  <div class="form-container">
    <h2>Super Admin Dashboard</h2>
    <?php if ($message) echo "<p class='msg'>$message</p>"; ?>

    <h3>Update Site Settings</h3>
    <form method="post">
      <input type="text" name="site_name" value="<?= $settings['site_name'] ?? '' ?>" placeholder="Site Name">
      <input type="text" name="homepage_heading" value="<?= $settings['homepage_heading'] ?? '' ?>" placeholder="Homepage Heading">
      <input type="text" name="homepage_subtitle" value="<?= $settings['homepage_subtitle'] ?? '' ?>" placeholder="Homepage Subtitle">
      <input type="email" name="contact_email" value="<?= $settings['contact_email'] ?? '' ?>" placeholder="Contact Email">
      <input type="text" name="contact_phone" value="<?= $settings['contact_phone'] ?? '' ?>" placeholder="Contact Phone">
      <button type="submit" name="update_settings" class="btn">Save Settings</button>
    </form>

    <h3>Create New User</h3>
    <form method="post">
      <input type="text" name="name" placeholder="Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="text" name="phone" placeholder="Phone">
      <select name="role" required>
        <option value="">-- Select Role --</option>
        <option value="manager">Manager</option>
        <option value="waiter">Waiter</option>
        <option value="finance">Finance</option>
        <option value="hr">HR</option>
      </select>
      <button type="submit" name="create_user" class="btn">Create</button>
    </form>

    <h3>All Users</h3>
    <table class="order-table">
      <tr><th>ID</th><th>Name</th><th>Email</th><th>Phone</th><th>Role</th></tr>
      <?php while ($u = $users->fetch_assoc()): ?>
        <tr>
          <td><?= $u["id"] ?></td>
          <td><?= $u["name"] ?></td>
          <td><?= $u["email"] ?></td>
          <td><?= $u["phone"] ?></td>
          <td><?= ucfirst($u["role"]) ?></td>
        </tr>
      <?php endwhile; ?>
    </table>
  </div>
</body>
</html>
<?php
// ... existing code (session + settings + users) ...

// Handle gallery upload
if (isset($_POST["upload_gallery"])) {
    if (!empty($_FILES["gallery_image"]["name"])) {
        $target_dir = "../assets/gallery/";
        $filename = time() . "_" . basename($_FILES["gallery_image"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["gallery_image"]["tmp_name"], $target_file)) {
            $caption = $_POST["caption"];
            $path = "assets/gallery/" . $filename;

            $stmt = $conn->prepare("INSERT INTO gallery (image_url, caption) VALUES (?, ?)");
            $stmt->bind_param("ss", $path, $caption);
            $stmt->execute();
            $message = "✅ Gallery image uploaded!";
        } else {
            $message = "❌ Failed to upload gallery image.";
        }
    }
}

// Handle slider upload
if (isset($_POST["upload_slider"])) {
    if (!empty($_FILES["slider_image"]["name"])) {
        $target_dir = "../assets/slider/";
        $filename = time() . "_" . basename($_FILES["slider_image"]["name"]);
        $target_file = $target_dir . $filename;

        if (move_uploaded_file($_FILES["slider_image"]["tmp_name"], $target_file)) {
            $caption = $_POST["caption"];
            $path = "assets/slider/" . $filename;

            $stmt = $conn->prepare("INSERT INTO slider (image_url, caption) VALUES (?, ?)");
            $stmt->bind_param("ss", $path, $caption);
            $stmt->execute();
            $message = "✅ Slider image uploaded!";
        } else {
            $message = "❌ Failed to upload slider image.";
        }
    }
}

// Fetch gallery & slider
$gallery = $conn->query("SELECT * FROM gallery ORDER BY created_at DESC LIMIT 12");
$slider = $conn->query("SELECT * FROM slider ORDER BY created_at DESC LIMIT 5");
?>

<!-- Inside HTML body after Users Section -->
<h3>Upload Gallery Image</h3>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="gallery_image" required>
  <input type="text" name="caption" placeholder="Caption (optional)">
  <button type="submit" name="upload_gallery" class="btn">Upload</button>
</form>

<h3>Upload Slider Image</h3>
<form method="post" enctype="multipart/form-data">
  <input type="file" name="slider_image" required>
  <input type="text" name="caption" placeholder="Caption (optional)">
  <button type="submit" name="upload_slider" class="btn">Upload</button>
</form>

<h3>Current Gallery</h3>
<div style="display:flex; flex-wrap:wrap; gap:10px;">
  <?php while ($g = $gallery->fetch_assoc()): ?>
    <img src="../<?= $g['image_url'] ?>" alt="<?= $g['caption'] ?>" style="width:120px; height:100px; object-fit:cover;">
  <?php endwhile; ?>
</div>

<h3>Current Slider Images</h3>
<div style="display:flex; flex-wrap:wrap; gap:10px;">
  <?php while ($s = $slider->fetch_assoc()): ?>
    <img src="../<?= $s['image_url'] ?>" alt="<?= $s['caption'] ?>" style="width:120px; height:100px; object-fit:cover;">
  <?php endwhile; ?>
</div>

