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
            const resume = document.getElementById('resume').value;
            
            if (!coverLetter || !resume) {
                event.preventDefault();
                alert('Please fill in all required fields');
            }
        });
    }
});
