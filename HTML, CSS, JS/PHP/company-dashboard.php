<?php
session_start();
require_once('config.php');

$isOwner = false;
$employer_id = isset($_GET['employer_id']) ? $_GET['employer_id'] : 
               (isset($_SESSION['employer_id']) ? $_SESSION['employer_id'] : null);

if (!$employer_id) {
    header('Location: homepage.php');
    exit();
}

try {
    // Fetch employer details
    $stmt = $pdo->prepare("
        SELECT e.*, 
               COUNT(DISTINCT j.job_id) as total_jobs,
               COUNT(DISTINCT a.application_id) as total_applications
        FROM employers e
        LEFT JOIN jobs j ON e.employer_id = j.employer_id AND j.status = 'active'
        LEFT JOIN applications a ON j.job_id = a.job_id
        WHERE e.employer_id = ?
        GROUP BY e.employer_id
    ");
    $stmt->execute([$employer_id]);
    $employer = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$employer) {
        $_SESSION['error'] = "Company not found";
        header('Location: homepage.php');
        exit();
    }

    // Check if logged-in employer is viewing their own dashboard
    if (isset($_SESSION['employer_id']) && $_SESSION['employer_id'] == $employer_id) {
        $isOwner = true;
    }

    // Fetch active job listings with application counts
    $stmt = $pdo->prepare("
        SELECT j.*, 
               COUNT(DISTINCT a.application_id) as application_count,
               GROUP_CONCAT(DISTINCT a.status) as application_statuses
        FROM jobs j
        LEFT JOIN applications a ON j.job_id = a.job_id
        WHERE j.employer_id = ? AND j.status = 'active'
        GROUP BY j.job_id
        ORDER BY j.created_at DESC
    ");
    $stmt->execute([$employer_id]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($isOwner) {
        // Fetch recent applications for owner view
        $stmt = $pdo->prepare("
            SELECT a.*, j.title as job_title, u.full_name, u.email
            FROM applications a
            JOIN jobs j ON a.job_id = j.job_id
            JOIN users u ON a.user_id = u.user_id
            WHERE j.employer_id = ?
            ORDER BY a.applied_at DESC
            LIMIT 10
        ");
        $stmt->execute([$employer_id]);
        $recent_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    $_SESSION['error'] = "An error occurred while loading the company profile";
    header('Location: homepage.php');
    exit();
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 3600) {
        return floor($diff / 60) . " minutes ago";
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . " hours ago";
    } elseif ($diff < 172800) {
        return "1 day ago";
    } else {
        return floor($diff / 86400) . " days ago";
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($employer['company_name']); ?> - JobSwipe</title>
    <link rel="stylesheet" href="../CSS/company-dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JS/theme.js" defer></script>
</head>
<body>
    <div class="sidebar">
        <h2>JobSwipe</h2>
        <a href="homepage.php"><i class="fas fa-home"></i> <span>Back to Jobs</span></a>
        <?php if (isset($_SESSION['user_id'])): ?>
            <a href="userprofile.php"><i class="fas fa-user"></i> <span>Your Profile</span></a>
            <a href="saved-jobs.php"><i class="fas fa-bookmark"></i> <span>Saved Jobs</span></a>
        <?php endif; ?>
        <button class="theme-toggle" onclick="toggleTheme()">
            <i class="fas fa-moon"></i> <span>Dark Mode</span>
        </button>
    </div>

    <div class="main-content">
        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="success-message">
                    <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
                </div>
            <?php endif; ?>

            <div class="company-profile section">
                <div class="profile-header">
                    <img src="<?php echo htmlspecialchars($employer['logo_url'] ?? '../assets/default-company-logo.png'); ?>" 
                         alt="<?php echo htmlspecialchars($employer['company_name']); ?>" 
                         class="company-logo">
                    <div class="profile-info">
                        <h1><?php echo htmlspecialchars($employer['company_name']); ?></h1>
                        <div class="company-stats">
                            <span><i class="fas fa-briefcase"></i> <?php echo $employer['total_jobs']; ?> Active Jobs</span>
                            <?php if ($isOwner): ?>
                                <span><i class="fas fa-users"></i> <?php echo $employer['total_applications']; ?> Total Applications</span>
                            <?php endif; ?>
                        </div>
                        <p class="company-description">
                            <?php echo nl2br(htmlspecialchars($employer['description'] ?? 'No company description available.')); ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="section">
                <div class="section-header">
                    <h2>Open Positions</h2>
                    <?php if ($isOwner): ?>
                        <a href="addJobs.php" class="add-job-button">
                            <i class="fas fa-plus"></i> Post New Job
                        </a>
                    <?php endif; ?>
                </div>

                <div class="jobs-grid">
                    <?php if (count($jobs) > 0): ?>
                        <?php foreach ($jobs as $job): ?>
                            <div class="job-card">
                                <div class="job-header">
                                    <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                    <?php if ($isOwner): ?>
                                        <span class="application-count">
                                            <?php echo $job['application_count']; ?> Applications
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="job-details">
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                    <?php if ($job['salary']): ?>
                                        <span><i class="fas fa-dollar-sign"></i> <?php echo htmlspecialchars($job['salary']); ?></span>
                                    <?php endif; ?>
                                    <span><i class="fas fa-clock"></i> <?php echo timeAgo($job['created_at']); ?></span>
                                </div>
                                <p class="job-description"><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                                <div class="job-actions">
                                    <?php if (!$isOwner && isset($_SESSION['user_id'])): ?>
                                        <a href="apply.php?job_id=<?php echo $job['job_id']; ?>" class="apply-button">
                                            Apply Now
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($isOwner): ?>
                                        <button onclick="editJob(<?php echo $job['job_id']; ?>)" class="edit-button">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button onclick="closeJob(<?php echo $job['job_id']; ?>)" class="close-button">
                                            <i class="fas fa-times"></i> Close
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="no-jobs-message">No active job openings at the moment.</p>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($isOwner && !empty($recent_applications)): ?>
            <div class="section">
                <h2>Recent Applications</h2>
                <div class="applications-list">
                    <?php foreach ($recent_applications as $application): ?>
                        <div class="application-card">
                            <div class="application-header">
                                <h4><?php echo htmlspecialchars($application['full_name']); ?></h4>
                                <span class="application-date"><?php echo timeAgo($application['applied_at']); ?></span>
                            </div>
                            <p>Applied for: <?php echo htmlspecialchars($application['job_title']); ?></p>
                            <div class="application-status">
                                Status: <span class="status-<?php echo strtolower($application['status']); ?>">
                                    <?php echo htmlspecialchars($application['status']); ?>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });

        <?php if ($isOwner): ?>
        function editJob(jobId) {
            window.location.href = `editJob.php?job_id=${jobId}`;
        }

        function closeJob(jobId) {
            if (confirm('Are you sure you want to close this job posting?')) {
                fetch(`api/close-job.php?job_id=${jobId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        } else {
                            alert('Error closing job posting');
                        }
                    });
            }
        }
        <?php endif; ?>
    </script>
</body>
</html>