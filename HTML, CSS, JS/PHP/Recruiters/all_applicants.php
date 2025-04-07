<?php
session_start();
require_once '../config.php';

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

$employer_id = $_SESSION['employer_id'];

try {
    // Query to fetch all applicants for jobs posted by this employer
    $sql = "SELECT a.application_id, a.status, a.applied_at, j.job_id, j.title AS job_title, u.user_id, u.full_name, u.email
            FROM applications a
            JOIN jobs j ON a.job_id = j.job_id
            JOIN users u ON a.user_id = u.user_id
            WHERE j.employer_id = :employer_id
            ORDER BY a.applied_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':employer_id' => $employer_id]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Applicants - JobSwipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../CSS/Recruiters/dashboard.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar (similar to dashboard sidebar) -->
        <aside class="sidebar">
            <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary">
                <div class="sidebar-brand mb-3">
                    <a href="index.php" class="link-body-emphasis text-decoration-none">
                        <span class="fs-4">JobSwipe</span>
                    </a>
                </div>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
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
                        <strong><?php echo htmlspecialchars($_SESSION['company_name'] ?? 'Recruiter'); ?></strong>
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="../logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content p-4 w-100">
            <h2>All Applicants</h2>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if (empty($applications)): ?>
                <div class="alert alert-info">No applications found.</div>
            <?php else: ?>
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Job Title</th>
                            <th>Applicant Name</th>
                            <th>Email</th>
                            <th>Applied At</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $applicant): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($applicant['job_title']); ?></td>
                                <td><?php echo htmlspecialchars($applicant['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                                <td><?php echo htmlspecialchars($applicant['applied_at']); ?></td>
                                <td><?php echo htmlspecialchars(ucfirst($applicant['status'] ?? 'pending')); ?></td>
                                <td>
                                    <!-- Action Buttons: Reject, Hire, Shortlist -->
                                    <form action="update_application_status.php" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="application_id" value="<?php echo $applicant['application_id']; ?>">
                                        <input type="hidden" name="new_status" value="hired">
                                        <button type="submit" class="btn btn-success btn-sm">Hire</button>
                                    </form>
                                    <form action="update_application_status.php" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="application_id" value="<?php echo $applicant['application_id']; ?>">
                                        <input type="hidden" name="new_status" value="shortlisted">
                                        <button type="submit" class="btn btn-warning btn-sm">Shortlist</button>
                                    </form>
                                    <form action="update_application_status.php" method="POST" style="display:inline-block;">
                                        <input type="hidden" name="application_id" value="<?php echo $applicant['application_id']; ?>">
                                        <input type="hidden" name="new_status" value="rejected">
                                        <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </main>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
