<?php
session_start();
require_once 'admin/admin_connection.php';

// You may want to use the logged-in user's ID or username from session
// For demo, we'll use username from session
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
if (!$username) {
    echo json_encode([]);
    exit;
}

$status = isset($_GET['status']) ? $_GET['status'] : '';
$status_map = [
    'to_ship' => 'to_ship',
    'to_receive' => 'to_receive',
    'completed' => 'completed',
    'cancelled' => 'cancelled',
];
if (!isset($status_map[$status])) {
    echo json_encode([]);
    exit;
}

// Query orders for this user and status
$stmt = $con->prepare("SELECT * FROM orders WHERE username = ? AND status = ? ORDER BY order_date DESC");
$stmt->execute([$username, $status_map[$status]]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Add status label and fallback image/price
foreach ($orders as &$order) {
    $order['status_label'] = ucfirst(str_replace('_', ' ', $order['status']));
    if (!isset($order['image']) || !$order['image']) $order['image'] = 'background.jpg';
    if (!isset($order['price'])) $order['price'] = '0.00';
}

header('Content-Type: application/json');
echo json_encode($orders);
