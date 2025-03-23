<?php
session_start();
require_once('config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get job details
if (isset($_GET['job_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT j.*, e.company_name, e.location 
                              FROM jobs j 
                              JOIN employers e ON j.employer_id = e.employer_id 
                              WHERE j.job_id = ?");
        $stmt->execute([$_GET['job_id']]);
        $job = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$job) {
            throw new Exception('Job not found');
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    }
} else {
    header('Location: homepage.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../CSS/apply.css">
    <title>Apply for <?php echo htmlspecialchars($job['title']); ?></title>
</head>
<body>
    <div class="container">
        <div class="logo"><?php echo htmlspecialchars($job['company_name']); ?></div>
        <img src="company-logo.png" alt="Company Logo" width="100">
        <h2>Apply for <span id="job-title"><?php echo htmlspecialchars($job['title']); ?></span></h2>
        <p><a href="homepage.php">&larr; Back to Jobs</a></p>
        
        <form id="application-form" method="POST" action="apply.php?job_id=<?php echo $job['job_id']; ?>" enctype="multipart/form-data">
            <input type="text" name="full-name" id="full-name" 
                   value="<?php echo htmlspecialchars($_SESSION['full_name'] ?? ''); ?>" placeholder="Full Name" required>
            <input type="email" name="email" id="email" 
                   value="<?php echo htmlspecialchars($_SESSION['email'] ?? ''); ?>" placeholder="Email" required>
            <textarea name="cover-letter" id="cover-letter" placeholder="Cover Letter" rows="4" required></textarea>
            <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
            <div class="file-upload">
                <label for="resume">Upload Resume (PDF, DOC, DOCX)</label>
                <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx" required>
            </div>
            <button type="submit">Submit Application</button>
        </form>

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <?php
            try {
                // Check if user already applied
                $stmt = $pdo->prepare("SELECT application_id FROM applications 
                                     WHERE user_id = ? AND job_id = ?");
                $stmt->execute([$_SESSION['user_id'], $_POST['job_id']]);
                if ($stmt->fetch()) {
                    throw new Exception('You have already applied for this position');
                }

                $coverLetter = filter_input(INPUT_POST, 'cover-letter', FILTER_SANITIZE_STRING);
                
                // Handle resume upload
                $resumePath = '';
                if(isset($_FILES['resume'])) {
                    $resume = $_FILES['resume'];
                    $uploadDir = 'uploads/resumes/';
                    if (!file_exists($uploadDir)) {
                        mkdir($uploadDir, 0777, true);
                    }
                    $resumePath = $uploadDir . uniqid() . '_' . basename($resume['name']);
                    
                    if(!move_uploaded_file($resume['tmp_name'], $resumePath)) {
                        throw new Exception('Failed to upload resume');
                    }
                }

                // Insert application with resume path
                $stmt = $pdo->prepare("INSERT INTO applications (job_id, user_id, cover_letter, resume_path, status) 
                                     VALUES (?, ?, ?, ?, 'Pending')");
                
                $stmt->execute([
                    $_POST['job_id'],
                    $_SESSION['user_id'],
                    $coverLetter,
                    $resumePath
                ]);

                echo "<div class='success-message'>Application submitted successfully!</div>";
                header("refresh:2;url=homepage.php");
            } catch (Exception $e) {
                echo "<div class='error-message'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
            }
            ?>
        <?php endif; ?>
    </div>
    <script src="../JS/apply.js"></script>
</body>
</html>