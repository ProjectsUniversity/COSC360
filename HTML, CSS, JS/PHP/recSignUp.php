<?php
session_start();
require_once 'config.php'; // Make sure to create this file with database credentials

// Initialize variables to store form data and errors
$email = $password = $companyName = $jobTitle = $companySize = $industry = "";
$errors = array();

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate and sanitize input
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $companyName = filter_var($_POST['companyName'], FILTER_SANITIZE_STRING);
    $jobTitle = filter_var($_POST['jobTitle'], FILTER_SANITIZE_STRING);
    $companySize = $_POST['companySize'];
    $industry = filter_var($_POST['industry'], FILTER_SANITIZE_STRING);

    // Validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Invalid email format";
    }
    if (strlen($password) < 8) {
        $errors['password'] = "Password must be at least 8 characters";
    }
    if (empty($companyName)) {
        $errors['companyName'] = "Company name is required";
    }
    if (!isset($_POST['terms'])) {
        $errors['terms'] = "You must agree to the terms and conditions";
    }

    // If no errors, process the registration
    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            // Check if email already exists
            $checkStmt = $pdo->prepare("SELECT employer_id FROM employers WHERE email = ?");
            $checkStmt->execute([$email]);
            
            if ($checkStmt->fetch()) {
                $errors['email'] = "Email already registered";
            } else {
                // Insert new employer with all fields
                $sql = "INSERT INTO employers (company_name, email, password_hash, location) 
                        VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($sql);
                
                if ($stmt->execute([$companyName, $email, $hashedPassword, $industry])) {
                    $_SESSION['employer_id'] = $pdo->lastInsertId();
                    $_SESSION['company_name'] = $companyName;
                    $_SESSION['success'] = "Registration successful!";
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $errors['db'] = "Registration failed. Please try again.";
                }
            }
        } catch (PDOException $e) {
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
    <link rel="stylesheet" href="../CSS/Recruiters/recSignUp.css">
    <script src="../JS/Recruiters/recSignUpValidation.js"></script>
    <title>Recruiter Sign Up</title>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <h2 class="form-title">Recruiter Sign Up</h2>
            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form id="recruiterSignupForm" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>">
                    <div class="form-text">We'll never share your email with anyone else.</div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password">
                    <div class="form-text">Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</div>
                </div>
                <div class="mb-3">
                    <label for="confirmPassword" class="form-label">Confirm Password</label>
                    <input type="password" class="form-control" id="confirmPassword" name="confirmPassword">
                </div>
                
                <div class="mb-3">
                    <label for="companyName" class="form-label">Company Name</label>
                    <input type="text" class="form-control" id="companyName" name="companyName" value="<?php echo htmlspecialchars($companyName); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="jobTitle" class="form-label">Your Job Title</label>
                    <input type="text" class="form-control" id="jobTitle" name="jobTitle" value="<?php echo htmlspecialchars($jobTitle); ?>">
                </div>
                
                <div class="mb-3">
                    <label for="companySize" class="form-label">Company Size</label>
                    <select class="form-select" id="companySize" name="companySize">
                        <option value="1-10" <?php echo ($companySize == '1-10') ? 'selected' : ''; ?>>1-10 employees</option>
                        <option value="11-50" <?php echo ($companySize == '11-50') ? 'selected' : ''; ?>>11-50 employees</option>
                        <option value="51-200" <?php echo ($companySize == '51-200') ? 'selected' : ''; ?>>51-200 employees</option>
                        <option value="201-500" <?php echo ($companySize == '201-500') ? 'selected' : ''; ?>>201-500 employees</option>
                        <option value="501-1000" <?php echo ($companySize == '501-1000') ? 'selected' : ''; ?>>501-1000 employees</option>
                        <option value="1000+" <?php echo ($companySize == '1000+') ? 'selected' : ''; ?>>1000+ employees</option>
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="industry" class="form-label">Industry</label>
                    <input type="text" class="form-control" id="industry" name="industry" value="<?php echo htmlspecialchars($industry); ?>">
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="terms" name="terms">
                    <label class="form-check-label" for="terms">I agree to the Terms and Conditions</label>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">Create Recruiter Account</button>
                
                <div class="mt-3 text-center">
                    <p>Already have an account? <a href="recLogin.php">Login as Recruiter</a></p>
                    <p>Looking for a job? <a href="signup.php">Sign up as a job seeker</a></p>
                    <p><a href="index.php">Continue as guest</a></p>
                </div>
            </form>
        </div>
    </div>
</body>
</html>