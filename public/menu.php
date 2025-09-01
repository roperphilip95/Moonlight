<?php
include __DIR__ . '/_partials_header.php';

try {
  $pdo = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4", DB_USER, DB_PASS, [
    PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC
  ]);
  $today = date('Y-m-d');
  // Join menu_items with daily_prices (today). If no daily price, show base_price.
  $sql = "SELECT m.id, m.name, m.category,
                 COALESCE(dp.price, m.base_price) AS price
          FROM menu_items m
          LEFT JOIN daily_prices dp
            ON dp.item_id = m.id AND dp.price_date = :d
          WHERE m.active = 1
          ORDER BY m.category, m.name";
  $st = $pdo->prepare($sql);
  $st->execute([':d'=>$today]);
  $items = $st->fetchAll();
} catch(Exception $e){
  $items = [];
}
?>
<h2>Our Menu</h2>
<p class="muted">Daily prices are applied automatically each day.</p>

<div class="grid grid-2">
  <div class="card">
    <table class="table">
      <tr><th>Item</th><th>Category</th><th>Price</th></tr>
      <?php if(!$items): ?>
        <tr><td colspan="3">No items yet. (Admin → Menu can add items.)</td></tr>
      <?php else: foreach($items as $it): ?>
        <tr>
          <td><?= htmlspecialchars($it['name']) ?></td>
          <td><?= htmlspecialchars($it['category']) ?></td>
          <td>₦<?= number_format($it['price'],2) ?></td>
        </tr>
      <?php endforeach; endif; ?>
    </table>
  </div>
  <div class="card">
    <h3>Book a Table</h3>
    <p class="muted">Message us on WhatsApp to reserve seats or a VIP booth.</p>
    <p><a class="btn" href="javascript:openWhatsApp('2348012345678','I want to book a table for tonight');">Chat on WhatsApp</a></p>
  </div>
</div>
<?php include __DIR__ . '/_partials_footer.php'; ?>
