<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

// Handle employer actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $employer_id = $_POST['employer_id'] ?? '';

    switch ($action) {
        case 'delete':
            if ($employer_id) {
                $stmt = $pdo->prepare("DELETE FROM employers WHERE employer_id = ?");
                $stmt->execute([$employer_id]);
                logAdminAction('delete_employer', "Deleted employer ID: $employer_id");
            }
            break;
        case 'toggle_status':
            if ($employer_id) {
                $stmt = $pdo->prepare("UPDATE employers SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE employer_id = ?");
                $stmt->execute([$employer_id]);
                logAdminAction('toggle_employer_status', "Toggled status for employer ID: $employer_id");
            }
            break;
    }
    header('Location: employers.php');
    exit();
}

// Build query with filters
$where = [];
$params = [];
$types = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where[] = "(company_name LIKE ? OR email LIKE ?)";
    $search = "%{$_GET['search']}%";
    $params[] = $search;
    $params[] = $search;
    $types .= 'ss';
}

if (isset($_GET['status']) && !empty($_GET['status'])) {
    $where[] = "status = ?";
    $params[] = $_GET['status'];
    $types .= 's';
}

$query = "SELECT e.*, 
          (SELECT COUNT(*) FROM jobs j WHERE j.employer_id = e.employer_id) as total_jobs,
          (SELECT COUNT(*) FROM jobs j WHERE j.employer_id = e.employer_id AND j.status = 'active') as active_jobs,
          (SELECT COUNT(*) FROM applications a 
           JOIN jobs j ON a.job_id = j.job_id 
           WHERE j.employer_id = e.employer_id) as total_applications
          FROM employers e";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$employers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employer Management - Admin Dashboard</title>
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
        
        /* Table column widths */
        .col-id { width: 5%; }
        .col-company { width: 16%; }
        .col-email { width: 19%; }
        .col-location { width: 14%; }
        .col-jobs { width: 8%; }
        .col-active { width: 9%; }
        .col-apps { width: 10%; }
        .col-status { width: 8%; }
        .col-actions { width: 16%; }
        
        .table-responsive table {
            width: 100%;
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
                        <a class="nav-link active" href="employers.php">
                            <i class="bi bi-building"></i> Employers
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="jobs.php">
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
                    <h2>Employer Management</h2>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by company name or email" 
                                       value="<?php echo htmlspecialchars($_GET['search'] ?? ''); ?>">
                            </div>
                            <div class="col-md-4">
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

                <!-- Employers Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive text-center">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th class="col-id">ID</th>
                                        <th class="col-company">Company</th>
                                        <th class="col-email">Email</th>
                                        <th class="col-location">Location</th>
                                        <th class="col-jobs">Total Jobs</th>
                                        <th class="col-active">Active Jobs</th>
                                        <th class="col-apps">Applications</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($employers as $employer): ?>
                                    <tr>
                                        <td><?php echo $employer['employer_id']; ?></td>
                                        <td><?php echo htmlspecialchars($employer['company_name']); ?></td>
                                        <td><?php echo htmlspecialchars($employer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($employer['location']); ?></td>
                                        <td><?php echo $employer['total_jobs']; ?></td>
                                        <td><?php echo $employer['active_jobs']; ?></td>
                                        <td><?php echo $employer['total_applications']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $employer['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst(htmlspecialchars($employer['status'] ?? 'unknown')); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info me-2" 
                                                        onclick="viewDetails(<?php echo $employer['employer_id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to toggle this employer\'s status?');">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="employer_id" value="<?php echo $employer['employer_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning me-2">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this employer? This action cannot be undone.');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="employer_id" value="<?php echo $employer['employer_id']; ?>">
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

    <!-- Employer Details Modal -->
    <div class="modal fade" id="employerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Employer Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="employerDetails"></div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewDetails(employerId) {
            const modal = new bootstrap.Modal(document.getElementById('employerModal'));
            const detailsDiv = document.getElementById('employerDetails');
            
            // Fetch employer details
            fetch(`get-employer-details.php?employer_id=${employerId}`)
                .then(response => response.json())
                .then(data => {
                    detailsDiv.innerHTML = `
                        <div class="row">
                            <div class="col-md-6">
                                <h6>Company Information</h6>
                                <p><strong>Name:</strong> ${data.company_name}</p>
                                <p><strong>Email:</strong> ${data.email}</p>
                                <p><strong>Location:</strong> ${data.location}</p>
                                <p><strong>Created:</strong> ${new Date(data.created_at).toLocaleDateString()}</p>
                            </div>
                            <div class="col-md-6">
                                <h6>Statistics</h6>
                                <p><strong>Total Jobs:</strong> ${data.total_jobs}</p>
                                <p><strong>Active Jobs:</strong> ${data.active_jobs}</p>
                                <p><strong>Total Applications:</strong> ${data.total_applications}</p>
                                <p><strong>Average Applications per Job:</strong> ${(data.total_applications / data.total_jobs).toFixed(1)}</p>
                            </div>
                        </div>
                        <div class="mt-4">
                            <h6>Recent Job Listings</h6>
                            <div class="table-responsive">
                                <table class="table table-sm">
                                    <thead>
                                        <tr>
                                            <th>Title</th>
                                            <th>Status</th>
                                            <th>Applications</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        ${data.recent_jobs.map(job => `
                                            <tr>
                                                <td>${job.title}</td>
                                                <td><span class="badge bg-${job.status === 'active' ? 'success' : 'danger'}">${job.status}</span></td>
                                                <td>${job.application_count}</td>
                                            </tr>
                                        `).join('')}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    `;
                })
                .catch(error => {
                    detailsDiv.innerHTML = '<div class="alert alert-danger">Error loading employer details</div>';
                });

            modal.show();
        }
    </script>
</body>
</html>