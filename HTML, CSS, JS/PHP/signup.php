<?php
session_start();
require_once('config.php');


if (isset($_SESSION['user_id'])) {
    header('Location: homepage.php');
    exit();
} else if (isset($_SESSION['employer_id'])) {
    header('Location: Recruiters/dashboard.php');
    exit();
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $type = htmlspecialchars($_POST['account_type'] ?? '', ENT_QUOTES, 'UTF-8');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'];
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $location = htmlspecialchars($_POST['location'] ?? '', ENT_QUOTES, 'UTF-8');

        // Check if email already exists in either table
        $stmt = $pdo->prepare("SELECT 'user' as type FROM users WHERE email = ?
                              UNION
                              SELECT 'employer' as type FROM employers WHERE email = ?");
        $stmt->execute([$email, $email]);
        
        if ($stmt->fetch()) {
            throw new Exception('Email already registered');
        }

        if ($type === 'jobseeker') {
            $full_name = htmlspecialchars($_POST['full_name'] ?? '', ENT_QUOTES, 'UTF-8');
            
            $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, location) 
                                  VALUES (?, ?, ?, ?)");
            $stmt->execute([$full_name, $email, $password_hash, $location]);
            
            $_SESSION['user_id'] = $pdo->lastInsertId();
            $_SESSION['user_type'] = 'user';
            
            header('Location: homepage.php');
            exit();
        } else {
            $company_name = htmlspecialchars($_POST['company_name'] ?? '', ENT_QUOTES, 'UTF-8');
            
            $stmt = $pdo->prepare("INSERT INTO employers (company_name, email, password_hash, location) 
                                  VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$company_name, $email, $password_hash, $location]);
            
            $_SESSION['employer_id'] = $pdo->lastInsertId();
            $_SESSION['user_type'] = 'employer';
            
            header('Location: Recruiters/dashboard.php');
            exit();
        }
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - JobSwipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../CSS/signup.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <h2 class="form-title">Create Account</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form id="signupForm" method="POST" action="signup.php" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Account Type</label>
                    <select class="form-select" name="account_type" id="accountType" required>
                        <option value="jobseeker">Job Seeker</option>
                        <option value="employer">Employer</option>
                    </select>
                </div>

                <div id="jobseekerFields">
                    <div class="mb-3">
                        <label for="full_name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" name="full_name" id="full_name">
                    </div>
                </div>

                <div id="employerFields" style="display: none;">
                    <div class="mb-3">
                        <label for="company_name" class="form-label">Company Name</label>
                        <input type="text" class="form-control" name="company_name" id="company_name">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" name="email" id="email" required>
                </div>

                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" name="password" id="password" required minlength="8">
                    <div class="form-text">Password must be at least 8 characters long.</div>
                </div>

                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" name="confirm_password" id="confirm_password" required minlength="8">
                </div>

                <div class="mb-3">
                    <label for="location" class="form-label">Location</label>
                    <input type="text" class="form-control" name="location" id="location" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">Sign Up</button>

                <div class="mt-3 text-center">
                    <p>Already have an account? <a href="login.php">Login</a></p>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('accountType').addEventListener('change', function() {
            const jobseekerFields = document.getElementById('jobseekerFields');
            const employerFields = document.getElementById('employerFields');
            
            if (this.value === 'jobseeker') {
                jobseekerFields.style.display = 'block';
                employerFields.style.display = 'none';
                document.getElementById('company_name').removeAttribute('required');
                document.getElementById('full_name').setAttribute('required', '');
            } else {
                jobseekerFields.style.display = 'none';
                employerFields.style.display = 'block';
                document.getElementById('full_name').removeAttribute('required');
                document.getElementById('company_name').setAttribute('required', '');
            }
        });

        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if (password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>
</html>