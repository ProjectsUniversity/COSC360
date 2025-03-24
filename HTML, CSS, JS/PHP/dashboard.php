<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

require_once 'config.php';

// Fetch recruiter data
$employer_id = $_SESSION['employer_id'];
$stmt = $pdo->prepare("SELECT * FROM employers WHERE employer_id = ?");
$stmt->execute([$employer_id]);
$employer = $stmt->fetch(PDO::FETCH_ASSOC);
$company_name = $employer['company_name'] ?? 'User';

// Initialize statistics with default values
$active_jobs = 0;
$total_applicants = 0;
$scheduled_interviews = 0;
$error = null;

// Fetch dashboard statistics
try {
    // Get active jobs count - jobs that are not marked as closed or inactive
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM jobs WHERE employer_id = ? AND (status IS NULL OR status = 'active')");
    $stmt->execute([$employer_id]);
    $active_jobs = $stmt->fetchColumn();

    // Get total applicants count for all jobs by this employer
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM applications a 
                          INNER JOIN jobs j ON a.job_id = j.job_id 
                          WHERE j.employer_id = ?");
    $stmt->execute([$employer_id]);
    $total_applicants = $stmt->fetchColumn();

    // Get scheduled interviews count
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM applications a 
                          INNER JOIN jobs j ON a.job_id = j.job_id 
                          WHERE j.employer_id = ? AND a.status = 'Interview Scheduled'");
    $stmt->execute([$employer_id]);
    $scheduled_interviews = $stmt->fetchColumn();

    // Fetch all jobs for this employer with applicant count and company name
    $stmt = $pdo->prepare("SELECT j.*, 
                          (SELECT COUNT(*) FROM applications WHERE job_id = j.job_id) as applicants_count,
                          COALESCE(j.status, 'active') as status,
                          e.company_name
                          FROM jobs j 
                          JOIN employers e ON j.employer_id = e.employer_id
                          WHERE j.employer_id = ? 
                          ORDER BY j.created_at DESC");
    $stmt->execute([$employer_id]);
    $jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=yes">
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
                        <span class="fs-4">JobSwipe</span>
                    </a>
                </div>
                <hr>
                <ul class="nav nav-pills flex-column w-1">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link active">
                            <i class="fa-solid fa-chart-simple"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="addJobs.php" class="nav-link">
                            <i class="fa-solid fa-plus"></i> Post New Job
                        </a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                        <strong><?php echo htmlspecialchars($company_name); ?></strong>
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="settings.php"><i class="fa-solid fa-gear"></i> Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php"><i class="fa-solid fa-door-open"></i> Sign out</a></li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content p-4">
            <?php if (isset($_SESSION['success_message'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo htmlspecialchars($_SESSION['success_message']);
                    unset($_SESSION['success_message']); 
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <header class="d-flex justify-content-between align-items-center" style="width: 100%; height: 100px;">
                <div class="dashboard-header d-flex justify-content-between align-items-center" style="width: 100%; height: 100px;">
                    <div class="d-flex align-items-center">
                        <div>
                            <h1 class="mb-0">Recruiter Dashboard</h1>
                            <p class="text-muted">Welcome back, <?php echo htmlspecialchars($company_name); ?>!</p>
                        </div>
                    </div>
                    <a href="addJobs.php" class="btn btn-success">
                        <i class="fa-solid fa-plus"></i> Post New Job
                    </a>
                </div>
            </header>

            <!-- Stats Widgets -->
            <div class="row py-4">
                <div class="col-md-4">
                    <div class="widgets-col p-3 bg-white rounded shadow-sm">
                        <h3 class="text-primary"><?php echo $active_jobs; ?></h3>
                        <p class="mb-0"><i class="fas fa-briefcase me-2"></i>Active Jobs</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widgets-col p-3 bg-white rounded shadow-sm">
                        <h3 class="text-info"><?php echo $total_applicants; ?></h3>
                        <p class="mb-0"><i class="fas fa-users me-2"></i>Total Applicants</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="widgets-col p-3 bg-white rounded shadow-sm">
                        <h3 class="text-success"><?php echo $scheduled_interviews; ?></h3>
                        <p class="mb-0"><i class="fas fa-calendar-check me-2"></i>Interviews Scheduled</p>
                    </div>
                </div>
            </div>

            <!-- Jobs Section -->
            <section id="mainJobs">
                <div class="card">
                    <div class="card-header bg-white">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#jobs">My Jobs</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="all_applicants.php">All Applicants</a>
                            </li>
                        </ul>
                    </div>
                    <div class="card-body">
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if (empty($jobs)): ?>
                            <div class="text-center py-5">
                                <i class="fas fa-briefcase fa-3x text-muted mb-3"></i>
                                <p class="lead">No jobs posted yet.</p>
                                <a href="addJobs.php" class="btn btn-primary">
                                    <i class="fas fa-plus"></i> Post Your First Job
                                </a>
                            </div>
                        <?php else: ?>
                            <?php foreach ($jobs as $job): ?>
                                <div class="card mb-3 job-card">
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
                                        <p class="card-text mt-3"><?php echo htmlspecialchars($job['description']); ?></p>
                                        <div class="job-details mt-3">
                                            <span><i class="fas fa-map-marker-alt text-muted"></i> <?php echo htmlspecialchars($job['location']); ?></span>
                                            <span><i class="fas fa-dollar-sign text-muted"></i> <?php echo number_format($job['salary']); ?></span>
                                            <span><i class="fas fa-clock text-muted"></i> Posted <?php echo timeAgo($job['created_at']); ?></span>
                                        </div>
                                        <hr>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="job-stats">
                                                <span>
                                                    <i class="fas fa-user text-primary"></i> <?php echo $job['applicants_count']; ?> Applicants
                                                </span>
                                            </div>
                                            <div class="job-actions">
                                                <a href="edit_job.php?id=<?php echo $job['job_id']; ?>" class="btn btn-sm btn-outline-primary me-2">
                                                    <i class="fas fa-edit"></i> Edit
                                                </a>
                                                <a href="toggle_job_status.php?id=<?php echo $job['job_id']; ?>" 
                                                   class="btn btn-sm btn-outline-<?php echo $job['status'] === 'active' ? 'danger' : 'success'; ?>">
                                                    <?php if ($job['status'] === 'active'): ?>
                                                        <i class="fas fa-times-circle"></i> Close
                                                    <?php else: ?>
                                                        <i class="fas fa-check-circle"></i> Reopen
                                                    <?php endif; ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
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
        case 'closed':
            return 'danger';
        default:
            return 'secondary';
    }
}

function timeAgo($datetime) {
    $time = time() - strtotime($datetime);
    $time = ($time < 1) ? 1 : $time;
    
    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits . ' ' . $text . (($numberOfUnits > 1) ? 's' : '') . ' ago';
    }
}
?>