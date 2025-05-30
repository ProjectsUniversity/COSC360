CREATE DATABASE IF NOT EXISTS shlok10;
USE shlok10;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    resume_path VARCHAR(255),
    status VARCHAR(50) DEFAULT 'active',  -- Add status column to users table
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL;

CREATE TABLE IF NOT EXISTS employers (
    employer_id INT AUTO_INCREMENT PRIMARY KEY,
    company_name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    location VARCHAR(255),
    status VARCHAR(50) DEFAULT 'active',  -- Add status column to employers table
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

ALTER TABLE employers ADD COLUMN profile_image VARCHAR(255) NULL;

CREATE TABLE IF NOT EXISTS jobs (
    job_id INT AUTO_INCREMENT PRIMARY KEY,
    employer_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    location VARCHAR(255),
    salary DECIMAL(10,2),
    status VARCHAR(50) DEFAULT 'active',  -- Adding status column with default 'active'
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (employer_id) REFERENCES employers(employer_id)
);

CREATE TABLE IF NOT EXISTS applications (
    application_id INT AUTO_INCREMENT PRIMARY KEY,
    job_id INT NOT NULL,
    user_id INT NOT NULL,  -- references 'users' table
    cover_letter TEXT,
    status VARCHAR(50) DEFAULT 'Pending',  -- e.g., Pending, Shortlisted, Hired, Rejected
    applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (job_id) REFERENCES jobs(job_id),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS saved_jobs (
    saved_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    job_id INT NOT NULL,
    saved_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (job_id) REFERENCES jobs(job_id),
    UNIQUE KEY unique_saved_job (user_id, job_id)  -- Prevent duplicate saves
);

-- Add indexes for saved_jobs queries
CREATE INDEX idx_saved_jobs_user ON saved_jobs(user_id);
CREATE INDEX idx_saved_jobs_job ON saved_jobs(job_id);
CREATE INDEX idx_saved_jobs_date ON saved_jobs(saved_at DESC);

-- Create messages table
CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT NOT NULL,
    receiver_id INT NOT NULL,
    message_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    sender_type ENUM('user', 'employer') NOT NULL,
    -- Add indexes for better query performance
    INDEX idx_sender (sender_id, sender_type),
    INDEX idx_receiver (receiver_id),
    INDEX idx_created_at (created_at)
);

-- Create message_status table for tracking read/unread messages
CREATE TABLE IF NOT EXISTS message_status (
    status_id INT AUTO_INCREMENT PRIMARY KEY,
    message_id INT NOT NULL,
    recipient_id INT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    read_at TIMESTAMP NULL,
    FOREIGN KEY (message_id) REFERENCES messages(message_id),
    INDEX idx_recipient_unread (recipient_id, is_read)
);

INSERT INTO employers (company_name, email, password_hash, location)
VALUES ('Tech Solutions Inc', 'hr@techsolutions.com', 'hashed_password_123', 'New York');
INSERT INTO employers (company_name, email, password_hash, location)
VALUES ('InnoVista Corp.', 'careers@innovista.com', 'hashed_pass_456', 'San Francisco');
INSERT INTO employers (company_name, email, password_hash, location)
VALUES ('Global Ventures LLC', 'recruitment@globalventures.com', 'hashed_pass_789', 'Chicago');
INSERT INTO jobs (employer_id, title, description, location, salary, status)
VALUES (
    1,
    'Software Engineer',
    'Develop and maintain web applications using modern frameworks.',
    'New York',
    90000.00,
    'active'
);

INSERT INTO jobs (employer_id, title, description, location, salary, status)
VALUES (
    2,
    'Data Analyst',
    'Collect, process, and perform statistical analysis on large datasets.',
    'San Francisco',
    75000.00,
    'active'
);

INSERT INTO jobs (employer_id, title, description, location, salary, status)
VALUES (
    3,
    'Project Manager',
    'Plan, execute, and oversee projects for our international clients.',
    'Chicago',
    105000.00,
    'active'
);

-- Update existing users and employers to have 'active' status by default
UPDATE users SET status = 'active' WHERE status IS NULL;
UPDATE employers SET status = 'active' WHERE status IS NULL;