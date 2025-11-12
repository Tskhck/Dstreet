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

        <button type="submit" class="btn">Place Order</button>
      </form>
    </div>
  </section>

  <script>
function loadCart() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  const orderItems = document.getElementById("order-items");
  orderItems.innerHTML = ""; 

  if (cart.length === 0) {
    orderItems.innerHTML = '<p class="empty-message">Your order is empty.</p>';
  } else {
    cart.forEach((item, index) => {
      const div = document.createElement("div");
      div.classList.add("item");
      div.innerHTML = `
        <img src="${item.image}" alt="${item.name}">
        <div class="details">
          <p><strong>${item.name}</strong></p>
          <p class="size">Size: ${item.size || "N/A"}</p>
          <span>₱${item.price.toLocaleString()}.00</span>
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
  updateTotal();
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

function updateTotal() {
  let cart = JSON.parse(localStorage.getItem("cart")) || [];
  let total = 0;

  cart.forEach(item => {
    total += item.price * item.quantity;
  });

  document.getElementById("total-price").textContent =
    "₱" + total.toLocaleString() + ".00";

  const orderItems = document.getElementById("order-items");
  if (cart.length === 0) {
    orderItems.innerHTML = '<p class="empty-message">Your order is empty.</p>';
  }
}
loadCart();
  </script>

</body>
</html>
