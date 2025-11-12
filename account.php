<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>D'Streetwear | My Account</title>
  <link rel="stylesheet" href="account.css">
  <link rel="stylesheet" href="global1.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<nav class="navbar">
  <a href="home.php" class="logo">D'Streetwear</a>
  <div class="nav-links">
    <a href="home.php">Home</a>
    <a href="home.php#collections">Shop</a>
    <a href="order.php">Order</a>
    <a href="account.hp" class="active">Account</a>
    <a href="login.php" onclick="confirmLogout(event, this)">Logout</a>
  </div>
</nav>

  <section class="account">
    <div class="account-header">
      <i class="fas fa-user-circle"></i>
      <h2>My Account</h2>
    </div>

    <div class="order-status">
      <div class="status-card" data-status="to_ship" onclick="filterOrders('to_ship', this)">
        <i class="fas fa-box"></i>
        <p>To Ship</p>
      </div>
      <div class="status-card" data-status="to_receive" onclick="filterOrders('to_receive', this)">
        <i class="fas fa-truck"></i>
        <p>To Receive</p>
      </div>
      <div class="status-card" data-status="completed" onclick="filterOrders('completed', this)">
        <i class="fas fa-check-circle"></i>
        <p>Completed</p>
      </div>
      <div class="status-card" data-status="cancelled" onclick="filterOrders('cancelled', this)">
        <i class="fas fa-times-circle"></i>
        <p>Cancelled</p>
      </div>
    </div>

    <div class="orders">
      <h3 id="orders-title">Recent Orders</h3>
      <div id="orders-list">
        <p style="text-align:center;color:#aaa;">Select a status to view your orders.</p>
      </div>

    </div>
  </section>

  <footer class="footer">
  <!-- Order Details Modal -->
  <div id="orderModal" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.7);z-index:1000;align-items:center;justify-content:center;">
    <div style="background:#222;padding:32px 24px;border-radius:16px;max-width:400px;width:90%;position:relative;">
      <span onclick="closeOrderModal()" style="position:absolute;top:12px;right:18px;font-size:22px;cursor:pointer;color:#fff;">&times;</span>
      <div id="orderModalContent" style="color:#fff;"></div>
    </div>
  </div>
    <p>© 2025 D’Streetwear. All Rights Reserved.</p>
  </footer>

  <script>
  // Filter orders by status
  function filterOrders(status, el) {
    // Highlight selected card
    document.querySelectorAll('.status-card').forEach(card => card.classList.remove('active'));
    el.classList.add('active');
    document.getElementById('orders-title').textContent = el.querySelector('p').textContent + ' Orders';
    // Fetch orders via AJAX
    fetch('fetch_orders.php?status=' + encodeURIComponent(status))
      .then(res => res.json())
      .then(data => {
        const list = document.getElementById('orders-list');
        if (data.length === 0) {
          list.innerHTML = '<p style="text-align:center;color:#aaa;">No orders found for this status.</p>';
        } else {
          list.innerHTML = data.map(order => `
            <div class="order-card" style="cursor:pointer;" onclick="showOrderModal(${order.id})">
              <img src="${order.image || 'background.jpg'}" alt="${order.product_type || 'Product'}">
              <div class="order-info">
                <h4>${order.product_type || order.name}</h4>
                <p>Status: <span class="${order.status}">${order.status_label}</span></p>
                <p>₱${parseFloat(order.price).toLocaleString(undefined, {minimumFractionDigits:2})}</p>
              </div>
            </div>
          `).join('');
        }
      });
  }

  // Show order details modal
  function showOrderModal(orderId) {
    fetch('fetch_order_details.php?id=' + orderId)
      .then(res => res.text())
      .then(html => {
        document.getElementById('orderModalContent').innerHTML = html;
        document.getElementById('orderModal').style.display = 'flex';
      });
  }
  function closeOrderModal() {
    document.getElementById('orderModal').style.display = 'none';
  }
  function confirmLogout(e, el) {
    e.preventDefault();
    if (confirm('Are you sure you want to log out?')) {
      window.location.href = el.getAttribute('href');
    }
  }
  // Auto filter if show param present
  (function(){
    const params = new URLSearchParams(window.location.search);
    const show = params.get('show');
    if (show === 'to_ship' || show === 'to_receive' || show === 'completed' || show === 'cancelled') {
      // Find matching status card and trigger filter
      const card = document.querySelector('.status-card[data-status="'+show+'"]');
      if (card) filterOrders(show, card);
    }
  })();
  </script>
</body>
</html>
