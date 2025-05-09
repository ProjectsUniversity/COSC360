// Get jobs data from PHP
let jobs = [];
let currentIndex = 0;
let lastUpdateTime = new Date().toISOString();

// Function to format date strings
function formatDate(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffTime = Math.abs(now - date);
    const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
    
    if (diffDays === 0) {
        return 'Today';
    } else if (diffDays === 1) {
        return 'Yesterday';
    } else if (diffDays < 7) {
        return `${diffDays} days ago`;
    } else {
        return date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
    }
}

// Function to format salary numbers
function formatSalary(salary) {
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'USD',
        minimumFractionDigits: 0,
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
    
    // Update company logo
    const companyLogo = document.getElementById('company-logo');
    companyLogo.src = job.profile_image ? '../../' + job.profile_image : '../images/default-company-logo.png';
    
    const locationSpan = document.getElementById('job-location');
    locationSpan.innerHTML = `<i class="fas fa-map-marker-alt"></i> ${job.location}`;
    
    const salarySpan = document.getElementById('job-salary');
    salarySpan.innerHTML = `<i class="fas fa-dollar-sign"></i> ${formatSalary(job.salary)}`;
    
    const postedSpan = document.getElementById('job-posted');
    postedSpan.innerHTML = `<i class="fas fa-calendar"></i> ${formatDate(job.created_at)}`;

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

// Search functionality
let searchTimeout;

function createSearchContainer() {
    const searchContainer = document.createElement('div');
    searchContainer.className = 'search-container';
    
    const searchInput = document.createElement('input');
    searchInput.type = 'text';
    searchInput.className = 'search-bar';
    searchInput.placeholder = 'Search jobs by title...';
    
    const searchResults = document.createElement('div');
    searchResults.className = 'search-results';
    
    searchContainer.appendChild(searchInput);
    searchContainer.appendChild(searchResults);
    
    const mainContent = document.querySelector('.main-content');
    mainContent.insertBefore(searchContainer, mainContent.firstChild);
    
    return { searchInput, searchResults };
}

function handleSearch(searchInput, searchResults) {
    const query = searchInput.value.trim();
    
    if (query.length < 2) {
        searchResults.classList.remove('active');
        return;
    }

    // Show loading state
    searchResults.innerHTML = '<div class="search-result-item">Searching...</div>';
    searchResults.classList.add('active');

    // Make AJAX request to search jobs
    fetch(`../PHP/search-jobs.php?query=${encodeURIComponent(query)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            searchResults.innerHTML = '';
            
            if (data.error) {
                throw new Error(data.message || 'Search failed');
            }
            
            if (data.length === 0) {
                const noResults = document.createElement('div');
                noResults.className = 'search-result-item';
                noResults.textContent = 'No jobs found';
                searchResults.appendChild(noResults);
            } else {
                data.forEach(job => {
                    const resultItem = document.createElement('div');
                    resultItem.className = 'search-result-item';
                    resultItem.innerHTML = `
                        <h3>${job.title}</h3>
                        <p>${job.company_name} - ${job.location}</p>
                        <p>${formatSalary(job.salary)}</p>
                    `;
                    
                    resultItem.addEventListener('click', () => {
                        const jobIndex = jobs.findIndex(j => j.job_id === job.job_id);
                        if (jobIndex !== -1) {
                            currentIndex = jobIndex;
                            updateJobCard();
                            searchResults.classList.remove('active');
                            searchInput.value = '';
                        }
                    });
                    
                    searchResults.appendChild(resultItem);
                });
            }
            
            searchResults.classList.add('active');
        })
        .catch(error => {
            console.error('Error searching jobs:', error);
            searchResults.innerHTML = `<div class="search-result-item">Error: ${error.message}</div>`;
            searchResults.classList.add('active');
        });
}

// Initialize jobs data and card
function initializeJobs(jobsData) {
    jobs = jobsData;
    if (jobs.length > 0) {
        // Use the most recent job's creation time as last update time
        lastUpdateTime = jobs[0].created_at;
        updateJobCard();
        // Start polling for new jobs
        startPolling();
    }
    
    // Initialize search functionality
    const { searchInput, searchResults } = createSearchContainer();
    
    // Add event listeners for search
    searchInput.addEventListener('input', () => {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            handleSearch(searchInput, searchResults);
        }, 300); // Debounce search for 300ms
    });
    
    // Close search results when clicking outside
    document.addEventListener('click', (e) => {
        if (!searchContainer.contains(e.target)) {
            searchResults.classList.remove('active');
        }
    });
}

// Check for new jobs
function checkForNewJobs() {
    fetch(`api/job-updates.php?lastUpdate=${encodeURIComponent(lastUpdateTime)}`)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.jobs && data.jobs.length > 0) {
                console.log('New jobs found:', data.jobs);
                // Add new jobs to the end of the array instead of the beginning
                jobs.push(...data.jobs);
                // Update the last update time
                lastUpdateTime = data.currentTime;
                // Move to the newest job that was just added
                currentIndex = jobs.length - 1;
                updateJobCard();
            }
        })
        .catch(error => {
            console.error('Error checking for new jobs:', error);
        });
}

// Start polling with a faster initial check
function startPolling() {
    // Check immediately after initialization
    setTimeout(checkForNewJobs, 1000);
    // Then check every 5 seconds
    setInterval(checkForNewJobs, 5000);
}

// Check for unread messages
function checkUnreadMessages() {
    if (!document.body.dataset.userId) return; // Only check if user is logged in
    
    fetch('api/get-message-count.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.getElementById('unread-badge');
                if (badge) {
                    if (data.unread_count > 0) {
                        badge.textContent = data.unread_count;
                        badge.style.display = 'inline';
                    } else {
                        badge.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => console.error('Error checking unread messages:', error));
}

// Initialize unread message checking
if (document.body.dataset.userId) {
    checkUnreadMessages();
    setInterval(checkUnreadMessages, 10000); // Check every 10 seconds
}
