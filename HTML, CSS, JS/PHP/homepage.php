<?php
session_start();
require_once('config.php');

try {
    $stmt = $pdo->prepare("SELECT j.job_id, j.title, j.description, j.location, j.salary, j.created_at,
                          e.company_name, e.employer_id
                          FROM jobs j 
                          JOIN employers e ON j.employer_id = e.employer_id 
                          WHERE j.status = 'active'
                          ORDER BY j.created_at DESC");
    $stmt->execute();
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for proper JSON encoding
    foreach ($jobs as &$job) {
        $job['created_at'] = date('c', strtotime($job['created_at']));
    }
    
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
<body <?php if (isset($_SESSION['user_id'])) echo 'data-user-id="' . $_SESSION['user_id'] . '"'; ?>>
    <div class="sidebar">
        <h2>JobSwipe</h2>        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="userprofile.php"><i class="fas fa-user"></i> Your Account</a>
            <a href="messages.php"><i class="fas fa-envelope"></i> Messages <span id="unread-badge" class="badge bg-danger" style="display: none;">0</span></a>
            <a href="saved-jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
            <button class="theme-toggle" onclick="toggleTheme()">
                <i class="fas fa-moon"></i> Dark Mode
            </button>
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
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
            <?php if (isset($_SESSION['user_id'])): ?>
                <div class="job-actions">
                    <button onclick="saveJob()" class="save-btn" id="save-btn">
                        <i class="fas fa-bookmark"></i>
                        <span>Save Job</span>
                    </button>
                </div>
            <?php endif; ?>
        </div>
        <div class="controls">
            <div class="action">
                <button onclick="rejectJob()" class="reject">
                    <i class="fas fa-times"></i>
                    Not Interested
                </button>
                <button onclick="applyToJob()" class="apply">
                    <i class="fas fa-paper-plane"></i>
                    Easy Apply
                </button>
            </div>
        </div>
    </div>

    <script src="../JS/theme.js"></script>
    <script src="../JS/homepage.js"></script>
    <script>
        // Initialize jobs data from PHP
        initializeJobs(<?php echo $jobsJson; ?>);
    </script>
</body>
</html>