<?php
session_start();
require_once 'config.php';

// Check if employer is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

$errors = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Modern input sanitization and validation
    $title = isset($_POST['title']) ? trim(htmlspecialchars($_POST['title'])) : '';
    $description = isset($_POST['description']) ? trim(htmlspecialchars($_POST['description'])) : '';
    $location = isset($_POST['location']) ? trim(htmlspecialchars($_POST['location'])) : '';
    $salary = isset($_POST['salary']) ? filter_var($_POST['salary'], FILTER_VALIDATE_FLOAT) : null;

    // Validation
    if (empty($title)) {
        $errors[] = "Job title is required";
    } elseif (strlen($title) > 255) {
        $errors[] = "Job title must be less than 255 characters";
    }

    if (empty($description)) {
        $errors[] = "Job description is required";
    }

    if (empty($location)) {
        $errors[] = "Location is required";
    } elseif (strlen($location) > 255) {
        $errors[] = "Location must be less than 255 characters";
    }

    if ($salary === null || $salary === false) {
        $errors[] = "Valid salary amount is required";
    } elseif ($salary <= 0) {
        $errors[] = "Salary must be greater than zero";
    }

    // If no errors, proceed with database insertion
    if (empty($errors)) {
        try {
            // Match exactly with the database schema
            $sql = "INSERT INTO jobs (employer_id, title, description, location, salary, status, created_at) 
                    VALUES (:employer_id, :title, :description, :location, :salary, :status, NOW())";
            $stmt = $pdo->prepare($sql);
            
            $params = [
                ':employer_id' => $_SESSION['employer_id'],
                ':title' => $title,
                ':description' => $description,
                ':location' => $location,
                ':salary' => $salary,
                ':status' => 'active'
            ];

            if ($stmt->execute($params)) {
                $_SESSION['success_message'] = "Job \"" . htmlspecialchars($title) . "\" has been posted successfully!";
                header("Location: dashboard.php");
                exit();
            } else {
                $errors[] = "Failed to post job. Please try again.";
            }
        } catch (PDOException $e) {
            error_log("Database error in addJobs.php: " . $e->getMessage());
            $errors[] = "A database error occurred. Please try again later.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job - JobSwipe</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/Recruiters/dashboard.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <aside class="sidebar">
            <div class="d-flex flex-column flex-shrink-0 p-3 bg-body-tertiary">
                <div class="sidebar-brand mb-3">
                    <a href="index.php" class="link-body-emphasis text-decoration-none">
                        <span class="fs-4">JobSwipe</span>
                    </a>
                </div>
                <hr>
                <ul class="nav nav-pills flex-column mb-auto">
                    <li class="nav-item">
                        <a href="dashboard.php" class="nav-link">
                            <i class="fa-solid fa-chart-simple"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="addJobs.php" class="nav-link active">
                            <i class="fa-solid fa-plus"></i> Post New Job
                        </a>
                    </li>
                </ul>
                <hr>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center link-body-emphasis text-decoration-none dropdown-toggle" 
                       data-bs-toggle="dropdown" aria-expanded="false">
                    </a>
                    <ul class="dropdown-menu text-small shadow">
                        <li><a class="dropdown-item" href="settings.php">Settings</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><a class="dropdown-item" href="logout.php">Sign out</a></li>
                    </ul>
                </div>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="main-content p-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h2 class="card-title mb-4">Post New Job</h2>

                                <?php if (!empty($errors)): ?>
                                    <div class="alert alert-danger">
                                        <ul class="mb-0">
                                            <?php foreach ($errors as $error): ?>
                                                <li><?php echo htmlspecialchars($error); ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>

                                <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST" class="needs-validation">
                                    <div class="mb-3">
                                        <label for="title" class="form-label">Job Title *</label>
                                        <input type="text" class="form-control" id="title" name="title" 
                                               value="<?php echo htmlspecialchars($title ?? ''); ?>" 
                                               maxlength="255" required>
                                        <div class="form-text">Enter a clear and concise title (max 255 characters)</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="description" class="form-label">Job Description *</label>
                                        <textarea class="form-control" id="description" name="description" 
                                                  rows="5" required><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                        <div class="form-text">Include key responsibilities, requirements, and qualifications</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="location" class="form-label">Location *</label>
                                        <input type="text" class="form-control" id="location" name="location" 
                                               value="<?php echo htmlspecialchars($location ?? ''); ?>" 
                                               maxlength="255" required>
                                        <div class="form-text">Enter city, state, or "Remote"</div>
                                    </div>

                                    <div class="mb-3">
                                        <label for="salary" class="form-label">Annual Salary *</label>
                                        <div class="input-group">
                                            <span class="input-group-text">$</span>
                                            <input type="number" class="form-control" id="salary" name="salary" 
                                                   value="<?php echo htmlspecialchars($salary ?? ''); ?>" 
                                                   min="1" step="0.01" required>
                                        </div>
                                        <div class="form-text">Enter the annual salary amount</div>
                                    </div>

                                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                        <a href="dashboard.php" class="btn btn-secondary me-md-2">Cancel</a>
                                        <button type="submit" class="btn btn-primary">Post Job</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>