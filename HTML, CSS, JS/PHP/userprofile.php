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
                    $_SESSION['message'] = "Profile updated successfully!";
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
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - JobSwipe</title>
    <link rel="stylesheet" href="../CSS/userprofile.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="../JS/theme.js" defer></script>
    <script src="../JS/userprofile.js" defer></script>
</head>
<body>
    <div class="sidebar">
        <h2>JobSwipe</h2>
        <a href="homepage.php"><i class="fas fa-home"></i> Home</a>
        <a href="userprofile.php" class="active"><i class="fas fa-user"></i> Profile</a>
        <a href="saved-jobs.php"><i class="fas fa-bookmark"></i> Saved Jobs</a>
        <a href="settings.php"><i class="fas fa-cog"></i> Settings</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        <button class="theme-toggle" onclick="toggleTheme()">
            <i class="fas fa-moon"></i> Dark Mode
        </button>
    </div>

    <div class="main-content">
        <h1>User Profile</h1>
        <div class="container">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="success-message"><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="error-message"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>
            
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
                <h2>Job Applications</h2>
                <div class="applications-list">
                    <?php if (count($applications) > 0): ?>
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
                    <?php else: ?>
                        <p>You haven't applied to any jobs yet.</p>
                    <?php endif; ?>
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
    <script>
        // Set initial state of theme toggle button
        document.addEventListener('DOMContentLoaded', () => {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
        });
    </script>
</body>
</html>