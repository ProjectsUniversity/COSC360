-- Create admins table
CREATE TABLE IF NOT EXISTS admins (
    admin_id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_login TIMESTAMP NULL,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Create audit_logs table
CREATE TABLE IF NOT EXISTS audit_logs (
    log_id INT PRIMARY KEY AUTO_INCREMENT,
    admin_id INT NOT NULL,
    action_type VARCHAR(50) NOT NULL,
    details TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(admin_id)
);

-- Insert mock admin users with plain text passwords ('admin123')
INSERT INTO admins (username, email, password_hash) VALUES
('admin', 'admin@jobboard.com', 'admin123'),
('john_doe', 'john@jobboard.com', 'admin123'),
('jane_smith', 'jane@jobboard.com', 'admin123');

-- Insert mock audit logs
INSERT INTO audit_logs (admin_id, action_type, details, ip_address, created_at) VALUES
(1, 'login', 'Admin logged in successfully', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(1, 'view_dashboard', 'Viewed dashboard overview', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 1 HOUR)),
(1, 'view_users', 'Viewed user management page', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 2 HOUR)),
(1, 'view_employers', 'Viewed employer management page', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 3 HOUR)),
(1, 'view_jobs', 'Viewed job management page', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 4 HOUR)),
(1, 'view_analytics', 'Viewed analytics dashboard', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 5 HOUR)),
(1, 'view_audit_logs', 'Viewed audit logs', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 6 HOUR)),
(1, 'toggle_user_status', 'Toggled status for user ID: 1', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 7 HOUR)),
(1, 'toggle_employer_status', 'Toggled status for employer ID: 1', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 8 HOUR)),
(1, 'toggle_job_status', 'Toggled status for job ID: 1', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 9 HOUR)),
(1, 'delete_user', 'Deleted user ID: 2', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 10 HOUR)),
(1, 'delete_employer', 'Deleted employer ID: 2', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 11 HOUR)),
(1, 'delete_job', 'Deleted job ID: 2', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 12 HOUR)),
(1, 'view_resume', 'Viewed resume for user ID: 1', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 13 HOUR)),
(1, 'view_job_details', 'Viewed details for job ID: 1', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 14 HOUR)),
(1, 'view_employer_details', 'Viewed details for employer ID: 1', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 15 HOUR)),
(1, 'logout', 'Admin logged out', '192.168.1.1', DATE_SUB(NOW(), INTERVAL 16 HOUR)),
(2, 'login', 'Admin logged in successfully', '192.168.1.2', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'view_dashboard', 'Viewed dashboard overview', '192.168.1.2', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'view_users', 'Viewed user management page', '192.168.1.2', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'toggle_user_status', 'Toggled status for user ID: 3', '192.168.1.2', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(2, 'logout', 'Admin logged out', '192.168.1.2', DATE_SUB(NOW(), INTERVAL 1 DAY)),
(3, 'login', 'Admin logged in successfully', '192.168.1.3', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'view_dashboard', 'Viewed dashboard overview', '192.168.1.3', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'view_analytics', 'Viewed analytics dashboard', '192.168.1.3', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'view_audit_logs', 'Viewed audit logs', '192.168.1.3', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(3, 'logout', 'Admin logged out', '192.168.1.3', DATE_SUB(NOW(), INTERVAL 2 DAY));