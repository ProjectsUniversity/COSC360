<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    die('User ID not provided');
}

// Fetch user's resume path
$stmt = $conn->prepare("SELECT resume_path FROM users WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user || !$user['resume_path']) {
    die('Resume not found');
}

$resume_path = $user['resume_path'];
$full_path = __DIR__ . '/../../' . $resume_path;

if (!file_exists($full_path)) {
    die('Resume file not found');
}

// Log the resume view
logAdminAction('view_resume', "Viewed resume for user ID: $user_id");

// Set appropriate headers for PDF viewing
header('Content-Type: application/pdf');
header('Content-Disposition: inline; filename="resume.pdf"');
header('Cache-Control: private, max-age=0, must-revalidate');
header('Pragma: public');

// Output the PDF file
readfile($full_path); 