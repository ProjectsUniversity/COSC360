<?php
session_start();
header('Content-Type: application/json');

require_once '../config.php';  // Adjust relative path as needed

// Ensure recruiter is logged in
if (!isset($_SESSION['employer_id'])) {
    http_response_code(403);
    echo json_encode(["error" => "Not authorized"]);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['application_id'], $_POST['new_status'])) {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['new_status'];
    
    // Validate new status
    $allowed_statuses = ['hired', 'rejected', 'shortlisted'];
    if (!in_array($new_status, $allowed_statuses)) {
        http_response_code(400);
        echo json_encode(["error" => "Invalid status provided."]);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE applications SET status = :status WHERE application_id = :application_id");
        $stmt->execute([
            ':status' => $new_status,
            ':application_id' => $application_id
        ]);
        echo json_encode(["success" => true, "new_status" => $new_status]);
    } catch (PDOException $e) {
        error_log("Database error in update_application_status.php: " . $e->getMessage());
        http_response_code(500);
        echo json_encode(["error" => "Failed to update application status."]);
    }
    exit();
} else {
    http_response_code(400);
    echo json_encode(["error" => "Invalid request."]);
    exit();
}
?>
