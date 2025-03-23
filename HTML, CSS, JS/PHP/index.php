<?php
session_start();
require_once('config.php');

try {
    // Fetch all active jobs with company details
    $stmt = $pdo->prepare("SELECT j.*, e.company_name, e.logo_path 
                          FROM jobs j 
                          JOIN employers e ON j.employer_id = e.employer_id 
                          ORDER BY j.created_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Convert jobs to JSON for JavaScript usage
    $jobsJson = json_encode($jobs);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JobSwipe - Find Your Next Career</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="/HTML, CSS, JS/CSS/homepage.css" />
</head>
<body>
    <div class="sidebar">
        <h2>JobSwipe</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="userprofile.php"><i class="fas fa-user"></i> Your Account</a>
            <a href="saved-jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
            <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
        <?php endif; ?>
        <a href="help.php"><i class="fas fa-question-circle"></i> Help</a>
    </div>

    <div class="main-content">
        <div class="job-card" id="job-card">
            <img src="" alt="Company Logo" id="company-logo" />
            <h2 id="job-title"></h2>
            <h4 id="company-name"></h4>
            <p id="job-description"></p>
            <div class="job-details">
                <span id="job-location"><i class="fas fa-map-marker-alt"></i></span>
                <span id="job-salary"><i class="fas fa-dollar-sign"></i></span>
            </div>
            <div class="social-icons">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <i class="fas fa-heart" onclick="likeJob()"></i>
                    <i class="fas fa-bookmark" onclick="saveJob()"></i>
                <?php endif; ?>
                <i class="fas fa-share" onclick="shareJob()"></i>
            </div>
        </div>
        <div class="controls">
            <button onclick="nextJob('reject')"><i class="fas fa-arrow-left"></i></button>
            <div class="actions">
                <button class="reject" onclick="nextJob('reject')">Reject</button>
                <button class="apply" onclick="applyToJob()">Apply</button>
            </div>
            <button onclick="nextJob('apply')"><i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <script>
        // Pass PHP jobs data to JavaScript
        const jobs = <?php echo $jobsJson; ?>;
        let currentJobIndex = 0;

        function displayJob(index) {
            if (index >= 0 && index < jobs.length) {
                const job = jobs[index];
                document.getElementById('company-logo').src = job.logo_path || 'company-logo.png';
                document.getElementById('job-title').textContent = job.title;
                document.getElementById('company-name').textContent = job.company_name;
                document.getElementById('job-description').textContent = job.description;
                document.getElementById('job-location').innerHTML = 
                    `<i class="fas fa-map-marker-alt"></i> ${job.location}`;
                document.getElementById('job-salary').innerHTML = 
                    `<i class="fas fa-dollar-sign"></i> ${job.salary}`;
            }
        }

        function nextJob(action) {
            if (action === 'reject') {
                currentJobIndex++;
            } else if (action === 'apply') {
                const currentJob = jobs[currentJobIndex];
                window.location.href = `apply.php?job_id=${currentJob.job_id}`;
                return;
            }
            
            if (currentJobIndex >= jobs.length) {
                currentJobIndex = 0;
            }
            displayJob(currentJobIndex);
        }

        function applyToJob() {
            <?php if (!isset($_SESSION['user_id'])): ?>
                window.location.href = 'login.php';
            <?php else: ?>
                const currentJob = jobs[currentJobIndex];
                window.location.href = `apply.php?job_id=${currentJob.job_id}`;
            <?php endif; ?>
        }

        // Initialize with first job
        displayJob(currentJobIndex);
    </script>
</body>
</html>