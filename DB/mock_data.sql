-- Mock data for Users with diverse profiles
INSERT INTO users (full_name, email, password_hash, location, resume_path, status) VALUES
-- Active users with resumes
('John Smith', 'john.smith@email.com', 'user123', 'New York', 'resumes/resume_67e0e0cfd0c0c.pdf', 'active'),
('Sarah Johnson', 'sarah.j@email.com', 'user123', 'San Francisco', 'resumes/resume_67e0ed755e543.pdf', 'active'),
('Mike Wilson', 'mike.w@email.com', 'user123', 'Chicago', 'resumes/resume_67e0ed755e544.pdf', 'active'),
('Emily Brown', 'emily.b@email.com', 'user123', 'Boston', 'resumes/resume_67e0ed755e545.pdf', 'active'),
('David Lee', 'david.l@email.com', 'user123', 'Seattle', 'resumes/resume_67e0ed755e546.pdf', 'active'),
('Lisa Chen', 'lisa.c@email.com', 'user123', 'Los Angeles', 'resumes/resume_67e0ed755e547.pdf', 'active'),
('James Wilson', 'james.w@email.com', 'user123', 'Austin', 'resumes/resume_67e0ed755e548.pdf', 'active'),
('Maria Garcia', 'maria.g@email.com', 'user123', 'Miami', 'resumes/resume_67e0ed755e549.pdf', 'active'),
('Robert Taylor', 'robert.t@email.com', 'user123', 'Denver', 'resumes/resume_67e0ed755e550.pdf', 'active'),
('Amanda White', 'amanda.w@email.com', 'user123', 'Portland', 'resumes/resume_67e0ed755e551.pdf', 'active'),
-- Active users without resumes
('Alex Thompson', 'alex.t@email.com', 'user123', 'Vancouver', NULL, 'active'),
('Priya Patel', 'priya.p@email.com', 'user123', 'Toronto', NULL, 'active'),
('Carlos Rodriguez', 'carlos.r@email.com', 'user123', 'Montreal', NULL, 'active'),
('Sophie Martin', 'sophie.m@email.com', 'user123', 'Ottawa', NULL, 'active'),
('Yuki Tanaka', 'yuki.t@email.com', 'user123', 'Calgary', NULL, 'active'),
-- Inactive users
('Hassan Ahmed', 'hassan.a@email.com', 'user123', 'Edmonton', 'resumes/resume_67e0ed755e552.pdf', 'inactive'),
('Emma Wilson', 'emma.w@email.com', 'user123', 'Victoria', NULL, 'inactive'),
('Wei Chen', 'wei.c@email.com', 'user123', 'Halifax', 'resumes/resume_67e0ed755e553.pdf', 'inactive'),
('Olivia Brown', 'olivia.b@email.com', 'user123', 'Winnipeg', NULL, 'inactive'),
('Lucas Silva', 'lucas.s@email.com', 'user123', 'Quebec City', 'resumes/resume_67e0ed755e554.pdf', 'inactive'),
-- Additional diverse users
('Mohammed Ali', 'mohammed.a@email.com', 'user123', 'Dubai', 'resumes/resume_67e0ed755e555.pdf', 'active'),
('Sofia Rodriguez', 'sofia.r@email.com', 'user123', 'Madrid', NULL, 'active'),
('Chen Wei', 'chen.w@email.com', 'user123', 'Shanghai', 'resumes/resume_67e0ed755e556.pdf', 'active'),
('Anna Kowalski', 'anna.k@email.com', 'user123', 'Warsaw', NULL, 'active'),
('Rajesh Kumar', 'rajesh.k@email.com', 'user123', 'Mumbai', 'resumes/resume_67e0ed755e557.pdf', 'active');

-- Mock data for Employers with diverse profiles
INSERT INTO employers (company_name, email, password_hash, location, status) VALUES
-- Large companies
('ABC Technology', 'hr@abctech.com', 'employer123', 'New York', 'active'),
('Global Solutions Inc', 'careers@globalsolutions.com', 'employer123', 'San Francisco', 'active'),
('Future Systems', 'jobs@futuresystems.com', 'employer123', 'Chicago', 'active'),
('Digital Innovations', 'hr@digitalinno.com', 'employer123', 'Boston', 'active'),
('Smart Tech Corp', 'careers@smarttech.com', 'employer123', 'Seattle', 'active'),
-- Medium companies
('Cloud Solutions', 'hr@cloudsol.com', 'employer123', 'Austin', 'active'),
('Data Dynamics', 'jobs@datadyn.com', 'employer123', 'Denver', 'active'),
('Tech Ventures', 'careers@techventures.com', 'employer123', 'Los Angeles', 'active'),
('Innovation Labs', 'hr@innolabs.com', 'employer123', 'Portland', 'active'),
('Next Gen Solutions', 'jobs@nextgen.com', 'employer123', 'Miami', 'active'),
-- International companies
('Canadian Tech Solutions', 'hr@cantech.com', 'employer123', 'Vancouver', 'active'),
('Maple Software Inc', 'careers@maplesoft.com', 'employer123', 'Toronto', 'active'),
('Quebec Digital', 'jobs@quebecdigital.com', 'employer123', 'Montreal', 'active'),
('Prairie Innovations', 'hr@prairieinno.com', 'employer123', 'Winnipeg', 'active'),
('Pacific Systems', 'careers@pacificsys.com', 'employer123', 'Victoria', 'active'),
-- Inactive companies
('Northern Solutions', 'hr@northsol.com', 'employer123', 'Edmonton', 'inactive'),
('Atlantic Technologies', 'jobs@atlantictech.com', 'employer123', 'Halifax', 'inactive'),
('Mountain View Tech', 'careers@mountaintech.com', 'employer123', 'Calgary', 'inactive'),
('Capital Innovations', 'hr@capitalinno.com', 'employer123', 'Ottawa', 'inactive'),
('Maritime Software', 'jobs@maritimesoft.com', 'employer123', 'St. John''s', 'inactive'),
-- Additional diverse companies
('Dubai Tech Solutions', 'hr@dubaitech.com', 'employer123', 'Dubai', 'active'),
('European Innovations', 'careers@euinno.com', 'employer123', 'Berlin', 'active'),
('Asia Pacific Tech', 'jobs@asiapactech.com', 'employer123', 'Singapore', 'active'),
('Latin American Systems', 'hr@latintech.com', 'employer123', 'São Paulo', 'active'),
('African Digital Solutions', 'careers@africatech.com', 'employer123', 'Cape Town', 'active');

-- Mock data for Jobs with diverse profiles
INSERT INTO jobs (employer_id, title, description, location, salary, status) VALUES
-- High-paying jobs
(1, 'Senior Software Engineer', 'Looking for an experienced software engineer with strong background in full-stack development.', 'New York', 150000.00, 'active'),
(1, 'Product Manager', 'Lead product development and strategy for our main product line.', 'New York', 160000.00, 'active'),
(2, 'Data Scientist', 'Work with big data and machine learning models to derive business insights.', 'San Francisco', 145000.00, 'active'),
(2, 'UX Designer', 'Design user-friendly interfaces for web and mobile applications.', 'San Francisco', 135000.00, 'active'),
(3, 'DevOps Engineer', 'Manage and improve our cloud infrastructure and deployment pipelines.', 'Chicago', 140000.00, 'active'),
-- Mid-range jobs
(4, 'Frontend Developer', 'Build responsive web applications using React and TypeScript.', 'Boston', 95000.00, 'active'),
(5, 'Backend Developer', 'Develop and maintain RESTful APIs and microservices.', 'Seattle', 100000.00, 'active'),
(6, 'Full Stack Developer', 'Work on both frontend and backend of our web applications.', 'Austin', 90000.00, 'active'),
(7, 'Cloud Architect', 'Design and implement cloud-based solutions using AWS.', 'Denver', 130000.00, 'active'),
(8, 'Mobile Developer', 'Develop native iOS applications using Swift.', 'Los Angeles', 110000.00, 'active'),
-- Entry-level jobs
(9, 'Junior Software Engineer', 'Entry-level position for recent graduates with basic programming skills.', 'Portland', 65000.00, 'active'),
(10, 'QA Engineer', 'Ensure software quality through automated testing.', 'Miami', 70000.00, 'active'),
(11, 'Junior Data Analyst', 'Analyze data and create reports for business insights.', 'Vancouver', 60000.00, 'active'),
(12, 'Junior Frontend Developer', 'Build and maintain web applications using modern frameworks.', 'Toronto', 65000.00, 'active'),
(13, 'Junior DevOps Engineer', 'Assist in maintaining cloud infrastructure and CI/CD pipelines.', 'Montreal', 70000.00, 'active'),
-- Inactive jobs
(14, 'System Administrator', 'Manage and maintain our IT infrastructure.', 'Winnipeg', 85000.00, 'inactive'),
(15, 'Database Administrator', 'Manage and optimize our database systems.', 'Victoria', 92000.00, 'inactive'),
(16, 'Cloud Security Engineer', 'Implement and maintain cloud security protocols.', 'Edmonton', 115000.00, 'inactive'),
(17, 'AI Research Scientist', 'Conduct research in artificial intelligence and ML.', 'Halifax', 130000.00, 'inactive'),
(18, 'Blockchain Developer', 'Develop blockchain solutions for fintech applications.', 'Calgary', 105000.00, 'inactive'),
-- International jobs
(19, 'Senior Software Engineer', 'Lead development team in Dubai office.', 'Dubai', 180000.00, 'active'),
(20, 'Product Designer', 'Design products for European market.', 'Berlin', 120000.00, 'active'),
(21, 'Technical Lead', 'Lead development team in Singapore.', 'Singapore', 160000.00, 'active'),
(22, 'Full Stack Developer', 'Develop applications for Latin American market.', 'São Paulo', 95000.00, 'active'),
(23, 'DevOps Engineer', 'Manage infrastructure for African operations.', 'Cape Town', 100000.00, 'active');

-- Mock data for Applications with diverse statuses
INSERT INTO applications (job_id, user_id, cover_letter, status) VALUES
-- Pending applications
(1, 1, 'I am very interested in this position and believe my skills match your requirements.', 'Pending'),
(2, 2, 'With my 5 years of experience, I would be a great fit for this role.', 'Pending'),
(3, 3, 'My product management experience makes me an ideal candidate.', 'Pending'),
(4, 4, 'I have extensive experience in data science and machine learning.', 'Pending'),
(5, 5, 'My portfolio demonstrates my UI/UX capabilities.', 'Pending'),
-- Shortlisted applications
(6, 6, 'I have been working with DevOps tools for 3 years.', 'Shortlisted'),
(7, 7, 'I am passionate about frontend development and user experience.', 'Shortlisted'),
(8, 8, 'My backend development skills would be valuable to your team.', 'Shortlisted'),
(9, 9, 'I have experience in both frontend and backend technologies.', 'Shortlisted'),
(10, 10, 'I have designed and implemented various cloud solutions.', 'Shortlisted'),
-- Hired applications
(11, 11, 'I have extensive experience in machine learning and AI implementations.', 'Hired'),
(12, 12, 'My research background in AI makes me an ideal candidate.', 'Hired'),
(13, 13, 'I have successfully led DevOps teams in my previous roles.', 'Hired'),
(14, 14, 'I am proficient in React Native and have published several apps.', 'Hired'),
(15, 15, 'My project management experience spans over 6 years.', 'Hired'),
-- Rejected applications
(16, 16, 'Je suis parfaitement bilingue et j''ai de l''expérience en développement.', 'Rejected'),
(17, 17, 'I have managed large-scale databases for Fortune 500 companies.', 'Rejected'),
(18, 18, 'My experience in cloud security includes AWS and Azure certifications.', 'Rejected'),
(19, 19, 'I have published research papers in leading AI conferences.', 'Rejected'),
(20, 20, 'I have worked on several blockchain projects in the fintech sector.', 'Rejected'),
-- Additional diverse applications
(21, 1, 'I have experience working in international teams.', 'Pending'),
(22, 2, 'My experience aligns well with your European market needs.', 'Shortlisted'),
(23, 3, 'I am familiar with Asian market requirements.', 'Hired'),
(24, 4, 'I have worked with Latin American clients before.', 'Rejected'),
(25, 5, 'I understand the unique challenges of the African market.', 'Pending');

-- Mock data for Saved Jobs with diverse patterns
INSERT INTO saved_jobs (user_id, job_id) VALUES
-- Users with multiple saved jobs
(1, 2),
(1, 3),
(1, 4),
(2, 1),
(2, 5),
(2, 6),
(3, 7),
(3, 8),
(3, 9),
(4, 10),
(4, 11),
(4, 12),
-- Users with single saved job
(5, 13),
(6, 14),
(7, 15),
(8, 16),
(9, 17),
-- Users with no saved jobs (edge case)
(10, 18),
(11, 19),
(12, 20),
(13, 21),
(14, 22),
-- Additional diverse saved jobs
(15, 23),
(16, 24),
(17, 25),
(18, 1),
(19, 2),
(20, 3);

-- Additional mock data for Audit Logs
INSERT INTO audit_logs (admin_id, action_type, details, ip_address) VALUES
-- User management actions
(1, 'review_application', 'Reviewed application ID: 11', '192.168.1.100'),
(2, 'update_job', 'Updated job posting ID: 13', '192.168.1.101'),
(3, 'delete_application', 'Removed inactive application ID: 5', '192.168.1.102'),
(1, 'user_report', 'Generated monthly user activity report', '192.168.1.103'),
(2, 'employer_verification', 'Verified new employer account ID: 11', '192.168.1.104'),
-- System operations
(3, 'system_backup', 'Initiated weekly system backup', '192.168.1.105'),
(1, 'security_alert', 'Investigated failed login attempts', '192.168.1.106'),
(2, 'content_moderation', 'Reviewed reported job posting ID: 15', '192.168.1.107'),
(3, 'analytics_export', 'Generated quarterly analytics report', '192.168.1.108'),
(1, 'maintenance', 'Performed routine database maintenance', '192.168.1.109'),
-- Additional diverse audit logs
(2, 'user_verification', 'Verified user account ID: 25', '192.168.1.110'),
(3, 'job_approval', 'Approved new job posting ID: 26', '192.168.1.111'),
(1, 'employer_suspension', 'Suspended employer account ID: 15', '192.168.1.112'),
(2, 'resume_review', 'Reviewed resume for user ID: 8', '192.168.1.113'),
(3, 'application_review', 'Reviewed application ID: 20', '192.168.1.114'),
(1, 'system_update', 'Applied system security patches', '192.168.1.115'),
(2, 'data_export', 'Exported user data for compliance', '192.168.1.116'),
(3, 'content_update', 'Updated job posting guidelines', '192.168.1.117'),
(1, 'user_communication', 'Sent mass email to inactive users', '192.168.1.118'),
(2, 'system_monitoring', 'Monitored system performance metrics', '192.168.1.119');