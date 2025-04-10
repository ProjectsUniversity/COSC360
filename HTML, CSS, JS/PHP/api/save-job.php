<?php
session_start();
require_once('../config.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Please log in to save jobs']);
    exit();
}

// Get job_id from POST data
$data = json_decode(file_get_contents('php://input'), true);
$job_id = $data['job_id'] ?? null;

if (!$job_id) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Job ID is required']);
    exit();
}

try {
    // Check if job exists
    $stmt = $pdo->prepare("SELECT job_id FROM jobs WHERE job_id = ? AND status = 'active'");
    $stmt->execute([$job_id]);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Job not found or inactive']);
        exit();
    }

    // Check if job is already saved
    $stmt = $pdo->prepare("SELECT saved_id FROM saved_jobs WHERE user_id = ? AND job_id = ?");
    $stmt->execute([$_SESSION['user_id'], $job_id]);
    if ($stmt->fetch()) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Job already saved']);
        exit();
    }

    // Save the job
    $stmt = $pdo->prepare("INSERT INTO saved_jobs (user_id, job_id) VALUES (?, ?)");
    $stmt->execute([$_SESSION['user_id'], $job_id]);

    echo json_encode(['success' => true, 'message' => 'Job saved successfully']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error occurred']);
}