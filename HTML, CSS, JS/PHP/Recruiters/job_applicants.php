<?php
session_start();
require_once '../config.php'; // Adjust relative path as needed

// Ensure recruiter is logged in
if (!isset($_SESSION['employer_id'])) {
    header("Location: recLogin.php");
    exit();
}

$employer_id = $_SESSION['employer_id'];

// Check for job_id in GET parameters
if (!isset($_GET['job_id'])) {
    header("Location: dashboard.php");
    exit();
}

$job_id = $_GET['job_id'];

// Fetch all applications for this job regardless of status
try {
    $sql = "SELECT a.application_id, a.status, a.applied_at, j.job_id, j.title AS job_title,
                   u.user_id, u.full_name, u.email
            FROM applications a
            JOIN jobs j ON a.job_id = j.job_id
            JOIN users u ON a.user_id = u.user_id
            WHERE j.employer_id = :employer_id 
              AND a.job_id = :job_id
            ORDER BY a.applied_at DESC";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':employer_id' => $employer_id,
        ':job_id' => $job_id
    ]);
    $applications = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Applicants for Job <?php echo htmlspecialchars($job_id); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
        
        .message-btn {
            background-color: #0d6efd;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .message-btn:hover {
            background-color: #0b5ed7;
        }
    </style>
</head>
<body class="p-3">
    <h2>Applicants for Job #<?php echo htmlspecialchars($job_id); ?></h2>
    
    <div id="message"></div>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <?php if (empty($applications)): ?>
        <div class="alert alert-warning">No applications for this job.</div>
    <?php else: ?>
        <table class="table table-bordered" id="applicationsTable">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Applied At</th>
                    <th>Status</th>
                    <th>Actions</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($applications as $app): 
                    $status = $app['status'] ?? 'pending';
                    $statusClass = ($status === 'hired') 
                                  ? 'status-hired' 
                                  : (($status === 'shortlisted') 
                                     ? 'status-shortlisted' 
                                     : (($status === 'rejected') 
                                        ? 'status-rejected' 
                                        : 'status-pending'));
            ?>
                <tr id="row-<?php echo $app['application_id']; ?>">
                    <td><?php echo htmlspecialchars($app['full_name']); ?></td>
                    <td><?php echo htmlspecialchars($app['email']); ?></td>
                    <td><?php echo htmlspecialchars($app['applied_at']); ?></td>
                    <td class="status-cell">
                        <span class="status-tag <?php echo $statusClass; ?>">
                            <?php echo ucfirst($status); ?>
                        </span>
                    </td>
                    <td>
                        <select class="form-select status-select" 
                                data-application-id="<?php echo $app['application_id']; ?>">
                            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                            <option value="shortlisted" <?php echo $status === 'shortlisted' ? 'selected' : ''; ?>>Shortlist</option>
                            <option value="hired" <?php echo $status === 'hired' ? 'selected' : ''; ?>>Hire</option>
                            <option value="rejected" <?php echo $status === 'rejected' ? 'selected' : ''; ?>>Reject</option>
                        </select>
                        <button class="message-btn mt-2" 
                                data-user-id="<?php echo $app['user_id']; ?>"
                                data-user-name="<?php echo htmlspecialchars($app['full_name']); ?>"
                                onclick="openMessageModal(this)">
                            <i class="fas fa-envelope"></i> Message
                        </button>
                    </td>
                    <td>
                        <a href="application_detail.php?application_id=<?php echo $app['application_id']; ?>" 
                           class="btn btn-info btn-sm">View Details</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    
    <!-- Message Modal -->
    <div class="modal fade" id="messageModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Message to <span id="recipientName"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <textarea id="messageText" class="form-control" rows="4" 
                        placeholder="Type your message here..."></textarea>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="sendMessage()">Send</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    let currentUserId = null;
    const messageModal = new bootstrap.Modal(document.getElementById('messageModal'));

    function openMessageModal(btn) {
        currentUserId = btn.dataset.userId;
        document.getElementById('recipientName').textContent = btn.dataset.userName;
        messageModal.show();
    }

    function sendMessage() {
        if (!currentUserId || !messageText.value.trim()) return;

        fetch('../api/start-conversation.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                user_id: currentUserId,
                message: messageText.value.trim()
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                messageModal.hide();
                document.getElementById('messageText').value = '';
                alert('Message sent successfully!');
            } else {
                alert(data.error || 'Failed to send message');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while sending the message');
        });
    }

    document.addEventListener("DOMContentLoaded", function(){
        document.querySelectorAll(".ajax-status-form").forEach(function(form){
            form.addEventListener("submit", function(e){
                e.preventDefault();
                let newStatus = form.getAttribute("data-new-status");
                // Display confirmation prompt
                if (!confirm("Are you sure you want to change the status to '" + newStatus + "'?")) {
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
                        throw new Error('Network response was not ok. Status code: ' + response.status);
                    }
                    return response.json();
                })
                .then(data => {
                    let messageDiv = document.getElementById("message");
                    if (data.error) {
                        messageDiv.innerHTML = '<div class="alert alert-danger">' + data.error + '</div>';
                    } else if (data.success) {
                        messageDiv.innerHTML = '<div class="alert alert-success">Application status updated to ' + data.new_status + '.</div>';
                        // Update the status tag in the row
                        let row = document.getElementById("row-" + applicationId);
                        if (row) {
                            let statusCell = row.querySelector(".status-cell");
                            let newStatusText = data.new_status.charAt(0).toUpperCase() + data.new_status.slice(1);
                            let newClass = "status-tag ";
                            if (data.new_status === "hired") {
                                newClass += "status-hired";
                            } else if (data.new_status === "shortlisted") {
                                newClass += "status-shortlisted";
                            } else if (data.new_status === "rejected") {
                                newClass += "status-rejected";
                            } else {
                                newClass += "status-pending";
                            }
                            statusCell.innerHTML = '<span class="' + newClass + '">' + newStatusText + '</span>';
                        }
                    }
                })
                .catch(error => {
                    console.error("Fetch error:", error);
                    document.getElementById("message").innerHTML = '<div class="alert alert-danger">An error occurred: ' + error.message + '</div>';
                });
            });
        });
    });
    </script>
</body>
</html>
