<?php
session_start();
require_once('../config.php');

header('Content-Type: application/json');

error_log("get-messages.php accessed with GET params: " . print_r($_GET, true));
error_log("Session data: " . print_r($_SESSION, true));

// Check if user or employer is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['employer_id'])) {
    error_log("Unauthorized: No user or employer session found");
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$is_employer = isset($_SESSION['employer_id']);
$current_id = $is_employer ? $_SESSION['employer_id'] : $_SESSION['user_id'];
$current_type = $is_employer ? 'employer' : 'user';

try {
    // If contact_id is provided, get messages for that conversation
    if (isset($_GET['contact_id'])) {
        $contact_id = intval($_GET['contact_id']);
        $contact_type = $is_employer ? 'user' : 'employer';
        
        error_log("Fetching messages for conversation between {$current_id} ({$current_type}) and {$contact_id} ({$contact_type})");
        
        // Get messages between current user and contact
        $stmt = $pdo->prepare("
            SELECT 
                m.*,
                CASE 
                    WHEN m.sender_type = 'employer' THEN e.company_name
                    ELSE u.full_name
                END as sender_name
            FROM messages m
            LEFT JOIN employers e ON m.sender_id = e.employer_id AND m.sender_type = 'employer'
            LEFT JOIN users u ON m.sender_id = u.user_id AND m.sender_type = 'user'
            WHERE (m.sender_id = ? AND m.receiver_id = ? AND m.sender_type = ?)
               OR (m.sender_id = ? AND m.receiver_id = ? AND m.sender_type = ?)
            ORDER BY m.created_at ASC
        ");
        
        // Execute with proper sender types for both directions
        $stmt->execute([
            $current_id, $contact_id, $current_type,  // Current user sending
            $contact_id, $current_id, $contact_type   // Contact sending
        ]);
        
        $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found " . count($messages) . " messages");

        // Mark messages as read
        $updateStmt = $pdo->prepare("
            UPDATE message_status ms
            JOIN messages m ON ms.message_id = m.message_id
            SET ms.is_read = TRUE, ms.read_at = CURRENT_TIMESTAMP
            WHERE m.receiver_id = ?
            AND m.sender_id = ?
            AND ms.recipient_id = ?
            AND ms.is_read = FALSE
        ");
        $updateStmt->execute([$current_id, $contact_id, $current_id]);
        $updatedCount = $updateStmt->rowCount();
        error_log("Marked {$updatedCount} messages as read");

        echo json_encode(['success' => true, 'messages' => $messages]);
    } else {
        error_log("Fetching conversation list for user {$current_id}");
        // Get list of contacts with latest message
        if ($is_employer) {
            // For employers, show users who have applied to their jobs or have conversations
            $stmt = $pdo->prepare("
                SELECT DISTINCT 
                    u.user_id as contact_id,
                    u.full_name as contact_name,
                    (SELECT message_text 
                     FROM messages 
                     WHERE (sender_id = ? AND receiver_id = u.user_id)
                        OR (sender_id = u.user_id AND receiver_id = ?)
                     ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at 
                     FROM messages 
                     WHERE (sender_id = ? AND receiver_id = u.user_id)
                        OR (sender_id = u.user_id AND receiver_id = ?)
                     ORDER BY created_at DESC LIMIT 1) as last_message_time,
                    COUNT(CASE WHEN ms.is_read = FALSE AND m.receiver_id = ? THEN 1 END) as unread_count
                FROM users u
                LEFT JOIN applications a ON u.user_id = a.user_id
                LEFT JOIN jobs j ON a.job_id = j.job_id
                LEFT JOIN messages m ON 
                    (m.sender_id = u.user_id AND m.receiver_id = ?) OR
                    (m.sender_id = ? AND m.receiver_id = u.user_id)
                LEFT JOIN message_status ms ON 
                    ms.message_id = m.message_id AND 
                    ms.recipient_id = ?
                WHERE j.employer_id = ? OR m.message_id IS NOT NULL
                GROUP BY u.user_id, u.full_name
                HAVING last_message IS NOT NULL
                ORDER BY last_message_time DESC NULLS LAST
            ");
            $params = array_fill(0, 9, $current_id);
            $stmt->execute($params);
        } else {
            // For users, show only employers who have messaged them
            $stmt = $pdo->prepare("
                SELECT DISTINCT 
                    e.employer_id as contact_id,
                    e.company_name as contact_name,
                    (SELECT message_text 
                     FROM messages 
                     WHERE ((sender_id = ? AND receiver_id = e.employer_id) 
                        OR (sender_id = e.employer_id AND receiver_id = ?))
                     ORDER BY created_at DESC LIMIT 1) as last_message,
                    (SELECT created_at 
                     FROM messages 
                     WHERE ((sender_id = ? AND receiver_id = e.employer_id) 
                        OR (sender_id = e.employer_id AND receiver_id = ?))
                     ORDER BY created_at DESC LIMIT 1) as last_message_time,
                    COUNT(CASE WHEN ms.is_read = FALSE AND m.receiver_id = ? THEN 1 END) as unread_count
                FROM employers e
                LEFT JOIN messages m ON 
                    (m.sender_id = e.employer_id AND m.receiver_id = ?) OR
                    (m.sender_id = ? AND m.receiver_id = e.employer_id)
                LEFT JOIN message_status ms ON 
                    ms.message_id = m.message_id AND 
                    ms.recipient_id = ?
                WHERE m.message_id IS NOT NULL
                GROUP BY e.employer_id, e.company_name
                ORDER BY last_message_time DESC
            ");
            $params = array_fill(0, 8, $current_id);
            $stmt->execute($params);
        }
        
        $contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
        error_log("Found " . count($contacts) . " contacts with messages");
        echo json_encode(['success' => true, 'contacts' => $contacts]);
    }
} catch (PDOException $e) {
    error_log("Database error in get-messages.php: " . $e->getMessage());
    error_log("SQL state: " . $e->errorInfo[0]);
    error_log("Error code: " . $e->errorInfo[1]);
    error_log("Error message: " . $e->errorInfo[2]);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>
