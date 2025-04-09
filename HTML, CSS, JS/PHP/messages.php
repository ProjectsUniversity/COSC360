<?php
session_start();
require_once('config.php');

// Check if user or employer is logged in
if (!isset($_SESSION['user_id']) && !isset($_SESSION['employer_id'])) {
    header("Location: login.php");
    exit();
}

$is_employer = isset($_SESSION['employer_id']);
$current_id = $is_employer ? $_SESSION['employer_id'] : $_SESSION['user_id'];

// For employers, fetch all applicants
$applicants = [];
$employer_conversations = [];

if ($is_employer) {
    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT 
                u.user_id, 
                u.full_name, 
                u.email,
                a.job_id,
                j.title as job_title,
                (SELECT COUNT(*) FROM messages m 
                 WHERE ((m.sender_id = ? AND m.receiver_id = u.user_id) 
                    OR (m.sender_id = u.user_id AND m.receiver_id = ?))
                ) as message_count,
                (SELECT message_text 
                 FROM messages m 
                 WHERE ((m.sender_id = ? AND m.receiver_id = u.user_id) 
                    OR (m.sender_id = u.user_id AND m.receiver_id = ?))
                 ORDER BY m.created_at DESC LIMIT 1
                ) as last_message,
                (SELECT COUNT(*) FROM messages m 
                 JOIN message_status ms ON m.message_id = ms.message_id
                 WHERE m.receiver_id = ? 
                 AND m.sender_id = u.user_id
                 AND ms.is_read = FALSE
                ) as unread_count
            FROM users u
            JOIN applications a ON u.user_id = a.user_id
            JOIN jobs j ON a.job_id = j.job_id
            WHERE j.employer_id = ?
            ORDER BY unread_count DESC, message_count DESC, u.full_name ASC
        ");
        $stmt->execute([$current_id, $current_id, $current_id, $current_id, $current_id, $current_id]);
        $applicants = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Debug output
        if (empty($applicants)) {
            error_log("No applicants found for employer_id: " . $current_id);
        } else {
            error_log("Found " . count($applicants) . " applicants for employer_id: " . $current_id);
            error_log("Sample applicant data: " . print_r($applicants[0], true));
        }
    } catch (PDOException $e) {
        error_log("Database error in messages.php: " . $e->getMessage());
    }
} else {
    // For users, fetch conversations with employers
    try {
        $stmt = $pdo->prepare("
            SELECT DISTINCT 
                e.employer_id,
                e.company_name,
                j.title as job_title,
                (SELECT COUNT(*) FROM messages m 
                 WHERE ((m.sender_id = ? AND m.receiver_id = e.employer_id) 
                    OR (m.sender_id = e.employer_id AND m.receiver_id = ?))
                ) as message_count,
                (SELECT message_text 
                 FROM messages m 
                 WHERE ((m.sender_id = ? AND m.receiver_id = e.employer_id) 
                    OR (m.sender_id = e.employer_id AND m.receiver_id = ?))
                 ORDER BY m.created_at DESC LIMIT 1
                ) as last_message,
                (SELECT created_at 
                 FROM messages m 
                 WHERE ((m.sender_id = ? AND m.receiver_id = e.employer_id) 
                    OR (m.sender_id = e.employer_id AND m.receiver_id = ?))
                 ORDER BY m.created_at DESC LIMIT 1
                ) as last_message_time,
                (SELECT COUNT(*) FROM messages m 
                 JOIN message_status ms ON m.message_id = ms.message_id
                 WHERE m.receiver_id = ? 
                 AND m.sender_id = e.employer_id
                 AND ms.is_read = FALSE
                ) as unread_count
            FROM employers e
            JOIN messages m ON (m.sender_id = e.employer_id AND m.receiver_id = ?)
                OR (m.sender_id = ? AND m.receiver_id = e.employer_id)
            LEFT JOIN jobs j ON j.employer_id = e.employer_id
            GROUP BY e.employer_id, e.company_name, j.title
            ORDER BY last_message_time DESC
        ");
        $stmt->execute(array_fill(0, 9, $current_id));
        $employer_conversations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        error_log("Found " . count($employer_conversations) . " employer conversations for user_id: " . $current_id);
        if (!empty($employer_conversations)) {
            error_log("Sample conversation data: " . print_r($employer_conversations[0], true));
        }
    } catch (PDOException $e) {
        error_log("Database error fetching user conversations: " . $e->getMessage());
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages - JobSwipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/messages.css">
</head>
<body data-user-type="<?php echo $is_employer ? 'employer' : 'user'; ?>" 
      data-user-id="<?php echo $current_id; ?>">

    <div class="page-container">
        <!-- Back to dashboard/home button -->
        <a href="<?php echo $is_employer ? 'Recruiters/dashboard.php' : 'homepage.php'; ?>" class="back-btn">
            <i class="fas fa-arrow-left"></i> Back to <?php echo $is_employer ? 'Dashboard' : 'Home'; ?>
        </a>

        <div class="messages-container">
            <!-- Contacts/Applicants List -->
            <div class="contacts-list">
                <div class="contacts-header">
                    <h2><?php echo $is_employer ? 'All Applicants' : 'Messages'; ?></h2>
                </div>
                <div class="contacts-search">
                    <input type="text" id="contact-search" placeholder="Search <?php echo $is_employer ? 'applicants' : 'conversations'; ?>...">
                </div>
                <?php if ($is_employer): ?>
                <div id="applicants-container">
                    <?php foreach ($applicants as $applicant): ?>
                    <div class="contact-item applicant-item" 
                         data-user-id="<?php echo $applicant['user_id']; ?>"
                         data-user-name="<?php echo htmlspecialchars($applicant['full_name']); ?>">
                        <div class="contact-info">
                            <div class="contact-name">
                                <?php echo htmlspecialchars($applicant['full_name']); ?>
                                <?php if ($applicant['unread_count'] > 0): ?>
                                <span class="unread-badge"><?php echo $applicant['unread_count']; ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="contact-email"><?php echo htmlspecialchars($applicant['email']); ?></div>
                            <div class="job-applied">Applied for: <?php echo htmlspecialchars($applicant['job_title']); ?></div>
                            <?php if ($applicant['last_message']): ?>
                            <div class="last-message"><?php echo htmlspecialchars(substr($applicant['last_message'], 0, 50)) . (strlen($applicant['last_message']) > 50 ? '...' : ''); ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($applicant['message_count'] == 0): ?>
                        <button class="start-chat-btn" onclick="startNewChat(this)">
                            <i class="fas fa-comment"></i> Start Chat
                        </button>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div id="contacts-container">
                    <?php foreach ($employer_conversations as $conversation): ?>                    <div class="contact-item employer-item" 
                         data-user-id="<?php echo $conversation['employer_id']; ?>"
                         data-user-name="<?php echo htmlspecialchars($conversation['company_name']); ?>">
                        <div class="contact-info">
                            <div class="contact-name">
                                <?php echo htmlspecialchars($conversation['company_name']); ?>
                                <?php if ($conversation['unread_count'] > 0): ?>
                                <span class="unread-badge"><?php echo $conversation['unread_count']; ?></span>
                                <?php endif; ?>
                            </div>
                            <?php if ($conversation['job_title']): ?>
                            <div class="job-title"><?php echo htmlspecialchars($conversation['job_title']); ?></div>
                            <?php endif; ?>
                            <?php if ($conversation['last_message']): ?>
                            <div class="last-message"><?php echo htmlspecialchars(substr($conversation['last_message'], 0, 50)) . (strlen($conversation['last_message']) > 50 ? '...' : ''); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <!-- Chat Area -->
            <div class="chat-area">
                <div id="chat-header">
                    <h3><?php echo $is_employer ? 'Select an applicant' : 'Select a conversation'; ?> to start chatting</h3>
                </div>
                <div id="messages-container">
                    <!-- Messages will be loaded here -->
                </div>
                <div class="message-input-container" style="display: none;">
                    <textarea id="message-input" placeholder="Type a message..."></textarea>
                    <button id="send-message" class="send-btn">
                        <i class="fas fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- New Chat Modal for recruiters -->
    <?php if ($is_employer): ?>
    <div class="modal fade" id="newChatModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">New Message to <span id="recipientName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="newMessageText" class="form-control" rows="4" 
                        placeholder="Type your first message..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="sendFirstMessage()">Send</button>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../JS/messages.js"></script>
</body>
</html>

