<?php
session_start();
require_once 'admin_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$id = $_GET['id'];

try {
    // First get the image path
    $stmt = $con->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($product && $product['image']) {
        // Delete the image file if it exists
        $image_path = "../" . $product['image'];
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // Delete the product from database
    $stmt = $con->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$id]);
    
    header('Location: admin_dashboard.php');
    exit;
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>