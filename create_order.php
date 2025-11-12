<?php
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/admin/admin_connection.php';

// Ensure orders table has required columns (username, status, order_date, product_type, image, price, size, quantity, notes)
try {
    $colCheck = $con->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_COLUMN, 0);
    $needed = ['username','status','order_date','product_type','image','price','size','quantity','notes'];
    $missing = array_diff($needed, $colCheck);
    if (!empty($missing)) {
        // Attempt to alter table for missing columns (best-effort). Types chosen generically.
        foreach ($missing as $col) {
            $type = 'VARCHAR(255) NULL';
            if ($col === 'price') $type = 'DECIMAL(10,2) NULL';
            if ($col === 'quantity') $type = 'INT NULL';
            if ($col === 'order_date') $type = 'DATETIME NULL';
            $con->exec("ALTER TABLE orders ADD COLUMN $col $type");
        }
    }
} catch (Exception $e) {
    // Log but don't block
    error_log('Order table column check failed: ' . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['ok'=>false,'error'=>'Invalid method']);
    exit;
}

$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
if (!$username) {
    echo json_encode(['ok'=>false,'error'=>'Not logged in']);
    exit;
}

// Expect JSON cart and basic shipping/payment fields
$raw = file_get_contents('php://input');
$data = json_decode($raw, true);
if (!is_array($data)) {
    echo json_encode(['ok'=>false,'error'=>'Bad JSON']);
    exit;
}
$cart = isset($data['cart']) && is_array($data['cart']) ? $data['cart'] : [];
if (count($cart) === 0) {
    echo json_encode(['ok'=>false,'error'=>'Cart empty']);
    exit;
}

$shipping = $data['shipping'] ?? [];
$payment  = $data['payment'] ?? '';
$notes    = $data['notes'] ?? '';

// Combine shipping info into notes for now
$combinedNotes = "Payment: $payment\n";
foreach ($shipping as $k=>$v) {
    $combinedNotes .= ucfirst($k) . ': ' . $v . "\n";
}
if ($notes) $combinedNotes .= "User Notes: $notes\n";

$now = date('Y-m-d H:i:s');
$inserted = [];
$error = null;

try {
    $con->beginTransaction();
    $stmt = $con->prepare("INSERT INTO orders (username,status,order_date,product_type,image,price,size,quantity,notes) VALUES (?,?,?,?,?,?,?,?,?)");
    foreach ($cart as $item) {
        $product_type = isset($item['name']) ? $item['name'] : 'Item';
        $image = isset($item['image']) ? $item['image'] : 'background.jpg';
        $price = (isset($item['price']) && is_numeric($item['price'])) ? $item['price'] : 0;
        $size = isset($item['size']) ? $item['size'] : 'N/A';
        $qty  = isset($item['quantity']) ? (int)$item['quantity'] : 1;
        $stmt->execute([$username,'to_ship',$now,$product_type,$image,$price,$size,$qty,$combinedNotes]);
        $inserted[] = $con->lastInsertId();
    }
    $con->commit();
} catch (Exception $e) {
    $con->rollBack();
    $error = $e->getMessage();
}

if ($error) {
    echo json_encode(['ok'=>false,'error'=>$error]);
} else {
    echo json_encode(['ok'=>true,'order_ids'=>$inserted]);
}
