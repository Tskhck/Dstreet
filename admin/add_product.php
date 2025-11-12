<?php
session_start();
// Simple admin check (replace with your own logic)
if (!isset($_SESSION['username']) || $_SESSION['username'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}
require_once 'admin_connection.php';

// Ensure products table has size column
try {
    $colCheck = $con->query("SHOW COLUMNS FROM products")->fetchAll(PDO::FETCH_COLUMN, 0);
    if (!in_array('size', $colCheck)) {
        $con->exec("ALTER TABLE products ADD COLUMN size VARCHAR(50) NULL");
    }
} catch (Exception $e) {
    error_log('Products table column check failed: ' . $e->getMessage());
}

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action']) && $_POST['action'] === 'edit') {
        // Handle product edit
        $product_id = intval($_POST['product_id']);
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $size = trim($_POST['size']);
        
        // Check if new image uploaded
        $image_path = $_POST['existing_image']; // Keep existing by default
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $target_dir = '../uploads/products/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $filename = uniqid('prod_', true) . '.' . $ext;
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/products/' . $filename;
            }
        }
        
        $stmt = $con->prepare('UPDATE products SET name=?, description=?, price=?, image=?, stock=?, size=? WHERE id=?');
        $stmt->execute([$name, $description, $price, $image_path, $stock, $size, $product_id]);
        header('Location: add_product.php?updated=1');
        exit();
    } else {
        // Handle product add
        $name = trim($_POST['name']);
        $description = trim($_POST['description']);
        $price = floatval($_POST['price']);
        $stock = intval($_POST['stock']);
        $size = trim($_POST['size']);
        $image_path = '';

        // Handle image upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
            $target_dir = '../uploads/products/';
            if (!file_exists($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $filename = uniqid('prod_', true) . '.' . $ext;
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/products/' . $filename;
            }
        }

        $stmt = $con->prepare('INSERT INTO products (name, description, price, image, stock, size) VALUES (?, ?, ?, ?, ?, ?)');
        $stmt->execute([$name, $description, $price, $image_path, $stock, $size]);
        header('Location: add_product.php?success=1');
        exit();
    }
}

// Fetch all products
try {
    $stmt = $con->query("SELECT * FROM products ORDER BY created_at DESC");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Product Management - Admin</title>
    <link rel="stylesheet" href="../global1.css">
    <style>
        body { background: #111; color: #fff; margin:0; padding:0; font-family: 'Poppins', sans-serif; }
        .header { background: #1a1a1a; padding: 20px 40px; display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #32CDD5; }
        .header h1 { color: #32CDD5; margin:0; font-size: 28px; }
        .back-home-btn { background: #32CDD5; color: #000; padding: 12px 24px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; text-decoration: none; transition: 0.3s; display: inline-block; }
        .back-home-btn:hover { background: #28b4bb; transform: scale(1.05); }
        .add-product-section { text-align: left; padding: 30px 40px; }
        .add-btn { background: #32CDD5; color: #000; padding: 14px 32px; border: none; border-radius: 8px; font-size: 18px; font-weight: 600; cursor: pointer; transition: 0.3s; }
        .add-btn:hover { background: #28b4bb; transform: scale(1.05); }
        .container { max-width: 1200px; margin: 40px auto; padding: 0 20px; }
        .products-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 24px; }
        .product-card { background: #1f1f1f; border-radius: 12px; overflow: hidden; border: 1px solid #333; transition: 0.3s; }
        .product-card:hover { transform: translateY(-4px); border-color: #32CDD5; }
        .product-card img { width: 100%; height: 200px; object-fit: cover; }
        .product-info { padding: 16px; }
        .product-info h3 { margin: 0 0 8px; color: #32CDD5; font-size: 18px; }
        .product-info p { margin: 4px 0; color: #aaa; font-size: 14px; }
        .product-info .price { color: #fff; font-weight: bold; font-size: 20px; margin-top: 8px; }
        .product-info .stock { color: #4caf50; font-size: 13px; }
        .edit-btn { background: #ff9800; color: #fff; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; margin-top: 12px; font-size: 14px; font-weight: 600; transition: 0.3s; width: 100%; }
        .edit-btn:hover { background: #e68900; }
        .empty { text-align: center; color: #666; margin-top: 60px; font-size: 18px; }
        
        /* Modal styles */
        .modal { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.85); z-index: 1000; align-items: center; justify-content: center; }
        .modal.show { display: flex; }
        .modal-content { background: #232323; padding: 32px; border-radius: 12px; max-width: 500px; width: 90%; max-height: 90vh; overflow-y: auto; position: relative; }
        .modal-content h2 { color: #32CDD5; text-align: center; margin-bottom: 24px; }
        .close-modal { position: absolute; top: 16px; right: 20px; font-size: 28px; color: #aaa; cursor: pointer; transition: 0.3s; }
        .close-modal:hover { color: #fff; }
        label { display: block; margin-top: 16px; color: #ddd; }
        input, textarea { width: 100%; padding: 10px; border-radius: 6px; border: none; margin-top: 6px; background: #292929; color: #fff; box-sizing: border-box; }
        textarea { resize: vertical; min-height: 80px; }
        button[type="submit"] { margin-top: 24px; width: 100%; background: #32CDD5; color: #000; border: none; padding: 12px; border-radius: 6px; font-size: 16px; font-weight: 600; cursor: pointer; }
        button[type="submit"]:hover { background: #28b4bb; }
        .msg { text-align: center; background: #4caf50; color: #fff; padding: 12px; border-radius: 8px; margin-bottom: 16px; }
        .msg.fade-out { opacity: 0; transition: opacity 0.5s ease-out; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Product Management</h1>
        <div style="display:flex;gap:15px;align-items:center;">
            <a href="customers.php" class="back-home-btn">Customers</a>
            <a href="../home.php" class="back-home-btn">&larr; Back to Home</a>
        </div>
    </div>

    <div class="add-product-section">
        <button class="add-btn" onclick="openModal()">+ Add Product</button>
    </div>

    <div class="container">
        <?php if (isset($_GET['success'])): ?>
            <div class="msg">Product added successfully!</div>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <div class="msg">Product updated successfully!</div>
        <?php endif; ?>

        <?php if (empty($products)): ?>
            <div class="empty">No products yet. Click "Add Product" to get started.</div>
        <?php else: ?>
            <div class="products-grid">
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <img src="../<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="product-info">
                            <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                            <p><?php echo htmlspecialchars($product['description']); ?></p>
                            <div class="price">₱<?php echo number_format((float)$product['price'], 2); ?></div>
                            <div class="stock">Stock: <?php echo htmlspecialchars($product['stock']); ?></div>
                            <?php if (!empty($product['size'])): ?>
                                <div class="stock">Size: <?php echo htmlspecialchars($product['size']); ?></div>
                            <?php endif; ?>
                            <button class="edit-btn" 
                                data-id="<?php echo htmlspecialchars($product['id']); ?>"
                                data-name="<?php echo htmlspecialchars($product['name']); ?>"
                                data-description="<?php echo htmlspecialchars($product['description']); ?>"
                                data-price="<?php echo htmlspecialchars($product['price']); ?>"
                                data-stock="<?php echo htmlspecialchars($product['stock']); ?>"
                                data-size="<?php echo htmlspecialchars($product['size'] ?? ''); ?>"
                                data-image="<?php echo htmlspecialchars($product['image']); ?>"
                                onclick="openEditModalFromBtn(this)">Edit Product</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Product Modal -->
    <div id="addProductModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal()">&times;</span>
            <h2>Add New Product</h2>
            <form method="POST" enctype="multipart/form-data">
                <label>Product Name</label>
                <input type="text" name="name" required>
                <label>Description</label>
                <textarea name="description" required></textarea>
                <label>Price (₱)</label>
                <input type="number" name="price" min="0" step="0.01" required>
                <label>Size</label>
                <input type="text" name="size" placeholder="e.g., S, M, L, XL or Custom" required>
                <label>Stock</label>
                <input type="number" name="stock" min="0" required>
                <label>Image</label>
                <input type="file" name="image" accept="image/*" required>
                <button type="submit">Add Product</button>
            </form>
        </div>
    </div>

    <!-- Edit Product Modal -->
    <div id="editProductModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeEditModal()">&times;</span>
            <h2>Edit Product</h2>
            <form method="POST" enctype="multipart/form-data" id="editForm">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="product_id" id="edit_product_id">
                <input type="hidden" name="existing_image" id="edit_existing_image">
                <label>Product Name</label>
                <input type="text" name="name" id="edit_name" required>
                <label>Description</label>
                <textarea name="description" id="edit_description" required></textarea>
                <label>Price (₱)</label>
                <input type="number" name="price" id="edit_price" min="0" step="0.01" required>
                <label>Size</label>
                <input type="text" name="size" id="edit_size" placeholder="e.g., S, M, L, XL or Custom" required>
                <label>Stock</label>
                <input type="number" name="stock" id="edit_stock" min="0" required>
                <label>Image (leave empty to keep current)</label>
                <input type="file" name="image" accept="image/*">
                <button type="submit">Update Product</button>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('addProductModal').classList.add('show');
        }
        function closeModal() {
            document.getElementById('addProductModal').classList.remove('show');
        }
        function openEditModalFromBtn(btn) {
            const product = {
                id: btn.getAttribute('data-id'),
                name: btn.getAttribute('data-name'),
                description: btn.getAttribute('data-description'),
                price: btn.getAttribute('data-price'),
                stock: btn.getAttribute('data-stock'),
                size: btn.getAttribute('data-size'),
                image: btn.getAttribute('data-image')
            };
            openEditModal(product);
        }
        function openEditModal(product) {
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_name').value = product.name;
            document.getElementById('edit_description').value = product.description;
            document.getElementById('edit_price').value = product.price;
            document.getElementById('edit_stock').value = product.stock;
            document.getElementById('edit_size').value = product.size || '';
            document.getElementById('edit_existing_image').value = product.image;
            document.getElementById('editProductModal').classList.add('show');
        }
        function closeEditModal() {
            document.getElementById('editProductModal').classList.remove('show');
        }
        // Close modal when clicking outside
        window.onclick = function(event) {
            const addModal = document.getElementById('addProductModal');
            const editModal = document.getElementById('editProductModal');
            if (event.target === addModal) {
                closeModal();
            }
            if (event.target === editModal) {
                closeEditModal();
            }
        }

        // Auto-hide success messages
        window.addEventListener('DOMContentLoaded', function() {
            const messages = document.querySelectorAll('.msg');
            messages.forEach(function(msg) {
                setTimeout(function() {
                    msg.classList.add('fade-out');
                    setTimeout(function() {
                        msg.style.display = 'none';
                    }, 500);
                }, 3000);
            });
        });
    </script>
</body>
</html>
