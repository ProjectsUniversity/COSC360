<?php
session_start();
require_once('config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get job and company details
if (isset($_GET['job_id'])) {
    try {
        // Get job details with company information
        $stmt = $pdo->prepare("
            SELECT j.*, e.company_name, e.location, e.employer_id
            FROM jobs j
            JOIN employers e ON j.employer_id = e.employer_id
            WHERE j.job_id = ? AND j.status = 'active'
        ");
        $stmt->execute([$_GET['job_id']]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            $_SESSION['error'] = 'Job not found or no longer active';
            header('Location: homepage.php');
            exit();
        }

        // Check if user has already applied
        $stmt = $pdo->prepare("
            SELECT application_id, status 
            FROM applications 
            WHERE user_id = ? AND job_id = ?
        ");
        $stmt->execute([$_SESSION['user_id'], $_GET['job_id']]);
        $existingApplication = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingApplication) {
            $_SESSION['error'] = "You have already applied for this position. Current status: " . $existingApplication['status'];
            header('Location: userprofile.php');
            exit();
        }
        
        // Get user details
        $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        $_SESSION['error'] = "An error occurred while processing your request";
        header('Location: homepage.php');
        exit();
    }
} else {
    header('Location: homepage.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        if (empty($_POST['cover-letter'])) {
            throw new Exception('Please provide a cover letter');
        }

        // Begin transaction
        $pdo->beginTransaction();

        // Insert application
        $stmt = $pdo->prepare("
            INSERT INTO applications (job_id, user_id, cover_letter, status, applied_at)
            VALUES (?, ?, ?, 'Pending', CURRENT_TIMESTAMP)
        ");
        $stmt->execute([
            $_POST['job_id'],
            $_SESSION['user_id'],
            $_POST['cover-letter']
        ]);

        // Commit transaction
        $pdo->commit();

        $_SESSION['success'] = "Application submitted successfully!";
        header("Location: userprofile.php");
        exit();

    } catch (Exception $e) {
        // Rollback transaction if there was an error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $_SESSION['error'] = $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apply - <?php echo htmlspecialchars($job['title']); ?></title>
    <link rel="stylesheet" href="../CSS/apply.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JS/theme.js" defer></script>
</head>
<body>
    <a href="homepage.php" class="back-button">
        <i class="fas fa-arrow-left"></i>
        <span>Back to Jobs</span>
    </a>

    <div class="container">
        <div class="header">
            <h1><?php echo htmlspecialchars($job['title']); ?></h1>
            <p><?php echo htmlspecialchars($job['company_name']); ?> • <?php echo htmlspecialchars($job['location']); ?></p>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error-message">
                <i class="fas fa-exclamation-circle"></i>
                <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form id="application-form" method="POST" action="apply.php?job_id=<?php echo $job['job_id']; ?>">
            <div class="form-group">
                <label for="full-name">Full Name</label>
                <input type="text" id="full-name" value="<?php echo htmlspecialchars($user['full_name']); ?>" 
                       readonly disabled>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                       readonly disabled>
            </div>

            <div class="form-group">
                <label for="cover-letter">Why are you a good fit for this role?</label>
                <textarea name="cover-letter" id="cover-letter" 
                          placeholder="Tell us about your relevant experience and why you're excited about this role"
                          required></textarea>
            </div>

            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
            <button type="submit">Submit Application</button>
        </form>
    </div>

    <script>
        // Set initial theme
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</body>
</html>