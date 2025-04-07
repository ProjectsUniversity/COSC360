<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $response = ['success' => false, 'message' => 'Invalid action'];

    switch ($action) {
        case 'create_user':
            // Validate input
            $required_fields = ['full_name', 'email', 'password'];
            foreach ($required_fields as $field) {
                if (empty($_POST[$field])) {
                    $response = ['success' => false, 'message' => 'All required fields must be filled out'];
                    header('Content-Type: application/json');
                    echo json_encode($response);
                    exit();
                }
            }
            
            // Check if email already exists
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            if ($stmt->fetchColumn() > 0) {
                $response = ['success' => false, 'message' => 'Email already exists'];
                header('Content-Type: application/json');
                echo json_encode($response);
                exit();
            }
            
            try {
                // Create new user
                $stmt = $pdo->prepare("INSERT INTO users (full_name, email, password_hash, location, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([
                    $_POST['full_name'],
                    $_POST['email'],
                    password_hash($_POST['password'], PASSWORD_DEFAULT),
                    $_POST['location'] ?? '',
                    $_POST['status'] ?? 'active'
                ]);
                
                $userId = $pdo->lastInsertId();
                
                // Get the created user
                $stmt = $pdo->prepare("SELECT u.*, 
                    (SELECT COUNT(*) FROM applications a WHERE a.user_id = u.user_id) as application_count 
                    FROM users u WHERE u.user_id = ?");
                $stmt->execute([$userId]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);
                
                logAdminAction('create_user', "Created new user: {$user['full_name']} (ID: $userId)");
                
                $response = [
                    'success' => true, 
                    'message' => 'User created successfully',
                    'user' => [
                        'user_id' => $user['user_id'],
                        'full_name' => $user['full_name'],
                        'email' => $user['email'],
                        'created_at' => $user['created_at'],
                        'application_count' => $user['application_count'],
                        'status' => $user['status'],
                        'status_badge' => $user['status'] === 'active' ? 'bg-success' : 'bg-danger',
                        'status_text' => ucfirst($user['status'])
                    ]
                ];
            } catch (PDOException $e) {
                $response = ['success' => false, 'message' => 'Error creating user: ' . $e->getMessage()];
            }
            break;
        case 'delete':
            if ($user_id) {
                try {
                    $pdo->beginTransaction();
                    
                    // First delete related records from applications table
                    $stmt = $pdo->prepare("DELETE FROM applications WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Then delete related records from saved_jobs table
                    $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Finally delete the user
                    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    $pdo->commit();
                    logAdminAction('delete_user', "Deleted user ID: $user_id");
                    $response = ['success' => true, 'message' => 'User deleted successfully', 'user_id' => $user_id];
                } catch (PDOException $e) {
                    $pdo->rollBack();
                    $response = ['success' => false, 'message' => 'Failed to delete user: ' . $e->getMessage()];
                }
            }
            break;
        case 'toggle_status':
            if ($user_id) {
                $stmt = $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE user_id = ?");
                if ($stmt->execute([$user_id])) {
                    // Get the new status
                    $stmt = $pdo->prepare("SELECT status FROM users WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    $new_status = $stmt->fetchColumn();
                    
                    logAdminAction('toggle_user_status', "Toggled status for user ID: $user_id");
                    $response = [
                        'success' => true, 
                        'message' => 'User status updated successfully', 
                        'user_id' => $user_id,
                        'new_status' => $new_status,
                        'badge_class' => $new_status === 'active' ? 'bg-success' : 'bg-danger',
                        'status_text' => ucfirst($new_status)
                    ];
                } else {
                    $response = ['success' => false, 'message' => 'Failed to update user status'];
                }
            }
            break;
    }

    header('Content-Type: application/json');
    echo json_encode($response);
    exit();
}

// Handle regular POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    switch ($action) {
        case 'delete':
            if ($user_id) {
                try {
                    $pdo->beginTransaction();
                    
                    // First delete related records from applications table
                    $stmt = $pdo->prepare("DELETE FROM applications WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Then delete related records from saved_jobs table
                    $stmt = $pdo->prepare("DELETE FROM saved_jobs WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    // Finally delete the user
                    $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                    $stmt->execute([$user_id]);
                    
                    $pdo->commit();
                    logAdminAction('delete_user', "Deleted user ID: $user_id");
                } catch (PDOException $e) {
                    $pdo->rollBack();
                }
            }
            break;
        case 'toggle_status':
            if ($user_id) {
                $stmt = $pdo->prepare("UPDATE users SET status = CASE WHEN status = 'active' THEN 'inactive' ELSE 'active' END WHERE user_id = ?");
                $stmt->execute([$user_id]);
                logAdminAction('toggle_user_status', "Toggled status for user ID: $user_id");
            }
            break;
    }
    header('Location: users.php');
    exit();
}

// Build query with filters
$where = [];
$params = [];
$types = '';

if (isset($_GET['search']) && !empty($_GET['search'])) {
    $where[] = "(full_name LIKE ? OR email LIKE ?)";
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

$query = "SELECT u.*, 
          (SELECT COUNT(*) FROM applications a WHERE a.user_id = u.user_id) as application_count 
          FROM users u";

if (!empty($where)) {
    $query .= " WHERE " . implode(" AND ", $where);
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management - Admin Dashboard</title>
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
        .col-name { width: 20%; }
        .col-email { width: 25%; }
        .col-date { width: 15%; }
        .col-apps { width: 10%; }
        .col-status { width: 10%; }
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
                        <a class="nav-link active" href="users.php">
                            <i class="bi bi-people"></i> Users
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="employers.php">
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
                    <h2>User Management</h2>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus"></i> Add User
                    </button>
                </div>

                <!-- Filters -->
                <div class="card mb-4">
                    <div class="card-body">
                        <form method="GET" class="row g-3">
                            <div class="col-md-6">
                                <input type="text" class="form-control" name="search" 
                                       placeholder="Search by name or email" 
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

                <!-- Users Table -->
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead">
                                    <tr>
                                        <th class="col-id">ID</th>
                                        <th class="col-name">Name</th>
                                        <th class="col-email">Email</th>
                                        <th class="col-date">Created</th>
                                        <th class="col-apps">Applications</th>
                                        <th class="col-status">Status</th>
                                        <th class="col-actions">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users as $user): ?>
                                    <tr data-user-id="<?php echo $user['user_id']; ?>">
                                        <td><?php echo $user['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td><?php echo $user['application_count']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?> status-badge">
                                                <?php echo ucfirst(htmlspecialchars($user['status'] ?? 'unknown')); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info me-2" 
                                                        onclick="viewDetails(<?php echo $user['user_id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to toggle this user\'s status?');">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning me-2">
                                                        <i class="bi bi-toggle-on"></i>
                                                    </button>
                                                </form>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
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

    <!-- Resume Modal -->
    <div class="modal fade" id="resumeModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">User Resume</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <iframe id="resumeFrame" width="100%" height="600px" frameborder="0"></iframe>
                </div>
            </div>
        </div>
    </div>

    <!-- Add User Modal -->
    <div class="modal fade" id="addUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addUserForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="full_name" required>
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
                        <button type="submit" class="btn btn-primary">Add User</button>
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
            mainContent.insertBefore(alertDiv, mainContent.firstChild);
            
            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }, 3000);
        }
        
        // Function to initialize AJAX for forms
        function initializeAjaxForms() {
            // Handle toggle status forms
            document.querySelectorAll('form[action=""][method="POST"]').forEach(form => {
                form.addEventListener('submit', function(e) {
                    const action = this.querySelector('[name="action"]').value;
                    if (action === 'toggle_status' || action === 'delete') {
                        e.preventDefault();
                        
                        // Get confirmation
                        let confirmed = false;
                        if (action === 'toggle_status') {
                            confirmed = confirm('Are you sure you want to toggle this user\'s status?');
                        } else if (action === 'delete') {
                            confirmed = confirm('Are you sure you want to delete this user? This action cannot be undone.');
                        }
                        
                        if (confirmed) {
                            const formData = new FormData(this);
                            const userId = formData.get('user_id');
                            
                            // Add AJAX header
                            const xhr = new XMLHttpRequest();
                            xhr.open('POST', 'users.php');
                            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                            xhr.responseType = 'json';
                            
                            xhr.onload = function() {
                                if (xhr.status === 200) {
                                    const response = xhr.response;
                                    
                                    if (response.success) {
                                        if (action === 'delete') {
                                            // Remove the row from the table
                                            const row = document.querySelector(`tr[data-user-id="${response.user_id}"]`);
                                            if (row) {
                                                row.remove();
                                            }
                                            showNotification('User deleted successfully');
                                        } else if (action === 'toggle_status') {
                                            // Update the status badge
                                            const statusBadge = document.querySelector(`tr[data-user-id="${response.user_id}"] .status-badge`);
                                            if (statusBadge) {
                                                statusBadge.className = `badge ${response.badge_class} status-badge`;
                                                statusBadge.textContent = response.status_text;
                                            }
                                            showNotification('User status updated successfully');
                                        }
                                    } else {
                                        showNotification(response.message, 'danger');
                                    }
                                } else {
                                    showNotification('An error occurred', 'danger');
                                }
                            };
                            
                            xhr.onerror = function() {
                                showNotification('Network error occurred', 'danger');
                            };
                            
                            xhr.send(formData);
                        }
                    }
                });
            });

            // Handle add user form
            document.getElementById('addUserForm').addEventListener('submit', function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                formData.append('action', 'create_user');
                
                // Add AJAX header
                const xhr = new XMLHttpRequest();
                xhr.open('POST', 'users.php');
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.responseType = 'json';
                
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        const response = xhr.response;
                        
                        if (response.success) {
                            // Add the new user to the table
                            const user = response.user;
                            const tbody = document.querySelector('table tbody');
                            const newRow = document.createElement('tr');
                            newRow.setAttribute('data-user-id', user.user_id);
                            newRow.innerHTML = `
                                <td>${user.user_id}</td>
                                <td>${user.full_name}</td>
                                <td>${user.email}</td>
                                <td>${new Date(user.created_at).toLocaleDateString()}</td>
                                <td>${user.application_count}</td>
                                <td>
                                    <span class="badge ${user.status_badge} status-badge">
                                        ${user.status_text}
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        <button type="button" class="btn btn-sm btn-info me-2" 
                                                onclick="viewDetails(${user.user_id})">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to toggle this user\'s status?');">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="user_id" value="${user.user_id}">
                                            <button type="submit" class="btn btn-sm btn-warning me-2">
                                                <i class="bi bi-toggle-on"></i>
                                            </button>
                                        </form>
                                        <form method="POST" class="d-inline" 
                                              onsubmit="return confirm('Are you sure you want to delete this user? This action cannot be undone.');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="user_id" value="${user.user_id}">
                                            <button type="submit" class="btn btn-sm btn-danger">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            `;
                            tbody.appendChild(newRow);
                            
                            // Reset the form
                            document.getElementById('addUserForm').reset();
                            const addUserModal = bootstrap.Modal.getInstance(document.getElementById('addUserModal'));
                            addUserModal.hide();
                            
                            showNotification('User created successfully');
                        } else {
                            showNotification(response.message, 'danger');
                        }
                    } else {
                        showNotification('An error occurred', 'danger');
                    }
                };
                
                xhr.onerror = function() {
                    showNotification('Network error occurred', 'danger');
                };
                
                xhr.send(formData);
            });
        }
        
        function viewResume(userId) {
            const modal = new bootstrap.Modal(document.getElementById('resumeModal'));
            const frame = document.getElementById('resumeFrame');
            frame.src = `view-resume.php?user_id=${userId}`;
            modal.show();
        }
        
        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', initializeAjaxForms);
    </script>
</body>
</html>