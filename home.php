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
  <link rel="stylesheet" href="global1.css">
  <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
  <style>
    
  </style>
</head>
<body>
  <div id="popup" class="popup">Item added to cart!</div>
  <nav class="navbar">
    <a href="home.php" class="logo">
      <img src="logo.jpg" alt="D'Streetwear Logo">
      <span>D'Streetwear</span>
    </a>
    <div class="nav-links">
      <a href="home.php">Home</a>
      <a href="#collections">Shop</a>
      <a href="order.php">Order</a>
      <a href="account.php">Account</a>
      <a href="landing.php" onclick="confirmLogout(event, this)">Logout</a>
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
          <div class="product-card">
            <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
            <div class="info">
              <h3><?php echo htmlspecialchars($product['name']); ?></h3>
              <p><?php echo htmlspecialchars($product['description']); ?></p>
              <button onclick="addToCart('<?php echo htmlspecialchars(addslashes($product['name'])); ?>', this)">Add to Cart</button>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p>No products found.</p>
      <?php endif; ?>

    </div>
  </section>

  <script>
    // Show notification when item is added to cart
    function showNotification(message) {
      const popup = document.getElementById("popup");
      popup.textContent = message;
      popup.classList.add("show");
      setTimeout(() => popup.classList.remove("show"), 2000);
    }

    // Add item to cart
    function addToCart(name, button) {
      const productCard = button.closest('.product-card');
      const image = productCard.querySelector('img').src;
      
      // Get cart from storage or create new
      const cart = JSON.parse(localStorage.getItem('cart') || '[]');
      
      // Find if product already exists
      const existingProduct = cart.find(item => item.name === name);
      
      if (existingProduct) {
        existingProduct.quantity++;
      } else {
        cart.push({
          name: name,
          image: image,
          quantity: 1
        });
      }
      
      // Save cart and show notification
      localStorage.setItem('cart', JSON.stringify(cart));
      showNotification(name + ' added to cart!');
    }

    // Confirm before logout
    function confirmLogout(event, link) {
      event.preventDefault();
      if (confirm('Are you sure you want to log out?')) {
        window.location.href = link.getAttribute('href');
      }
    }
  </script>

<section id="about" class="section about">
  <h2>About Us</h2>
  <p>Thank you for choosing DSTREETWEAR!</p>
  <ul>
    <li>Basic rate 90 pesos for A4 size (Minimum of 30pcs)</li>
    <li>₱110 for A3+ size + ₱10 per color (Minimum of 30pcs). Options: Silkscreen Rubberized Print, Puff, High Density, Glow In The Dark</li>
    <li>Packages starting ₱350+ (Depends on design) minimum of 30–50 pcs shirt</li>
    <li>Inclusions: T-shirt, Prints, Inner/Care Label, Plastic with Adhesive</li>
    <li>Optional: Add ₱20 Etiketa (Bottom or Sleeve), ₱30 for both sides</li>
  </ul>
</section>



  <footer class="footer" id="contact">
    <div class="footer-content">
      <div class="footer-section">
        <h4>Quick Links</h4>
        <a href="home.php">Home</a>
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
        <p>D STREETWEAR, Bergado's Compound, Orchids Street, Putatan, Muntinlupa, Metro Manila, Philippines</p>
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

  <div class="chat-trigger" onclick="toggleChat()">
    <i class='bx bx-message-dots'></i>
  </div>

  <div class="chat-box" id="chatBox">
    <div class="chat-header">
      <h3>Chat with Us</h3>
      <span class="close-chat" onclick="toggleChat()">×</span>
    </div>
    <div class="chat-messages" id="chatMessages">
      <div class="message bot-message">Hi! How can I help you today?</div>
    </div>
    <div class="chat-input">
      <input type="text" id="messageInput" placeholder="Type your message..." onkeypress="handleKeyPress(event)">
      <button onclick="sendMessage()">Send</button>
    </div>
  </div>

  <div class="popup-overlay" id="popupOverlay"></div>
  <div id="orderPopup">
    <h3>Start Your Order</h3>
    <p>Would you like to proceed with creating your order?</p>
    <div class="popup-buttons">
      <button class="cancel-btn" onclick="closeOrderPopup()">Cancel</button>
      <button class="confirm-btn" onclick="goToOrderForm()">Create Order</button>
    </div>
  </div>

  <script>
    function toggleChat() {
      const chatBox = document.getElementById('chatBox');
      chatBox.classList.toggle('show');
    }

    function sendMessage() {
      const input = document.getElementById('messageInput');
      const message = input.value.trim();
      if (message) {
        addMessage(message, 'user');
        input.value = '';

        // Check if message contains order-related keywords
        const orderKeywords = ['order', 'buy', 'purchase', 'get', 'shirt', 'hoodie', 'design'];
        if (orderKeywords.some(keyword => message.toLowerCase().includes(keyword))) {
          setTimeout(() => {
            addMessage("Would you like to place an order? I can help you with that!", 'bot');
            showOrderPopup();
          }, 500);
        } else {
          setTimeout(() => {
            addMessage("How can I assist you today?", 'bot');
          }, 500);
        }
      }
    }

    function handleKeyPress(event) {
      if (event.key === 'Enter') {
        sendMessage();
      }
    }

    function addMessage(text, type) {
      const messagesDiv = document.getElementById('chatMessages');
      const messageDiv = document.createElement('div');
      messageDiv.classList.add('message', `${type}-message`);
      messageDiv.textContent = text;
      messagesDiv.appendChild(messageDiv);
      messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function showOrderPopup() {
      document.getElementById('orderPopup').classList.add('show');
      document.getElementById('popupOverlay').classList.add('show');
    }

    function closeOrderPopup() {
      document.getElementById('orderPopup').classList.remove('show');
      document.getElementById('popupOverlay').classList.remove('show');
    }

    function goToOrderForm() {
      window.location.href = 'order_form.php';
    }
  </script>
</body>
</html>
