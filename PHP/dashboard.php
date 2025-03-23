<?php
session_start();

// Check if user is logged in
/*if (!isset($_SESSION['recruiter_id'])) {
    header("Location: recLogin.php");
    exit();
}

require_once 'config.php';*/

// Fetch recruiter data
$recruiter_id = $_SESSION['recruiter_id'];
$recruiter_name = $_SESSION['recruiter_company'] ?? 'User';

// Fetch dashboard statistics
/*try {
    // Get active jobs count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM jobs WHERE recruiter_id = ? AND status = 'active'");
    $stmt->bind_param("i", $recruiter_id);
    $stmt->execute();
    $active_jobs = $stmt->get_result()->fetch_row()[0];

    // Get total applicants count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM applications WHERE job_id IN (SELECT id FROM jobs WHERE recruiter_id = ?)");
    $stmt->bind_param("i", $recruiter_id);
    $stmt->execute();
    $total_applicants = $stmt->get_result()->fetch_row()[0];

    // Get scheduled interviews count
    $stmt = $conn->prepare("SELECT COUNT(*) FROM interviews WHERE job_id IN (SELECT id FROM jobs WHERE recruiter_id = ?)");
    $stmt->bind_param("i", $recruiter_id);
    $stmt->execute();
    $scheduled_interviews = $stmt->get_result()->fetch_row()[0];

    // Fetch all jobs for this recruiter
    $stmt = $conn->prepare("SELECT * FROM jobs WHERE recruiter_id = ? ORDER BY created_at DESC");
    $stmt->bind_param("i", $recruiter_id);
    $stmt->execute();
    $jobs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

} catch (Exception $e) {
    $error = "Database error: " . $e->getMessage();
}*/
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recruiter Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/Recruiters/dashboard.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary">
                <div class="sidebar-brand mb-3">
                    <a href="index.php" class="link-body-emphasis text-decoration-none">
                        <span class="fs-4"><h3>Job Swipe</h3></span>
                    </a>
                </div>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active">
                            <i class="fa-solid fa-chart-simple"></i> Dashboard
                        </a>
                    </li>
                    <li>
                        <a href="messages.php" class="nav-link link-body-emphasis">Messages</a>
                    </li>
                    <li>
                        <a href="company_profile.php" class="nav-link link-body-emphasis">Company Profile</a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? '../images/default-avatar.png'); ?>" 
                             alt="Profile" width="32" height="32" class="rounded-circle me-2">
                        <strong><?php echo htmlspecialchars($recruiter_name); ?></strong>
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                        <li><a class="dropdown-item" href="profile.php"><i class="fa-solid fa-user"></i> Profile</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fa-solid fa-door-open"></i> Sign out</a></li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content">
            <header>
                <div class="dashboard-header d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="mb-0">Recruiter Dashboard</h1>
                        <p class="text-muted">Welcome back, <?php echo htmlspecialchars($recruiter_name); ?>!</p>
                    </div>
                    <a href="post_job.php" class="btn btn-success">Post New Job</a>
                </div>
            </header>

            <!-- Stats Widgets -->
            <div class="row py-4">
                <div class="col-md-4">
                    <div class="widgets-col">
                        <h3><?php echo $active_jobs; ?></h3>
                        <p>Active Jobs</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widgets-col">
                        <h3><?php echo $total_applicants; ?></h3>
                        <p>Total Applicants</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widgets-col">
                        <h3><?php echo $scheduled_interviews; ?></h3>
                        <p>Interviews Scheduled</p>
                    </div>
                </div>
            </div>

            <!-- Jobs Section -->
            <section id="mainJobs">
                <ul class="nav nav-tabs mb-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="#jobs">My Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="all_applicants.php">All Applicants</a>
                    </li>
                </ul>

                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if (empty($jobs)): ?>
                    <div class="alert alert-info">No jobs posted yet. Click "Post New Job" to get started!</div>
                <?php else: ?>
                    <?php foreach ($jobs as $job): ?>
                        <div class="card mb-3">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h5 class="card-title"><?php echo htmlspecialchars($job['title']); ?></h5>
                                        <h6 class="card-subtitle mb-2 text-muted"><?php echo htmlspecialchars($job['company_name']); ?></h6>
                                    </div>
                                    <span class="badge bg-<?php echo getStatusColor($job['status']); ?>">
                                        <?php echo ucfirst(htmlspecialchars($job['status'])); ?>
                                    </span>
                                </div>
                                <p class="card-text"><?php echo htmlspecialchars($job['description']); ?></p>
                                <div class="mb-3">
                                    <span class="badge bg-primary"><?php echo htmlspecialchars($job['employment_type']); ?></span>
                                    <span class="badge bg-secondary"><?php echo htmlspecialchars($job['work_type']); ?></span>
                                    <span class="badge bg-info"><?php echo htmlspecialchars($job['category']); ?></span>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="job-stats">
                                        <span class="me-3"><i class="fas fa-eye"></i> <?php echo $job['views']; ?> Views</span>
                                        <span class="me-3"><i class="fas fa-user"></i> <?php echo $job['applicants_count']; ?> Applicants</span>
                                        <span><i class="fas fa-calendar"></i> Posted <?php echo timeAgo($job['created_at']); ?></span>
                                    </div>
                                    <div class="job-actions">
                                        <a href="edit_job.php?id=<?php echo $job['id']; ?>" class="btn btn-sm btn-outline-primary me-2">Edit</a>
                                        <a href="toggle_job_status.php?id=<?php echo $job['id']; ?>" 
                                           class="btn btn-sm btn-outline-<?php echo $job['status'] === 'active' ? 'danger' : 'success'; ?>">
                                            <?php echo $job['status'] === 'active' ? 'Close' : 'Reopen'; ?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </section>
        </main>
    </div>

    <script src="../JS/Recruiters/dashboard.js"></script>
</body>
</html>

<?php
function getStatusColor($status) {
    switch ($status) {
        case 'active':
            return 'success';
        case 'on_hold':
            return 'warning';
        case 'inactive':
            return 'danger';
        default:
            return 'secondary';
    }
}

function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return "just now";
    } elseif ($diff < 3600) {
        return floor($diff / 60) . " minutes ago";
    } elseif ($diff < 86400) {
        return floor($diff / 3600) . " hours ago";
    } elseif ($diff < 604800) {
        return floor($diff / 86400) . " days ago";
    } else {
        return date("M j, Y", $timestamp);
    }
}
?>