<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

try {

    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT a.*, j.title as job_title, j.location, 
                          e.company_name, a.applied_at, a.status
                          FROM applications a
                          JOIN jobs j ON a.job_id = j.job_id
                          JOIN employers e ON j.employer_id = e.employer_id
                          WHERE a.user_id = ?
                          ORDER BY a.applied_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'update_profile':
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, location = ? WHERE user_id = ?");
                    $stmt->execute([
                        $_POST['full_name'],
                        $_POST['location'],
                        $_SESSION['user_id']
                    ]);
                    break;

                case 'update_resume':
                    if (isset($_FILES['resume'])) {
                        $uploadDir = 'uploads/resumes/';
                        $resumePath = $uploadDir . uniqid() . '_' . basename($_FILES['resume']['name']);
                        
                        if (move_uploaded_file($_FILES['resume']['tmp_name'], $resumePath)) {
                            $stmt = $pdo->prepare("UPDATE users SET resume_link = ? WHERE user_id = ?");
                            $stmt->execute([$resumePath, $_SESSION['user_id']]);
                        }
                    }
                    break;
            }
            header('Location: userprofile.php');
            exit();
        }
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - JobSwipe</title>
    <link rel="stylesheet" href="../CSS/userprofile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JS/userprofile.js" defer></script>
</head>
<body>
    <div class="sidebar">
        <h2>JobSwipe</h2>
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="userprofile.php" class="active"><i class="fas fa-user"></i> Profile</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

    <div class="main-content">
        <div class="container">
            <div class="profile-header">
                <img src="<?php echo htmlspecialchars($user['profile_picture'] ?? 'default-profile.jpg'); ?>" 
                     alt="Profile Picture" class="profile-picture" id="profile-picture">
                <div class="profile-info">
                    <h1><?php echo htmlspecialchars($user['full_name']); ?></h1>
                    <p><?php echo htmlspecialchars($user['location']); ?></p>
                    <button class="edit-button" onclick="openEditModal('profile')">Edit Profile</button>
                </div>
            </div>

            <div class="section">
                <h2>Resume</h2>
                <div class="resume-section">
                    <?php if ($user['resume_link']): ?>
                        <p>Current Resume: <a href="<?php echo htmlspecialchars($user['resume_link']); ?>" target="_blank">View Resume</a></p>
                    <?php else: ?>
                        <p>No resume uploaded</p>
                    <?php endif; ?>
                    <form action="userprofile.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="action" value="update_resume">
                        <input type="file" name="resume" accept=".pdf,.doc,.docx">
                        <button type="submit" class="edit-button">Upload Resume</button>
                    </form>
                </div>
            </div>

            <div class="section">
                <h2>Job Applications</h2>
                <div class="applications-list">
                    <?php foreach ($applications as $application): ?>
                        <div class="application-item">
                            <h3><?php echo htmlspecialchars($application['job_title']); ?> at 
                                <?php echo htmlspecialchars($application['company_name']); ?></h3>
                            <p>Location: <?php echo htmlspecialchars($application['location']); ?></p>
                            <p>Applied: <?php echo date('F j, Y', strtotime($application['applied_at'])); ?></p>
                            <p>Status: <span class="status-<?php echo strtolower($application['status']); ?>">
                                <?php echo htmlspecialchars($application['status']); ?></span></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Edit Profile</h2>
            <form action="userprofile.php" method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" 
                           value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location" 
                           value="<?php echo htmlspecialchars($user['location']); ?>">
                </div>
                <button type="submit" class="edit-button">Save Changes</button>
            </form>
        </div>
    </div>
</body>
</html>