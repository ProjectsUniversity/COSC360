<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/xml; charset=utf-8');
    $action = $_POST['action'] ?? '';
    $employer_id = $_POST['employer_id'] ?? '';
    
    // Start XML output
    $xml = new SimpleXMLElement('<response/>');

    switch ($action) {
        case 'create_employer':
            // Validate input
            $required_fields = ['company_name', 'email', 'password'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $xml->addChild('success', 'false');
                    $xml->addChild('message', 'All required fields must be filled out');
                    echo $xml->asXML();
                    exit();
                }
            }
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM employers WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            if ($stmt->fetchColumn() > 0) {
                $xml->addChild('success', 'false');
                $xml->addChild('message', 'Email already exists');
                echo $xml->asXML();
                exit();
            }
            
            try {
                // Create new employer
                $stmt = $pdo->prepare("INSERT INTO employers (company_name, email, password_hash, location, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['company_name'],
                    $_POST['email'],
                    password_hash($_POST['password'], PASSWORD_DEFAULT),
                    $_POST['location'] ?? '',
                    $_POST['status'] ?? 'active'
                ]);
                
                $employerId = $pdo->lastInsertId();
                
                // Get the created employer with statistics
                $stmt = $pdo->prepare("SELECT e.*, 
                                      (SELECT COUNT(*) FROM jobs j WHERE j.employer_id = e.employer_id) as total_jobs,
                                      (SELECT COUNT(*) FROM jobs j WHERE j.employer_id = e.employer_id AND j.status = 'active') as active_jobs,
                                      (SELECT COUNT(*) FROM applications a 
                                       JOIN jobs j ON a.job_id = j.job_id 
                                       WHERE j.employer_id = e.employer_id) as total_applications
                                      FROM employers e WHERE e.employer_id = ?");
                $stmt->execute([$employerId]);
                $employer = $stmt->fetch(PDO::FETCH_ASSOC);
                
                logAdminAction('create_employer', "Created new employer: {$employer['company_name']} (ID: $employerId)");
                
                $xml->addChild('success', 'true');
                $xml->addChild('message', 'Employer created successfully');
                $employerNode = $xml->addChild('employer');
                $employerNode->addChild('employer_id', $employer['employer_id']);
                $employerNode->addChild('company_name', htmlspecialchars($employer['company_name']));
                $employerNode->addChild('email', htmlspecialchars($employer['email']));
                $employerNode->addChild('location', htmlspecialchars($employer['location'] ?? ''));
                $employerNode->addChild('created_at', $employer['created_at']);
                $employerNode->addChild('total_jobs', $employer['total_jobs']);
                $employerNode->addChild('active_jobs', $employer['active_jobs']);
                $employerNode->addChild('total_applications', $employer['total_applications']);
                $employerNode->addChild('status', $employer['status']);
                $employerNode->addChild('status_badge', $employer['status'] === 'active' ? 'bg-success' : 'bg-danger');
                $employerNode->addChild('status_text', ucfirst($employer['status']));

            } catch (PDOException $e) {
                $xml->addChild('success', 'false');
                $xml->addChild('message', 'Error creating employer: ' . htmlspecialchars($e->getMessage()));
            }
            break;
        case 'delete':
            if ($employer_id) {
                $stmt = $pdo->prepare("DELETE FROM employers WHERE employer_id = ?");
                 // Note: Deleting an employer might require deleting related jobs/applications first
                 // For simplicity, this example directly deletes the employer. Add cascading deletes or checks if needed.
                if ($stmt->execute([$employer_id])) {
                    logAdminAction('delete_employer', "Deleted employer ID: $employer_id");
                    $xml->addChild('success', 'true');
                    $xml->addChild('message', 'Employer deleted successfully');
                    $xml->addChild('employer_id', $employer_id);
                } else {
                    $xml->addChild('success', 'false');
                    $xml->addChild('message', 'Failed to delete employer');
                }
            }
            break;
        case 'toggle_status':
            if ($employer_id) {
                $stmt = $pdo->prepare("UPDATE employers SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE employer_id = ?");
                if ($stmt->execute([$employer_id])) {
                    // Get the new status
                    $stmt = $pdo->prepare("SELECT status FROM employers WHERE employer_id = ?");
                    $stmt->execute([$employer_id]);
                    $new_status = $stmt->fetchColumn();
                    
                    logAdminAction('toggle_employer_status', "Toggled status for employer ID: $employer_id");
                    $xml->addChild('success', 'true');
                    $xml->addChild('message', 'Employer status updated successfully');
                    $xml->addChild('employer_id', $employer_id);
                    $xml->addChild('new_status', $new_status);
                    $xml->addChild('badge_class', $new_status === 'active' ? 'bg-success' : 'bg-danger');
                    $xml->addChild('status_text', ucfirst($new_status));
                } else {
                    $xml->addChild('success', 'false');
                    $xml->addChild('message', 'Failed to update employer status');
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
            height: 100vh;
            background: #343a40;
            color: white;
            position: fixed;
            display: flex;
            flex-direction: column;
            width: 16%;
            overflow-y: auto;
            z-index: 1000;
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
                    <h2>Employer Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployerModal">
                        <i class="bi bi-building-add"></i> Add Employer
                    </button>
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
                        <div class="table-responsive">
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
                                    <tr data-employer-id="<?php echo $employer['employer_id']; ?>">
                                        <td><?php echo $employer['employer_id']; ?></td>
                                        <td><?php echo htmlspecialchars($employer['company_name']); ?></td>
                                        <td><?php echo htmlspecialchars($employer['email']); ?></td>
                                        <td><?php echo htmlspecialchars($employer['location']); ?></td>
                                        <td><?php echo $employer['total_jobs']; ?></td>
                                        <td><?php echo $employer['active_jobs']; ?></td>
                                        <td><?php echo $employer['total_applications']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $employer['status'] === 'active' ? 'success' : 'danger'; ?> status-badge">
                                                <?php echo ucfirst(htmlspecialchars($employer['status'] ?? 'unknown')); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info me-2" 
                                                        onclick="viewDetails(<?php echo $employer['employer_id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <form method="POST" class="d-inline ajax-form">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="employer_id" value="<?php echo $employer['employer_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning me-2">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline ajax-form">
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
    
    <!-- Add Employer Modal -->
    <div class="modal fade" id="addEmployerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New Employer</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addEmployerForm">
                        <div class="mb-3">
                            <label for="companyName" class="form-label">Company Name</label>
                            <input type="text" class="form-control" id="companyName" name="company_name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="location" class="form-label">Location</label>
                            <input type="text" class="form-control" id="location" name="location">
                        </div>
                        <div class="mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Add Employer</button>
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

        // Initialize AJAX forms for employer actions
        function initializeAjaxForms() {
            // Handle existing forms (for toggle status and delete)
            document.querySelectorAll('form.ajax-form').forEach(form => {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const action = this.querySelector('[name="action"]').value;
                    const employerId = this.querySelector('[name="employer_id"]').value;
                    
                    // Get confirmation
                    let confirmed = false;
                    if (action === 'toggle_status') {
                        confirmed = confirm('Are you sure you want to toggle this employer\'s status?');
                    } else if (action === 'delete') {
                        confirmed = confirm('Are you sure you want to delete this employer? This action cannot be undone.');
                    }
                    
                    if (confirmed) {
                        const formData = new FormData(this);
                        
                        // Use fetch API for AJAX request
                        fetch('employers.php', {
                            method: 'POST',
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: formData
                        })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! status: ${response.status}`);
                            }
                            return response.text(); // Get response as text first
                        })
                        .then(str => (new window.DOMParser()).parseFromString(str, "text/xml")) // Parse text as XML
                        .then(xmlDoc => {
                            if (!xmlDoc || xmlDoc.getElementsByTagName('parsererror').length > 0) {
                                showNotification('Error parsing XML response', 'danger');
                                console.error("XML Parsing Error:", xmlDoc ? xmlDoc.getElementsByTagName('parsererror')[0] : 'No XML Document');
                                return;
                            }
                            
                            const success = xmlDoc.getElementsByTagName('success')[0]?.textContent === 'true';
                            const message = xmlDoc.getElementsByTagName('message')[0]?.textContent || 'An unknown error occurred.';
                            const employerIdResponse = xmlDoc.getElementsByTagName('employer_id')[0]?.textContent;

                            if (success) {
                                if (action === 'delete') {
                                    const row = document.querySelector(`tr[data-employer-id="${employerIdResponse}"]`);
                                    if (row) {
                                        row.remove();
                                        showNotification(message || 'Employer deleted successfully');
                                    }
                                } else if (action === 'toggle_status') {
                                    const statusBadge = document.querySelector(`tr[data-employer-id="${employerIdResponse}"] .status-badge`);
                                    const badgeClass = xmlDoc.getElementsByTagName('badge_class')[0]?.textContent;
                                    const statusText = xmlDoc.getElementsByTagName('status_text')[0]?.textContent;
                                    if (statusBadge && badgeClass && statusText) {
                                        statusBadge.className = `badge ${badgeClass} status-badge`;
                                        statusBadge.textContent = statusText;
                                        showNotification(message || 'Employer status updated successfully');
                                    }
                                }
                            } else {
                                showNotification(message, 'danger');
                            }
                        })
                        .catch(error => {
                            showNotification('An error occurred while processing your request', 'danger');
                        });
                    }
                });
            });
            
            // Handle add employer form
            document.getElementById('addEmployerForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'create_employer');
                
                // Use fetch API for AJAX request
                fetch('employers.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text(); // Get response as text first
                })
                .then(str => (new window.DOMParser()).parseFromString(str, "text/xml")) // Parse text as XML
                .then(xmlDoc => {
                     if (!xmlDoc || xmlDoc.getElementsByTagName('parsererror').length > 0) {
                        showNotification('Error parsing XML response', 'danger');
                        console.error("XML Parsing Error:", xmlDoc ? xmlDoc.getElementsByTagName('parsererror')[0] : 'No XML Document');
                        return;
                    }

                    const success = xmlDoc.getElementsByTagName('success')[0]?.textContent === 'true';
                    const message = xmlDoc.getElementsByTagName('message')[0]?.textContent || 'An unknown error occurred.';

                    if (success) {
                        const employerNode = xmlDoc.getElementsByTagName('employer')[0];
                        if (employerNode) {
                             const employer = {
                                employer_id: employerNode.getElementsByTagName('employer_id')[0]?.textContent,
                                company_name: employerNode.getElementsByTagName('company_name')[0]?.textContent,
                                email: employerNode.getElementsByTagName('email')[0]?.textContent,
                                location: employerNode.getElementsByTagName('location')[0]?.textContent,
                                total_jobs: employerNode.getElementsByTagName('total_jobs')[0]?.textContent,
                                active_jobs: employerNode.getElementsByTagName('active_jobs')[0]?.textContent,
                                total_applications: employerNode.getElementsByTagName('total_applications')[0]?.textContent,
                                status_badge: employerNode.getElementsByTagName('status_badge')[0]?.textContent,
                                status_text: employerNode.getElementsByTagName('status_text')[0]?.textContent
                            };

                            const tbody = document.querySelector('table tbody');
                            const newRow = document.createElement('tr');
                            newRow.setAttribute('data-employer-id', employer.employer_id);
                            newRow.innerHTML = `
                                <td>${employer.employer_id || 'N/A'}</td>
                                <td>${employer.company_name || 'N/A'}</td>
                                <td>${employer.email || 'N/A'}</td>
                                <td>${employer.location || ''}</td>
                                <td>${employer.total_jobs || '0'}</td>
                                <td>${employer.active_jobs || '0'}</td>
                                <td>${employer.total_applications || '0'}</td>
                                <td>
                                    <span class="badge ${employer.status_badge || 'bg-secondary'} status-badge">
                                        ${employer.status_text || 'Unknown'}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info me-2"
                                                onclick="viewDetails(${employer.employer_id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" class="d-inline ajax-form">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="employer_id" value="${employer.employer_id}">
                                            <button type="submit" class="btn btn-sm btn-warning me-2">
                                                <i class="bi bi-toggle-on"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline ajax-form">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="employer_id" value="${employer.employer_id}">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(newRow); // Add at the top
                            
                            // Re-attach event listeners to the new forms
                            newRow.querySelectorAll('form.ajax-form').forEach(form => {
                                initializeSingleAjaxForm(form); // Assumes helper function or repeat logic
                            });

                            // Reset the form and close the modal
                            this.reset();
                            const addEmployerModalEl = document.getElementById('addEmployerModal');
                             if (addEmployerModalEl) {
                                 const modal = bootstrap.Modal.getInstance(addEmployerModalEl);
                                 if (modal) {
                                     modal.hide();
                                 }
                             }
                            
                            showNotification(message || 'Employer created successfully');
                        } else {
                             showNotification('Employer data not found in response.', 'danger');
                        }
                    } else {
                        showNotification(message, 'danger');
                    }
                })
                .catch(error => {
                    showNotification('An error occurred while processing your request', 'danger');
                    console.error('Error:', error);
                });
            });
        }
        
        // Helper function to initialize a single form (to avoid re-initializing all)
        function initializeSingleAjaxForm(form) {
             form.addEventListener('submit', function(e) {
                e.preventDefault();
                const action = this.querySelector('[name="action"]').value;
                const employerId = this.querySelector('[name="employer_id"]').value;
                let confirmed = false;
                if (action === 'toggle_status') confirmed = confirm('Are you sure you want to toggle this employer\'s status?');
                else if (action === 'delete') confirmed = confirm('Are you sure you want to delete this employer? This action cannot be undone.');
                
                if (confirmed) {
                    const formData = new FormData(this);
                    fetch('employers.php', { /* ... rest of fetch logic as above ... */ })
                    .then(response => response.text())
                    .then(str => (new window.DOMParser()).parseFromString(str, "text/xml"))
                    .then(xmlDoc => { /* ... rest of XML handling logic ... */ });
                }
            });
        }

        function viewDetails(employerId) {
            const modal = new bootstrap.Modal(document.getElementById('employerModal'));
            const detailsDiv = document.getElementById('employerDetails');
            
            // Display loading indicator
            detailsDiv.innerHTML = '<div class="text-center"><div class="spinner-border" role="status"></div><p>Loading...</p></div>';
            
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
                                <p><strong>Average Applications per Job:</strong> ${(data.total_jobs > 0 ? (data.total_applications / data.total_jobs).toFixed(1) : 0)}</p>
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
                                        ${data.recent_jobs && data.recent_jobs.length > 0 ? 
                                            data.recent_jobs.map(job => `
                                                <tr>
                                                    <td>${job.title}</td>
                                                    <td><span class="badge bg-${job.status === 'active' ? 'success' : 'danger'}">${job.status}</span></td>
                                                    <td>${job.application_count}</td>
                                                </tr>
                                            `).join('') : 
                                            '<tr><td colspan="3" class="text-center">No jobs found</td></tr>'
                                        }
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

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', initializeAjaxForms);
    </script>
</body>
</html>