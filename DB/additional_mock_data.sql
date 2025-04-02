-- Additional Users (Expanding the user base)
INSERT INTO users (full_name, email, password_hash, location, resume_path, status) VALUES
-- More active users with resumes
('Michael Chang', 'michael.c@email.com', 'user123', 'San Diego', 'resumes/resume_67e0ed755e558.pdf', 'active'),
('Rachel Kim', 'rachel.k@email.com', 'user123', 'Houston', 'resumes/resume_67e0ed755e559.pdf', 'active'),
('Thomas Anderson', 'thomas.a@email.com', 'user123', 'Phoenix', 'resumes/resume_67e0ed755e560.pdf', 'active'),
('Isabella Martinez', 'isabella.m@email.com', 'user123', 'Dallas', 'resumes/resume_67e0ed755e561.pdf', 'active'),
('William Chen', 'william.c@email.com', 'user123', 'San Jose', 'resumes/resume_67e0ed755e562.pdf', 'active'),
('Sophia Patel', 'sophia.p@email.com', 'user123', 'Philadelphia', 'resumes/resume_67e0ed755e563.pdf', 'active'),
('Daniel Kim', 'daniel.k@email.com', 'user123', 'San Antonio', 'resumes/resume_67e0ed755e564.pdf', 'active'),
('Emma Rodriguez', 'emma.r@email.com', 'user123', 'Jacksonville', 'resumes/resume_67e0ed755e565.pdf', 'active'),
('Lucas Thompson', 'lucas.t@email.com', 'user123', 'Fort Worth', 'resumes/resume_67e0ed755e566.pdf', 'active'),
('Ava Wilson', 'ava.w@email.com', 'user123', 'Columbus', 'resumes/resume_67e0ed755e567.pdf', 'active'),
-- More active users without resumes
('Oliver Brown', 'oliver.b@email.com', 'user123', 'Charlotte', NULL, 'active'),
('Mia Garcia', 'mia.g@email.com', 'user123', 'Indianapolis', NULL, 'active'),
('Ethan Lee', 'ethan.l@email.com', 'user123', 'San Francisco', NULL, 'active'),
('Charlotte Taylor', 'charlotte.t@email.com', 'user123', 'Seattle', NULL, 'active'),
('Mason White', 'mason.w@email.com', 'user123', 'Denver', NULL, 'active'),
-- More inactive users
('Harper Davis', 'harper.d@email.com', 'user123', 'Washington DC', 'resumes/resume_67e0ed755e568.pdf', 'inactive'),
('Liam Johnson', 'liam.j@email.com', 'user123', 'Nashville', NULL, 'inactive'),
('Amelia Clark', 'amelia.c@email.com', 'user123', 'El Paso', 'resumes/resume_67e0ed755e569.pdf', 'inactive'),
('Henry Wright', 'henry.w@email.com', 'user123', 'Detroit', NULL, 'inactive'),
('Evelyn Lewis', 'evelyn.l@email.com', 'user123', 'Boston', 'resumes/resume_67e0ed755e570.pdf', 'inactive'),
-- More international users
('Yusuf Ahmed', 'yusuf.a@email.com', 'user123', 'Istanbul', 'resumes/resume_67e0ed755e571.pdf', 'active'),
('Lina Chen', 'lina.c@email.com', 'user123', 'Hong Kong', NULL, 'active'),
('Marcus Schmidt', 'marcus.s@email.com', 'user123', 'Munich', 'resumes/resume_67e0ed755e572.pdf', 'active'),
('Sofia Silva', 'sofia.silva@email.com', 'user123', 'Lisbon', NULL, 'active'),
('Arjun Patel', 'arjun.p@email.com', 'user123', 'Bangalore', 'resumes/resume_67e0ed755e573.pdf', 'active'),
-- Additional diverse users
('Nina Popova', 'nina.popova@email.com', 'user123', 'Moscow', 'resumes/resume_67e0ed755e574.pdf', 'active'),
('Hiroshi Tanaka', 'hiroshi.t@email.com', 'user123', 'Tokyo', NULL, 'active'),
('Maria Santos', 'maria.santos@email.com', 'user123', 'Barcelona', 'resumes/resume_67e0ed755e575.pdf', 'active'),
('Ali Hassan', 'ali.hassan@email.com', 'user123', 'Cairo', NULL, 'active'),
('Sarah O''Brien', 'sarah.o@email.com', 'user123', 'Dublin', 'resumes/resume_67e0ed755e576.pdf', 'active'),
-- Additional batch of Users for more realistic user-to-job ratio
-- Active users batch 1 (with resumes)
('Benjamin Foster', 'ben.f@email.com', 'user123', 'Vancouver', 'resumes/resume_67e0ed755e577.pdf', 'active'),
('Aisha Patel', 'aisha.patel@email.com', 'user123', 'Toronto', 'resumes/resume_67e0ed755e578.pdf', 'active'),
('Samuel Chang', 'samuel.chang@email.com', 'user123', 'Los Angeles', 'resumes/resume_67e0ed755e579.pdf', 'active'),
('Zara Khan', 'zara.k@email.com', 'user123', 'Chicago', 'resumes/resume_67e0ed755e580.pdf', 'active'),
('Diego Martinez', 'diego.martinez@email.com', 'user123', 'Miami', 'resumes/resume_67e0ed755e581.pdf', 'active'),
('Gabriel Silva', 'gabriel.s@email.com', 'user123', 'Boston', 'resumes/resume_67e0ed755e582.pdf', 'active'),
('Nina Patel', 'nina.p@email.com', 'user123', 'Seattle', 'resumes/resume_67e0ed755e583.pdf', 'active'),
('Liam O''Connor', 'liam.o@email.com', 'user123', 'Dublin', 'resumes/resume_67e0ed755e584.pdf', 'active'),
('Fatima Ahmed', 'fatima.a@email.com', 'user123', 'Dubai', 'resumes/resume_67e0ed755e585.pdf', 'active'),
('Viktor Petrov', 'viktor.p@email.com', 'user123', 'Moscow', 'resumes/resume_67e0ed755e586.pdf', 'active'),

-- Active users batch 2 (without resumes)
('Julia Santos', 'julia.s@email.com', 'user123', 'São Paulo', NULL, 'active'),
('Chen Liu', 'chen.l@email.com', 'user123', 'Beijing', NULL, 'active'),
('Hassan Ali', 'hassan.ali@email.com', 'user123', 'Dubai', NULL, 'active'),
('Eva Novak', 'eva.n@email.com', 'user123', 'Prague', NULL, 'active'),
('Marco Rossi', 'marco.r@email.com', 'user123', 'Rome', NULL, 'active'),
('Sophia Kowalski', 'sophia.k@email.com', 'user123', 'Warsaw', NULL, 'active'),
('Raj Sharma', 'raj.s@email.com', 'user123', 'Mumbai', NULL, 'active'),
('Yuki Sato', 'yuki.s@email.com', 'user123', 'Tokyo', NULL, 'active'),
('Anders Nielsen', 'anders.n@email.com', 'user123', 'Copenhagen', NULL, 'active'),
('Isabel Torres', 'isabel.t@email.com', 'user123', 'Madrid', NULL, 'active'),

-- More international users batch 3 (mixed resume status)
('Juan Carlos', 'juan.c@email.com', 'user123', 'Mexico City', 'resumes/resume_67e0ed755e587.pdf', 'active'),
('Anita Kumar', 'anita.k@email.com', 'user123', 'Bangalore', NULL, 'active'),
('Felix Weber', 'felix.w@email.com', 'user123', 'Berlin', 'resumes/resume_67e0ed755e588.pdf', 'active'),
('Luna Park', 'luna.p@email.com', 'user123', 'Seoul', NULL, 'active'),
('Ahmed Hassan', 'ahmed.h@email.com', 'user123', 'Cairo', 'resumes/resume_67e0ed755e589.pdf', 'active'),
('Emma Andersson', 'emma.a@email.com', 'user123', 'Stockholm', NULL, 'active'),
('Paolo Conti', 'paolo.c@email.com', 'user123', 'Milan', 'resumes/resume_67e0ed755e590.pdf', 'active'),
('Maria Costa', 'maria.costa@email.com', 'user123', 'Lisbon', NULL, 'active'),
('Leo Wong', 'leo.w@email.com', 'user123', 'Hong Kong', 'resumes/resume_67e0ed755e591.pdf', 'active'),
('Sara Nielsen', 'sara.n@email.com', 'user123', 'Oslo', NULL, 'active'),

-- Tech professionals batch (all with resumes)
('Alex Kumar', 'alex.kumar@email.com', 'user123', 'San Francisco', 'resumes/resume_67e0ed755e592.pdf', 'active'),
('Jessica Zhang', 'jessica.z@email.com', 'user123', 'Singapore', 'resumes/resume_67e0ed755e593.pdf', 'active'),
('Ryan Patel', 'ryan.p@email.com', 'user123', 'Austin', 'resumes/resume_67e0ed755e594.pdf', 'active'),
('Maya Williams', 'maya.w@email.com', 'user123', 'London', 'resumes/resume_67e0ed755e595.pdf', 'active'),
('Aiden Chen', 'aiden.c@email.com', 'user123', 'Seattle', 'resumes/resume_67e0ed755e596.pdf', 'active'),
('Sophie Martin', 'sophie.martin@email.com', 'user123', 'Paris', 'resumes/resume_67e0ed755e597.pdf', 'active'),
('Lucas Silva', 'lucas.silva@email.com', 'user123', 'Rio de Janeiro', 'resumes/resume_67e0ed755e598.pdf', 'active'),
('Emma Wilson', 'emma.wilson@email.com', 'user123', 'Melbourne', 'resumes/resume_67e0ed755e599.pdf', 'active'),
('Omar Ahmed', 'omar.a@email.com', 'user123', 'Abu Dhabi', 'resumes/resume_67e0ed755e600.pdf', 'active'),
('Ling Wang', 'ling.w@email.com', 'user123', 'Shanghai', 'resumes/resume_67e0ed755e601.pdf', 'active');

-- Additional Employers (Maintaining realistic ratio)
INSERT INTO employers (company_name, email, password_hash, location, status) VALUES
-- More large companies
('Tech Giants Inc', 'hr@techgiants.com', 'employer123', 'San Francisco', 'active'),
('Global Innovations', 'careers@globalinno.com', 'employer123', 'New York', 'active'),
('Future Tech Solutions', 'jobs@futuretech.com', 'employer123', 'Chicago', 'active'),
('Digital Systems Corp', 'hr@digitalsys.com', 'employer123', 'Boston', 'active'),
('Smart Solutions Inc', 'careers@smartsol.com', 'employer123', 'Seattle', 'active'),
-- More medium companies
('Cloud Systems', 'hr@cloudsys.com', 'employer123', 'Austin', 'active'),
('Data Analytics Co', 'jobs@dataanalytics.com', 'employer123', 'Denver', 'active'),
('Tech Solutions', 'careers@techsol.com', 'employer123', 'Los Angeles', 'active'),
('Innovation Systems', 'hr@innosys.com', 'employer123', 'Portland', 'active'),
('Next Level Tech', 'jobs@nextlevel.com', 'employer123', 'Miami', 'active'),
-- More international companies
('European Tech Solutions', 'hr@eurotech.com', 'employer123', 'London', 'active'),
('Asia Pacific Systems', 'careers@asiapac.com', 'employer123', 'Singapore', 'active'),
('Latin American Tech', 'jobs@latamtech.com', 'employer123', 'Mexico City', 'active'),
('African Innovations', 'hr@africainno.com', 'employer123', 'Johannesburg', 'active'),
('Middle East Systems', 'careers@mideast.com', 'employer123', 'Dubai', 'active');

-- Additional Jobs (Maintaining realistic ratio)
INSERT INTO jobs (employer_id, title, description, location, salary, status) VALUES
-- More high-paying jobs
(24, 'Lead Software Architect', 'Lead the architecture team in designing scalable solutions.', 'San Francisco', 170000.00, 'active'),
(24, 'Senior Product Manager', 'Lead product strategy for enterprise solutions.', 'San Francisco', 165000.00, 'active'),
(25, 'Principal Data Scientist', 'Lead data science initiatives and mentor team members.', 'New York', 175000.00, 'active'),
(25, 'Director of Engineering', 'Lead multiple engineering teams and drive technical strategy.', 'New York', 190000.00, 'active'),
(26, 'Chief Technology Officer', 'Lead all technical initiatives and drive innovation.', 'Chicago', 200000.00, 'active'),
-- More mid-range jobs
(27, 'Senior Frontend Developer', 'Build complex web applications using modern frameworks.', 'Boston', 110000.00, 'active'),
(28, 'Senior Backend Engineer', 'Design and implement scalable backend systems.', 'Seattle', 115000.00, 'active'),
(29, 'Full Stack Lead', 'Lead full stack development team and mentor junior developers.', 'Austin', 105000.00, 'active'),
(30, 'Cloud Solutions Architect', 'Design and implement cloud infrastructure solutions.', 'Denver', 125000.00, 'active'),
(31, 'Mobile Development Lead', 'Lead mobile app development team and drive innovation.', 'Los Angeles', 120000.00, 'active'),
-- More entry-level jobs
(32, 'Junior Frontend Developer', 'Build responsive web applications using modern frameworks.', 'Portland', 70000.00, 'active'),
(33, 'Junior Backend Developer', 'Develop and maintain RESTful APIs and microservices.', 'Miami', 65000.00, 'active'),
(34, 'Junior Full Stack Developer', 'Work on both frontend and backend of web applications.', 'Vancouver', 68000.00, 'active'),
(35, 'Junior DevOps Engineer', 'Assist in maintaining cloud infrastructure and CI/CD pipelines.', 'Toronto', 72000.00, 'active'),
(36, 'Junior Mobile Developer', 'Develop mobile applications using modern frameworks.', 'Montreal', 67000.00, 'active'),
-- More international jobs
(37, 'Senior Software Engineer', 'Lead development team in London office.', 'London', 150000.00, 'active'),
(38, 'Technical Lead', 'Lead development team in Singapore office.', 'Singapore', 160000.00, 'active'),
(39, 'Full Stack Developer', 'Develop applications for Latin American market.', 'Mexico City', 90000.00, 'active'),
(40, 'DevOps Engineer', 'Manage infrastructure for African operations.', 'Johannesburg', 95000.00, 'active'),
(41, 'Senior Developer', 'Lead development team in Dubai office.', 'Dubai', 170000.00, 'active');

-- Additional Applications (Maintaining realistic ratio)
INSERT INTO applications (job_id, user_id, cover_letter, status) VALUES
-- More pending applications
(26, 26, 'I have extensive experience in software architecture and team leadership.', 'Pending'),
(27, 27, 'My product management experience spans multiple industries.', 'Pending'),
(28, 28, 'I have led data science teams and delivered significant business impact.', 'Pending'),
(29, 29, 'My engineering leadership experience makes me an ideal candidate.', 'Pending'),
(30, 30, 'I have successfully led technical initiatives at multiple companies.', 'Pending'),
-- More shortlisted applications
(31, 31, 'I have extensive experience in frontend development and team leadership.', 'Shortlisted'),
(32, 32, 'My backend development experience includes scalable systems.', 'Shortlisted'),
(33, 33, 'I have led full stack teams and delivered complex projects.', 'Shortlisted'),
(34, 34, 'My cloud architecture experience includes multiple platforms.', 'Shortlisted'),
(35, 35, 'I have successfully led mobile development teams.', 'Shortlisted'),
-- More hired applications
(36, 36, 'I have mentored junior developers and led successful projects.', 'Hired'),
(37, 37, 'My experience in API development and team leadership is extensive.', 'Hired'),
(38, 38, 'I have successfully delivered multiple full stack projects.', 'Hired'),
(39, 39, 'My DevOps experience includes complex infrastructure projects.', 'Hired'),
(40, 40, 'I have led mobile development teams and delivered successful apps.', 'Hired'),
-- More rejected applications
(41, 41, 'I have experience in international team leadership.', 'Rejected'),
(42, 42, 'My technical leadership experience spans multiple regions.', 'Rejected'),
(43, 43, 'I have successfully led teams in emerging markets.', 'Rejected'),
(44, 44, 'My experience includes managing remote teams across continents.', 'Rejected'),
(45, 45, 'I have led technical initiatives in multiple countries.', 'Rejected'),
-- More pending applications for new users
(1, 56, 'I am excited about the opportunity to join your team.', 'Pending'),
(2, 57, 'My experience aligns perfectly with this role.', 'Pending'),
(3, 58, 'I bring unique international perspective to this position.', 'Pending'),
(4, 59, 'I am passionate about innovation and technology.', 'Pending'),
(5, 60, 'My technical skills match your requirements perfectly.', 'Pending'),
-- More shortlisted applications for new users
(6, 61, 'I have extensive experience in similar roles.', 'Shortlisted'),
(7, 62, 'My background in technology would be valuable.', 'Shortlisted'),
(8, 63, 'I am ready to contribute to your team''s success.', 'Shortlisted'),
(9, 64, 'My international experience sets me apart.', 'Shortlisted'),
(10, 65, 'I am excited about the challenges this role offers.', 'Shortlisted'),
-- More hired applications for new users
(11, 66, 'Thank you for considering my application.', 'Hired'),
(12, 67, 'I look forward to joining your team.', 'Hired'),
(13, 68, 'I am thrilled about this opportunity.', 'Hired'),
(14, 69, 'I am ready to start contributing immediately.', 'Hired'),
(15, 70, 'I appreciate your consideration of my application.', 'Hired'),
-- More rejected applications for new users
(16, 71, 'I hope to hear back from you soon.', 'Rejected'),
(17, 72, 'Thank you for reviewing my application.', 'Rejected'),
(18, 73, 'I would be grateful for the opportunity.', 'Rejected'),
(19, 74, 'I am confident in my ability to contribute.', 'Rejected'),
(20, 75, 'I look forward to your response.', 'Rejected'),
-- Additional diverse applications from new users
(21, 76, 'I am excited about working in an international environment.', 'Pending'),
(22, 77, 'My experience in the Asian market would be valuable.', 'Shortlisted'),
(23, 78, 'I have worked with diverse teams across Europe.', 'Hired'),
(24, 79, 'My understanding of Latin American markets is extensive.', 'Pending'),
(25, 80, 'I bring unique insights from African tech markets.', 'Shortlisted'),
(26, 81, 'My skills align perfectly with this opportunity.', 'Pending'),
(27, 82, 'I am passionate about innovative technologies.', 'Shortlisted'),
(28, 83, 'My experience in similar roles makes me an ideal candidate.', 'Hired'),
(29, 84, 'I have a proven track record of success.', 'Rejected'),
(30, 85, 'I am ready to contribute to your team immediately.', 'Pending');

-- Additional Saved Jobs (Maintaining realistic ratio)
INSERT INTO saved_jobs (user_id, job_id) VALUES
-- More users with multiple saved jobs
(26, 26),
(26, 27),
(26, 28),
(27, 29),
(27, 30),
(27, 31),
(28, 32),
(28, 33),
(28, 34),
(29, 35),
(29, 36),
(29, 37),
-- More users with single saved job
(30, 38),
(31, 39),
(32, 40),
(33, 41),
(34, 42),
-- More users with no saved jobs (edge case)
(35, 43),
(36, 44),
(37, 45),
(38, 26),
(39, 27),
(40, 28),
-- Users saving multiple jobs for new users
(56, 1), (56, 2), (56, 3),
(57, 2), (57, 3), (57, 4),
(58, 3), (58, 4), (58, 5),
(59, 4), (59, 5), (59, 6),
(60, 5), (60, 6), (60, 7),
(61, 6), (61, 7), (61, 8),
(62, 7), (62, 8), (62, 9),
(63, 8), (63, 9), (63, 10),
(64, 9), (64, 10), (64, 11),
(65, 10), (65, 11), (65, 12),
-- Users saving single jobs for new users
(66, 13),
(67, 14),
(68, 15),
(69, 16),
(70, 17),
(71, 18),
(72, 19),
(73, 20),
(74, 21),
(75, 22),
-- Users saving jobs based on their interests and location
(76, 21), (76, 22), (76, 23),
(77, 22), (77, 23), (77, 24),
(78, 23), (78, 24), (78, 25),
(79, 24), (79, 25), (79, 26),
(80, 25), (80, 26), (80, 27),
(81, 26), (81, 27), (81, 28),
(82, 27), (82, 28), (82, 29),
(83, 28), (83, 29), (83, 30),
(84, 29), (84, 30), (84, 21),
(85, 30), (85, 21), (85, 22);

-- Additional Audit Logs
INSERT INTO audit_logs (admin_id, action_type, details, ip_address) VALUES
-- More user management actions
(1, 'review_application', 'Reviewed application ID: 26', '192.168.1.120'),
(2, 'update_job', 'Updated job posting ID: 28', '192.168.1.121'),
(3, 'delete_application', 'Removed inactive application ID: 15', '192.168.1.122'),
(1, 'user_report', 'Generated weekly user activity report', '192.168.1.123'),
(2, 'employer_verification', 'Verified new employer account ID: 26', '192.168.1.124'),
-- More system operations
(3, 'system_backup', 'Initiated daily system backup', '192.168.1.125'),
(1, 'security_alert', 'Investigated suspicious login attempts', '192.168.1.126'),
(2, 'content_moderation', 'Reviewed reported job posting ID: 25', '192.168.1.127'),
(3, 'analytics_export', 'Generated monthly analytics report', '192.168.1.128'),
(1, 'maintenance', 'Performed system maintenance', '192.168.1.129'),
-- More diverse audit logs
(2, 'user_verification', 'Verified user account ID: 45', '192.168.1.130'),
(3, 'job_approval', 'Approved new job posting ID: 46', '192.168.1.131'),
(1, 'employer_suspension', 'Suspended employer account ID: 25', '192.168.1.132'),
(2, 'resume_review', 'Reviewed resume for user ID: 18', '192.168.1.133'),
(3, 'application_review', 'Reviewed application ID: 30', '192.168.1.134'),
(1, 'system_update', 'Applied system updates', '192.168.1.135'),
(2, 'data_export', 'Exported employer data for compliance', '192.168.1.136'),
(3, 'content_update', 'Updated user guidelines', '192.168.1.137'),
(1, 'user_communication', 'Sent welcome email to new users', '192.168.1.138'),
(2, 'system_monitoring', 'Monitored system health metrics', '192.168.1.139');