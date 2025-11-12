<?php
session_start();
require_once 'connection.php';

// Convert mysqli connection to PDO for better prepared statements
$con = null;
try {
    $con = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    $_SESSION['error_message'] = "Connection failed: " . $e->getMessage();
    header("Location: order_form.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_type = $_POST['product'];
    $size = $_POST['selected_size'];
    $quantity = (int)$_POST['quantity'];
    $notes = $_POST['notes'];
    
    // Validate inputs
    if (empty($product_type) || empty($size) || $quantity < 1) {
        $_SESSION['error_message'] = "Please fill in all required fields.";
        header("Location: order_form.php");
        exit();
    }
    
    // Handle file upload
    $design_path = '';
    if (isset($_FILES['design']) && $_FILES['design']['error'] == 0) {
        $upload_dir = 'uploads/designs/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = pathinfo($_FILES['design']['name'], PATHINFO_EXTENSION);
        $file_name = uniqid() . '.' . $file_extension;
        $target_path = $upload_dir . $file_name;
        
        if (move_uploaded_file($_FILES['design']['tmp_name'], $target_path)) {
            $design_path = $target_path;
        } else {
            $_SESSION['error_message'] = "Failed to upload design file.";
            header("Location: order_form.php");
            exit();
        }
    } else {
        $_SESSION['error_message'] = "Please upload a design file.";
        header("Location: order_form.php");
        exit();
    }
    
    try {
        $sql = "INSERT INTO orders (product_type, size, quantity, design_path, notes, order_date) 
                VALUES (?, ?, ?, ?, ?, NOW())";
        $stmt = $con->prepare($sql);
        $stmt->execute([$product_type, $size, $quantity, $design_path, $notes]);
        
        $_SESSION['success_message'] = "Order submitted successfully!";
        header("Location: home.php");
        exit();
    } catch(PDOException $e) {
        $_SESSION['error_message'] = "Error submitting order. Please try again.";
        header("Location: order_form.php");
        exit();
    }
}
?>