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
    $stmt = $con->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $product = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$product) {
        header('Location: admin_dashboard.php');
        exit;
    }
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    $image_path = $product['image']; // Keep existing image by default
    
    // Handle new image upload if provided
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_dir = "../uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        if (getimagesize($_FILES["image"]["tmp_name"])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Delete old image if it exists
                if ($product['image'] && file_exists("../" . $product['image'])) {
                    unlink("../" . $product['image']);
                }
                $image_path = "uploads/" . basename($_FILES["image"]["name"]);
            }
        }
    }
    
    try {
        $stmt = $con->prepare("UPDATE products SET name = ?, description = ?, image = ? WHERE id = ?");
        $stmt->execute([$name, $description, $image_path, $id]);
        
        header('Location: admin_dashboard.php');
        exit;
    } catch(PDOException $e) {
        $error = "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Product</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Edit Product</h1>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>

        <?php if (isset($error)): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Product Name</label>
                <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description</label>
                <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($product['description']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Product Image</label>
                <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="Current product image" style="max-width: 200px; margin: 10px 0;">
                <input type="file" id="image" name="image" class="form-control" accept="image/*">
                <small>Leave empty to keep current image</small>
            </div>
            

            
            <button type="submit" class="btn btn-primary">Update Product</button>
        </form>
    </div>
</body>
</html>