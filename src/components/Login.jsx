import React, { useState } from 'react';
import '../styles/Login.css';

export default function Login({ onClose, onSignUpClick }) {
  const [formData, setFormData] = useState({
    email: '',
    password: '',
    rememberMe: false
  });

  const handleSubmit = (e) => {
    e.preventDefault();
    // TODO: Implement actual login logic
    window.location.href = '/homepage';
  };

  const handleInputChange = (e) => {
    const { name, value, type, checked } = e.target;
    setFormData(prev => ({
      ...prev,
      [name]: type === 'checkbox' ? checked : value
    }));
  };

  return (
    <div className="container">
      <div className="login-container">
        <h2 className="form-title">Login</h2>
        {onClose && (
          <button 
            onClick={onClose}
            className="close-button"
            aria-label="Close"
          >×</button>
        )}
        <form onSubmit={handleSubmit}>
          <div className="mb-3">
            <label htmlFor="email" className="form-label">Email address or Username</label>
            <input
              type="text"
              className="form-control"
              id="email"
              name="email"
              value={formData.email}
              onChange={handleInputChange}
            />
          </div>
          <div className="mb-3">
            <label htmlFor="password" className="form-label">Password</label>
            <input
              type="password"
              className="form-control"
              id="password"
              name="password"
              value={formData.password}
              onChange={handleInputChange}
            />
          </div>
          <div className="mb-3 form-check">
            <input
              type="checkbox"
              className="form-check-input"
              id="rememberMe"
              name="rememberMe"
              checked={formData.rememberMe}
              onChange={handleInputChange}
            />
            <label className="form-check-label" htmlFor="rememberMe">Remember me</label>
          </div>
          <button type="submit" className="btn btn-primary w-100">Login</button>
          <div className="mt-3 text-center">
            <p>Don't have an account? <a href="#" onClick={(e) => {
              e.preventDefault();
              onSignUpClick();
            }}>Sign Up</a></p>
            <a href="/recruiter/login">Login as a recruiter</a>
            <p><a href="#" className="text-muted">Forgot Password?</a></p>
            <p><a href="/">Continue as guest</a></p>
          </div>
        </form>
      </div>
    </div>
  );
}