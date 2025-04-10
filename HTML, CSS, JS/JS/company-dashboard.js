function openModal(type) {
    const modal = document.getElementById('editModal');
    modal.style.display = 'block';
}

function closeModal() {
    document.getElementById('editModal').style.display = 'none';
}

function handleFilter(event) {
    event.preventDefault();
    const filters = {
        jobType: document.getElementById('jobType').value,
        location: document.getElementById('location').value
    };

    filterJobs(filters);
    closeModal();
    return false;
}

function filterJobs(filters) {
    const jobCards = document.querySelectorAll('.job-card');
    
    jobCards.forEach(card => {
        const jobDetails = card.querySelector('.job-details').textContent.toLowerCase();
        const jobTitle = card.querySelector('h3').textContent.toLowerCase();
        let visible = true;

        if (filters.jobType && !jobDetails.includes(filters.jobType.toLowerCase())) {
            visible = false;
        }
        
        if (filters.location && !jobDetails.includes(filters.location.toLowerCase())) {
            visible = false;
        }

        card.style.display = visible ? 'flex' : 'none';
    });
}

document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.filter-button[data-type="filter"]').forEach(button => {
        button.addEventListener('click', () => openModal('filter'));
    });

    document.querySelector('.close').addEventListener('click', closeModal);
});