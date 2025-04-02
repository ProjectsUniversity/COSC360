<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

// Handle user actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $user_id = $_POST['user_id'] ?? '';

    switch ($action) {
        case 'delete':
            if ($user_id) {
                $stmt = $pdo->prepare("DELETE FROM users WHERE user_id = ?");
                $stmt->execute([$user_id]);
                logAdminAction('delete_user', "Deleted user ID: $user_id");
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
        .col-name { width: 20%; }
        .col-email { width: 25%; }
        .col-date { width: 15%; }
        .col-apps { width: 10%; }
        .col-status { width: 10%; }
        .col-actions { width: 15%; }
        
        /* Ensure table takes full width */
        .table-responsive table {
            width: 100%;
            table-layout: fixed;
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
                    <h2>User Management</h2>
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
                            <table class="table table-hover ">
                                <thead>
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
                                    <tr>
                                        <td><?php echo $user['user_id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                                        <td><?php echo $user['application_count']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['status'] === 'active' ? 'success' : 'danger'; ?>">
                                                <?php echo ucfirst($user['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-info" 
                                                        onclick="viewDetails(<?php echo $user['user_id']; ?>)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <form method="POST" class="d-inline" 
                                                      onsubmit="return confirm('Are you sure you want to toggle this user\'s status?');">
                                                    <input type="hidden" name="action" value="toggle_status">
                                                    <input type="hidden" name="user_id" value="<?php echo $user['user_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-warning">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function viewResume(userId) {
            const modal = new bootstrap.Modal(document.getElementById('resumeModal'));
            const frame = document.getElementById('resumeFrame');
            frame.src = `view-resume.php?user_id=${userId}`;
            modal.show();
        }
    </script>
</body>
</html>