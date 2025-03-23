<?php
session_start();
require_once('config.php');

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header('Location: login.php');
    exit();
}

try {
    // Fetch employer details
    $stmt = $pdo->prepare("SELECT * FROM employers WHERE employer_id = ?");
    $stmt->execute([$_SESSION['employer_id']]);
    $employer = $stmt->fetch(PDO::FETCH_ASSOC);

    // Fetch active job listings
    $stmt = $pdo->prepare("SELECT * FROM jobs WHERE employer_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['employer_id']]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Company Dashboard - JobSwipe</title>
    <link rel="stylesheet" href="/HTML, CSS, JS/CSS/company-dashboard.css">
    <script src="/HTML, CSS, JS/JS/company-dashboard.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <div class="sidebar">
        <h2>JobSwipe</h2>
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="userprofile.php"><i class="fas fa-user"></i> Profile</a>
        <a href="#" class="active"><i class="fas fa-building"></i> Company</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="section">
                <div class="profile-header">
                    <img src="<?php echo htmlspecialchars($employer['logo_path'] ?? 'tech-corp-logo.png'); ?>" alt="Company Logo" class="profile-picture">
                    <div class="profile-info">
                        <h1><?php echo htmlspecialchars($employer['company_name']); ?></h1>
                        <p><?php echo htmlspecialchars($employer['description'] ?? 'Technology Solutions Provider'); ?></p>
                        <div class="company-info">
                            <p><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($employer['location']); ?></p>
                            <p><i class="fas fa-globe"></i> <?php echo htmlspecialchars($employer['website'] ?? 'www.company.com'); ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="section">
                <h2>Active Job Openings</h2>
                <div class="filters">
                    <button class="filter-button" data-type="filter">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                    <button class="filter-button" data-type="sort">
                        <i class="fas fa-sort"></i> Sort
                    </button>
                    <button class="filter-button">
                        <i class="fas fa-calendar"></i> Date Posted
                    </button>
                    <button class="filter-button">
                        <i class="fas fa-user-clock"></i> Status
                    </button>
                </div>
                <div class="jobs-list">
                    <?php foreach ($jobs as $job): ?>
                        <div class="job-card">
                            <div class="job-info">
                                <h3><?php echo htmlspecialchars($job['title']); ?></h3>
                                <div class="job-details">
                                    <span><i class="fas fa-clock"></i> Full-time</span> •
                                    <span><i class="fas fa-dollar-sign"></i> <?php echo htmlspecialchars($job['salary']); ?></span> •
                                    <span><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                </div>
                                <p>Posted <?php echo timeAgo($job['created_at']); ?> • <span class="job-status">Active</span></p>
                            </div>
                            <div class="job-actions">
                                <button class="apply-button" onclick="window.location.href='apply.php?job=<?php echo urlencode($job['title']); ?>&job_id=<?php echo $job['job_id']; ?>'">
                                    Apply Now
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div id="editModal" class="modal">
                <div class="modal-content">
                    <span class="close">&times;</span>
                    <h2 id="modal-title">Filter Jobs</h2>
                    <form id="filterForm" onsubmit="return handleFilter(event)">
                        <div class="form-group">
                            <label for="jobType">Job Type</label>
                            <select id="jobType" name="jobType">
                                <option value="">All Types</option>
                                <option value="full-time">Full-time</option>
                                <option value="part-time">Part-time</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="location">Location</label>
                            <input type="text" id="location" name="location">
                        </div>
                        <button type="submit" class="apply-button">Apply Filters</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
<?php
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 86400) {
        return "today";
    } elseif ($diff < 172800) {
        return "1 day ago";
    } else {
        return floor($diff / 86400) . " days ago";
    }
}
?>