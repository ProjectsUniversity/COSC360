// Sample job data - would typically come from an API
const jobs = [
  { 
    title: "Software Engineer", 
    company: "Tech Corp", 
    description: "Looking for a skilled developer with experience in JavaScript and Python." 
  },
  { 
    title: "Product Manager", 
    company: "Biz Solutions", 
    description: "Seeking a highly motivated individual with leadership skills." 
  },
  { 
    title: "Data Analyst", 
    company: "Data Insights", 
    description: "Strong analytical skills required. Experience in SQL preferred." 
  }
];

let currentJobIndex = 0;

function updateJob() {
  document.getElementById("job-title").textContent = jobs[currentJobIndex].title;
  document.getElementById("company-name").textContent = jobs[currentJobIndex].company;
  document.getElementById("job-description").textContent = jobs[currentJobIndex].description;
}

function previousJob() {
  if (currentJobIndex > 0) {
    currentJobIndex--;
    showJob(currentJobIndex);
  } else {
    alert("This is the first job.");
  }
}

function nextJob() {
  if (currentJobIndex < jobs.length - 1) {
    currentJobIndex++;
    showJob(currentJobIndex);
  } else {
    alert("No more jobs available.");
  }
}

function showJob(index) {
  const job = jobs[index];
  document.getElementById('job-title').textContent = job.title;
  document.getElementById('company-name').textContent = job.company;
  document.getElementById('job-description').textContent = job.description;
}

function hideOverlay() {
  document.getElementById('overlayMessage').style.display = 'none';
}

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
  // Initialize with first job
  showJob(currentJobIndex);
  
  // Set up guest action handlers
  const guestActions = document.querySelectorAll('.guest-action');
  guestActions.forEach(element => {
    element.addEventListener('click', function(e) {
      e.preventDefault();
      document.getElementById('overlayMessage').style.display = 'block';
    });
  });
  
  // Set up control buttons
  const rejectBtn = document.querySelector('.control-btn.reject');
  const likeBtn = document.querySelector('.control-btn.like');
  const applyBtn = document.querySelector('.control-btn.apply');
  
  if (rejectBtn) rejectBtn.addEventListener('click', () => nextJob());
  if (likeBtn) likeBtn.addEventListener('click', () => alert("Job liked!"));
  if (applyBtn) applyBtn.addEventListener('click', () => window.location.href = 'apply.html?job=' + encodeURIComponent(jobs[currentJobIndex].title));
});
