<?php
session_start();
require_once('config.php');

// Check if already logged in
if (isset($_SESSION['user_id']) || isset($_SESSION['employer_id'])) {
    header('Location: homepage.php');
    exit();
}

$error = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    try {
        // Check both users and employers tables
        $stmt = $pdo->prepare("SELECT user_id, password_hash, 'user' as type FROM users 
                              WHERE email = ?
                              UNION
                              SELECT employer_id, password_hash, 'employer' as type 
                              FROM employers WHERE email = ?");
        $stmt->execute([$email, $email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            // Set session variables based on user type
            if ($user['type'] === 'user') {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_type'] = 'user';
            } else {
                $_SESSION['employer_id'] = $user['user_id'];
                $_SESSION['user_type'] = 'employer';
            }

            // Always redirect to homepage after successful login
            header('Location: homepage.php');
            exit();
        } else {
            $error = 'Invalid email or password';
        }
    } catch (PDOException $e) {
        $error = 'Login failed. Please try again.';
        error_log($e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="../CSS/login.css">
    <script src="../JS/loginValidation.js"></script>
    <title>Login - JobSwipe</title>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="form-title">Login</h2>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form id="loginForm" method="POST" action="login.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="remember_me">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="mt-3 text-center">
                    <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                    <p><a href="forgot-password.php" class="text-muted">Forgot Password?</a></p>
                    <p><a href="index.php">Continue as guest</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>