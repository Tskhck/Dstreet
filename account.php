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
      <div class="status-card">
        <i class="fas fa-box"></i>
        <p>To Ship</p>
      </div>
      <div class="status-card">
        <i class="fas fa-truck"></i>
        <p>To Receive</p>
      </div>
      <div class="status-card">
        <i class="fas fa-check-circle"></i>
        <p>Completed</p>
      </div>
      <div class="status-card">
        <i class="fas fa-times-circle"></i>
        <p>Cancelled</p>
      </div>
    </div>

    <div class="orders">
      <h3>Recent Orders</h3>

      <div class="order-card">
        <img src="background.jpg" alt="Hoodie">
        <div class="order-info">
          <h4>Streetwear Hoodie</h4>
          <p>Status: <span class="completed">Completed</span></p>
          <p>₱1,299.00</p>
        </div>
      </div>

      <div class="order-card">
        <img src="background.jpg" alt="Tee">
        <div class="order-info">
          <h4>Graphic Tee</h4>
          <p>Status: <span class="completed">Completed</span></p>
          <p>₱799.00</p>
        </div>
      </div>

      <div class="order-card">
        <img src="background.jpg" alt="Jacket">
        <div class="order-info">
          <h4>Varsity Jacket</h4>
          <p>Status: <span class="completed">Completed</span></p>
          <p>₱1,999.00</p>
        </div>
      </div>

    </div>
  </section>

  <footer class="footer">
    <p>© 2025 D’Streetwear. All Rights Reserved.</p>
  </footer>

  <script>
  function confirmLogout(e, el) {
    e.preventDefault();
    if (confirm('Are you sure you want to log out?')) {
      window.location.href = el.getAttribute('href');
    }
  }
  </script>
</body>
</html>
