import React, { useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../../../HTML, CSS, JS/CSS/Recruiters/recLogin.css';

const RecruiterLogin = ({ onClose, onJobSeekerLoginClick, onRecruiterSignUpClick }) => {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        rememberMe: false
    });

    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value,
        });
        
        // Clear error when user starts typing in a field
        if (errors[name]) {
            setErrors({
                ...errors,
                [name]: ''
            });
        }
    };

    const validateForm = () => {
        const newErrors = {};
        
        if (!formData.email.trim()) {
            newErrors.email = 'Email is required';
        }
        
        if (!formData.password) {
            newErrors.password = 'Password is required';
        }
        
        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (validateForm()) {
            console.log('Form submitted successfully:', formData);
            // Navigate to dashboard without React Router
            window.location.href = '/recruiter/dashboard';
        }
    };

    const handleJobSeekerLogin = (e) => {
        e.preventDefault();
        if (onJobSeekerLoginClick) {
            onJobSeekerLoginClick();
        } else {
            window.location.href = '/login';
        }
    };

    const handleRecruiterSignUp = (e) => {
        e.preventDefault();
        if (onRecruiterSignUpClick) {
            onRecruiterSignUpClick();
        } else {
            window.location.href = '/recruiter/signup';
        }
    };

    return (
        <div className="container">
            <div className="login-container position-relative">
                {onClose && (
                    <button 
                        onClick={onClose}
                        className="btn-close position-absolute"
                        style={{ top: '1rem', right: '1rem' }}
                        aria-label="Close"
                    />
                )}
                <h2 className="form-title">Recruiter Login</h2>
                <form id="recruiterLoginForm" onSubmit={handleSubmit}>
                    <div className="mb-3">
                        <label htmlFor="email" className="form-label">Email address</label>
                        <input 
                            type="email" 
                            className={`form-control ${errors.email ? 'is-invalid' : ''}`} 
                            id="email" 
                            name="email" 
                            value={formData.email}
                            onChange={handleChange}
                        />
                        {errors.email && <div className="invalid-feedback">{errors.email}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="password" className="form-label">Password</label>
                        <input 
                            type="password" 
                            className={`form-control ${errors.password ? 'is-invalid' : ''}`} 
                            id="password" 
                            name="password" 
                            value={formData.password}
                            onChange={handleChange}
                        />
                        {errors.password && <div className="invalid-feedback">{errors.password}</div>}
                    </div>
                    <div className="mb-3 form-check">
                        <input 
                            type="checkbox" 
                            className="form-check-input" 
                            id="rememberMe" 
                            name="rememberMe"
                            checked={formData.rememberMe}
                            onChange={handleChange}
                        />
                        <label className="form-check-label" htmlFor="rememberMe">Remember me</label>
                    </div>
                    <button type="submit" className="btn btn-primary w-100">Login</button>
                    <div className="mt-3 text-center">
                        <p>Don't have a recruiter account? <a href="#" onClick={handleRecruiterSignUp}>Sign Up as Recruiter</a></p>
                        <p><a href="#" onClick={handleJobSeekerLogin}>Login as a job seeker</a></p>
                        <p><a href="#" className="text-muted">Forgot Password?</a></p>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default RecruiterLogin;
