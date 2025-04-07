<?php
session_start();
require_once '../config.php';

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: dashboard.php");
    exit();
}

$job_id = $_GET['id'];
$employer_id = $_SESSION['employer_id'];

try {
    // Ensure the job belongs to the logged in employer
    $stmt = $pdo->prepare("SELECT status FROM jobs WHERE job_id = :job_id AND employer_id = :employer_id");
    $stmt->execute([':job_id' => $job_id, ':employer_id' => $employer_id]);
    $job = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$job) {
        die("Job not found or you do not have permission to modify this job.");
    }
    
    // Toggle status: if active or null then set to inactive, otherwise set to active
    $current_status = $job['status'];
    $new_status = ($current_status === 'active' || $current_status === null) ? 'inactive' : 'active';
    
    $stmt = $pdo->prepare("UPDATE jobs SET status = :new_status WHERE job_id = :job_id");
    $stmt->execute([':new_status' => $new_status, ':job_id' => $job_id]);
    
    $_SESSION['success_message'] = "Job status updated successfully.";
} catch (PDOException $e) {
    error_log("Database error in toggle_job_status.php: " . $e->getMessage());
    $_SESSION['success_message'] = "Failed to update job status.";
}

header("Location: dashboard.php");
exit();
?>
