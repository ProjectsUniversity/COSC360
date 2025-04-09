-- Add some test messages between employers and users
INSERT INTO messages (sender_id, receiver_id, message_text, sender_type, created_at)
VALUES 
    (1, 1, 'Hi! We were impressed by your application. Would you be available for an interview next week?', 'employer', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (1, 1, 'Yes, I would love to! What time works best for you?', 'user', DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (1, 1, 'How about Tuesday at 2 PM?', 'employer', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (1, 1, 'That works perfectly for me. Looking forward to it!', 'user', DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (2, 2, 'Thanks for applying! We have a few questions about your experience.', 'employer', DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (2, 2, 'Of course! Happy to help clarify anything.', 'user', DATE_SUB(NOW(), INTERVAL 3 DAY));

-- Add message status records
INSERT INTO message_status (message_id, recipient_id, is_read, read_at)
VALUES 
    (1, 1, TRUE, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (2, 1, TRUE, DATE_SUB(NOW(), INTERVAL 2 DAY)),
    (3, 1, TRUE, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (4, 1, TRUE, DATE_SUB(NOW(), INTERVAL 1 DAY)),
    (5, 2, TRUE, DATE_SUB(NOW(), INTERVAL 3 DAY)),
    (6, 2, FALSE, NULL);
