<?php
    include 'connection.php';
    $error_message = '';
    $success_message = '';

    if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize form inputs
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirm_password = mysqli_real_escape_string($conn, $_POST['confirm-password']);
    // Terms checkbox (present only if user agreed)
    $terms_accepted = isset($_POST['terms']) ? 1 : 0;

        // Validation
        if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
            $error_message = "All fields are required.";
        } elseif (!$terms_accepted) {
            $error_message = "You must accept the Terms & Conditions to register.";
        } elseif ($password !== $confirm_password) {
            $error_message = "Passwords do not match.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = "Invalid email format.";
        } else {
            // Check if email exists
            $checkEmail = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $checkEmail);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "s", $email);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_store_result($stmt);
                
                if(mysqli_stmt_num_rows($stmt) > 0) {
                    $error_message = "Email is already registered.";
                } else {
                    // Hash password and insert user
                    $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                    
                    // Prepare insert statement
                    $insert_sql = "INSERT INTO users (username, email, password) VALUES (?, ?, ?)";
                    $insert_stmt = mysqli_prepare($conn, $insert_sql);
                    
                    if ($insert_stmt) {
                        mysqli_stmt_bind_param($insert_stmt, "sss", $username, $email, $hashed_password);
                        
                        if (mysqli_stmt_execute($insert_stmt)) {
                            // Redirect to login with a success flag
                            header('Location: login.php?registered=1');
                            exit;
                        } else {
                            $error_message = "Registration failed. Please try again.";
                        }
                        mysqli_stmt_close($insert_stmt);
                    } else {
                        $error_message = "Database error. Please try again.";
                    }
                }
                mysqli_stmt_close($stmt);
            } else {
                $error_message = "Database error. Please try again.";
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>D'Streetwear - Register</title>
    <link rel="stylesheet" href="register.css">
    <link href="https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css" rel="stylesheet">
</head>
<body>
    <form class="container" method="post" novalidate>
        <h1 class="login-title">Register</h1>

        <?php if (!empty($error_message)): ?>
            <div class="error-message" style="color: #ff4444; margin-bottom: 15px; text-align: center;">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success_message)): ?>
            <div class="success-message" style="color: #4CAF50; margin-bottom: 15px; text-align: center;">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>

        <section class="input-box">
            <input type="text" name="username" placeholder="Username" required
                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            <i class="bx bxs-user"></i>
        </section>

        <section class="input-box">
            <input type="email" name="email" placeholder="Email" required
                   value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            <i class="bx bxs-envelope"></i>
        </section>

        <section class="input-box">
            <input type="password" name="password" placeholder="Password" required>
            <i class="bx bxs-lock-alt"></i>
        </section>

        <section class="input-box">
            <input type="password" name="confirm-password" placeholder="Confirm Password" required>
            <i class="bx bxs-lock"></i>
        </section>

        <div class="remember-forgot-box">
            <label class="remember-me">
                <input type="checkbox" name="terms" required>
                <span>I agree to the <a href="terms.php">Terms & Conditions</a></span>
            </label>
        </div>

        <button class="login-button" type="submit">Register</button>

        <p class="dont-have-an-account">
            Already have an account? <a href="login.php"><b>Login</b></a>
        </p>
    </form>
</body>
</html>
