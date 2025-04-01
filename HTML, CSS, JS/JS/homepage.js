let currentIndex = 0;
const totalJobs = 10;

function showNextJob() {
    if (currentIndex < totalJobs - 1) {
        currentIndex++;
        updateJobCard();
    }
}

function showPreviousJob() {
    if (currentIndex > 0) {
        currentIndex--;
        updateJobCard();
    }
}

function updateJobCard() {
    document.getElementById('job-title').textContent = `Job Title ${currentIndex + 1}`;
    document.getElementById('company-name').textContent = `Company ${currentIndex + 1}`;
    document.getElementById('job-description').textContent = `Description for job ${currentIndex + 1}`;
}

function likeJob() {
    alert('Feature coming soon!');
}

function shareJob() {
    alert('Feature coming soon!');
}

function saveJob() {
    alert('Feature coming soon!');
}

function applyToJob() {
    window.location.href = 'apply.html';
}

function rejectJob() {
    showNextJob();
}

document.addEventListener('DOMContentLoaded', () => {
    const guestActions = document.querySelectorAll('.guest-action');
    const overlayMessage = document.getElementById('overlayMessage');

    guestActions.forEach(action => {
        action.addEventListener('click', (e) => {
            e.preventDefault();
            overlayMessage.style.display = 'flex';
        });
    });
});
