import React, { useState } from 'react';
import './styles/index.css';
import './styles/homepage.css';
import SignUp from './components/SignUp';

export default function App() {
  const [showSignUp, setShowSignUp] = useState(false);
  const [jobs, setJobs] = useState([
    { title: "Software Engineer", company: "Tech Corp", description: "Looking for a skilled developer with experience in JavaScript and Python." },
    { title: "Product Manager", company: "Biz Solutions", description: "Seeking a highly motivated individual with leadership skills." },
    { title: "Data Analyst", company: "Data Insights", description: "Strong analytical skills required. Experience in SQL preferred." }
  ]);

  const [currentJobIndex, setCurrentJobIndex] = useState(0);

  const nextJob = () => {
    if (currentJobIndex < jobs.length - 1) {
      setCurrentJobIndex(prevIndex => prevIndex + 1);
    } else {
      alert("No more jobs available.");
    }
  };

  const previousJob = () => {
    if (currentJobIndex > 0) {
      setCurrentJobIndex(prevIndex => prevIndex - 1);
    } else {
      alert("This is the first job.");
    }
  };

  const likeJob = () => {
    alert("You liked this job!");
  };

  const saveJob = () => {
    alert("Job saved!");
  };

  const goToApply = () => {
    window.location.href = "apply.html?job=" + encodeURIComponent(jobs[currentJobIndex].title);
  };

  const handleSignUpClose = () => {
    setShowSignUp(false);
  };

  if (showSignUp) {
    return <SignUp onClose={handleSignUpClose} />;
  }

  return (
    <div>
      <div className="login-buttons">
        <a href="login.html">Login</a>
        <a href="#" onClick={(e) => { e.preventDefault(); setShowSignUp(true); }}>Sign Up</a>
      </div>
      <div className="sidebar">
        <h2>JobSwipe</h2>
        <a href="userprofile.html">Your Account</a>
        <a href="#">Settings</a>
        <a href="#">Help</a>
        <a href="login.html">Login</a>
      </div>

      <div className="main-content">
        <div className="job-card" id="job-card">
          <img src="company-logo.png" alt="Company Logo" />
          <h2 id="job-title">{jobs[currentJobIndex].title}</h2>
          <h4 id="company-name">{jobs[currentJobIndex].company}</h4>
          <p id="job-description">{jobs[currentJobIndex].description}</p>
          <div className="social-icons">
            <i className="fas fa-heart" onClick={likeJob}></i>
            <i className="fas fa-bookmark" onClick={saveJob}></i>
            <i className="fas fa-share"></i>
          </div>
        </div>
        <div className="controls">
          <button onClick={previousJob}><i className="fas fa-arrow-left"></i></button>
          <div className="actions">
            <button className="reject" onClick={previousJob}>Reject</button>
            <button className="apply" onClick={goToApply}>Apply</button>
          </div>
          <button onClick={nextJob}><i className="fas fa-arrow-right"></i></button>
        </div>
      </div>
    </div>
  );
}

