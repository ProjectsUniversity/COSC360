<?php
session_start();
require_once '../config.php';

// Ensure recruiter is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

// Check if a job_id is provided
if (!isset($_GET['job_id'])) {
    header("Location: dashboard.php");
    exit();
}

$job_id = $_GET['job_id'];
$employer_id = $_SESSION['employer_id'];

// Verify that the job belongs to the logged-in recruiter
$stmt = $pdo->prepare("SELECT * FROM jobs WHERE job_id = :job_id AND employer_id = :employer_id");
$stmt->execute([':job_id' => $job_id, ':employer_id' => $employer_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    die("Job not found or you do not have permission to view this job.");
}

// Fetch applicants for the specific job
$stmt = $pdo->prepare("SELECT a.*, u.full_name, u.email 
                       FROM applications a 
                       JOIN users u ON a.user_id = u.user_id 
                       WHERE a.job_id = :job_id 
                       ORDER BY a.applied_at DESC");
$stmt->execute([':job_id' => $job_id]);
$applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicants for <?php echo htmlspecialchars($job['title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>Applicants for "<?php echo htmlspecialchars($job['title']); ?>"</h1>
    <a href="dashboard.php" class="btn btn-secondary mb-3">Back to Dashboard</a>
    <?php if (empty($applications)): ?>
        <div class="alert alert-info">No applications found for this job.</div>
    <?php else: ?>
        <table class="table table-bordered table-hover">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Applied At</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($applications as $application): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($application['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($application['email']); ?></td>
                        <td><?php echo htmlspecialchars($application['applied_at']); ?></td>
                        <td><?php echo htmlspecialchars(ucfirst($application['status'] ?? 'pending')); ?></td>
                        <td>
                            <!-- Action Buttons: Reject, Hire, Shortlist -->
                            <form action="update_application_status.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                <input type="hidden" name="new_status" value="hired">
                                <button type="submit" class="btn btn-success btn-sm">Hire</button>
                            </form>
                            <form action="update_application_status.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                <input type="hidden" name="new_status" value="shortlisted">
                                <button type="submit" class="btn btn-warning btn-sm">Shortlist</button>
                            </form>
                            <form action="update_application_status.php" method="POST" style="display:inline-block;">
                                <input type="hidden" name="application_id" value="<?php echo $application['application_id']; ?>">
                                <input type="hidden" name="new_status" value="rejected">
                                <button type="submit" class="btn btn-danger btn-sm">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
</body>
</html>
