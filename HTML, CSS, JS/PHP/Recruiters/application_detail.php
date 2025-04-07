<?php
session_start();
require_once '../config.php'; // Adjust relative path as needed

// Ensure recruiter is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

// Check for application_id in GET parameters
if (!isset($_GET['application_id'])) {
    header("Location: dashboard.php");
    exit();
}

$application_id = $_GET['application_id'];
$employer_id = $_SESSION['employer_id'];

// Fetch application details ensuring the application belongs to a job posted by the recruiter
$sql = "SELECT a.*, j.title AS job_title, j.job_id, u.full_name, u.email
        FROM applications a
        JOIN jobs j ON a.job_id = j.job_id
        JOIN users u ON a.user_id = u.user_id
        WHERE a.application_id = :application_id
          AND j.employer_id = :employer_id";
$stmt = $pdo->prepare($sql);
$stmt->execute([':application_id' => $application_id, ':employer_id' => $employer_id]);
$application = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$application) {
    die("Application not found or you do not have permission to view this application.");
}

$status = $application['status'] ?? 'pending';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Application Details for <?php echo htmlspecialchars($application['job_title']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .status-tag {
            font-weight: bold;
            padding: 0.2em 0.5em;
            border-radius: 0.3em;
            color: #fff;
        }
        .status-hired { background-color: green; }
        .status-shortlisted { background-color: orange; }
        .status-pending { background-color: gray; }
        .status-rejected { background-color: red; }
    </style>
</head>
<body class="p-3">
    <div id="message"></div>
    <h1>Application Details</h1>
    <a href="job_applicants.php?job_id=<?php echo $application['job_id']; ?>" class="btn btn-secondary mb-3">Back to Applicants</a>
    
    <div class="card mb-3">
        <div class="card-header">
            <strong><?php echo htmlspecialchars($application['full_name']); ?></strong> – <?php echo htmlspecialchars($application['email']); ?>
        </div>
        <div class="card-body">
            <h5 class="card-title">Job: <?php echo htmlspecialchars($application['job_title']); ?></h5>
            <p class="card-text"><strong>Cover Letter:</strong><br>
                <?php echo nl2br(htmlspecialchars($application['cover_letter'])); ?>
            </p>
            <?php if (!empty($application['resume'])): ?>
                <p class="card-text">
                    <strong>Resume:</strong> 
                    <a href="<?php echo htmlspecialchars($application['resume']); ?>" download>Download Resume</a>
                </p>
            <?php endif; ?>
            <p class="card-text">
                <strong>Status:</strong>
                <span id="detail-status" class="status-tag <?php
                    if ($status === 'hired') echo 'status-hired';
                    elseif ($status === 'shortlisted') echo 'status-shortlisted';
                    elseif ($status === 'rejected') echo 'status-rejected';
                    else echo 'status-pending';
                ?>"><?php echo ucfirst($status); ?></span>
            </p>
            <p class="card-text"><strong>Applied At:</strong> <?php echo htmlspecialchars($application['applied_at']); ?></p>
        </div>
    </div>
    
    <!-- Action buttons with AJAX update -->
    <div>
        <form class="ajax-detail-status" data-app-id="<?php echo $application['application_id']; ?>" data-new-status="hired" style="display:inline-block;">
            <button type="submit" class="btn btn-success">Hire</button>
        </form>
        <form class="ajax-detail-status" data-app-id="<?php echo $application['application_id']; ?>" data-new-status="shortlisted" style="display:inline-block;">
            <button type="submit" class="btn btn-warning">Shortlist</button>
        </form>
        <form class="ajax-detail-status" data-app-id="<?php echo $application['application_id']; ?>" data-new-status="rejected" style="display:inline-block;">
            <button type="submit" class="btn btn-danger">Reject</button>
        </form>
    </div>
    
    <script>
    document.addEventListener("DOMContentLoaded", function(){
        document.querySelectorAll(".ajax-detail-status").forEach(function(form){
            form.addEventListener("submit", function(e){
                e.preventDefault();
                let newStatus = form.getAttribute("data-new-status");
                if(!confirm("Are you sure you want to change the status to '" + newStatus + "'?")) {
                    return;
                }
                let applicationId = form.getAttribute("data-app-id");
                let formData = new FormData();
                formData.append("application_id", applicationId);
                formData.append("new_status", newStatus);
                
                fetch("update_application_status.php", {
                    method: "POST",
                    body: formData,
                    headers: { "X-Requested-With": "XMLHttpRequest" }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok. Status: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    let messageDiv = document.getElementById("message");
                    if(data.error) {
                        messageDiv.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                    } else if(data.success) {
                        messageDiv.innerHTML = '<div class="alert alert-success">Status updated to ' + data.new_status + '.</div>';
                        let detailStatus = document.getElementById("detail-status");
                        let newStatusText = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);
                        let newClass = "status-tag ";
                        if(data.new_status === "hired") newClass += "status-hired";
                        else if(data.new_status === "shortlisted") newClass += "status-shortlisted";
                        else if(data.new_status === "rejected") newClass += "status-rejected";
                        else newClass += "status-pending";
                        detailStatus.innerHTML = newStatusText;
                        detailStatus.className = newClass;
                    }
                })
                .catch(error => {
                    console.error("Error:", error);
                    document.getElementById("message").innerHTML = '<div class="alert alert-danger">An error occurred: ' + error.message + '</div>';
                });
            });
        });
    });
    </script>
</body>
</html>
