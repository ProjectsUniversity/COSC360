-- Mock data for Users
INSERT INTO users (full_name, email, password_hash, location, resume_path) VALUES
('John Smith', 'john.smith@email.com', 'user123', 'New York', 'resumes/resume_67e0e0cfd0c0c.pdf'),
('Sarah Johnson', 'sarah.j@email.com', 'user123', 'San Francisco', 'resumes/resume_67e0ed755e543.pdf'),
('Mike Wilson', 'mike.w@email.com', 'user123', 'Chicago', NULL),
('Emily Brown', 'emily.b@email.com', 'user123', 'Boston', NULL),
('David Lee', 'david.l@email.com', 'user123', 'Seattle', NULL),
('Lisa Chen', 'lisa.c@email.com', 'user123', 'Los Angeles', NULL),
('James Wilson', 'james.w@email.com', 'user123', 'Austin', NULL),
('Maria Garcia', 'maria.g@email.com', 'user123', 'Miami', NULL),
('Robert Taylor', 'robert.t@email.com', 'user123', 'Denver', NULL),
('Amanda White', 'amanda.w@email.com', 'user123', 'Portland', NULL),
('Alex Thompson', 'alex.t@email.com', 'user123', 'Vancouver', 'resumes/resume_67e0e0cfd0c0d.pdf'),
('Priya Patel', 'priya.p@email.com', 'user123', 'Toronto', 'resumes/resume_67e0ed755e544.pdf'),
('Carlos Rodriguez', 'carlos.r@email.com', 'user123', 'Montreal', NULL),
('Sophie Martin', 'sophie.m@email.com', 'user123', 'Ottawa', 'resumes/resume_67e0ed755e545.pdf'),
('Yuki Tanaka', 'yuki.t@email.com', 'user123', 'Calgary', NULL),
('Hassan Ahmed', 'hassan.a@email.com', 'user123', 'Edmonton', 'resumes/resume_67e0ed755e546.pdf'),
('Emma Wilson', 'emma.w@email.com', 'user123', 'Victoria', NULL),
('Wei Chen', 'wei.c@email.com', 'user123', 'Halifax', 'resumes/resume_67e0ed755e547.pdf'),
('Olivia Brown', 'olivia.b@email.com', 'user123', 'Winnipeg', NULL),
('Lucas Silva', 'lucas.s@email.com', 'user123', 'Quebec City', 'resumes/resume_67e0ed755e548.pdf');

-- Mock data for Employers
INSERT INTO employers (company_name, email, password_hash, location) VALUES
('ABC Technology', 'hr@abctech.com', 'employer123', 'New York'),
('Global Solutions Inc', 'careers@globalsolutions.com', 'employer123', 'San Francisco'),
('Future Systems', 'jobs@futuresystems.com', 'employer123', 'Chicago'),
('Digital Innovations', 'hr@digitalinno.com', 'employer123', 'Boston'),
('Smart Tech Corp', 'careers@smarttech.com', 'employer123', 'Seattle'),
('Cloud Solutions', 'hr@cloudsol.com', 'employer123', 'Austin'),
('Data Dynamics', 'jobs@datadyn.com', 'employer123', 'Denver'),
('Tech Ventures', 'careers@techventures.com', 'employer123', 'Los Angeles'),
('Innovation Labs', 'hr@innolabs.com', 'employer123', 'Portland'),
('Next Gen Solutions', 'jobs@nextgen.com', 'employer123', 'Miami'),
('Canadian Tech Solutions', 'hr@cantech.com', 'employer123', 'Vancouver'),
('Maple Software Inc', 'careers@maplesoft.com', 'employer123', 'Toronto'),
('Quebec Digital', 'jobs@quebecdigital.com', 'employer123', 'Montreal'),
('Prairie Innovations', 'hr@prairieinno.com', 'employer123', 'Winnipeg'),
('Pacific Systems', 'careers@pacificsys.com', 'employer123', 'Victoria'),
('Northern Solutions', 'hr@northsol.com', 'employer123', 'Edmonton'),
('Atlantic Technologies', 'jobs@atlantictech.com', 'employer123', 'Halifax'),
('Mountain View Tech', 'careers@mountaintech.com', 'employer123', 'Calgary'),
('Capital Innovations', 'hr@capitalinno.com', 'employer123', 'Ottawa'),
('Maritime Software', 'jobs@maritimesoft.com', 'employer123', 'St. John''s');

-- Mock data for Jobs
INSERT INTO jobs (employer_id, title, description, location, salary, status) VALUES
(1, 'Senior Software Engineer', 'Looking for an experienced software engineer with strong background in full-stack development.', 'New York', 120000.00, 'active'),
(1, 'Product Manager', 'Lead product development and strategy for our main product line.', 'New York', 130000.00, 'active'),
(2, 'Data Scientist', 'Work with big data and machine learning models to derive business insights.', 'San Francisco', 115000.00, 'active'),
(2, 'UX Designer', 'Design user-friendly interfaces for web and mobile applications.', 'San Francisco', 95000.00, 'active'),
(3, 'DevOps Engineer', 'Manage and improve our cloud infrastructure and deployment pipelines.', 'Chicago', 110000.00, 'active'),
(4, 'Frontend Developer', 'Build responsive web applications using React and TypeScript.', 'Boston', 90000.00, 'active'),
(5, 'Backend Developer', 'Develop and maintain RESTful APIs and microservices.', 'Seattle', 100000.00, 'active'),
(6, 'Full Stack Developer', 'Work on both frontend and backend of our web applications.', 'Austin', 95000.00, 'active'),
(7, 'Cloud Architect', 'Design and implement cloud-based solutions using AWS.', 'Denver', 130000.00, 'active'),
(8, 'Mobile Developer', 'Develop native iOS applications using Swift.', 'Los Angeles', 105000.00, 'active'),
(9, 'System Administrator', 'Manage and maintain our IT infrastructure.', 'Portland', 85000.00, 'inactive'),
(10, 'Quality Assurance Engineer', 'Ensure software quality through automated testing.', 'Miami', 80000.00, 'active'),
(11, 'Machine Learning Engineer', 'Develop and implement ML models for our AI platform.', 'Vancouver', 125000.00, 'active'),
(11, 'DevOps Team Lead', 'Lead our DevOps team and improve deployment processes.', 'Vancouver', 135000.00, 'active'),
(12, 'React Native Developer', 'Build cross-platform mobile applications.', 'Toronto', 95000.00, 'active'),
(12, 'Technical Project Manager', 'Manage complex technical projects and teams.', 'Toronto', 110000.00, 'active'),
(13, 'Bilingual Software Developer', 'Full-stack development with French language skills.', 'Montreal', 98000.00, 'active'),
(14, 'Database Administrator', 'Manage and optimize our database systems.', 'Winnipeg', 92000.00, 'active'),
(15, 'Cloud Security Engineer', 'Implement and maintain cloud security protocols.', 'Victoria', 115000.00, 'active'),
(16, 'AI Research Scientist', 'Conduct research in artificial intelligence and ML.', 'Edmonton', 130000.00, 'active'),
(17, 'Blockchain Developer', 'Develop blockchain solutions for fintech applications.', 'Halifax', 105000.00, 'active'),
(18, 'QA Automation Lead', 'Lead the automation testing team.', 'Calgary', 98000.00, 'active');

-- Mock data for Applications
INSERT INTO applications (job_id, user_id, cover_letter, status) VALUES
(1, 1, 'I am very interested in this position and believe my skills match your requirements.', 'Pending'),
(1, 2, 'With my 5 years of experience, I would be a great fit for this role.', 'Shortlisted'),
(2, 3, 'My product management experience makes me an ideal candidate.', 'Pending'),
(3, 4, 'I have extensive experience in data science and machine learning.', 'Hired'),
(4, 5, 'My portfolio demonstrates my UI/UX capabilities.', 'Rejected'),
(5, 6, 'I have been working with DevOps tools for 3 years.', 'Pending'),
(6, 7, 'I am passionate about frontend development and user experience.', 'Shortlisted'),
(7, 8, 'My backend development skills would be valuable to your team.', 'Pending'),
(8, 9, 'I have experience in both frontend and backend technologies.', 'Pending'),
(9, 10, 'I have designed and implemented various cloud solutions.', 'Shortlisted'),
(13, 11, 'I have extensive experience in machine learning and AI implementations.', 'Pending'),
(13, 12, 'My research background in AI makes me an ideal candidate.', 'Shortlisted'),
(14, 13, 'I have successfully led DevOps teams in my previous roles.', 'Pending'),
(15, 14, 'I am proficient in React Native and have published several apps.', 'Hired'),
(16, 15, 'My project management experience spans over 6 years.', 'Shortlisted'),
(17, 16, 'Je suis parfaitement bilingue et j''ai de l''expérience en développement.', 'Pending'),
(18, 17, 'I have managed large-scale databases for Fortune 500 companies.', 'Rejected'),
(19, 18, 'My experience in cloud security includes AWS and Azure certifications.', 'Pending'),
(20, 19, 'I have published research papers in leading AI conferences.', 'Shortlisted'),
(21, 20, 'I have worked on several blockchain projects in the fintech sector.', 'Pending');

-- Mock data for Saved Jobs
INSERT INTO saved_jobs (user_id, job_id) VALUES
(1, 2),
(1, 3),
(2, 1),
(2, 4),
(3, 5),
(4, 6),
(5, 7),
(6, 8),
(7, 9),
(8, 10),
(11, 14),
(11, 15),
(12, 13),
(12, 16),
(13, 17),
(14, 18),
(15, 19),
(16, 20),
(17, 21),
(18, 22);

-- Mock data for Admins (if not already added from setup.sql)
INSERT IGNORE INTO admins (username, email, password_hash) VALUES
('admin', 'admin@jobboard.com', 'admin123'),
('john_doe', 'john@jobboard.com', 'admin123'),
('jane_smith', 'jane@jobboard.com', 'admin123');

-- Additional mock data for Audit Logs
INSERT INTO audit_logs (admin_id, action_type, details, ip_address) VALUES
(1, 'review_application', 'Reviewed application ID: 11', '192.168.1.100'),
(2, 'update_job', 'Updated job posting ID: 13', '192.168.1.101'),
(3, 'delete_application', 'Removed inactive application ID: 5', '192.168.1.102'),
(1, 'user_report', 'Generated monthly user activity report', '192.168.1.103'),
(2, 'employer_verification', 'Verified new employer account ID: 11', '192.168.1.104'),
(3, 'system_backup', 'Initiated weekly system backup', '192.168.1.105'),
(1, 'security_alert', 'Investigated failed login attempts', '192.168.1.106'),
(2, 'content_moderation', 'Reviewed reported job posting ID: 15', '192.168.1.107'),
(3, 'analytics_export', 'Generated quarterly analytics report', '192.168.1.108'),
(1, 'maintenance', 'Performed routine database maintenance', '192.168.1.109');