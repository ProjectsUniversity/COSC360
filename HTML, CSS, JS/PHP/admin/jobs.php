<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/xml; charset=utf-8');
    $action = $_POST['action'] ?? '';
    $jobId = $_POST['job_id'] ?? '';
    
    // Start XML output
    $xml = new SimpleXMLElement('<response/>');
    
    switch ($action) {
        case 'create_job':
            // Validate input
            $required_fields = ['title', 'employer_id', 'description', 'location', 'salary'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $xml->addChild('success', 'false');
                    $xml->addChild('message', 'All required fields must be filled out');
                    echo $xml->asXML();
                    exit();
                }
            }
            
            try {
                // Create new job
                $stmt = $pdo->prepare("INSERT INTO jobs (employer_id, title, description, location, salary, status) 
                                      VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['employer_id'],
                    $_POST['title'],
                    $_POST['description'],
                    $_POST['location'],
                    $_POST['salary'],
                    $_POST['status'] ?? 'active'
                ]);
                
                $jobId = $pdo->lastInsertId();
                
                // Get the created job with company info
                $stmt = $pdo->prepare("SELECT j.*, e.company_name,
                                      (SELECT COUNT(*) FROM applications a WHERE a.job_id = j.job_id) as application_count
                                      FROM jobs j
                                      JOIN employers e ON j.employer_id = e.employer_id
                                      WHERE j.job_id = ?");
                $stmt->execute([$jobId]);
                $job = $stmt->fetch(PDO::FETCH_ASSOC);
                
                logAdminAction('create_job', "Created new job: {$job['title']} (ID: $jobId)");
                
                $xml->addChild('success', 'true');
                $xml->addChild('message', 'Job created successfully');
                $jobNode = $xml->addChild('job');
                $jobNode->addChild('job_id', $job['job_id']);
                $jobNode->addChild('title', htmlspecialchars($job['title']));
                $jobNode->addChild('company_name', htmlspecialchars($job['company_name']));
                $jobNode->addChild('location', htmlspecialchars($job['location']));
                $jobNode->addChild('salary', $job['salary']);
                $jobNode->addChild('created_at', $job['created_at']);
                $jobNode->addChild('application_count', $job['application_count']);
                $jobNode->addChild('status', $job['status']);
                $jobNode->addChild('badge_class', $job['status'] === 'active' ? 'success' : 'danger');
                $jobNode->addChild('status_text', ucfirst($job['status']));

            } catch (PDOException $e) {
                // Ensure the XML response indicates failure
                if ($xml->xpath('//success')) { // Check if success node already exists
                     $xml->success = 'false'; // Update existing node
                } else {
                     $xml->addChild('success', 'false'); // Add new node
                }
                 if ($xml->xpath('//message')) {
                     $xml->message = 'Error creating job: ' . htmlspecialchars($e->getMessage());
                 } else {
                     $xml->addChild('message', 'Error creating job: ' . htmlspecialchars($e->getMessage()));
                 }
            }
            break;
        case 'delete':
        case 'toggle_status':
            if ($jobId && in_array($action, ['delete', 'toggle_status'])) {
                try {
                    if ($action === 'delete') {
                        $pdo->beginTransaction();
                        
                        // Delete in order: saved_jobs -> applications -> jobs
                        $tables = ['saved_jobs', 'applications', 'jobs'];
                        foreach ($tables as $table) {
                            $stmt = $pdo->prepare("DELETE FROM $table WHERE job_id = ?");
                            $stmt->execute([$jobId]);
                        }
                        
                        $pdo->commit();
                        logAdminAction('delete_job', "Deleted job ID: $jobId");
                        $xml->addChild('success', 'true');
                        $xml->addChild('message', 'Job deleted successfully');
                        $xml->addChild('job_id', $jobId);
                    } else if ($action === 'toggle_status') {
                        $stmt = $pdo->prepare("UPDATE jobs SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE job_id = ?");
                        if ($stmt->execute([$jobId])) {
                            // Get the new status
                            $stmt = $pdo->prepare("SELECT status FROM jobs WHERE job_id = ?");
                            $stmt->execute([$jobId]);
                            $new_status = $stmt->fetchColumn();
                            
                            logAdminAction('toggle_job_status', "Toggled status for job ID: $jobId");
                            $xml->addChild('success', 'true');
                            $xml->addChild('message', 'Job status updated successfully');
                            $xml->addChild('job_id', $jobId);
                            $xml->addChild('new_status', $new_status);
                            $xml->addChild('badge_class', $new_status === 'active' ? 'success' : 'danger');
                            $xml->addChild('status_text', ucfirst($new_status));
                        }
                    }
                } catch (PDOException $e) {
                    if ($pdo->inTransaction()) {
                        $pdo->rollBack();
                    }
                    $xml->addChild('success', 'false');
                    $xml->addChild('message', 'Database error: ' . htmlspecialchars($e->getMessage()));
                }
            }
            break;
    }
    
    // If no specific action matched or default case needed
    if ($xml->count() == 0) { // Check if anything was added
         $xml->addChild('success', 'false');
         $xml->addChild('message', 'Invalid action or no data processed.');
    }
    echo $xml->asXML();
    exit();
}

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
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addJobModal">
                        <i class="bi bi-briefcase-fill"></i> Add Job
                    </button>
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
                                        <tr data-job-id="<?= $job['job_id'] ?>">
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
                                                <span class="badge bg-<?= $job['status'] === 'active' ? 'success' : 'danger' ?> status-badge">
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
                                                    
                                                    <form method="POST" class="d-inline ajax-form">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="job_id" value="<?= $job['job_id'] ?>">
                                                        <button type="submit" class="btn btn-sm btn-warning" title="Toggle Status">
                                                            <i class="bi bi-toggle-on"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" class="d-inline ajax-form">
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

    <!-- Add Job Modal -->
    <div class="modal fade" id="addJobModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Job</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addJobForm">
                        <div class="mb-3">
                            <label for="title" class="form-label">Job Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                        <div class="mb-3">
                            <label for="employerId" class="form-label">Employer</label>
                            <select class="form-select" id="employerId" name="employer_id" required>
                                <option value="">Select Employer</option>
                                <?php foreach ($employers as $employer): ?>
                                    <option value="<?= $employer['employer_id'] ?>">
                                        <?= htmlspecialchars($employer['company_name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="description" class="form-label">Job Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label for="location" class="form-label">Location</label>
                                <input type="text" class="form-control" id="location" name="location" required>
                            </div>
                            <div class="col">
                                <label for="salary" class="form-label">Salary</label>
                                <input type="number" class="form-control" id="salary" name="salary" step="0.01" min="0" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="jobStatus" class="form-label">Status</label>
                            <select class="form-select" id="jobStatus" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Job</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Add notification system
    function showNotification(message, type = 'success') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        const mainContent = document.querySelector('.main-content');
        mainContent.insertBefore(alertDiv, mainContent.firstChild.nextSibling);
        
        // Auto-dismiss after 3 seconds
        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 150);
        }, 3000);
    }
    
    // Initialize AJAX forms for job actions
    function initializeAjaxForms() {
        document.querySelectorAll('form.ajax-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const action = this.querySelector('[name="action"]').value;
                const jobId = this.querySelector('[name="job_id"]').value;
                
                // Get confirmation
                let confirmed = false;
                if (action === 'toggle_status') {
                    confirmed = confirm('Are you sure you want to toggle this job\'s status?');
                } else if (action === 'delete') {
                    confirmed = confirm('Are you sure you want to delete this job? This action cannot be undone.');
                }
                
                if (confirmed) {
                    const formData = new FormData(this);
                    
                    // Use fetch API for AJAX request
                    fetch('jobs.php', {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                        return response.text(); // Get text first
                    })
                    .then(str => (new window.DOMParser()).parseFromString(str, "text/xml")) // Parse as XML
                    .then(xmlDoc => {
                        if (!xmlDoc || xmlDoc.getElementsByTagName('parsererror').length > 0) {
                            showNotification('Error parsing XML response', 'danger');
                            console.error("XML Parsing Error:", xmlDoc ? xmlDoc.getElementsByTagName('parsererror')[0] : 'No XML Document');
                            return;
                        }
                        
                        const success = xmlDoc.getElementsByTagName('success')[0]?.textContent === 'true';
                        const message = xmlDoc.getElementsByTagName('message')[0]?.textContent || 'An unknown error occurred.';
                        const jobIdResponse = xmlDoc.getElementsByTagName('job_id')[0]?.textContent;

                        if (success) {
                            if (action === 'delete') {
                                const row = document.querySelector(`tr[data-job-id="${jobIdResponse}"]`);
                                if (row) {
                                    row.remove();
                                    showNotification(message || 'Job deleted successfully');
                                }
                            } else if (action === 'toggle_status') {
                                const statusBadge = document.querySelector(`tr[data-job-id="${jobIdResponse}"] .status-badge`);
                                const badgeClass = xmlDoc.getElementsByTagName('badge_class')[0]?.textContent;
                                const statusText = xmlDoc.getElementsByTagName('status_text')[0]?.textContent;
                                if (statusBadge && badgeClass && statusText) {
                                    statusBadge.className = `badge bg-${badgeClass} status-badge`;
                                    statusBadge.textContent = statusText;
                                    showNotification(message || 'Job status updated successfully');
                                }
                            }
                        } else {
                            showNotification(message, 'danger');
                        }
                    })
                    .catch(error => {
                        showNotification('An error occurred while processing your request', 'danger');
                        console.error('Error:', error);
                    });
                }
            });
        });
        
        // Handle add job form
        document.getElementById('addJobForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'create_job');
            
            // Use fetch API for AJAX request
            fetch('jobs.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.text(); // Get text first
            })
            .then(str => (new window.DOMParser()).parseFromString(str, "text/xml")) // Parse as XML
            .then(xmlDoc => {
                 if (!xmlDoc || xmlDoc.getElementsByTagName('parsererror').length > 0) {
                    showNotification('Error parsing XML response', 'danger');
                    console.error("XML Parsing Error:", xmlDoc ? xmlDoc.getElementsByTagName('parsererror')[0] : 'No XML Document');
                    return;
                }

                const success = xmlDoc.getElementsByTagName('success')[0]?.textContent === 'true';
                const message = xmlDoc.getElementsByTagName('message')[0]?.textContent || 'An unknown error occurred.';

                if (success) {
                    const jobNode = xmlDoc.getElementsByTagName('job')[0];
                    if (jobNode) {
                        const job = {
                            job_id: jobNode.getElementsByTagName('job_id')[0]?.textContent,
                            title: jobNode.getElementsByTagName('title')[0]?.textContent,
                            company_name: jobNode.getElementsByTagName('company_name')[0]?.textContent,
                            location: jobNode.getElementsByTagName('location')[0]?.textContent,
                            salary: jobNode.getElementsByTagName('salary')[0]?.textContent,
                            created_at: jobNode.getElementsByTagName('created_at')[0]?.textContent,
                            application_count: jobNode.getElementsByTagName('application_count')[0]?.textContent,
                            badge_class: jobNode.getElementsByTagName('badge_class')[0]?.textContent,
                            status_text: jobNode.getElementsByTagName('status_text')[0]?.textContent
                        };

                        let tbody = document.querySelector('table tbody');
                        const noJobsMessage = document.querySelector('.table-responsive .text-center.py-4');
                        if (!tbody && noJobsMessage) {
                             // Remove "No jobs found" message and create table structure
                             const tableDiv = document.querySelector('.table-responsive');
                             noJobsMessage.remove();
                             if (tableDiv) {
                                tableDiv.innerHTML = `
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th class="col-id">ID</th> <th class="col-title">Title</th> <th class="col-company">Company</th>
                                                <th class="col-location">Location</th> <th class="col-salary">Salary</th> <th class="col-apps">Applications</th>
                                                <th class="col-status">Status</th> <th class="col-date">Posted</th> <th class="col-actions">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody></tbody>
                                    </table>
                                `;
                                tbody = document.querySelector('table tbody');
                             }
                        }

                        if (tbody) {
                            const createdDate = job.created_at ? new Date(job.created_at) : null;
                            const formattedDate = createdDate ? createdDate.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) : 'N/A';
                            const formattedSalary = job.salary ? `$${parseFloat(job.salary).toFixed(2)}` : 'N/A';

                            const newRow = document.createElement('tr');
                            newRow.setAttribute('data-job-id', job.job_id || '');
                            newRow.innerHTML = `
                                <td>${job.job_id || 'N/A'}</td>
                                <td><div><strong>${job.title || 'N/A'}</strong></div></td>
                                <td>${job.company_name || 'N/A'}</td>
                                <td>${job.location || 'N/A'}</td>
                                <td>${formattedSalary}</td>
                                <td>${job.application_count || '0'}</td>
                                <td>
                                    <span class="badge bg-${job.badge_class || 'secondary'} status-badge">
                                        ${job.status_text || 'Unknown'}
                                    </span>
                                </td>
                                <td>${formattedDate}</td>
                                <td>
                                    <div class="actions-group">
                                        <button type="button" class="btn btn-sm btn-info"
                                                onclick="viewJobDetails(${job.job_id})"
                                                title="View Details">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        
                                        <form method="POST" class="d-inline ajax-form">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="job_id" value="${job.job_id}">
                                            <button type="submit" class="btn btn-sm btn-warning" title="Toggle Status">
                                                <i class="bi bi-toggle-on"></i>
                                            </button>
                                        </form>
                                        
                                        <form method="POST" class="d-inline ajax-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="job_id" value="${job.job_id}">
                                            <button type="submit" class="btn btn-sm btn-danger" title="Delete Job">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(newRow); // Add to top of table
                            
                            // Re-initialize AJAX for the new forms on this row
                            newRow.querySelectorAll('form.ajax-form').forEach(form => {
                                initializeSingleAjaxForm(form); // Use helper or repeat logic
                            });

                            // Reset form and close modal
                            this.reset();
                            const addJobModalEl = document.getElementById('addJobModal');
                            if (addJobModalEl) {
                                const modal = bootstrap.Modal.getInstance(addJobModalEl);
                                if (modal) modal.hide();
                            }
                            
                            showNotification(message || 'Job created successfully');
    // Helper function to initialize a single form (to avoid re-initializing all)
    function initializeSingleAjaxForm(form) {
         form.addEventListener('submit', function(e) {
            e.preventDefault();
            const action = this.querySelector('[name="action"]').value;
            const jobId = this.querySelector('[name="job_id"]').value;
            let confirmed = false;
            if (action === 'toggle_status') confirmed = confirm('Are you sure you want to toggle this job\'s status?');
            else if (action === 'delete') confirmed = confirm('Are you sure you want to delete this job? This action cannot be undone.');
            
            if (confirmed) {
                const formData = new FormData(this);
                fetch('jobs.php', {
                    method: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                 })
                .then(response => {
                    if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                    return response.text();
                })
                .then(str => (new window.DOMParser()).parseFromString(str, "text/xml"))
                .then(xmlDoc => { /* ... rest of XML handling logic as in initializeAjaxForms ... */
                    if (!xmlDoc || xmlDoc.getElementsByTagName('parsererror').length > 0) {
                        showNotification('Error parsing XML response', 'danger'); return;
                    }
                    const success = xmlDoc.getElementsByTagName('success')[0]?.textContent === 'true';
                    const message = xmlDoc.getElementsByTagName('message')[0]?.textContent || 'An unknown error occurred.';
                    const jobIdResponse = xmlDoc.getElementsByTagName('job_id')[0]?.textContent;
                    if (success) {
                        if (action === 'delete') {
                            const row = document.querySelector(`tr[data-job-id="${jobIdResponse}"]`);
                            if (row) { row.remove(); showNotification(message || 'Job deleted successfully'); }
                        } else if (action === 'toggle_status') {
                            const statusBadge = document.querySelector(`tr[data-job-id="${jobIdResponse}"] .status-badge`);
                            const badgeClass = xmlDoc.getElementsByTagName('badge_class')[0]?.textContent;
                            const statusText = xmlDoc.getElementsByTagName('status_text')[0]?.textContent;
                            if (statusBadge && badgeClass && statusText) {
                                statusBadge.className = `badge bg-${badgeClass} status-badge`;
                                statusBadge.textContent = statusText;
                                showNotification(message || 'Job status updated successfully');
                            }
                        }
                    } else { showNotification(message, 'danger'); }
                })
                .catch(error => { showNotification('AJAX Error: ' + error.message, 'danger'); });
            }
        });
    }
                        } else {
                             showNotification('Could not find table body to add new job.', 'danger');
                        }
                    } else {
                         showNotification('Job data not found in response.', 'danger');
                    }
                } else {
                    showNotification(message, 'danger');
                }
            })
            .catch(error => {
                showNotification('An error occurred while processing your request: ' + error.message, 'danger');
                console.error('Error:', error);
            });
        });
    }
    
    function viewJobDetails(jobId) {
        const modal = new bootstrap.Modal(document.getElementById('jobModal'));
        const detailsDiv = document.getElementById('jobDetails');
        
        // Display loading indicator
        detailsDiv.innerHTML = `
            <div class="text-center">
                <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
            </div>
        `;
        
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
                            <h5>Job Description</h5>
                            <p>${data.description}</p>
                        </div>
                        
                        <div class="mb-4">
                            <h5>Recent Applications</h5>
                            ${data.recent_applications && data.recent_applications.length ? `
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
    
    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', initializeAjaxForms);
    </script>
</body>
</html>