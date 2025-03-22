import React, { useState } from 'react';
import './styles/index.css';
import './styles/homepage.css';
import SignUp from './components/SignUp';
import Login from './components/Login';

export default function App() {
  const [currentView, setCurrentView] = useState('home');  // possible values: 'home', 'login', 'signup'
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
    alert("Please login or sign up to like jobs!");
    setCurrentView('signup');
  };

  const saveJob = () => {
    alert("Please login or sign up to save jobs!");
    setCurrentView('signup');
  };

  const goToApply = () => {
    alert("Please login or sign up to apply for jobs!");
    setCurrentView('signup');
  };

  const handleSignUpClick = () => {
    setCurrentView('signup');
  };

  const handleLoginClick = () => {
    setCurrentView('login');
  };

  const handleClose = () => {
    setCurrentView('home');
  };

  // Render appropriate component based on currentView
  if (currentView === 'signup') {
    return <SignUp onClose={handleClose} onLoginClick={handleLoginClick} />;
  }

  if (currentView === 'login') {
    return <Login onClose={handleClose} onSignUpClick={handleSignUpClick} />;
  }

  // Default home view
  return (
    <div>
      <div className="login-buttons">
        <a href="#" onClick={(e) => { e.preventDefault(); handleLoginClick(); }}>Login</a>
        <a href="#" onClick={(e) => { e.preventDefault(); handleSignUpClick(); }}>Sign Up</a>
      </div>

      <div className="sidebar">
        <h2>JobSwipe</h2>
        <a href="#" onClick={(e) => { e.preventDefault(); handleLoginClick(); }}>Your Account</a>
        <a href="#" onClick={(e) => { e.preventDefault(); handleLoginClick(); }}>Settings</a>
        <a href="#" onClick={(e) => { e.preventDefault(); handleLoginClick(); }}>Help</a>
        <a href="#" onClick={(e) => { e.preventDefault(); handleLoginClick(); }}>Login</a>
      </div>

      <div className="main-content" style={{textAlign: "center"}}>
        <div className="job-card" id="job-card" style={{margin: "auto", height: "80%", width: "35vw"}}>
          <img src="company-logo.png" alt="Company Logo" />
          <h2 id="job-title">{jobs[currentJobIndex].title}</h2>
          <h4 id="company-name">{jobs[currentJobIndex].company}</h4>
          <p id="job-description">{jobs[currentJobIndex].description}</p>
          <div className="social-icons">
            <i className="fas fa-heart" onClick={likeJob}></i>
            <i className="fas fa-bookmark" onClick={saveJob}></i>
            <i className="fas fa-share" onClick={handleSignUpClick}></i>
          </div>
        </div>
        <div className="controls" style={{margin: "auto"}}>
          <button onClick={previousJob}><i className="fas fa-arrow-left"></i></button>
          <div className="actions">
            <button className="reject" onClick={() => nextJob('reject')}>Reject</button>
            <button className="apply" onClick={goToApply}>Apply</button>
          </div>
          <button onClick={() => nextJob('apply')}><i className="fas fa-arrow-right"></i></button>
        </div>
      </div>
    </div>
  );
}

