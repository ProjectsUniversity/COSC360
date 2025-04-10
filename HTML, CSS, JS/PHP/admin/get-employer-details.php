<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

header('Content-Type: application/json');

$employer_id = $_GET['employer_id'] ?? null;

if (!$employer_id) {
    http_response_code(400);
    echo json_encode(['error' => 'Employer ID not provided']);
    exit();
}

// Fetch employer details
$stmt = $pdo->prepare("
    SELECT e.*, 
           (SELECT COUNT(*) FROM jobs j WHERE j.employer_id = e.employer_id) as total_jobs,
           (SELECT COUNT(*) FROM jobs j WHERE j.employer_id = e.employer_id AND j.status = 'active') as active_jobs,
           (SELECT COUNT(*) FROM applications a 
            JOIN jobs j ON a.job_id = j.job_id 
            WHERE j.employer_id = e.employer_id) as total_applications
    FROM employers e 
    WHERE e.employer_id = ?
");
$stmt->execute([$employer_id]);
$employer = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$employer) {
    http_response_code(404);
    echo json_encode(['error' => 'Employer not found']);
    exit();
}

// Fetch recent job listings
$stmt = $pdo->prepare("
    SELECT j.*, 
           (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) as application_count
    FROM jobs j 
    WHERE j.employer_id = ? 
    ORDER BY j.created_at DESC 
    LIMIT 5
");
$stmt->execute([$employer_id]);
$recent_jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Combine the data
$response = array_merge($employer, ['recent_jobs' => $recent_jobs]);

// Log the action
logAdminAction('view_employer_details', "Viewed details for employer ID: $employer_id");

echo json_encode($response); 