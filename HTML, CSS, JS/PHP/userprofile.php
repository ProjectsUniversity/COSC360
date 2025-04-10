<?php
session_start();
require_once('config.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

define('UPLOAD_DIR', dirname(dirname(dirname(__FILE__))) . '/Uploads/resumes/');

try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("SELECT a.*, j.title as job_title, j.location, 
                          e.company_name, e.employer_id, a.applied_at, a.status
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
                case 'update_profile_image':
                    // Image upload handling                    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
                        $file_name = $_FILES['profile_image']['name'];
                        $file_tmp = $_FILES['profile_image']['tmp_name'];
                        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

                        $extensions = array("jpeg", "jpg", "png");

                        if (in_array($file_ext, $extensions) === false) {
                            $_SESSION['error'] = "Extension not allowed, please choose a JPEG or PNG file.";
                            error_log("Invalid file extension: " . $file_ext);
                        } else {
                            $image_name = uniqid() . '.' . $file_ext;
                            $target_dir = dirname(dirname(dirname(__FILE__))) . '/Uploads/profile_images/';
                            error_log("Attempting to upload to directory: " . $target_dir);
                            
                            if (!file_exists($target_dir)) {
                                if (!@mkdir($target_dir, 0777, true)) {
                                    error_log("Failed to create directory: " . error_get_last()['message']);
                                    $_SESSION['error'] = "Failed to create upload directory";
                                } else {
                                    error_log("Directory created successfully");
                                }
                            }
                            
                            if (!is_writable($target_dir)) {
                                error_log("Directory not writable: " . $target_dir);
                                chmod($target_dir, 0777);
                            }
                            
                            $target_file = $target_dir . $image_name;

                            if (move_uploaded_file($file_tmp, $target_file)) {
                                // Delete old profile image if exists
                                $stmt = $pdo->prepare("SELECT profile_image FROM users WHERE user_id = ?");
                                $stmt->execute([$_SESSION['user_id']]);
                                $old_image = $stmt->fetch(PDO::FETCH_ASSOC)['profile_image'];
                                if (!empty($old_image) && file_exists($old_image)) {
                                    unlink($old_image);
                                }

                                $relative_path = "Uploads/profile_images/" . $image_name;
                                $stmt = $pdo->prepare("UPDATE users SET profile_image = ? WHERE user_id = ?");
                                $stmt->execute([$relative_path, $_SESSION['user_id']]);
                                $_SESSION['message'] = "Profile image updated successfully!";
                            } else {
                                $_SESSION['error'] = "Sorry, there was an error uploading your file.";
                            }
                        }
                    }
                    break;
                    
                case 'update_resume':
                    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
                        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
                        $maxSize = 5 * 1024 * 1024; // 5MB
                        
                        if (!in_array($_FILES['resume']['type'], $allowedTypes)) {
                            $_SESSION['error'] = "Invalid file type.";
                            break;
                        }
                        
                        if ($_FILES['resume']['size'] > $maxSize) {
                            $_SESSION['error'] = "File is too large.";
                            break;
                        }
                        
                        // Create uploads directory if it doesn't exist
                        if (!file_exists(UPLOAD_DIR)) {
                            if (!mkdir(UPLOAD_DIR, 0777, true)) {
                                $_SESSION['error'] = "Failed to create upload directory.";
                                break;
                            }
                        }
                        
                        // Generate unique filename
                        $extension = pathinfo($_FILES['resume']['name'], PATHINFO_EXTENSION);
                        $filename = uniqid('resume_') . '.' . $extension;
                        $filepath = UPLOAD_DIR . $filename;
                        
                        // Delete old resume if exists
                        if (!empty($user['resume_path'])) {
                            $oldFile = UPLOAD_DIR . basename($user['resume_path']);
                            if (file_exists($oldFile)) {
                                unlink($oldFile);
                            }
                        }
                        error_log("UPLOAD_DIR: " . UPLOAD_DIR);
                        
                        error_log("Temp file path: " . $_FILES['resume']['tmp_name']);
                        error_log("Destination file path: " . $filepath);
                        error_log("Attempting to move uploaded file...");
                        if (move_uploaded_file($_FILES['resume']['tmp_name'], $filepath)) {
                            $stmt = $pdo->prepare("UPDATE users SET resume_path = ? WHERE user_id = ?");
                            $stmt->execute([$filename, $_SESSION['user_id']]);
                            $_SESSION['message'] = "Resume updated successfully!";
                        } else {
                            $error_message = error_get_last()['message'];
                            error_log("Upload failed. Error: " . $error_message);
                            $_SESSION['error'] = "Failed to upload resume. Please try again.";
                        }
                    }
                    break;
            }
            header('Location: userprofile.php');
            exit();
        }
    }
} catch (PDOException $e) {
    error_log("Database Error: " . $e->getMessage());
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
                <img src="<?php echo htmlspecialchars('../../' . ($user['profile_image'] ?? 'images/default-company-logo.png')); ?>"
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
                    <?php if ($user['resume_path']): ?>
                        <div class="resume-info">
                            <i class="fas fa-file-pdf"></i>
                            <span>Current Resume: <?php echo htmlspecialchars(basename($user['resume_path'])); ?></span>
                        </div>
                    <?php else: ?>
                        <p>No resume uploaded yet</p>
                    <?php endif; ?>
                    <button class="edit-button" onclick="openEditModal('resume')">
                        <?php echo $user['resume_path'] ? 'Update Resume' : 'Upload Resume'; ?>
                    </button>
                </div>
            </div>

            <div class="section">
                <h2>Job Applications</h2>
                <div class="applications-list">
                    <?php if (count($applications) > 0): ?>
                        <?php foreach ($applications as $application): ?>
                            <div class="application-item">
                                <h3><?php echo htmlspecialchars($application['job_title']); ?> at 
                                    <a href="company-dashboard.php?employer_id=<?php echo $application['employer_id']; ?>" 
                                       class="company-link">
                                        <?php echo htmlspecialchars($application['company_name']); ?>
                                    </a>
                                </h3>
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
            <form action="userprofile.php" method="POST" enctype="multipart/form-data">
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
            <form action="userprofile.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_profile_image">
                <div class="form-group">
                    <label for="profile_image">Upload New Profile Image</label>
                    <input type="file" name="profile_image" id="profile_image" accept=".jpg,.jpeg,.png">
                </div>
                <button type="submit" class="edit-button">Update Profile Image</button>
            </form>
            <form action="userprofile.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="update_resume">
                <div class="form-group">
                    <label for="resume">Upload Resume</label>
                    <input type="file" name="resume" id="resume" accept=".pdf,.doc,.docx">
                </div>
                <button type="submit" class="edit-button">Upload Resume</button>
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