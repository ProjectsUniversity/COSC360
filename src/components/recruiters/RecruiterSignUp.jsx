import React, { useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../../../HTML, CSS, JS/CSS/Recruiters/recSignUp.css';

const RecruiterSignUp = ({ onClose, onLoginClick, onJobSeekerSignUpClick }) => {
    const [formData, setFormData] = useState({
        email: '',
        password: '',
        confirmPassword: '',
        companyName: '',
        jobTitle: '',
        companySize: '',
        industry: '',
        terms: false,
    });

    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value, type, checked } = e.target;
        setFormData({
            ...formData,
            [name]: type === 'checkbox' ? checked : value,
        });

        // Clear error when user starts typing
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
        
        if (!formData.confirmPassword) {
            newErrors.confirmPassword = 'Please confirm your password';
        } else if (formData.password !== formData.confirmPassword) {
            newErrors.confirmPassword = 'Passwords do not match';
        }
        
        if (!formData.companyName.trim()) {
            newErrors.companyName = 'Company name is required';
        }
        
        if (!formData.jobTitle.trim()) {
            newErrors.jobTitle = 'Job title is required';
        }
        
        if (!formData.terms) {
            newErrors.terms = 'You must agree to the Terms and Conditions';
        }

        setErrors(newErrors);
        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        
        if (validateForm()) {
            console.log('Form submitted successfully:', formData);
            window.location.href = '/recruiter/dashboard';
        }
    };

    const handleJobSeekerSignUp = (e) => {
        e.preventDefault();
        if (onJobSeekerSignUpClick) {
            onJobSeekerSignUpClick();
        } else {
            window.location.href = '/signup';
        }
    };

    return (
        <div className="container">
            <div className="signup-container">
                <h2 className="form-title">Recruiter Sign Up</h2>
                {onClose && (
                  <button 
                    onClick={onClose}
                    className="btn-close position-absolute top-0 end-0 m-3"
                    aria-label="Close"
                  />
                )}
                <form id="recruiterSignupForm" onSubmit={handleSubmit}>
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
                        <div className="form-text">We'll never share your email with anyone else.</div>
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
                        <div className="form-text">Password must be at least 8 characters and include uppercase, lowercase, number, and special character.</div>
                    </div>
                    <div className="mb-3">
                        <label htmlFor="confirmPassword" className="form-label">Confirm Password</label>
                        <input 
                            type="password" 
                            className={`form-control ${errors.confirmPassword ? 'is-invalid' : ''}`}
                            id="confirmPassword" 
                            name="confirmPassword" 
                            value={formData.confirmPassword} 
                            onChange={handleChange} 
                        />
                        {errors.confirmPassword && <div className="invalid-feedback">{errors.confirmPassword}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="companyName" className="form-label">Company Name</label>
                        <input 
                            type="text" 
                            className={`form-control ${errors.companyName ? 'is-invalid' : ''}`}
                            id="companyName" 
                            name="companyName" 
                            value={formData.companyName} 
                            onChange={handleChange} 
                        />
                        {errors.companyName && <div className="invalid-feedback">{errors.companyName}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="jobTitle" className="form-label">Your Job Title</label>
                        <input 
                            type="text" 
                            className={`form-control ${errors.jobTitle ? 'is-invalid' : ''}`}
                            id="jobTitle" 
                            name="jobTitle" 
                            value={formData.jobTitle} 
                            onChange={handleChange} 
                        />
                        {errors.jobTitle && <div className="invalid-feedback">{errors.jobTitle}</div>}
                    </div>
                    <div className="mb-3">
                        <label htmlFor="companySize" className="form-label">Company Size</label>
                        <select 
                            className="form-select" 
                            id="companySize" 
                            name="companySize" 
                            value={formData.companySize} 
                            onChange={handleChange}
                        >
                            <option value="1-10">1-10 employees</option>
                            <option value="11-50">11-50 employees</option>
                            <option value="51-200">51-200 employees</option>
                            <option value="201-500">201-500 employees</option>
                            <option value="501-1000">501-1000 employees</option>
                            <option value="1000+">1000+ employees</option>
                        </select>
                    </div>
                    <div className="mb-3">
                        <label htmlFor="industry" className="form-label">Industry</label>
                        <input 
                            type="text" 
                            className="form-control" 
                            id="industry" 
                            name="industry" 
                            value={formData.industry} 
                            onChange={handleChange} 
                        />
                    </div>
                    <div className="mb-3 form-check">
                        <input 
                            type="checkbox" 
                            className={`form-check-input ${errors.terms ? 'is-invalid' : ''}`}
                            id="terms" 
                            name="terms" 
                            checked={formData.terms} 
                            onChange={handleChange} 
                        />
                        <label className="form-check-label" htmlFor="terms">I agree to the Terms and Conditions</label>
                        {errors.terms && <div className="invalid-feedback">{errors.terms}</div>}
                    </div>
                    <button type="submit" className="btn btn-primary w-100">Create Recruiter Account</button>
                    <div className="mt-3 text-center">
                        <p>Already have an account? <a href="#" onClick={(e) => {
                            e.preventDefault();
                            onLoginClick();
                        }}>Login as Recruiter</a></p>
                        <p>Looking for a job? <a href="#" onClick={handleJobSeekerSignUp}>Sign up as a job seeker</a></p>
                    </div>
                </form>
            </div>
        </div>
    );
};

export default RecruiterSignUp;