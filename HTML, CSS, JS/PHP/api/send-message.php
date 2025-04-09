<?php
session_start();
require_once('../config.php');

header('Content-Type: application/json');

// Debug logging
error_log("Received message request. POST data: " . file_get_contents('php://input'));
error_log("Session data: " . print_r($_SESSION, true));

// Check if either user or employer is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['employer_id'])) {
    error_log("Unauthorized: No user or employer session found");
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$is_employer = isset($_SESSION['employer_id']);
$sender_id = $is_employer ? $_SESSION['employer_id'] : $_SESSION['user_id'];
$sender_type = $is_employer ? 'employer' : 'user';

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
error_log("Decoded JSON data: " . print_r($data, true));

if (!isset($data['receiver_id']) || !isset($data['message'])) {
    error_log("Missing required fields in request");
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$receiver_id = intval($data['receiver_id']);
$message = trim($data['message']);

try {
    $pdo->beginTransaction();

    // Insert the message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, message_text, sender_type)
        VALUES (?, ?, ?, ?)
    ");
    $stmt->execute([$sender_id, $receiver_id, $message, $sender_type]);
    $message_id = $pdo->lastInsertId();
    error_log("Inserted message with ID: " . $message_id);

    // Create unread message status
    $stmt = $pdo->prepare("
        INSERT INTO message_status (message_id, recipient_id, is_read)
        VALUES (?, ?, FALSE)
    ");
    $stmt->execute([$message_id, $receiver_id]);

    $pdo->commit();
    error_log("Transaction committed successfully");

    // Return success with message data for UI update
    echo json_encode([
        'success' => true,
        'message' => [
            'message_id' => $message_id,
            'sender_id' => $sender_id,
            'receiver_id' => $receiver_id,
            'message_text' => $message,
            'created_at' => date('Y-m-d H:i:s'),
            'sender_type' => $sender_type
        ]
    ]);

} catch (PDOException $e) {
    $pdo->rollBack();
    error_log("Database error in send-message.php: " . $e->getMessage());
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
