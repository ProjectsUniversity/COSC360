<?php
session_start();
require_once('../config.php');

header('Content-Type: application/json');

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    echo json_encode(['error' => 'Only employers can initiate conversations']);
    exit();
}

$employer_id = $_SESSION['employer_id'];

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['user_id']) || !isset($data['message'])) {
    echo json_encode(['error' => 'Missing required fields']);
    exit();
}

$user_id = intval($data['user_id']);
$message = trim($data['message']);

try {
    $pdo->beginTransaction();

    // Insert the message
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, message_text, sender_type)
        VALUES (?, ?, ?, 'employer')
    ");
    $stmt->execute([$employer_id, $user_id, $message]);
    $message_id = $pdo->lastInsertId();

    // Create unread message status
    $stmt = $pdo->prepare("
        INSERT INTO message_status (message_id, recipient_id, is_read)
        VALUES (?, ?, FALSE)
    ");
    $stmt->execute([$message_id, $user_id]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message_id' => $message_id]);

} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
