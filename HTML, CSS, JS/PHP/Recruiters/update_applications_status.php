<?php
session_start();
require_once '../config.php';

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['application_id'], $_POST['new_status'])) {
    $application_id = $_POST['application_id'];
    $new_status = $_POST['new_status'];
    
    // Validate new status
    $allowed_statuses = ['rejected', 'hired', 'shortlisted'];
    if (!in_array($new_status, $allowed_statuses)) {
        die("Invalid status provided.");
    }
    
    try {
        $sql = "UPDATE applications SET status = :status WHERE application_id = :application_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':status' => $new_status,
            ':application_id' => $application_id
        ]);
        $_SESSION['success_message'] = "Application status updated successfully.";
    } catch (PDOException $e) {
        error_log("Database error in update_application_status.php: " . $e->getMessage());
        $_SESSION['success_message'] = "Failed to update application status.";
    }
    
    header("Location: all_applicants.php");
    exit();
} else {
    die("Invalid request.");
}
?>
