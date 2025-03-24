const params = new URLSearchParams(window.location.search);
const jobId = params.get('job_id');

if (!jobId) {
    window.location.href = 'homepage.php';
}

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('application-form');
    
    if (form) {
        form.addEventListener('submit', function(event) {
            const coverLetter = document.getElementById('cover-letter').value.trim();
            
            if (!coverLetter) {
                event.preventDefault();
                alert('Please enter a cover letter');
                return;
            }
            
            // Show loading state
            const submitButton = form.querySelector('button[type="submit"]');
            if (submitButton) {
                submitButton.disabled = true;
                submitButton.textContent = 'Submitting...';
            }
        });
    }
});
