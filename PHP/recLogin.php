<?php

//<?php
session_start();
 // Uncomment this line

// ...existing code...

                if (password_verify($password, $user['password'])) {
                    $_SESSION['recruiter_id'] = $user['id'];
                    $_SESSION['recruiter_email'] = $user['email'];
                    $_SESSION['recruiter_company'] = $user['company_name'];
                    
                    // Handle Remember Me
                    if (isset($_POST['rememberMe'])) {
                        // Set cookie for 30 days
                        setcookie("recruiter_email", $email, time() + (30 * 24 * 60 * 60));
                    }

                    header("Location: dashboard.php"); // Changed from recDashboard.php to dashboard.php
                    exit();
                } else {
                    $errors['login'] = "Invalid email or password";
                }

// ...existing code...require_once 'config.php';

$email = $password = "";
$errors = array();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
    if (empty($password)) {
        $errors['password'] = "Password is required";
    }

    // If no validation errors, attempt login
    if (empty($errors)) {
        try {
            $sql = "SELECT * FROM recruiters WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows === 1) {
                $user = $result->fetch_assoc();
                if (password_verify($password, $user['password'])) {
                    $_SESSION['recruiter_id'] = $user['id'];
                    $_SESSION['recruiter_email'] = $user['email'];
                    $_SESSION['recruiter_company'] = $user['company_name'];
                    
                    // Handle Remember Me
                    if (isset($_POST['rememberMe'])) {
                        // Set cookie for 30 days
                        setcookie("recruiter_email", $email, time() + (30 * 24 * 60 * 60));
                    }

                    header("Location: recDashboard.php");
                    exit();
                } else {
                    $errors['login'] = "Invalid email or password";
                }
            } else {
                $errors['login'] = "Invalid email or password";
            }
        } catch (Exception $e) {
            $errors['db'] = "Database error: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--Bootstrap CSS and JS-->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../CSS/Recruiters/recLogin.css">
    <script src="../JS/Recruiters/recLoginValidation.js"></script>
    <title>Recruiter Login</title>
</head>
<body>
    <div class="container">
        <div class="login-container">
            <h2 class="form-title">Recruiter Login</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($email); ?>"
                           value="<?php echo isset($_COOKIE['recruiter_email']) ? htmlspecialchars($_COOKIE['recruiter_email']) : ''; ?>">
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                </div>
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="rememberMe" name="rememberMe">
                    <label class="form-check-label" for="rememberMe">Remember me</label>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
                <div class="mt-3 text-center">
                    <p>Don't have a recruiter account? <a href="recSignUp.php">Sign Up as Recruiter</a></p>
                    <a href="login.php">Login as a job seeker</a>
                    <p><a href="forgotPassword.php" class="text-muted">Forgot Password?</a></p>
                    <p><a href="index.php">Continue as guest</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>