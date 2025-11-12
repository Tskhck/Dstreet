<?php
session_start();
// Simple admin check
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
require_once 'admin_connection.php';

// Fetch all customers (users who are not admin)
try {
    $stmt = $con->query("SELECT * FROM users WHERE username != 'admin' ORDER BY username ASC");
    $customers = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $customers = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Management - Admin</title>
    <link rel="stylesheet" href="../global1.css">
    <style>
        body { background: #111; color: #fff; margin:0; padding:0; font-family: 'Poppins', sans-serif; }
        .header { background: #1a1a1a; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #32CDD5; }
        .header h1 { color: #32CDD5; margin:0; font-size: 28px; }
        .nav-links { display: flex; gap: 20px; }
        .nav-links a { background: #32CDD5; color: #000; padding: 10px 20px; border: none; border-radius: 8px; font-size: 14px; font-weight: 600; text-decoration: none; transition: 0.3s; }
        .nav-links a:hover { background: #28b4bb; transform: scale(1.05); }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .customers-table { width: 100%; background: #1f1f1f; border-radius: 12px; overflow: hidden; border: 1px solid #333; }
        .customers-table th { background: #2a2a2a; padding: 16px; text-align: left; color: #32CDD5; font-weight: 600; border-bottom: 2px solid #32CDD5; }
        .customers-table td { padding: 16px; border-bottom: 1px solid #333; }
        .customers-table tr:hover { background: #252525; }
        .customers-table tr:last-child td { border-bottom: none; }
        .empty { text-align: center; color: #666; margin-top: 60px; font-size: 18px; }
        .customer-count { background: #32CDD5; color: #000; padding: 8px 16px; border-radius: 20px; font-weight: 600; display: inline-block; margin-bottom: 20px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Customer Management</h1>
        <div class="nav-links">
            <a href="add_product.php">Products</a>
            <a href="customers.php">Customers</a>
            <a href="../home.php">Back to Home</a>
        </div>
    </div>

    <div class="container">
        <?php if (!empty($customers)): ?>
            <div class="customer-count">Total Customers: <?php echo count($customers); ?></div>
            <table class="customers-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Registered Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($customers as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['id'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($customer['username']); ?></td>
                            <td><?php echo htmlspecialchars($customer['email'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($customer['created_at'] ?? 'N/A'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="empty">No customers registered yet.</div>
        <?php endif; ?>
    </div>
</body>
</html>
