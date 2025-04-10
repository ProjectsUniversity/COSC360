<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

header('Content-Type: application/json');

$job_id = $_GET['job_id'] ?? null;

if (!$job_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Job ID not provided']);
    exit();
}

// Fetch job details
$stmt = $pdo->prepare("
    SELECT j.*, e.company_name,
           (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) as application_count
    FROM jobs j
    JOIN employers e ON j.employer_id = e.employer_id
    WHERE j.job_id = ?
");
$stmt->execute([$job_id]);
$job = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$job) {
    http_response_code(404);
    echo json_encode(['error' => 'Job not found']);
    exit();
}

// Fetch application status breakdown
$stmt = $pdo->prepare("
    SELECT status, COUNT(*) as count
    FROM applications
    WHERE job_id = ?
    GROUP BY status
");
$stmt->execute([$job_id]);
$application_status = [];
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $application_status[$row['status']] = $row['count'];
}

// Fetch recent applications
$stmt = $pdo->prepare("
    SELECT a.*, u.full_name as applicant_name
    FROM applications a
    JOIN users u ON a.user_id = u.user_id
    WHERE a.job_id = ?
    ORDER BY a.applied_at DESC
    LIMIT 5
");
$stmt->execute([$job_id]);
$recent_applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Combine the data
$response = array_merge($job, [
    'application_status' => $application_status,
    'recent_applications' => $recent_applications
]);

// Log the action
logAdminAction('view_job_details', "Viewed details for job ID: $job_id");

echo json_encode($response); 