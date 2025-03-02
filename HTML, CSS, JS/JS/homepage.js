const jobs = [
    { title: "Software Engineer", company: "Tech Corp", description: "Looking for a skilled developer with experience in JavaScript and Python." },
    { title: "Product Manager", company: "Biz Solutions", description: "Seeking a highly motivated individual with leadership skills." },
    { title: "Data Analyst", company: "Data Insights", description: "Strong analytical skills required. Experience in SQL preferred." }
  ];
  
  let currentJobIndex = 0;
  
  function updateJob() {
    document.getElementById("job-title").textContent = jobs[currentJobIndex].title;
    document.getElementById("company-name").textContent = jobs[currentJobIndex].company;
    document.getElementById("job-description").textContent = jobs[currentJobIndex].description;
  }
  
  function nextJob(action) {
    const jobCard = document.getElementById("job-card");
    jobCard.classList.add('card-hidden');
    
    // If action is 'apply', move card right; if 'reject', move card left.
    if (action === 'apply') {
      jobCard.classList.add('card-move-right');
    } else {
      jobCard.classList.add('card-move-left');
    }
  
    setTimeout(() => {
      if (action === "apply") {
        window.location.href = "apply.html?job=" + encodeURIComponent(jobs[currentJobIndex].title);
      }
      if (currentJobIndex < jobs.length - 1) {
        currentJobIndex++;
        updateJob();
        jobCard.classList.remove('card-hidden', 'card-move-right', 'card-move-left');
        jobCard.classList.add('card-visible');
      } else {
        alert("No more jobs available.");
      }
    }, 400);
  }
  
  function likeJob() {
    alert("You liked this job!");
  }
  
  function saveJob() {
    alert("Job saved!");
  }
  