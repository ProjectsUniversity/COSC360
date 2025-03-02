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
    jobCard.style.opacity = "0";
    // If action is 'apply', move card right; if 'reject', move card left.
    jobCard.style.transform = action === 'apply' ? "translateX(100%)" : "translateX(-100%)";
  
    setTimeout(() => {
      if (action === "apply") {
        window.location.href = "apply.html?job=" + encodeURIComponent(jobs[currentJobIndex].title);
      }
      if (currentJobIndex < jobs.length - 1) {
        currentJobIndex++;
        updateJob();
        jobCard.style.opacity = "1";
        jobCard.style.transform = "translateX(0)";
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
  