<?php
session_start();
require_once('config.php');

try {
    // Update the SQL query to include employer_id
    $stmt = $pdo->prepare("SELECT j.job_id, j.title, j.description, j.location, j.salary, j.created_at,
                          e.company_name, e.employer_id
                          FROM jobs j 
                          JOIN employers e ON j.employer_id = e.employer_id 
                          WHERE j.status = 'active'
                          ORDER BY j.created_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $jobsJson = json_encode($jobs);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>JobSwipe - Find Your Next Career</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="../CSS/homepage.css" />
    <script src="../JS/homepage.js" defer></script>
    <script src="../JS/logout.js" defer></script>
    <style>
        .company-link {
            color: inherit;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        
        .company-link:hover {
            color: var(--highlight-color);
        }

        #company-name {
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>JobSwipe</h2>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="userprofile.php"><i class="fas fa-user"></i> Your Account</a>
            <a href="saved-jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
            <button class="theme-toggle" onclick="toggleTheme()">
                <i class="fas fa-moon"></i> Dark Mode
            </button>
            <a href="#" onclick="confirmLogout()"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <?php else: ?>
            <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
            <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
            <button class="theme-toggle" onclick="toggleTheme()">
                <i class="fas fa-moon"></i> Dark Mode
            </button>
        <?php endif; ?>
    </div>

    <div class="main-content">
        <div class="job-card" id="job-card">
            <img src="company-logo.png" alt="Company Logo" id="company-logo" />
            <h2 id="job-title"></h2>
            <h4 id="company-name" onclick="viewCompanyProfile()">
                <a href="#" class="company-link" id="company-link"></a>
            </h4>
            <p id="job-description"></p>
            <div class="job-details">
                <span id="job-location"><i class="fas fa-map-marker-alt"></i></span>
                <span id="job-salary"><i class="fas fa-dollar-sign"></i></span>
                <span id="job-posted"><i class="fas fa-calendar"></i></span>
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
            <div class="actions">
                <button class="reject" onclick="nextJob('reject')">Reject</button>
                <button class="apply" onclick="applyToJob()">Apply</button>
            </div>
            <button onclick="nextJob('reject')"><i class="fas fa-arrow-right"></i></button>
        </div>
    </div>

    <script>
        const jobs = <?php echo $jobsJson; ?>;
        let currentIndex = 0;

        function viewCompanyProfile() {
            const currentJob = jobs[currentIndex];
            if (currentJob && currentJob.employer_id) {
                window.location.href = `company-dashboard.php?employer_id=${currentJob.employer_id}`;
            }
        }
        
        function updateJobCard() {
            const job = jobs[currentIndex];
            document.getElementById('job-title').textContent = job.title;
            document.getElementById('company-link').textContent = job.company_name;
            document.getElementById('job-description').textContent = job.description;
            document.getElementById('job-location').innerHTML = `<i class="fas fa-map-marker-alt"></i> ${job.location}`;
            document.getElementById('job-salary').innerHTML = `<i class="fas fa-dollar-sign"></i> ${job.salary}`;
            document.getElementById('job-posted').innerHTML = `<i class="fas fa-calendar"></i> ${formatDate(job.created_at)}`;
        }
        
        // Initialize first job
        if (jobs.length > 0) {
            updateJobCard();
        }
    </script>
    <script src="../JS/theme.js"></script>
</body>
</html>