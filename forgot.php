<?php
    include 'connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>D'Streetwear - Forgot Password</title>
  <link rel="stylesheet" href="login.css">
  <link rel="stylesheet" href="global1.css">

  <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
  <form class="container" action="#" method="post" novalidate>
    <h1 class="login-title">Forgot Password</h1>

    <p style="font-size: 14px; text-align: center; margin-bottom: 12px; color: rgba(255,255,255,0.8);">
      Enter your registered email address and we’ll send you a reset link.
    </p>

    <section class="input-box">
      <input type="email" name="email" placeholder="Email Address" required>
      <i class="bx bxs-envelope"></i>
    </section>

    <button class="login-button" type="submit">Send Reset Link</button>


    <p class="dont-have-an-account">
      Remembered your password? <a href="login.php"><b>Login</b></a>
    </p>
  </form>
</body>
</html>
