<?php
session_start();
require_once('../config.php');
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'not_logged_in']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$jobId = $data['job_id'] ?? null;

if (!$jobId) {
    echo json_encode(['success' => false, 'error' => 'invalid_input']);
    exit();
}

try {
    // Check if already saved
    $stmt = $pdo->prepare("SELECT saved_id FROM saved_jobs WHERE user_id = ? AND job_id = ?");
    $stmt->execute([$_SESSION['user_id'], $jobId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'already_saved']);
        exit();
    }

    // Save the job
    $stmt = $pdo->prepare("INSERT INTO saved_jobs (user_id, job_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $jobId]);
    
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'database_error']);
}