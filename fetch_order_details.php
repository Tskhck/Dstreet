<?php
session_start();
require_once 'admin/admin_connection.php';

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
if (!$username || !isset($_GET['id'])) {
    echo '<p>Order not found.</p>';
    exit;
}
$order_id = intval($_GET['id']);

$stmt = $con->prepare("SELECT * FROM orders WHERE id = ? AND username = ?");
$stmt->execute([$order_id, $username]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    echo '<p>Order not found.</p>';
    exit;
}

// Show order details
?>
<h3>Order #<?php echo htmlspecialchars($order['id']); ?></h3>
<p><strong>Product:</strong> <?php echo htmlspecialchars($order['product_type']); ?></p>
<p><strong>Size:</strong> <?php echo htmlspecialchars($order['size']); ?></p>
<p><strong>Quantity:</strong> <?php echo htmlspecialchars($order['quantity']); ?></p>
<p><strong>Status:</strong> <?php echo ucfirst(str_replace('_', ' ', $order['status'])); ?></p>
<p><strong>Order Date:</strong> <?php echo htmlspecialchars($order['order_date']); ?></p>
<?php if (!empty($order['design_path'])): ?>
  <p><strong>Design:</strong><br><img src="<?php echo htmlspecialchars($order['design_path']); ?>" alt="Design" style="max-width:100%;border-radius:8px;margin-top:8px;"></p>
<?php endif; ?>
<?php if (!empty($order['notes'])): ?>
  <p><strong>Notes:</strong> <?php echo nl2br(htmlspecialchars($order['notes'])); ?></p>
<?php endif; ?>
