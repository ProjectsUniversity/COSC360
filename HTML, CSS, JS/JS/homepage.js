let currentJobIndex = 0;

function formatSalary(salary) {
    if (!salary) return 'Salary not specified';
    return new Intl.NumberFormat('en-US', { 
        style: 'currency', 
        currency: 'USD',
        maximumFractionDigits: 0 
    }).format(salary);
}

function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) return 'Today';
    if (diffDays === 1) return 'Yesterday';
    if (diffDays < 7) return `${diffDays} days ago`;
    if (diffDays < 30) return `${Math.floor(diffDays / 7)} weeks ago`;
    return `${Math.floor(diffDays / 30)} months ago`;
}

function displayJob(index) {
    if (index >= 0 && index < jobs.length) {
        const job = jobs[index];
        document.getElementById('company-logo').src = 'company-logo.png';
        document.getElementById('job-title').textContent = job.title;
        document.getElementById('company-name').textContent = job.company_name;
        document.getElementById('job-description').textContent = job.description;
        document.getElementById('job-location').innerHTML = 
            `<i class="fas fa-map-marker-alt"></i> ${job.location || 'Remote/Various'}`;
        document.getElementById('job-salary').innerHTML = 
            `<i class="fas fa-dollar-sign"></i> ${formatSalary(job.salary)}`;
        document.getElementById('job-posted').innerHTML = 
            `<i class="fas fa-calendar"></i> Posted ${formatDate(job.created_at)}`;
    }
}

function nextJob(action) {
    if (action === 'reject' || action === 'apply') {
        currentJobIndex++;
    }
    
    if (currentJobIndex >= jobs.length) {
        currentJobIndex = 0;
    }
    displayJob(currentJobIndex);
}

function applyToJob() {
    const currentJob = jobs[currentJobIndex];
    if (!currentJob) return;
    window.location.href = `apply.php?job_id=${currentJob.job_id}`;
}

function likeJob() {
    // TODO: Implement like functionality with database
    alert("Feature coming soon!");
}

function saveJob() {
    const currentJob = jobs[currentJobIndex];
    if (!currentJob) return;

    fetch('api/save-job.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ job_id: currentJob.job_id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const saveBtn = document.querySelector('.fa-bookmark').closest('i');
            saveBtn.style.color = '#2196f3';  // Change color to indicate saved state
            alert('Job saved successfully!');
        } else if (data.error === 'already_saved') {
            alert('You have already saved this job');
        }
    })
    .catch(error => console.error('Error:', error));
}

function shareJob() {
    const currentJob = jobs[currentJobIndex];
    if (!currentJob) return;
    // Basic share functionality
    if (navigator.share) {
        navigator.share({
            title: currentJob.title,
            text: `Check out this job: ${currentJob.title} at ${currentJob.company_name}`,
            url: window.location.href
        }).catch(console.error);
    } else {
        alert("Share functionality coming soon!");
    }
}

document.addEventListener('DOMContentLoaded', function() {
    if (jobs && jobs.length > 0) {
        displayJob(currentJobIndex);
    }
});
