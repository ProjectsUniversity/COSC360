<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $job_id = $_POST['job_id'] ?? '';

    switch ($action) {
        case 'delete':
            if ($job_id) {
                $pdo->beginTransaction();
                try {
                    // First delete associated saved jobs
                    $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE job_id = ?");
                    $stmt->execute([$job_id]);
                    
                    // Then delete associated applications
                    $stmt = $pdo->prepare("DELETE FROM applications WHERE job_id = ?");
                    $stmt->execute([$job_id]);
                    
                    // Finally delete the job
                    $stmt = $pdo->prepare("DELETE FROM jobs WHERE job_id = ?");
                    $stmt->execute([$job_id]);
                    
                    $pdo->commit();
                    logAdminAction('delete_job', "Deleted job ID: $job_id");
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    throw $e;
                }
            }
            break;
        case 'toggle_status':
            if ($job_id) {
                $stmt = $pdo->prepare("UPDATE jobs SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE job_id = ?");
                $stmt->execute([$job_id]);
                logAdminAction('toggle_job_status', "Toggled status for job ID: $job_id");
            }
            break;
    }
    header('Location: jobs.php');
    exit();
}

// Build query with filters
$where = [];
$params = [];
$types = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where[] = "(j.title LIKE ? OR j.description LIKE ?)";
    $search = "%{$_GET['search']}%";
    $params[] = $search;
    $params[] = $search;
    $types .= 'ss';
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $where[] = "j.status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

if (isset($_GET['employer_id']) && !empty($_GET['employer_id'])) {
    $where[] = "j.employer_id = ?";
    $params[] = $_GET['employer_id'];
    $types .= 'i';
}

$query = "SELECT j.*, e.company_name,
          (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) as application_count
          FROM jobs j
          JOIN employers e ON j.employer_id = e.employer_id";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY j.created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch employers for filter
$employers = $pdo->query("SELECT employer_id, company_name FROM employers ORDER BY company_name")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: #343a40;
            color: white;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
        }
        .sidebar .nav-link:hover {
            color: white;
        }
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,.1);
        }
        .job-description {
            max-height: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="p-3">
                    <h4>Admin Panel</h4>
                </div>
                <ul class="nav flex-column">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="users.php">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employers.php">
                            <i class="bi bi-building"></i> Employers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="jobs.php">
                            <i class="bi bi-briefcase"></i> Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="analytics.php">
                            <i class="bi bi-graph-up"></i> Analytics
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="audit-logs.php">
                            <i class="bi bi-journal-text"></i> Audit Logs
                        </a>
                    </li>
                    <li class="nav-item mt-3">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Job Management</h2>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by title or description" 
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="employer_id">
                                    <option value="">All Employers</option>
                                    <?php foreach ($employers as $employer): ?>
                                        <option value="<?php echo $employer['employer_id']; ?>" 
                                                <?php echo ($_GET['employer_id'] ?? '') == $employer['employer_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($employer['company_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" <?php echo ($_GET['status'] ?? '') === 'active' ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($_GET['status'] ?? '') === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Filter</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Jobs Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Company</th>
                                        <th>Location</th>
                                        <th>Salary</th>
                                        <th>Applications</th>
                                        <th>Status</th>
                                        <th>Posted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($jobs as $job): ?>
                                    <tr>
                                        <td><?php echo $job['job_id']; ?></td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span><?php echo htmlspecialchars($job['title']); ?></span>
                                                <small class="text-muted job-description"><?php echo htmlspecialchars($job['description']); ?></small>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($job['company_name']); ?></td>
                                        <td><?php echo htmlspecialchars($job['location']); ?></td>
                                        <td>$<?php echo number_format($job['salary'], 2); ?></td>
                                        <td><?php echo $job['application_count']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $job['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($job['status']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('M d, Y', strtotime($job['created_at'])); ?></td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewDetails(<?php echo $job['job_id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to toggle this job\'s status?');">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this job? This action cannot be undone.');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="job_id" value="<?php echo $job['job_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-danger">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Job Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="jobDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDetails(jobId) {
            const modal = new bootstrap.Modal(document.getElementById('jobModal'));
            const detailsDiv = document.getElementById('jobDetails');
            
            // Fetch job details
            fetch(`get-job-details.php?job_id=${jobId}`)
                .then(response => response.json())
                .then(data => {
                    detailsDiv.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Title:</strong> ${data.title}</p>
                                <p><strong>Company:</strong> ${data.company_name}</p>
                                <p><strong>Location:</strong> ${data.location}</p>
                                <p><strong>Salary:</strong> $${parseFloat(data.salary).toLocaleString()}</p>
                                <p><strong>Status:</strong> <span class="badge bg-${data.status === 'active' ? 'success' : 'danger'}">${data.status}</span></p>
                                <p><strong>Posted:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total Applications:</strong> ${data.application_count}</p>
                                <p><strong>Application Status Breakdown:</strong></p>
                                <ul class="list-unstyled">
                                    ${Object.entries(data.application_status).map(([status, count]) => `
                                        <li>${status}: ${count}</li>
                                    `).join('')}
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4">
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Applicant</th>
                                            <th>Applied</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.recent_applications.map(app => `
                                            <tr>
                                                <td>${app.applicant_name}</td>
                                                <td>${new Date(app.applied_at).toLocaleDateString()}</td>
                                                <td><span class="badge bg-${app.status === 'Pending' ? 'warning' : app.status === 'Shortlisted' ? 'info' : app.status === 'Hired' ? 'success' : 'danger'}">${app.status}</span></td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    detailsDiv.innerHTML = '<div class="alert alert-danger">Error loading job details</div>';
                });

            modal.show();
        }
    </script>
</body>
</html>