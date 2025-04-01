<?php
require_once '../config.php';
require_once 'auth.php';
requireAdmin();

// Fetch overall statistics
$stats = [
    'total_users' => $pdo->query("SELECT COUNT(*) as count FROM users")->fetch(PDO::FETCH_ASSOC)['count'],
    'total_employers' => $pdo->query("SELECT COUNT(*) as count FROM employers")->fetch(PDO::FETCH_ASSOC)['count'],
    'total_jobs' => $pdo->query("SELECT COUNT(*) as count FROM jobs")->fetch(PDO::FETCH_ASSOC)['count'],
    'total_applications' => $pdo->query("SELECT COUNT(*) as count FROM applications")->fetch(PDO::FETCH_ASSOC)['count'],
    'active_jobs' => $pdo->query("SELECT COUNT(*) as count FROM jobs WHERE status = 'active'")->fetch(PDO::FETCH_ASSOC)['count']
];

// Fetch user growth data
$user_growth = $pdo->query("
    SELECT DATE(created_at) as date, COUNT(*) as count
    FROM users
    GROUP BY DATE(created_at)
    ORDER BY date ASC
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch application activity data
$application_activity = $pdo->query("
    SELECT DATE(applied_at) as date, COUNT(*) as count
    FROM applications
    GROUP BY DATE(applied_at)
    ORDER BY date DESC
    LIMIT 30
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch top employers by job count
$top_employers = $pdo->query("
    SELECT e.company_name, COUNT(j.job_id) as job_count
    FROM employers e
    LEFT JOIN jobs j ON e.employer_id = j.employer_id
    GROUP BY e.employer_id
    ORDER BY job_count DESC
    LIMIT 5
")->fetchAll(PDO::FETCH_ASSOC);

// Fetch application status distribution
$application_status = $pdo->query("
    SELECT status, COUNT(*) as count
    FROM applications
    GROUP BY status
")->fetchAll(PDO::FETCH_ASSOC);

// Convert query results to arrays for JavaScript
$user_growth_data = array_map(function($row) {
    return [
        'date' => $row['date'],
        'count' => (int)$row['count']
    ];
}, $user_growth);

$application_activity_data = array_map(function($row) {
    return [
        'date' => $row['date'],
        'count' => (int)$row['count']
    ];
}, $application_activity);

$top_employers_data = array_map(function($row) {
    return [
        'company' => $row['company_name'],
        'count' => (int)$row['job_count']
    ];
}, $top_employers);

$application_status_data = array_map(function($row) {
    return [
        'status' => $row['status'],
        'count' => (int)$row['count']
    ];
}, $application_status);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Analytics - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .chart-container {
            position: relative;
            height: 300px;
            margin-bottom: 2rem;
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
                        <a class="nav-link" href="jobs.php">
                            <i class="bi bi-briefcase"></i> Jobs
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="analytics.php">
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
                <h2 class="mb-4">Analytics Dashboard</h2>

                <!-- Key Metrics -->
                <div class="row mb-4">
                    <div class="col-md-3">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Users</h5>
                                <h2><?php echo $stats['total_users']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Employers</h5>
                                <h2><?php echo $stats['total_employers']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5 class="card-title">Active Jobs</h5>
                                <h2><?php echo $stats['active_jobs']; ?></h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5 class="card-title">Total Applications</h5>
                                <h2><?php echo $stats['total_applications']; ?></h2>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">User Growth</h5>
                                <div class="chart-container">
                                    <canvas id="userGrowthChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Application Activity (Last 30 Days)</h5>
                                <div class="chart-container">
                                    <canvas id="applicationActivityChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Top Employers by Job Count</h5>
                                <div class="chart-container">
                                    <canvas id="topEmployersChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="card-title">Application Status Distribution</h5>
                                <div class="chart-container">
                                    <canvas id="applicationStatusChart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // User Growth Chart
        new Chart(document.getElementById('userGrowthChart'), {
            type: 'line',
            data: {
                labels: <?php echo json_encode(array_column($user_growth_data, 'date')); ?>,
                datasets: [{
                    label: 'New Users',
                    data: <?php echo json_encode(array_column($user_growth_data, 'count')); ?>,
                    borderColor: 'rgb(75, 192, 192)',
                    tension: 0.1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Application Activity Chart
        new Chart(document.getElementById('applicationActivityChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($application_activity_data, 'date')); ?>,
                datasets: [{
                    label: 'Applications',
                    data: <?php echo json_encode(array_column($application_activity_data, 'count')); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.5)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });

        // Top Employers Chart
        new Chart(document.getElementById('topEmployersChart'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode(array_column($top_employers_data, 'company')); ?>,
                datasets: [{
                    label: 'Job Count',
                    data: <?php echo json_encode(array_column($top_employers_data, 'count')); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y'
            }
        });

        // Application Status Chart
        new Chart(document.getElementById('applicationStatusChart'), {
            type: 'doughnut',
            data: {
                labels: <?php echo json_encode(array_column($application_status_data, 'status')); ?>,
                datasets: [{
                    data: <?php echo json_encode(array_column($application_status_data, 'count')); ?>,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.5)',
                        'rgba(54, 162, 235, 0.5)',
                        'rgba(255, 206, 86, 0.5)',
                        'rgba(75, 192, 192, 0.5)'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false
            }
        });
    </script>
</body>
</html> 