<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>D'Streetwear | Order</title>
  <link rel="stylesheet" href="order.css">
  <link rel="stylesheet" href="global1.css">

</head>
<body>

  <nav class="navbar">
    <a href="index.html" class="logo">D'Streetwear</a>
    <div class="nav-links">
      <a href="home.php">Home</a>
      <a href="home.php#collections">Shop</a>
      <a href="order.php" class="active">Order</a>
      <a href="account.php">Account</a>
    </div>
  </nav>

  <section class="order">
    <div class="order-container">
      <h2>Checkout</h2>

      <div class="order-summary">
        <h3>Order Summary</h3>
        <div id="order-items">
          <p class="empty-message">Your order is empty.</p>
        </div>

        <div class="total">
          <p>Total</p>
          <span id="total-price">₱0.00</span>
        </div>
      </div>

      <form class="checkout-form">
        <h3>Shipping Information</h3>
        <label>Full Name</label>
        <input type="text" placeholder="Juan Dela Cruz" required>

        <label>Email</label>
        <input type="email" placeholder="you@example.com" required>

        <label>Phone</label>
        <input type="text" placeholder="0912 345 6789" required>

        <label>Address</label>
        <input type="text" placeholder="Street, Barangay, City" required>

        <label>Postal Code</label>
        <input type="text" placeholder="1000" required>

        <h3>Payment Method</h3>
        <label>
          <input type="radio" name="payment" required> Cash on Delivery
        </label>
        <label>
          <input type="radio" name="payment"> GCash
        </label>

  <button type="submit" class="btn" id="placeOrderBtn">Place Order</button>
      </form>
    </div>
  </section>

  <script>
// DEBUG PANEL SETUP
// Debug panel removed per request
function updateDebug(_) { /* noop */ }
console.log('[ORDER] Raw cart string:', localStorage.getItem('cart'));
function loadCart() {
  let raw = localStorage.getItem("cart");
  let cart = [];
  try { cart = JSON.parse(raw || '[]'); } catch(e) { console.warn('Failed to parse cart JSON', e); cart = []; }
  // Migrate legacy entries without price
  let migrated = false;
  cart.forEach(item => {
    if (typeof item.price !== 'number' || isNaN(item.price)) {
      // Attempt simple heuristic: if item has a cached originalPrice use it
      if (item.originalPrice && !isNaN(parseFloat(item.originalPrice))) {
        item.price = parseFloat(item.originalPrice);
      } else {
        // Fallback to 0 so UI still renders
        item.price = 0;
      }
      migrated = true;
    }
    if (typeof item.quantity !== 'number' || isNaN(item.quantity) || item.quantity < 1) {
      item.quantity = 1;
      migrated = true;
    }
  });
  if (migrated) {
    localStorage.setItem('cart', JSON.stringify(cart));
    console.log('Migrated cart entries without price.');
  }
  updateDebug({cart, migrated});

  const orderItems = document.getElementById("order-items");
  orderItems.innerHTML = "";
  if (cart.length === 0) {
    orderItems.innerHTML = '<p class="empty-message">Your order is empty.</p>';
  } else {
    cart.forEach((item, index) => {
      const safePrice = (typeof item.price === 'number' && !isNaN(item.price)) ? item.price : 0;
      const div = document.createElement("div");
      div.classList.add("item");
      div.innerHTML = `
        <img src="${item.image}" alt="${item.name}">
        <div class="details">
          <p><strong>${item.name}</strong></p>
          <p class="size">Size: ${item.size || "N/A"}</p>
          <span>₱${safePrice.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2})}</span>
          <div class="qty-controls">
            <button class="qty-btn minus" data-index="${index}">-</button>
            <span>${item.quantity}</span>
            <button class="qty-btn plus" data-index="${index}">+</button>
          </div>
        </div>
      `;
      orderItems.appendChild(div);
    });
  }
  updateTotal(cart);
}

document.addEventListener("click", function(e) {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];

  if (e.target.classList.contains("plus")) {
    let index = e.target.getAttribute("data-index");
    cart[index].quantity += 1;
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
  }

  if (e.target.classList.contains("minus")) {
    let index = e.target.getAttribute("data-index");
    if (cart[index].quantity > 1) {
      cart[index].quantity -= 1;
    } else {
      cart.splice(index, 1); 
    }
    localStorage.setItem("cart", JSON.stringify(cart));
    loadCart();
  }
});

function updateTotal(cartOverride) {
  let cart = cartOverride || JSON.parse(localStorage.getItem("cart")) || [];
  let total = 0;
  cart.forEach(item => {
    const priceNum = (typeof item.price === 'number' && !isNaN(item.price)) ? item.price : 0;
    total += priceNum * item.quantity;
  });
  document.getElementById("total-price").textContent =
    "₱" + total.toLocaleString(undefined,{minimumFractionDigits:2,maximumFractionDigits:2});
  if (cart.length === 0) {
    document.getElementById("order-items").innerHTML = '<p class="empty-message">Your order is empty.</p>';
  }
  updateDebug({cart, total});
}
loadCart();
  </script>
  <script>
  // Intercept form submission to create orders
  (function(){
    const form = document.querySelector('.checkout-form');
    if(!form) return;
    form.addEventListener('submit', function(ev){
      ev.preventDefault();
      let cartRaw = localStorage.getItem('cart');
      let cart = [];
      try { cart = JSON.parse(cartRaw||'[]'); } catch(e) {}
      if (cart.length === 0) {
        alert('Cart is empty.');
        return;
      }
      const inputs = form.querySelectorAll('input[type="text"], input[type="email"]');
      const shipping = {};
      inputs.forEach(inp => {
        const label = inp.previousElementSibling ? inp.previousElementSibling.textContent.trim() : inp.name;
        shipping[label.toLowerCase().replace(/\s+/g,'_')] = inp.value.trim();
      });
      // payment method
      const paymentEl = form.querySelector('input[name="payment"]:checked');
      const payment = paymentEl ? paymentEl.parentElement.textContent.trim() : 'Unknown';
      fetch('create_order.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body: JSON.stringify({cart, shipping, payment})
      }).then(r=>r.json()).then(resp=>{
        if(!resp.ok){
          alert('Failed to place order: ' + (resp.error||'unknown error'));
        } else {
          // Clear cart
          localStorage.removeItem('cart');
          alert('Order placed!');
          // Redirect to account with to_ship filter parameter
          window.location.href = 'account.php?show=to_ship';
        }
      }).catch(err=>{
        alert('Network error placing order');
        console.error(err);
      });
    });
  })();
  </script>

</body>
</html>
  <!-- Diagnostics instrumentation -->
  <script>
  (function(){
    try {
      const inventory = Array.from(document.scripts).map((s,i)=>({index:i, src:s.getAttribute('src')||null, inlineLength:(s.text||'').length}));
      console.log('[DIAG] Script inventory on order.php:', inventory);
      window.addEventListener('error', function(ev){
        console.log('[DIAG] Global error caught:', ev.message, 'at', ev.filename, ev.lineno+':'+ev.colno);
      });
      console.log('[DIAG] Initial cart raw (order.php):', localStorage.getItem('cart'));
    } catch(e){
      console.log('[DIAG] Instrumentation failed (order.php):', e);
    }
  })();
  </script>
