<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

// Handle job actions (delete, toggle status)
function handleJobAction($pdo, $action, $jobId) {
    if ($action === 'delete') {
        try {
            $pdo->beginTransaction();
            
            // Delete in order: saved_jobs -> applications -> jobs
            $tables = ['saved_jobs', 'applications', 'jobs'];
            foreach ($tables as $table) {
                $stmt = $pdo->prepare("DELETE FROM $table WHERE job_id = ?");
                $stmt->execute([$jobId]);
            }
            
            $pdo->commit();
            logAdminAction('delete_job', "Deleted job ID: $jobId");
        } catch (PDOException $e) {
            $pdo->rollBack();
            throw $e;
        }
    } elseif ($action === 'toggle_status') {
        $stmt = $pdo->prepare("UPDATE jobs SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE job_id = ?");
        $stmt->execute([$jobId]);
        logAdminAction('toggle_job_status', "Toggled status for job ID: $jobId");
    }
}

// Process POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $jobId = $_POST['job_id'] ?? '';
    
    if ($jobId && in_array($action, ['delete', 'toggle_status'])) {
        handleJobAction($pdo, $action, $jobId);
    }
    header('Location: jobs.php');
    exit();
}

// Build search query
function buildJobQuery($filters) {
    $where = [];
    $params = [];
    
    if (!empty($filters['search'])) {
        $where[] = "(j.title LIKE ? OR j.description LIKE ?)";
        $search = "%{$filters['search']}%";
        $params[] = $search;
        $params[] = $search;
    }
    
    if (!empty($filters['status'])) {
        $where[] = "j.status = ?";
        $params[] = $filters['status'];
    }
    
    if (!empty($filters['employer_id'])) {
        $where[] = "j.employer_id = ?";
        $params[] = $filters['employer_id'];
    }
    
    $query = "SELECT j.*, e.company_name,
              (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) as application_count
              FROM jobs j
              JOIN employers e ON j.employer_id = e.employer_id";
              
    if (!empty($where)) {
        $query .= " WHERE " . implode(" AND ", $where);
    }
    
    $query .= " ORDER BY j.created_at DESC";
    
    return ['query' => $query, 'params' => $params];
}

// Get filtered jobs
$filters = [
    'search' => $_GET['search'] ?? '',
    'status' => $_GET['status'] ?? '',
    'employer_id' => $_GET['employer_id'] ?? ''
];

$query = buildJobQuery($filters);
$stmt = $pdo->prepare($query['query']);
$stmt->execute($query['params']);
$jobs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get employers for filter dropdown
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
            height: 100vh; 
            background: #343a40; 
            color: white; 
            position: fixed;
            display: flex;
            flex-direction: column;
            width: 16%;
            z-index: 1000;
        }
        .sidebar .nav-link { color: rgba(255,255,255,.75); }
        .sidebar .nav-link:hover { color: white; }
        .sidebar .nav-link.active { color: white; background: rgba(255,255,255,.1); }
        
        .sidebar .nav {
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        
        .sidebar .logout-item {
            margin-top: auto;
        }
        
        .main-content {
            margin-left: 16.666667%; /* For col-md-2 */
        }
        
        @media (max-width: 767.98px) {
            .main-content {
                margin-left: 0;
            }
        }
        
        .job-description { max-height: 100px; overflow: hidden; text-overflow: ellipsis; }
        .actions-group { display: flex; gap: 0.25rem; }
        .badge { font-size: 0.85em; }
        
        /* Table column widths */
        .col-id { width: 3%; }
        .col-title { width: 25%; }
        .col-company { width: 19%; }
        .col-location { width: 15%; }
        .col-salary { width: 15%; }
        .col-apps { width: 4%; }
        .col-status { width: 10%; }
        .col-date { width: 25%; }
        .col-actions { width: 15%; }
        
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
                        <a class="nav-link" href="audit-logs.php">
                            <i class="bi bi-journal-text"></i> Audit Logs
                        </a>
                    </li>
                    <li class="nav-item logout-item">
                        <a class="nav-link" href="logout.php">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4 main-content">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2>Job Management</h2>
                </div>

                <!-- Search Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-4">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search jobs..." 
                                       value="<?= htmlspecialchars($filters['search']) ?>">
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="employer_id">
                                    <option value="">All Employers</option>
                                    <?php foreach ($employers as $employer): ?>
                                    <option value="<?= $employer['employer_id'] ?>"
                                            <?= $filters['employer_id'] == $employer['employer_id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($employer['company_name']) ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <select class="form-select" name="status">
                                    <option value="">All Status</option>
                                    <option value="active" <?= $filters['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                    <option value="inactive" <?= $filters['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary w-100">Search</button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Jobs List -->
                <div class="card">
                    <div class="card-body">
                        <?php if (empty($jobs)): ?>
                            <div class="text-center py-4">
                                <p class="text-muted">No jobs found matching your criteria.</p>
                            </div>
                        <?php else: ?>
                            <div class="table-responsive ">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-id">ID</th>
                                            <th class="col-title">Title</th>
                                            <th class="col-company">Company</th>
                                            <th class="col-location">Location</th>
                                            <th class="col-salary">Salary</th>
                                            <th class="col-apps">Applications</th>
                                            <th class="col-status">Status</th>
                                            <th class="col-date">Posted</th>
                                            <th class="col-actions">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($jobs as $job): ?>
                                        <tr>
                                            <td><?= $job['job_id'] ?></td>
                                            <td>
                                                <div>
                                                    <strong><?= htmlspecialchars($job['title']) ?></strong>
                                                </div>
                                            </td>
                                            <td><?= htmlspecialchars($job['company_name']) ?></td>
                                            <td><?= htmlspecialchars($job['location']) ?></td>
                                            <td>$<?= number_format($job['salary'], 2) ?></td>
                                            <td><?= $job['application_count'] ?></td>
                                            <td>
                                                <span class="badge bg-<?= $job['status'] === 'active' ? 'success' : 'danger' ?>">
                                                    <?= ucfirst($job['status']) ?>
                                                </span>
                                            </td>
                                            <td><?= date('M d, Y', strtotime($job['created_at'])) ?></td>
                                            <td>
                                                <div class="actions-group">
                                                    <button type="button" class="btn btn-sm btn-info" 
                                                            onclick="viewJobDetails(<?= $job['job_id'] ?>)" 
                                                            title="View Details">
                                                        <i class="bi bi-eye"></i>
                                                    </button>
                                                    
                                                    <form method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Toggle job status?');">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Toggle Status">
                                                            <i class="bi bi-toggle-on"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" class="d-inline" 
                                                          onsubmit="return confirm('Delete this job permanently?');">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-danger" title="Delete Job">
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
                        <?php endif; ?>
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
                <div class="modal-body" id="jobDetails">
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function viewJobDetails(jobId) {
        const modal = new bootstrap.Modal(document.getElementById('jobModal'));
        const detailsDiv = document.getElementById('jobDetails');
        
        modal.show();
        
        fetch(`get-job-details.php?job_id=${jobId}`)
            .then(response => response.json())
            .then(data => {
                detailsDiv.innerHTML = `
                    <div class="job-details">
                        <div class="mb-4">
                            <h4>${data.title}</h4>
                            <div class="text-muted">${data.company_name} - ${data.location}</div>
                            <div class="mt-2">
                                <span class="badge bg-${data.status === 'active' ? 'success' : 'danger'}">
                                    ${data.status.charAt(0).toUpperCase() + data.status.slice(1)}
                                </span>
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Recent Applications</h5>
                            ${data.recent_applications.length ? `
                                <div class="table-responsive">
                                    <table class="table table-sm">
                                        <thead>
                                            <tr>
                                                <th>Applicant</th>
                                                <th>Status</th>

                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${data.recent_applications.map(app => `
                                                <tr>
                                                    <td>${app.applicant_name}</td>
                                                    <td>
                                                        <span class="badge bg-${getStatusColor(app.status)}">
                                                            ${app.status}
                                                        </span>
                                                    </td>
                                                </tr>
                                            `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            ` : '<p class="text-muted">No applications yet</p>'}
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                detailsDiv.innerHTML = `
                    <div class="alert alert-danger">
                        Failed to load job details. Please try again.
                    </div>
                `;
            });
    }

    function getStatusColor(status) {
        const colors = {
            'Pending': 'warning',
            'Shortlisted': 'info',
            'Hired': 'success',
            'Rejected': 'danger'
        };
        return colors[status] || 'secondary';
    }
    </script>
</body>
</html>