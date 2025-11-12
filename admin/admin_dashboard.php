<?php
session_start();
require_once 'admin_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header('Location: admin_login.php');
    exit;
}

// Handle file upload and product creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $description = $_POST['description'] ?? '';
    
    $target_dir = "../uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }
    
    $success = false;
    $message = '';
    
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
        
        // Check if image file is an actual image
        if (getimagesize($_FILES["image"]["tmp_name"])) {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_path = "uploads/" . basename($_FILES["image"]["name"]);
                
                try {
                    $stmt = $con->prepare("INSERT INTO products (name, description, image) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $description, $image_path]);
                    $success = true;
                    $message = "Product added successfully!";
                } catch(PDOException $e) {
                    $message = "Error: " . $e->getMessage();
                }
            } else {
                $message = "Sorry, there was an error uploading your file.";
            }
        } else {
            $message = "File is not an image.";
        }
    } else {
        $message = "Please select an image file.";
    }
}

// Fetch existing products
try {
    $stmt = $con->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $products = [];
    $message = "Error fetching products: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="admin.css">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>
    <div class="admin-container">
        <div class="admin-header">
            <h1 class="admin-title">Product Management</h1>
            <button class="btn btn-primary" onclick="openAddProductModal()">Add New Product</button>
            <a href="admin_logout.php" class="btn btn-danger">Logout</a>
        </div>

        <?php if (isset($message)): ?>
            <div class="alert <?php echo $success ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="product-grid">
            <?php foreach ($products as $product): ?>
                <div class="product-card">
                    <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                    <p><?php echo htmlspecialchars($product['description']); ?></p>
                    <div class="product-actions">
                        <button class="btn btn-edit" onclick="editProduct(<?php echo $product['id']; ?>)">Edit</button>
                        <button class="btn btn-danger" onclick="deleteProduct(<?php echo $product['id']; ?>)">Delete</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <button class="modal-close" onclick="closeAddProductModal()">&times;</button>
            <h2>Add New Product</h2>
            <form action="" method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="name">Product Name</label>
                    <input type="text" id="name" name="name" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" required></textarea>
                </div>
                
                <div class="form-group">
                    <label for="image">Product Image</label>
                    <input type="file" id="image" name="image" class="form-control" accept="image/*" required>
                </div>
                

                
                <button type="submit" class="btn btn-primary">Add Product</button>
            </form>
        </div>
    </div>

    <script>
        function openAddProductModal() {
            document.getElementById('addProductModal').classList.add('show');
        }

        function closeAddProductModal() {
            document.getElementById('addProductModal').classList.remove('show');
        }

        function deleteProduct(id) {
            if (confirm('Are you sure you want to delete this product?')) {
                window.location.href = 'delete_product.php?id=' + id;
            }
        }

        function editProduct(id) {
            window.location.href = 'edit_product.php?id=' + id;
        }
    </script>
</body>
</html>