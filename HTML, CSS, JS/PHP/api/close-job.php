<?php
session_start();
require_once('../config.php');

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

if (!isset($_GET['job_id'])) {
    echo json_encode(['success' => false, 'message' => 'Job ID is required']);
    exit();
}

try {
    // Verify the job belongs to this employer
    $stmt = $pdo->prepare("
        SELECT employer_id 
        FROM jobs 
        WHERE job_id = ? AND employer_id = ?
    ");
    $stmt->execute([$_GET['job_id'], $_SESSION['employer_id']]);
    
    if (!$stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit();
    }

    // Update job status to closed
    $stmt = $pdo->prepare("
        UPDATE jobs 
        SET status = 'closed' 
        WHERE job_id = ? AND employer_id = ?
    ");
    $stmt->execute([$_GET['job_id'], $_SESSION['employer_id']]);

    echo json_encode(['success' => true]);

} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}
?>