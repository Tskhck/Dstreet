<?php
    // Load DB connection and start session
    require_once 'connection.php';
    session_start();

    // Initialize error message variable
    $login_error = '';
    // Show message after successful registration
    $registered_message = '';
    if (isset($_GET['registered']) && $_GET['registered'] == '1') {
        $registered_message = 'Registration successful.
        Please log in.';
    }

    // Handle form submission
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Trim inputs
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        if ($username === '' || $password === '') {
            $login_error = 'Please enter both username and password.';
        } else {
            // Prepare statement to avoid SQL injection
            $stmt = mysqli_prepare($conn, 'SELECT password FROM users WHERE username = ? LIMIT 1');
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 's', $username);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_bind_result($stmt, $db_password);
                if (mysqli_stmt_fetch($stmt)) {
                    // If your passwords are hashed (recommended), use password_verify().
                    // This code accepts either a hashed password or plain-text stored password.
                    $password_ok = false;
                    if (password_verify($password, $db_password)) {
                        $password_ok = true;
                    } elseif ($password === $db_password) {
                        $password_ok = true;
                    }

                    if ($password_ok) {
                        // Credentials valid — set session and redirect
                        session_regenerate_id(true);
                        $_SESSION['username'] = $username;
                        // Admin goes to add product page, regular users to home
                        if ($username === 'admin') {
                            header('Location: admin/add_product.php');
                        } else {
                            header('Location: home.php');
                        }
                        exit;
                    } else {
                        $login_error = 'Incorrect password.';
                    }
                } else {
                    $login_error = 'Account not found.';
                }
                mysqli_stmt_close($stmt);
            } else {
                $login_error = 'Database error: could not prepare statement.';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>D'Streetwear - Login</title>
    <link rel="stylesheet" href="login.css">
    <link rel="stylesheet" href="global1.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <a href="landing.php" class="go-back-btn">
        <i class='bx bx-arrow-back'></i>
        <span>Go Back</span>
    </a>

    <div class="container" id="container">
        <div class ="form-container sign-in-container">
    <form action="" method="POST">
        <h1 class="login-title">Login</h1>

        <?php if (!empty($registered_message)): ?>
            <div class="success-message" style="color: #4CAF50; margin-bottom: 12px; text-align: center;">
                <?php echo htmlspecialchars($registered_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($login_error)): ?>
            <div class="error-message" style="color: #ff4444; margin-bottom: 12px; text-align: center;">
                <?php echo htmlspecialchars($login_error); ?>
            </div>
        <?php endif; ?>

        <section class="input-box">
            <input type="text" name="username" placeholder="Username" required>
            <i class='bx bxs-user'></i>
        </section>

        <section class="input-box">
            <input type="password" name="password" placeholder="Password" required>
            <i class='bx bxs-lock-alt'></i>
        </section>

        <div class="remember-forgot-box">
            <label class="remember-me">
                <input type="checkbox" name="remember-me" id="remember-me">
                <span>Remember me</span>
            </label>
            <a href="forgot.php" class="forgot-password">Forgot password?</a>
        </div>

        <button class="login-button" type="submit">
            Login
        </button>

        <p class="dont-have-an-account">
            Don’t have an account? <a href="register.php"><b>Register</b></a>
        </p>
    </form>

</body>
</html>
