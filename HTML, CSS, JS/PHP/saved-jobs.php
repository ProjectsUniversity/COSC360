<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT j.*, e.company_name, e.location, s.saved_at
                          FROM saved_jobs s
                          JOIN jobs j ON s.job_id = j.job_id
                          JOIN employers e ON j.employer_id = e.employer_id
                          WHERE s.user_id = ?
                          ORDER BY s.saved_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $savedJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Saved Jobs - JobSwipe</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/saved-jobs.css">
    <script src="../JS/theme.js" defer></script>
</head>
<body>
    <div class="sidebar">
        <h2>JobSwipe</h2>
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="userprofile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="saved-jobs.php" class="active"><i class="fas fa-bookmark"></i> Saved Jobs</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <button class="theme-toggle" onclick="toggleTheme()">
            <i class="fas fa-moon"></i> Dark Mode
        </button>
    </div>

    <div class="main-content">
        <h1 style="margin-left: 2%;">Saved Jobs</h1>
        <div class="saved-jobs-container">
            <?php if (empty($savedJobs)): ?>
                <div class="no-jobs-message">
                    <i class="fas fa-bookmark"></i>
                    <p>You haven't saved any jobs yet.</p>
                    <a href="homepage.php" class="browse-jobs-btn">Browse Jobs</a>
                </div>
            <?php else: ?>
                <?php foreach ($savedJobs as $job): ?>
                    <div class="job-card" data-job-id="<?php echo htmlspecialchars($job['job_id']); ?>">
                        <div class="job-header">
                            <div class="job-title-company">
                                <h2><?php echo htmlspecialchars($job['title']); ?></h2>
                                <h3><?php echo htmlspecialchars($job['company_name']); ?></h3>
                            </div>
                            <button class="unsave-btn" onclick="unsaveJob(<?php echo $job['job_id']; ?>)">
                                <i class="fas fa-bookmark"></i>
                            </button>
                        </div>
                        <div class="job-details">
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location'] ?? 'Remote/Various'); ?></p>
                            <p><i class="fas fa-dollar-sign"></i> <?php echo $job['salary'] ? number_format($job['salary'], 2) : 'Salary not specified'; ?></p>
                            <p><i class="fas fa-calendar"></i> Saved <?php echo date('M d, Y', strtotime($job['saved_at'])); ?></p>
                        </div>
                        <p class="job-description"><?php echo htmlspecialchars($job['description']); ?></p>
                        <div class="job-actions">
                            <button onclick="applyToJob(<?php echo $job['job_id']; ?>)" class="apply-btn">Apply Now</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="../JS/logout.js"></script>
    <script src="../JS/saved-jobs.js"></script>
    <script>
        // Set initial state of theme toggle button
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</body>
</html>