// Get jobs data from PHP
let jobs = [];
let currentIndex = 0;

// Function to format date strings
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

// Function to format salary numbers
function formatSalary(salary) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        maximumFractionDigits: 0
    }).format(salary);
}

// Update the job card with current job details
function updateJobCard() {
    if (jobs.length === 0) return;
    
    const job = jobs[currentIndex];
    document.getElementById('job-title').textContent = job.title;
    document.getElementById('company-link').textContent = job.company_name;
    document.getElementById('job-description').textContent = job.description;
    
    const locationSpan = document.getElementById('job-location');
    locationSpan.textContent = job.location;
    
    const salarySpan = document.getElementById('job-salary');
    salarySpan.textContent = formatSalary(job.salary);
    
    const postedSpan = document.getElementById('job-posted');
    postedSpan.textContent = formatDate(job.created_at);
    
    // Reset saved state
    const saveBtn = document.getElementById('save-btn');
    if (saveBtn) {
        saveBtn.classList.remove('saved');
        saveBtn.innerHTML = '<i class="fas fa-bookmark"></i><span>Save Job</span>';
    }
}

// Navigate to next job
function showNextJob() {
    if (currentIndex < jobs.length - 1) {
        currentIndex++;
        updateJobCard();
    }
}

// Navigate to previous job
function showPreviousJob() {
    if (currentIndex > 0) {
        currentIndex--;
        updateJobCard();
    }
}

// View company profile
function viewCompanyProfile() {
    const currentJob = jobs[currentIndex];
    if (currentJob?.employer_id) {
        window.location.href = `company-dashboard.php?employer_id=${currentJob.employer_id}`;
    }
}

// Handle job application
function applyToJob() {
    const currentJob = jobs[currentIndex];
    if (!document.body.dataset.userId) {
        window.location.href = 'login.php';
    } else {
        window.location.href = `apply.php?job_id=${currentJob.job_id}`;
    }
}

// Handle job rejection
function rejectJob() {
    showNextJob();
}

// Save current job
function saveJob() {
    const currentJob = jobs[currentIndex];
    if (!document.body.dataset.userId) {
        window.location.href = 'login.php';
        return;
    }

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
            const saveBtn = document.getElementById('save-btn');
            saveBtn.classList.add('saved');
            saveBtn.innerHTML = '<i class="fas fa-bookmark"></i><span>Saved</span>';
        } else {
            alert(data.message || 'Failed to save job');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while saving the job');
    });
}

// Initialize jobs data and card
function initializeJobs(jobsData) {
    jobs = jobsData;
    if (jobs.length > 0) {
        updateJobCard();
    }
}
