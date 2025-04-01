function unsaveJob(jobId) {
    fetch('api/unsave-job.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ job_id: jobId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Remove the job card from the page
            const jobCard = document.querySelector(`.job-card[data-job-id="${jobId}"]`);
            jobCard.style.opacity = '0';
            setTimeout(() => {
                jobCard.remove();
                // If no more jobs, show the empty state
                if (document.querySelectorAll('.job-card').length === 0) {
                    const container = document.querySelector('.saved-jobs-container');
                    container.innerHTML = `
                        <div class="no-jobs-message">
                            <i class="fas fa-bookmark"></i>
                            <p>You haven't saved any jobs yet.</p>
                            <a href="homepage.php" class="browse-jobs-btn">Browse Jobs</a>
                        </div>
                    `;
                }
            }, 300);
        }
    })
    .catch(error => console.error('Error:', error));
}

function applyToJob(jobId) {
    window.location.href = `apply.php?job_id=${jobId}`;
}

function toggleDescription(element) {
    element.classList.toggle('expanded');
    const button = element.nextElementSibling.querySelector('.expand-btn');
    if (element.classList.contains('expanded')) {
        button.textContent = 'Show Less';
    } else {
        button.textContent = 'Show More';
    }
}

// Add click handlers for job descriptions
document.addEventListener('DOMContentLoaded', function() {
    const descriptions = document.querySelectorAll('.job-description');
    descriptions.forEach(desc => {
        if (desc.scrollHeight > 100) {
            const expandBtn = document.createElement('button');
            expandBtn.className = 'expand-btn';
            expandBtn.textContent = 'Show More';
            expandBtn.onclick = () => toggleDescription(desc);
            desc.parentElement.querySelector('.job-actions').insertBefore(
                expandBtn, 
                desc.parentElement.querySelector('.apply-btn')
            );
        }
    });
});