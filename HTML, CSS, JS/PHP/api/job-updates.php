<?php
session_start();
require_once('../config.php');
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Get the timestamp of the last job we have
$lastJobTime = isset($_GET['lastUpdate']) ? $_GET['lastUpdate'] : date('Y-m-d H:i:s');

try {
    // Get any new jobs since the last update
    $stmt = $pdo->prepare("
        SELECT j.*, e.company_name 
        FROM jobs j
        JOIN employers e ON j.employer_id = e.employer_id
        WHERE j.status = 'active' 
        AND j.created_at > ?
        ORDER BY j.created_at DESC
    ");
    
    $stmt->execute([$lastJobTime]);
    $newJobs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Format dates for proper JSON encoding
    foreach ($newJobs as &$job) {
        $job['created_at'] = date('c', strtotime($job['created_at']));
    }
    
    echo json_encode([
        'success' => true,
        'jobs' => $newJobs,
        'currentTime' => date('c')
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Database error occurred'
    ]);
}