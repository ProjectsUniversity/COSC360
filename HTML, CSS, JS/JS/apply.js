const params = new URLSearchParams(window.location.search);
const jobTitle = params.get('job');
if (jobTitle) {
  document.getElementById('job-title').textContent = jobTitle.replace('-', ' ');
}
