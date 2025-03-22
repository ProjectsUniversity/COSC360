// Sample job data - would typically come from an API
const jobs = [
  {
    title: "Software Engineer",
    company: "Tech Corp",
    description: "Looking for a skilled developer with experience in JavaScript and Python.",
    location: "San Francisco, CA",
    salary: "$120k - $150k",
    type: "Full-time"
  },
  {
    title: "Product Manager",
    company: "Biz Solutions",
    description: "Seeking a highly motivated individual with leadership skills.",
    location: "New York, NY",
    salary: "$130k - $160k",
    type: "Full-time"
  },
  {
    title: "Data Analyst",
    company: "Data Insights",
    description: "Strong analytical skills required. Experience in SQL preferred.",
    location: "Remote",
    salary: "$90k - $120k",
    type: "Full-time"
  }
];

class JobCard {
  constructor(isLoggedIn = false) {
    this.currentJobIndex = 0;
    this.jobCard = document.getElementById('job-card');
    this.isLoggedIn = isLoggedIn;
    this.setupEventListeners();
    this.showJob(this.currentJobIndex);
  }

  showJob(index) {
    const job = jobs[index];
    this.jobCard.style.opacity = '0';
    
    setTimeout(() => {
      document.getElementById('job-title').textContent = job.title;
      document.getElementById('company-name').textContent = job.company;
      document.getElementById('job-description').textContent = job.description;
      document.querySelector('.job-meta').innerHTML = `
        <span><i class="fas fa-map-marker-alt"></i> ${job.location}</span>
        <span><i class="fas fa-dollar-sign"></i> ${job.salary}</span>
        <span><i class="fas fa-clock"></i> ${job.type}</span>
      `;
      this.jobCard.style.opacity = '1';
    }, 300);
  }

  nextJob() {
    if (this.currentJobIndex < jobs.length - 1) {
      this.currentJobIndex++;
      this.showJob(this.currentJobIndex);
    } else {
      alert("No more jobs available.");
    }
  }

  rejectJob() {
    this.nextJob();
  }

  applyJob() {
    if (!this.isLoggedIn) {
      document.getElementById('overlayMessage').style.display = 'flex';
    } else {
      // Handle job application for logged-in users
      alert("Application submitted successfully!");
      this.nextJob();
    }
  }

  likeJob() {
    if (!this.isLoggedIn) {
      document.getElementById('overlayMessage').style.display = 'flex';
    } else {
      // Handle job liking for logged-in users
      alert("Job saved to favorites!");
    }
  }

  setupEventListeners() {
    // Keyboard navigation
    document.addEventListener('keydown', (e) => {
      if (e.key === 'ArrowLeft') {
        this.rejectJob();
      } else if (e.key === 'ArrowRight') {
        this.applyJob();
      }
    });

    // Button click handlers
    document.querySelector('.reject').addEventListener('click', () => this.rejectJob());
    document.querySelector('.apply').addEventListener('click', () => this.applyJob());
    if (this.isLoggedIn) {
      document.querySelector('.like').addEventListener('click', () => this.likeJob());
    }
  }
}

// Initialize job card based on login status
document.addEventListener('DOMContentLoaded', () => {
  const isLoggedIn = document.body.classList.contains('logged-in');
  new JobCard(isLoggedIn);
}); 