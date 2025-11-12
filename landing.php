<?php
require_once __DIR__ . '/admin/admin_connection.php';

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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>D'Streetwear | Home</title>
  <link rel="stylesheet" href="front.css">
  <link rel="stylesheet" href="nav.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
</head>
<body>

  <div id="popup" class="popup">Login first!</div>

  <nav class="navbar">
    <a href="landing.php" class="logo">
      <img src="logo.jpg" alt="D'Streetwear Logo">
      <span>D'Streetwear</span>
    </a>
    <div class="nav-links">
      <div class="dropdown">
        <div class="dropbtn">Login / Signup<i class='bx bx-chevron-down'></i></a>
        <div class="dropdown-content">
          <a href="#collections">Shop</a>
          <a href="#contact">About</a>
          <a href="#contact">Customer Support</a>
         <a href="login.php" class="login-btn">Login / Signup</a>  
        </div>
  </nav>

  <section class="hero">
    <div class="hero-content">
      <h1>CAN CREATE PRODUCTS THAT WORK FOR YOU.</h1>
      <p>Aspire. Integrity. Mastery.</p>
      <a href="#collections" class="btn">Shop Now</a>
    </div>
  </section>

  <section id="collections" class="section">
    <h2>Featured Collections</h2>
    <div class="products-grid">

      <?php if (!empty($products)): ?>
        <?php foreach ($products as $product): ?>
          <?php 
            $pName = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
            $pDesc = htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8');
            $pImg  = htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8');
            $pPrice = (float)($product['price'] ?? 0);
            $pSize = htmlspecialchars($product['size'] ?? 'N/A', ENT_QUOTES, 'UTF-8');
          ?>
          <div class="product-card">
            <img src="<?php echo $pImg; ?>" alt="<?php echo $pName; ?>">
            <div class="info">
              <h3><?php echo $pName; ?></h3>
              <p><?php echo $pDesc; ?></p>
              <p style="font-size:14px;color:#aaa;">Size: <?php echo $pSize; ?></p>
              <span class="price">₱<?php echo number_format($pPrice, 2); ?></span>
              <button onclick="promptLogin()">Add to Cart</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products available.</p>
      <?php endif; ?>

    </div>
  </section>

  <section id="about" class="section about">
    <h2>About Us</h2>
    <p>Thank you for choosing D'Streetwear! We specialize in premium-quality, customized apparel printing that matches your unique style.</p>
    <ul>
      <li>Basic rate ₱90 for A4 size (Minimum of 30pcs)</li>
      <li>₱110 for A3+ size + ₱10 per color (Minimum of 30pcs)</li>
      <li>Packages starting ₱350+ (Depends on design)</li>
      <li>Inclusions: T-shirt, Prints, Label, and Packaging</li>
      <li>Optional: Add ₱20 Etiketa (Bottom/Sleeve), ₱30 for both sides</li>
    </ul>
  </section>

  <footer class="footer" id="contact">
    <div class="footer-content">
      <div class="footer-section">
        <h4>Quick Links</h4>
        <a href="index.html">Home</a>
        <a href="#collections">Shop</a>
        <a href="#about">About</a>
        <a href="#contact">Contact</a>
      </div>

      <div class="footer-section">
        <h4>Support</h4>
        <a href="#">FAQ</a>
        <a href="#">Shipping Policy</a>
        <a href="#">Returns</a>
      </div>

      <div class="footer-section">
        <h4>Contact</h4>
        <p>+63 976 268 1015</p>
        <p>Bergado's Compound, Orchids Street, Putatan, Muntinlupa, Metro Manila, Philippines</p>
      </div>

      <div class="footer-section">
        <h4>Follow Us</h4>
        <a href="#">Instagram</a>
        <a href="https://www.facebook.com/DSTRTWR">Facebook</a>
      </div>
    </div>
    <div class="footer-bottom">
      <p>© 2025 D’Streetwear. All Rights Reserved.</p>
    </div>
  </footer>

<script>
function promptLogin() {
  const popup = document.getElementById("popup");
  popup.textContent = "Please login first to add items to your cart.";
  popup.classList.add("show");

  setTimeout(() => {
    popup.classList.remove("show");
    window.location.href = "login.php";
  }, 1500);
}
</script>


</body>
</html>
