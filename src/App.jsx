import React from 'react';
import './styles/index.css';
import './styles/homepage.css';

export default function App() {
  return (
    <div>
      <div className="login-buttons">
        <a href="login.html">Login</a>
        <a href="signup.html">Sign Up</a>
      </div>

      <div className="main-header">
        <h1>JobSwipe</h1>
        <p>Swipe your way to your dream job</p>
      </div>

      <div className="sidebar">
        <h2>JobSwipe</h2>
        <a href="#" className="guest-action">Your Account</a>
        <a href="#" className="guest-action">Settings</a>
        <a href="#" className="guest-action">Help</a>
        <a href="login.html">Login</a>
      </div>

      <div className="main-content">
        <div className="job-card" id="job-card">
          <img src="company-logo.png" alt="Company Logo" />
          <h2 id="job-title">Software Engineer</h2>
          <h4 id="company-name">Tech Corp</h4>
          <p id="job-description">Looking for a skilled developer with experience in JavaScript and Python.</p>
          <div className="social-icons">
            <i className="fas fa-heart guest-action"></i>
            <i className="fas fa-bookmark guest-action"></i>
            <i className="fas fa-share guest-action"></i>
          </div>
        </div>
        <div className="controls">
          <button className="guest-action"><i className="fas fa-arrow-left"></i></button>
          <div className="actions">
            <button className="reject guest-action">Reject</button>
            <button className="apply guest-action">Apply</button>
          </div>
          <button className="guest-action"><i className="fas fa-arrow-right"></i></button>
        </div>
      </div>

      <div id="overlayMessage" className="overlay-message">
        <h3>Sign Up to Continue</h3>
        <p>You need to create an account to use this feature.</p>
        <button onClick={() => window.location.href='signup.html'}>Sign Up Now</button>
        <button onClick={() => document.getElementById('overlayMessage').style.display='none'}>Cancel</button>
      </div>
    </div>
  );
}

