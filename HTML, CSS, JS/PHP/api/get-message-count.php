<?php
session_start();
require_once('../config.php');

header('Content-Type: application/json');

// Check if user or employer is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['employer_id'])) {
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$is_employer = isset($_SESSION['employer_id']);
$current_id = $is_employer ? $_SESSION['employer_id'] : $_SESSION['user_id'];

try {
    // Count unread messages for the current user
    $stmt = $pdo->prepare("
        SELECT COUNT(*) as unread_count
        FROM messages m
        JOIN message_status ms ON m.message_id = ms.message_id
        WHERE m.receiver_id = ?
        AND ms.recipient_id = ?
        AND ms.is_read = FALSE
    ");
    $stmt->execute([$current_id, $current_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'unread_count' => intval($result['unread_count'])
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
