<?php
session_start();
require_once __DIR__ . '/admin/admin_connection.php';

try {
  $stmt = $con->query("SELECT * FROM products ORDER BY created_at DESC");
  $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
  $products = [];
}
$isAdmin = (isset($_SESSION['username']) && $_SESSION['username'] === 'admin');
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
      <?php if (!$isAdmin): ?>
        <a href="order.php" id="cartNavLink">Order <span id="cartCount" class="cart-count" style="display:none;">0</span></a>
      <?php endif; ?>
      <a href="account.php">Account</a>
      <?php if ($isAdmin): ?>
        <a href="admin/add_product.php" style="background:#32CDD5;padding:8px 16px;border-radius:20px;color:#000;font-weight:600;">Admin</a>
      <?php endif; ?>
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
          <?php 
            $pName = htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8');
            $pDesc = htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8');
            $pImg  = htmlspecialchars($product['image'] ?? '', ENT_QUOTES, 'UTF-8');
            $pPrice = (float)($product['price'] ?? 0);
          ?>
          <div class="product-card" data-name="<?php echo $pName; ?>" data-price="<?php echo number_format($pPrice,2,'.',''); ?>" data-image="<?php echo $pImg; ?>">
            <img src="<?php echo $pImg; ?>" alt="<?php echo $pName; ?>">
            <div class="info">
              <h3><?php echo $pName; ?></h3>
              <p><?php echo $pDesc; ?></p>
              <span class="price">₱<?php echo number_format($pPrice, 2); ?></span>
              <button class="add-to-cart-btn" type="button">Add to Cart</button>
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
      if (!popup) { console.warn('[HOME] popup element missing'); return; }
      popup.textContent = message;
      // Inline fallback in case CSS not loaded yet
      popup.style.visibility = 'visible';
      popup.style.opacity = '1';
      popup.style.transform = 'translateY(0)';
      popup.style.zIndex = '3000';
      popup.classList.add('show');
      console.log('[HOME] Notification shown:', message, popup.getBoundingClientRect());
      setTimeout(() => {
        popup.classList.remove('show');
        popup.style.opacity = '0';
        popup.style.visibility = 'hidden';
      }, 2200);
    }

    // Add item to cart (delegated)
    function addToCart(productCard) {
      const name = productCard.getAttribute('data-name');
      const price = parseFloat(productCard.getAttribute('data-price')) || 0;
      const image = productCard.getAttribute('data-image') || productCard.querySelector('img').src;
      let cart;
      try { cart = JSON.parse(localStorage.getItem('cart') || '[]'); } catch(e) { cart = []; }
      const existing = cart.find(i => i.name === name);
      if (existing) {
        existing.quantity = (existing.quantity||0) + 1;
      } else {
        cart.push({ name, image, price, quantity:1 });
      }
      localStorage.setItem('cart', JSON.stringify(cart));
      console.log('[HOME] Cart after add:', cart);
      updateCartBadge(cart);
      updateCartCount(cart);
      showNotification(name + ' added to cart!');
    }

    function updateCartBadge(cart) {
      let badge = document.getElementById('cartBadge');
      if (!badge) {
        const a = document.createElement('span');
        a.id = 'cartBadge';
        a.style.position='fixed';
        a.style.bottom='12px';
        a.style.left='12px';
        a.style.background='#32CDD5';
        a.style.color='#000';
        a.style.padding='6px 10px';
        a.style.borderRadius='6px';
        a.style.fontSize='12px';
        a.style.zIndex='1000';
        document.body.appendChild(a);
        badge = a;
      }
      const totalQty = cart.reduce((sum,i)=>sum + (i.quantity||0),0);
      badge.textContent = 'Cart items: ' + totalQty;
    }
    function updateCartCount(cart) {
      const span = document.getElementById('cartCount');
      if (!span) return;
      const totalQty = cart.reduce((sum,i)=>sum + (i.quantity||0),0);
      span.textContent = totalQty;
      span.style.display = totalQty > 0 ? 'inline-block' : 'none';
    }
    // Initialize badge on load if existing cart
    try { const existing = JSON.parse(localStorage.getItem('cart')||'[]'); updateCartBadge(existing); updateCartCount(existing);} catch(e) {}
    // Delegated click for all add-to-cart buttons
    document.addEventListener('click', function(ev){
      if (ev.target && ev.target.classList.contains('add-to-cart-btn')) {
        const card = ev.target.closest('.product-card');
        if (card) { addToCart(card); }
      }
    });

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
  <!-- Diagnostics instrumentation -->
  <script>
  (function(){
    try {
      const inventory = Array.from(document.scripts).map((s,i)=>({index:i, src:s.getAttribute('src')||null, inlineLength:(s.text||'').length}));
      console.log('[DIAG] Script inventory on home.php:', inventory);
      window.addEventListener('error', function(ev){
        console.log('[DIAG] Global error caught:', ev.message, 'at', ev.filename, ev.lineno+':'+ev.colno);
      });
      // Ensure popup exists even if earlier markup failed to load
      if(!document.getElementById('popup')) {
        const p=document.createElement('div');
        p.id='popup';
        p.className='popup';
        p.textContent='Popup (reconstructed)';
        p.style.visibility='hidden';
        document.body.appendChild(p);
        console.log('[DIAG] Popup element reconstructed');
      }
      // Log localStorage cart raw value early
      console.log('[DIAG] Initial cart raw:', localStorage.getItem('cart'));
    } catch(e){
      console.log('[DIAG] Instrumentation failed:', e);
    }
  })();
  </script>
</body>
</html>
